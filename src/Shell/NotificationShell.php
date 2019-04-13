<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\I18n\Time;
use Cake\Utility\Text;
use Cake\Log\Log;


class NotificationShell extends Shell
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Users');
        $this->loadModel('Orders');
        $this->loadModel('Students');
        $this->loadModel('Notifications');
        $this->loadModel('NotificationSettings');
    }


    public function main()
    {
        $order = $this->Orders->find('list')->toArray();
        $this->out(print_r($order));
    }

    public function checkInterviewDate()
    {
        $setting = $this->NotificationSettings->get(1);
        $receiversArr = explode(',', $setting->receivers_groups);
        array_shift($receiversArr);
        array_pop($receiversArr);
        $now = Time::now()->addDays($setting->send_before)->i18nFormat('yyyy-MM-dd');
        $orders = $this->Orders->find()->where(['interview_date' => $now, 'del_flag' => FALSE]);
        $data = [];
        foreach ($orders as $key => $order) {
            foreach ($receiversArr as $key => $role) {
                $receivers = $this->Users->find()->where(['role_id' => $role, 'del_flag' => FALSE]);
                foreach ($receivers as $user) {
                    $noti = [
                        'user_id' => $user->id,
                        'content' => Text::insert($setting->template, [
                            'time' => $order->interview_date->i18nFormat('dd-MM-yyyy'),
                            'orderName' => $order->name
                        ]),
                        'url' => '/orders/view/' . $order->id
                    ];
                    array_push($data, $noti);
                }
            }
        }

        $entities = $this->Notifications->newEntities($data);
        // save to db
        $this->Notifications->saveMany($entities);
    }

    public function checkHealthCheckDate()
    {
        $setting = $this->NotificationSettings->get(2);
        $receiversArr = explode(',', $setting->receivers_groups);
        array_shift($receiversArr);
        array_pop($receiversArr);
        $now = Time::now()->addDays($setting->send_before)->i18nFormat('yyyy-MM-dd');
        $students = $this->Students->find()
            ->contain([
                'PhysicalExams' => function ($q) use ($now) {
                    return $q->where(['PhysicalExams.exam_date' => $now]);
                }
            ])
            ->where(['Students.del_flag' => FALSE]);
        $data = [];
        foreach ($students as $key => $student) {
            foreach ($receiversArr as $key => $role) {
                $receivers = $this->Users->find()->where(['role_id' => $role, 'del_flag' => FALSE]);
                foreach ($receivers as $user) {
                    foreach ($student->physical_exams as $key => $exam) {
                        $noti = [
                            'user_id' => $user->id,
                            'content' => Text::insert($setting->template, [
                                'time' => $exam->exam_date->i18nFormat('dd-MM-yyyy'),
                                'fullname' => $student->fullname
                            ]),
                            'url' => '/students/view/' . $student->id . '#tab_content4'
                        ];
                        array_push($data, $noti);
                    }
                }
            }
        }
        $entities = $this->Notifications->newEntities($data);
        // save to db
        $this->Notifications->saveMany($entities);
    }

    public function checkReturnDate()
    {
        $setting = $this->NotificationSettings->get(3);
        $receiversArr = explode(',', $setting->receivers_groups);
        array_shift($receiversArr);
        array_pop($receiversArr);
        $now = Time::now()->addDays($setting->send_before)->i18nFormat('yyyy-MM');
        $students = $this->Students->find()->where(['return_date' => $now, 'del_flag' => FALSE]);
        $data = [];
        foreach ($students as $key => $student) {
            foreach ($receiversArr as $key => $role) {
                $receivers = $this->Users->find()->where(['role_id' => $role, 'del_flag' => FALSE]);
                foreach ($receivers as $user) {
                    $noti = [
                        'user_id' => $user->id,
                        'content' => Text::insert($setting->template, [
                            'time' => Time::now()->addDays($setting->send_before)->i18nFormat('MM-yyyy'),
                            'fullname' => $student->fullname
                        ]),
                        'url' => '/students/view/' . $student->id
                    ];
                    array_push($data, $noti);
                }
            }
        }

        $entities = $this->Notifications->newEntities($data);
        // save to db
        $this->Notifications->saveMany($entities);
    }

    public function checkEnrolledDate()
    {
        $setting = $this->NotificationSettings->get(4);
        $receiversArr = explode(',', $setting->receivers_groups);
        array_shift($receiversArr);
        array_pop($receiversArr);
        $now = Time::now()->addDays($setting->send_before)->i18nFormat('yyyy-MM-dd');
        $students = $this->Students->find()->where(['enrolled_date' => $now, 'del_flag' => FALSE]);
        $data = [];
        foreach ($students as $key => $student) {
            foreach ($receiversArr as $key => $role) {
                $receivers = $this->Users->find()->where(['role_id' => $role, 'del_flag' => FALSE]);
                foreach ($receivers as $user) {
                    $noti = [
                        'user_id' => $user->id,
                        'content' => Text::insert($setting->template, [
                            'time' => $student->enrolled_date->i18nFormat('dd-MM-yyyy'),
                            'fullname' => $student->fullname
                        ]),
                        'url' => '/students/view/' . $student->id
                    ];
                    array_push($data, $noti);
                }
            }
        }
        $entities = $this->Notifications->newEntities($data);
        // save to db
        $this->Notifications->saveMany($entities);
    }
}
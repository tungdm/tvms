<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\I18n\Time;
use Cake\Utility\Text;
use Cake\I18n\Number;

use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 *
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{
    
    public function initialize()
    {
        parent::initialize();
        $this->entity = 'đơn hàng';
        $this->loadComponent('SystemEvent');
        $this->loadComponent('Util');
        $this->loadComponent('ExportFile');
        $this->missingFields = '';
        $this->studentError = '';
    }

    public function isAuthorized($user)
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        $session = $this->request->session();
        $permissionsTable = TableRegistry::get('Permissions');
        $userPermission = $permissionsTable->find()->where(['user_id' => $user['id'], 'scope' => $controller])->first();

        if (!empty($userPermission)) {
            // only admin can recover deleted record
            if ($action == 'recover') {
                return false;
            }
            if ($action == 'edit') {
                $target_id = $this->request->getParam('pass');
                if (!empty($target_id)) {
                    $target_id = $target_id[0];
                    $order = $this->Orders->get($target_id);
                    if ($order->status == '5') {
                        return false;
                    }
                }
            }
            
            if ($userPermission->action == 0 || ($userPermission->action == 1 && (in_array($action, ['index', 'view', 'schedule']) || strpos($action, 'export') === 0))) {
                $session->write($controller, $userPermission->action);
                return true;
            }
        }
        return parent::isAuthorized($user);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $query = $this->request->getQuery();
        $now = Time::now()->i18nFormat('yyyy-MM-dd');
        $controller = $this->request->getParam('controller');
        
        if (!empty($query)) {
            $allOrders = $this->Orders->find();

            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['name']) && !empty($query['name'])) {
                $allOrders->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%' . $query['name'] . '%');
                });
            }
            if (isset($query['interview_date']) && !empty($query['interview_date'])) {
                $interview_date = $this->Util->convertDate($query['interview_date']);
                $allOrders->where(['interview_date >=' => $interview_date]);
            }
            if (isset($query['work_at']) && !empty($query['work_at'])) {
                $allOrders->where(['work_at' => $query['work_at']]);
            }
            if (isset($query['ad_comp_id']) && !empty($query['ad_comp_id'])) {
                $allOrders->where(['admin_company_id' => $query['ad_comp_id']]);
            }
            if (isset($query['guild_id']) && !empty($query['guild_id'])) {
                $allOrders->where(['Companies.guild_id' => $query['guild_id']]);
            }
            if (isset($query['departure_month']) && !empty($query['departure_month'])) {
                $from = new Time('01-' . $query['departure_month']);
                $to = $this->Util->getLastDayOfMonth('01-' . $query['departure_month']);
                $to = new Time($to);
                $allOrders->where(['Orders.departure_date >=' => $from->i18nFormat('yyyy-MM'), 'Orders.departure_date <=' => $to->i18nFormat('yyyy-MM')]);
                $query['departure_month'] = $from->i18nFormat('yyyy-MM');
            }
            if (isset($query['status']) && !empty($query['status'])) {
                switch ($query['status']) {
                    case "1":
                        $allOrders->where(['interview_date >' => $now]);
                        break;
                    case "2":
                        $allOrders->where(['interview_date' => $now]);
                        break;
                    case "3":
                        $allOrders->where(['interview_date <' => $now]);
                        break;
                    case "4":
                        $allOrders->where(['status' => "4"]);
                        break;
                }
            }
            if (isset($query['created']) && !empty($query['created'])) {
                $allOrders->where(['Orders.created >=' => $query['created']]);
            }
            $allOrders->order(['Orders.interview_date' => 'DESC']);
        } else {
            $query['records'] = $this->defaultDisplay;
            $allOrders = $this->Orders->find()->order(['Orders.interview_date' => 'DESC']);
        }

        $deleted = false;
        if (isset($query['deleted']) && $this->Auth->user('role_id') == 1) {
            $deleted = $query['deleted'];
        }
        $allOrders->where(['Orders.del_flag' => $deleted]);
        $query['deleted'] = $deleted;

        $this->paginate = [
            'contain' => [
                'Companies', 
                'Guilds',
                'Jobs',
                'AdminCompanies'
            ],
            'sortWhitelist' => ['name', 'interview_date'],
            'limit' => $query['records'],
        ];

        $orders = $this->paginate($allOrders);
        
        $jobs = $this->Orders->Jobs->find('list');
        $companies = $this->Orders->Companies->find('list');
        $guilds = $this->Orders->Companies->Guilds->find('list');
        $adminCompanies = TableRegistry::get('AdminCompanies')->find('list')->where(['deleted' => FALSE])->toArray();
        $this->set(compact('orders', 'jobs', 'companies', 'guilds', 'query', 'adminCompanies'));
    }

    /**
     * View method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => [
                'Companies' => function ($q) {
                    return $q->where(['Companies.del_flag' => FALSE]);
                },
                'DisCompanies' => function ($q) {
                    return $q->where(['DisCompanies.del_flag' => FALSE]);
                },
                'Guilds' => function ($q) {
                    return $q->where(['Guilds.del_flag' => FALSE]);
                },
                'Jobs' => function ($q) {
                    return $q->where(['Jobs.del_flag' => FALSE]);
                },
                'Students' => function ($q) {
                    return $q->where(['Students.del_flag' => FALSE])->order(['result' => 'ASC']);
                },
                'Students.InterviewDeposits',
                'Students.Addresses' => function($q) {
                    return $q->where(['Addresses.type' => '1']);
                },
                'Students.Addresses.Cities',
                'CreatedByUsers',
                'ModifiedByUsers',
                'AdminCompanies'
            ]
        ]);
        $this->checkDeleteFlag($order, $this->Auth->user());
        $this->set(compact('order'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $order = $this->Orders->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['departure_date'] = $this->Util->reverseStr($data['departure_date']);
            // create system event
            $event = $this->SystemEvent->create('PHỎNG VẤN', $this->Util->convertDate($data['interview_date']));
            $data['events'][0] = $event;
            $order = $this->Orders->patchEntity($order, $data, ['associated' => ['Students', 'Events']]);
            $order = $this->Orders->setAuthor($order, $this->Auth->user('id'), $this->request->getParam('action'));
            if ($this->Orders->save($order)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $order->name
                ]));

                return $this->redirect(['action' => 'edit', $order->id]);
            }
            $this->Flash->error($this->errorMessage['add']);
        }
        $guilds = $companies = [];
        $disCompanies = $this->Orders->Companies->find('list')->where(['type' => '1', 'del_flag' => FALSE]);
        $adminCompanies = TableRegistry::get('AdminCompanies')->find('list')->where(['deleted' => FALSE])->toArray();
        $jobs = $this->Orders->Jobs->find('list');
        $this->set(compact('order', 'guilds', 'companies', 'disCompanies', 'jobs', 'adminCompanies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => [
                'Students' => ['sort' => ['result' => 'ASC']], 
                'Students.Jclasses',
                'Students.Addresses' => function($q) {
                    return $q->where(['Addresses.type' => '1']);
                }, 
                'Students.Addresses.Cities',
                'Events',
                'Guilds',
                'DisCompanies' => function ($q) {
                    return $q->where(['DisCompanies.del_flag' => FALSE]);
                },
                'Schedules',
                'Schedules.Holidays' => [
                    'sort' => ['Holidays.day' => 'ASC']
                ]
            ]
        ]);
        $currentApplicationDate = $order->application_date;
        $currentStatus = $order->status;
        $this->checkDeleteFlag($order, $this->Auth->user());
        if ($order->status == '5' && $this->Auth->user('role_id') != 1) {
            $this->Flash->error($this->errorMessage['unAuthor']);
            return $this->redirect(['controller' => 'Pages', 'action' => 'display']);
        }
        $orderName = $order->name;
        $currentInterviewDate = $order->interview_date->i18nFormat('yyyy-MM-dd');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $data['departure_date'] = $this->Util->reverseStr($data['departure_date']);
            $newInterviewDate = $this->Util->convertDate($data['interview_date']);
            if ($currentInterviewDate !== $newInterviewDate) {
                // update system event
                $event = $this->SystemEvent->update($order->events[0]->id, $data['interview_date']);
                $data['events'][0] = $event;
            }
            $order = $this->Orders->patchEntity($order, $data, ['associated' => ['Students', 'Events']]);
            $order = $this->Orders->setAuthor($order, $this->Auth->user('id'), $this->request->getParam('action'));

            if ($this->Orders->save($order)) {
                if ($order->status == 4 && $currentStatus !== $order->status) {
                    // da co ket qua
                    $notifications = [];
                    $setting = TableRegistry::get('NotificationSettings')->get(5);
                    $receiversArr = explode(',', $setting->receivers_groups);
                    array_shift($receiversArr);
                    array_pop($receiversArr);
                    foreach ($receiversArr as $key => $role) {
                        $receivers = TableRegistry::get('Users')->find()->where(['role_id' => $role, 'del_flag' => FALSE]);
                        foreach ($receivers as $user) {
                            $noti = [
                                'user_id' => $user->id,
                                'content' => Text::insert($setting->template, [
                                    'time' => $order->interview_date->i18nFormat('dd-MM-yyyy'),
                                    'orderName' => $order->name
                                ])
                            ];
                            array_push($notifications, $noti);
                        }
                    }
                    $entities = TableRegistry::get('Notifications')->newEntities($notifications);
                    // save to db
                    TableRegistry::get('Notifications')->saveMany($entities);
                }
                if ($currentApplicationDate !== $order->application_date && !empty($order->schedule)) {
                    $end = $this->addBussinessDay($order->application_date, 24, $order->schedule->holidays);
                    $schedule = $order->schedule;
                    $schedule->end_date = $end;
                    $this->Orders->Schedules->save($schedule);
                }
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity,
                    'name' => $order->name
                ]));
                return $this->redirect(['action' => 'edit', $order->id]);
            }

            $this->Flash->error(Text::insert($this->errorMessage['edit'], [
                'entity' => $this->entity,
                'name' => $orderName
            ]));
        }
        $companies = $guilds = $disCompanies = [];
        if (!empty($order->guild_id) && !$order->guild->del_flag) {
            $guilds = $this->Orders->Guilds->find('list');
            $companies = $this->Orders->Companies->find('list')
                ->where(['Companies.del_flag' => FALSE])
                ->matching('Guilds', function ($q) use ($order) {
                    return $q->where(['Guilds.id' => $order->guild_id]);
                });
        }
        $disCompanies = $this->Orders->Companies->find('list')->where(['type' => '1', 'del_flag' => FALSE]);
        $jobs = $this->Orders->Jobs->find('list');
        $adminCompanies = TableRegistry::get('AdminCompanies')->find('list')->where(['deleted' => FALSE])->toArray();
        $this->set(compact('order', 'guilds', 'companies', 'disCompanies', 'jobs', 'adminCompanies'));
        $this->render('/Orders/add');
    }

    /**
     * Delete method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $order = $this->Orders->get($id, [
            'contain' => ['Students', 'Events']
        ]);

        if ($order->del_flag) {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $order->name
                ]));
            return $this->redirect(['action' => 'index']);
        }
        
        $data = [
            'del_flag' => TRUE, // order del_flag
        ];
        // update event delete_flag
        if (!empty($order->events)) {
            $data['events'][0]['id'] = $order->events[0]->id;
            $data['events'][0]['del_flag'] = TRUE;
        }
        $order = $this->Orders->patchEntity($order, $data, ['associated' => ['Students', 'Events']]);
        $order = $this->Orders->setAuthor($order, $this->Auth->user('id'), 'edit');
        if (!empty($order->students)) {
            foreach ($order->students as $key => $student) {
                if ($student->status == '3') { // change status "dau phong van" => "chua dau phong van"
                    $student->status = '2';
                    $student->return_date = null;
                }
            }
            if ($this->Orders->Students->saveMany($order->students) && $this->Orders->save($order)) {
                $this->Flash->success(Text::insert($this->successMessage['delete'], [
                    'entity' => $this->entity, 
                    'name' => $order->name
                    ]));
            } else {
                $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                    'entity' => $this->entity,
                    'name' => $order->name
                    ]));
            }
        } else {
            if ($this->Orders->save($order)) {
                $this->Flash->success(Text::insert($this->successMessage['delete'], [
                    'entity' => $this->entity, 
                    'name' => $order->name
                    ]));
            } else {
                $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                    'entity' => $this->entity,
                    'name' => $order->name
                    ]));
            }
        }
        
        return $this->redirect(['action' => 'index']);
    }

    public function fixReturnDate()
    {
        $workTime = Configure::read('workTime');
        $orders = $this->Orders->find('all', ['contain' => ['Students']])->toArray();
        foreach ($orders as $key => $order) {
            if ($order->del_flag) {
                continue;
            }
            Log::write('debug', "{$order->id}. Order: {$order->name}, working time: {$order->work_time}, departure date: {$order->departure}");
            foreach ($order->students as $key => $student) {
                if ($order->departure && !empty($student->return_date) && $student->_joinData->result == '1') {
                    $currentReturnDate = $student->return_date;
                    $newReturnDate = $order->departure->addYear((int)$workTime[$order->work_time])->i18nFormat('yyyy-MM');
                    if ($currentReturnDate != $newReturnDate) {
                        Log::write('debug', "\t\t {$student->id} - {$student->fullname} {$currentReturnDate} ->{$newReturnDate}");
                        $student->return_date = $newReturnDate;
                    }
                }
            }
            $this->Orders->Students->saveMany($order->students);
        }
        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $order = $this->Orders->get($id, ['contain' => ['Students', 'Events']]);
        if (!$order->del_flag) {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $order->name
                ]));
            return $this->redirect(['action' => 'index']);
        }
        
        $data = [
            'del_flag' => FALSE, // order del_flag
        ];
        // update event delete_flag
        if (!empty($order->events)) {
            $data['events'][0]['id'] = $order->events[0]->id;
            $data['events'][0]['del_flag'] = FALSE;
        }
        $order = $this->Orders->patchEntity($order, $data, ['associated' => ['Students', 'Events']]);
        $order = $this->Orders->setAuthor($order, $this->Auth->user('id'), 'edit');

        $order->del_flag = FALSE;
        $order = $this->Orders->setAuthor($order, $this->Auth->user('id'), 'edit'); // update modified user
        if (!empty($order->students)) {
            // update student status
            $workTime = Configure::read('workTime');
            foreach ($order->students as $key => $student) {
                if ($student->status == '2' && $student->_joinData->result == '1') {
                    $student->status = '3'; // change status "chua dau phong van" => "dau phong van"
                    if (!empty($order->departure) && !empty($order->work_time)) {
                        $student->return_date = $order->interview_date->addYear((int)$workTime[$order->work_time])->i18nFormat('yyyy-MM');
                    }
                }
            }
            if ($this->Orders->Students->saveMany($order->students) && $this->Orders->save($order)) {
                $this->Flash->success(Text::insert($this->successMessage['recover'], [
                    'entity' => $this->entity, 
                    'name' => $order->name
                    ]));
            } else {
                $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                    'entity' => $this->entity,
                    'name' => $order->name
                    ]));
            }
        } else {
            if ($this->Orders->save($order)) {
                $this->Flash->success(Text::insert($this->successMessage['recover'], [
                    'entity' => $this->entity, 
                    'name' => $order->name
                    ]));
            } else {
                $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                    'entity' => $this->entity,
                    'name' => $order->name
                    ]));
            }
        }
        
        return $this->redirect(['action' => 'index']);
    }

    public function close($id = null)
    {
        $this->request->allowMethod(['post']);
        $order = $this->Orders->get($id, ['contain' => ['Students', 'Students.Jclasses']]);
        if ($order->status !== '4') {
            $this->Flash->error($this->errorMessage['error']);
        } else {
            $data = [
                'status' => '5',
                'students' => []
            ];
            foreach ($order->students as $key => $student) {
                $tmp = [
                    'id' => $student->id,
                    'status' => $student->_joinData->result == '1' ? 4 : $student->status // update student status when they passed the interview
                ];
                if ($student->_joinData->result == '1') {
                    $tmp['last_class'] = $student->jclasses ? $student->jclasses[0]->name : NULL;
                    $tmp['last_lesson'] = $student->jclasses ? $student->jclasses[0]->current_lesson : NULL;
                }
                array_push($data['students'], $tmp);
            }
            $order = $this->Orders->patchEntity($order, $data, ['associated' => ['Students']]);
            $order = $this->Orders->setAuthor($order, $this->Auth->user('id'), 'edit');

            if ($this->Orders->save($order)) {
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity, 
                    'name' => $order->name
                    ]));
            } else {
                $this->Flash->error($this->errorMessage['error']);
            }
        }
        return $this->redirect(['action' => 'index']);
    }

    public function schedule($id = null)
    {
        $order = $this->Orders->get($id, ['contain' => [
            'Schedules', 
            'Schedules.Holidays' => [
                'sort' => ['Holidays.day' => 'ASC']
            ]
        ]]);
        $start = $order->application_date;
        if (empty($order->application_date)) {
            $this->Flash->error('Thiếu thông tin ngày làm hồ sơ');
            return $this->redirect(['action' => 'index']);
        }
        if (empty($order->schedule)) {
            $schedule = $this->Orders->Schedules->newEntity();
            $action = 'add';
            $end = $this->addBussinessDay($start, 24, []);
        } else {
            $schedule = $order->schedule;
            $action = 'edit';
            $end = $schedule->end_date;
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $schedule = $this->Orders->Schedules->patchEntity($schedule, $data, ['associated' => ['Holidays']]);
            $end = $this->addBussinessDay($start, 24, $schedule->holidays);
            $schedule->end_date = $end;
            $schedule = $this->Orders->Schedules->setAuthor($schedule, $this->Auth->user('id'), $action);
            if ($this->Orders->Schedules->save($schedule)) {
                $this->Flash->success($this->successMessage['addNoName']);
            } else{
                $this->Flash->error($this->errorMessage['error']);
            }
            return $this->redirect(['action' => 'schedule', $order->id]);
        }
        
        $this->set(compact('order', 'schedule', 'start', 'end', 'action'));
    }

    public function deleteSchedule($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $schedule = $this->Orders->Schedules->get($id);
        if ($this->Orders->Schedules->delete($schedule)) {
            $this->Flash->success($this->successMessage['deleteNoName']);
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }
        return $this->redirect(['action' => 'index']);
    }

    public function deleteHoliday()
    {
        $this->request->allowMethod('ajax');
        $holidayId = $this->request->getData('holidayId');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        try {
            $holiday = $this->Orders->Schedules->Holidays->get($holidayId);
            $schedule = $this->Orders->Schedules->get($holiday->schedule_id, ['contain' => [
                'Orders',
                'Holidays' => [
                    'sort' => ['Holidays.day' => 'ASC']
                ]
            ]]);
            $holidays = $schedule->holidays;
            $key = array_search($holiday->id, array_column($holidays, 'id'));
            if ($key !== FALSE) {
                unset($holidays[$key]);
            }
            $newEndDate = $this->addBussinessDay($schedule->order->application_date, 24, $holidays);
            $schedule->end_date = $newEndDate;
            $schedule = $this->Orders->Schedules->setAuthor($schedule, $this->Auth->user('id'), 'edit');
            if ($this->Orders->Schedules->Holidays->delete($holiday) && $this->Orders->Schedules->save($schedule)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => $this->successMessage['deleteNoName']
                    ]
                ];
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function refreshScheduleEndDate($scheduleId)
    {
        $this->request->allowMethod('ajax');
        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        try {
            $schedule = $this->Orders->Schedules->get($scheduleId, ['contain' => [
                'Orders',
                'Holidays' => [
                    'sort' => ['Holidays.day' => 'ASC']
                ]
            ]]);
            $newEndDate = $this->addBussinessDay($schedule->order->application_date, 24, $schedule->holidays);
            if ($newEndDate != $schedule->end_date) {
                Log::write('debug', $newEndDate);
                $schedule->end_date = $newEndDate;
                $schedule = $this->Orders->Schedules->setAuthor($schedule, $this->Auth->user('id'), 'edit');
                $this->Orders->Schedules->save($schedule);
            }
            $resp = [
                'status' => 'success',
                'alert' => [
                    'title' => 'Thành Công',
                    'type' => 'success',
                    'message' => 'Cập nhật thời gian dự kiến thành công!'
                ],
                'newEndDate' => $newEndDate->i18nFormat('dd-MM-yyyy')
            ];
        } catch (Exception $e) {
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function addBussinessDay($date, $numOfDays, $holidays)
    {
        $holidaysArr = [];
        if (!empty($holidays)) {
            foreach ($holidays as $holiday) {
                array_push($holidaysArr, $holiday->day->i18nFormat('dd-MM-yyyy'));
            }
        }
        
        while ($numOfDays > 0) {
            $date = $date->addDay(1);
            $dayOfWeek = date('N', strtotime($date));
            if ($dayOfWeek != 6 && $dayOfWeek != 7 && !in_array($date->i18nFormat('dd-MM-yyyy'), $holidaysArr )) {
                $numOfDays--;
            }
        }
        return $date;
    }

    public function deleteCandidate()
    {
        $this->request->allowMethod('ajax');
        $interviewId = $this->request->getData('id');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];

        try {
            $table = TableRegistry::get('OrdersStudents');
            $interview = $table->find()->where(['OrdersStudents.id' => $interviewId])->contain(['Students'])->first();
            $order = $this->Orders->get($interview->order_id);
            $order = $this->Orders->setAuthor($order, $this->Auth->user('id'), 'edit');
            $student = $this->Orders->Students->get($interview->student_id);
            if ($student->status == 3 && $interview->result == 1) {
                $student->status = 2;
                $student = $this->Orders->setAuthor($student, $this->Auth->user('id'), 'edit');
            }

            $candidateName = $interview->student->fullname;
            if (!empty($interview) && $table->delete($interview) && $this->Orders->save($order) && $this->Orders->Students->save($student)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => Text::insert($this->successMessage['edit'], [
                            'entity' => $this->entity, 
                            'name' => $order->name
                            ])
                    ]
                ];
            } else {
                $resp = [
                    'status' => 'error',
                    'alert' => [
                        'title' => 'Lỗi',
                        'type' => 'error',
                        'message' => $this->errorMessage['error']
                    ]
                ];
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }

        return $this->jsonResponse($resp);
    }

    public function searchCandidate()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        if (isset($query['q']) && !empty($query['q'])) {
            $students = $this->Orders->Students
                ->find('list')
                ->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('fullname', '%'.$query['q'].'%');
                })
                ->where(['status <' => '3']);
            $resp['items'] = $students;
        }
        return $this->jsonResponse($resp);        
    }

    public function searchOrder()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        if (isset($query['q']) && !empty($query['q'])) {
            $orders = $this->Orders
                ->find()
                ->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%'.$query['q'].'%');
                })
                ->map(function ($row) {
                    $row->name = $row->name . ' (' . $row->interview_date->i18nFormat('dd/MM/yyyy') . ')';
                    return $row;
                })
                ->combine('id', 'name')
                ->toArray();
            $resp['items'] = $orders;
        }
        return $this->jsonResponse($resp);   
    }

    public function recommendCandidate()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $candidates = $this->Orders->Students->find()->contain([
            'Jclasses',
            'Addresses' => function($q) {
                return $q->where(['Addresses.type' => '1']);
            }, 
            'Addresses.Cities',
        ]);
        if (!empty($query)) {
            if (isset($query['ageFrom']) 
                && isset($query['ageTo']) 
                && !empty($query['ageFrom'])
                && !empty($query['ageTo'])
                ) {
                $candidates->where(function (QueryExpression $exp, Query $q) use ($query) {
                    $minDate = Time::now()->subYears((int) $query['ageTo'])->year . '-01-01';
                    $maxDate = Time::now()->subYears((int) $query['ageFrom'])->year . '-12-31';
                    return $exp->between('birthday', $minDate, $maxDate, 'date');
                });
            }

            if (isset($query['height']) && (int)$query['height'] !== 0) {
                Log::write('debug', $query['height']);
                $candidates->where(['height >=' => (float) $query['height']]);
            }

            if (isset($query['weight']) && (int)$query['weight'] !== 0) {
                Log::write('debug', $query['weight']);
                $candidates->where(['weight >=' => (float) $query['weight']]);
            }
            
            $candidates->where(['status <' => '3'])->order(['Students.fullname' => 'ASC']);
            $now = Time::now();
            $candidates->formatResults(function ($results) use ($now) {
                return $results->map(function ($row) use ($now) {
                    $age = $row['birthday'] ? ($now->diff($row['birthday']))->y : 'N/A';
                    $row['age'] = $age;
                    return $row;
                });
            });
        }
        
        $resp = [
            'candidates' => $candidates
        ];
        
        return $this->jsonResponse($resp); 
    }

    public function getCandidate()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];

        try {
            if (isset($query['id']) && !empty($query['id'])) {
                $student = $this->Orders->Students->get($query['id']);
                $now = Time::now();
                $age = ($now->diff($student['birthday']))->y;
                $student['age'] = $age;
                $resp = $student;
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        
        return $this->jsonResponse($resp);   
    }

    public function exportCv()
    {
        // load configure
        $cvTemplateConfig = Configure::read('cvTemplate');
        $genderJP = Configure::read('genderJP');
        $addressLevel = Configure::read('addressLevel');
        $yesNoJP = Configure::read('yesNoJP');
        $eduLevel = Configure::read('eduLevel');
        $relationship = Configure::read('relationship');
        $maritalStatus = Configure::read('maritalStatus');
        $smokedrink = Configure::read('smokedrink');

        $query = $this->request->getQuery();

        try {
            // load template
            $template = WWW_ROOT . 'document' . DS . $cvTemplateConfig['template'];
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
            $order = $this->Orders->get($query['orderId']);
            $student = $this->Orders->Students->get($query['studentId'], [
                'contain' => [
                    'Addresses' => function($q) {
                        return $q->where(['Addresses.type' => '1']);
                    },
                    'Addresses.Cities',
                    'Addresses.Districts',
                    'Addresses.Wards',
                    'Educations' => ['sort' => ['Educations.degree' => 'ASC']],
                    'Experiences',
                    'Experiences.Jobs',
                    'LanguageAbilities' => function($q) {
                        return $q->where(['LanguageAbilities.type' => 'external']);
                    },
                    'Families',
                    'Families.Jobs',
                    'Jclasses',
                ]
            ]);
            $studentName_VN = mb_strtoupper($student->fullname);
            $studentName_EN = $this->Util->convertV2E($studentName_VN);
            $studentName = explode(' ', $studentName_EN);
            $studentFirstName = array_pop($studentName);
            $outputFileName = Text::insert($cvTemplateConfig['filename'], [
                'firstName' => $studentFirstName, 
                ]);
            $now = Time::now();
            $fullname_kata = $this->checkData($student->fullname_kata, 'Tên phiên âm');

            // address
            $household = $student->addresses[0];
            $address = "";
            $currentCity = $household->city->name;
            $cityType = $household->city->type;
            if ($cityType == 'Thành phố Trung ương') {
                $address .= $this->Util->convertV2E(str_replace("Thành phố", "", $currentCity)) . " 市 ";
            } else {
                $address .= $this->Util->convertV2E(str_replace($cityType, "", $currentCity)) . " 省 ";
            }

            $currentDistrict = $household->district->name;
            $districtType = $household->district->type;
            $address .= $this->Util->convertV2E(str_replace($districtType, "", $currentDistrict) . " " . $addressLevel[$districtType]['jp']) . " ";

            $currentWard = $household->ward->name;
            $wardType = $household->ward->type;
            $address .= $this->Util->convertV2E(str_replace($wardType, "", $currentWard) . " " . $addressLevel[$wardType]['jp']) . " ";

            $cityCode = (int) $household->city->id;
            if ($cityCode <= 37) {
                $address .= "(北部)"; // north
            } else if ($cityCode <= 69) {
                $address .= "(中部)"; // middle
            } else {
                $address .= "(南部)"; // south
            }

            $eduHis = [];
            $certificate = [];
            if (empty($student->educations)) {
                $history = [
                    'year' => "",
                    'month' => "",
                    'schoolName' => "",
                    'schoolJP' => "",
                ];
                $this->checkData('', 'Quá trình học tập');
                array_push($eduHis, $history);
            } else {
                foreach ($student->educations as $key => $value) {
                    $schoolName = $this->Util->convertV2E($value->school);
                    $fromDate = new Time($value->from_date);
                    $toDate = new Time($value->to_date);
                    $specialized = $value->specialized_jp ? '（' . $value->specialized_jp . '）' : ''; 
                    $history = [
                        'year' => $fromDate->year . " ～ " . $toDate->year,
                        'month' => $fromDate->month . " ～ " . $toDate->month,
                        'schoolName' => $schoolName,
                        'schoolJP' => $eduLevel[$value->degree]['jp'] . "卒業" . $specialized,
                    ];
                    array_push($eduHis, $history);

                    // certificate
                    if (!empty($value->certificate)) {
                        $certificate = [];
                        $certificateDate = new Time($value->certificate);
                        $data = [
                            'year' => $certificateDate->year,
                            'month' => $certificateDate->month,
                            'certificate' => $eduLevel[$value->degree]['jp'] . "卒業証明書"
                        ];
                        array_push($certificate, $data);
                    }
                }
            }
            $this->tbs->MergeBlock('a', $eduHis);

            $expHis = [];
            if (empty($student->experiences)) {
                $history = [
                    'year' => "",
                    'month' => "",
                    'company' => "",
                ];
                $this->checkData('', 'Kinh nghiệm làm việc');
                array_push($expHis, $history);
            } else {
                foreach ($student->experiences as $key => $value) {
                    if (empty($value->company_jp)) {
                        $this->checkData('', 'Tên tiếng Nhật của công ty ' . $value->company);
                    }
                    $fromDate = new Time($value->from_date);
                    $toDate = new Time($value->to_date);
                    $history = [
                        'year' => $fromDate->year . " ～ " . $toDate->year,
                        'month' => $fromDate->month . " ～ " . $toDate->month,
                        'company' => $value->company_jp . '（' . $value->job->job_name_jp . '）'  ,
                    ];
                    array_push($expHis, $history);
                }
            }
            $this->tbs->MergeBlock('b', $expHis);

            if (!empty($student->language_abilities)) {
                foreach ($student->language_abilities as $key => $value) {
                    $fromDate = new Time($value->from_date);
                    $data = [
                        'year' => $fromDate->year,
                        'month' => $fromDate->month,
                        'certificate' => $value->certificate
                    ];
                    array_push($certificate, $data);
                }
            }
            
            $this->tbs->MergeBlock('c', $certificate);

            $families = [];
            $memberInJP = false;
            $memberInJPRel = '';
            if (empty($student->families)) {
                $this->checkData('', 'Quan hệ gia đình');
            }
            $max = count($student->families) > 4 ? count($student->families) : 4;
            for ($i=0; $i < $max; $i++) { 
                $member = [
                    'name' => "",
                    'relationship' => "",
                    'age' => "",
                    'job' => "",
                ];
                if (!empty($student->families) && !empty($student->families[$i])) {
                    $value = $student->families[$i];
                    if ($value->job->job_name_jp == '死別') {
                        $age = '';
                    } else {
                        $age = $value->birthday ? ($now->diff($value->birthday))->y : '';
                    }
                    $member = [
                        'name' => $this->Util->convertV2E($value->fullname),
                        'relationship' => $relationship[$value->relationship]['jp'],
                        'age' => $age,
                        'job' => $value->job->job_name_jp,
                    ];

                    if ($value->living_at == '02') {
                        $memberInJP = true;
                        $memberInJPRel = $relationship[$value->relationship]['jp'];
                    }

                    if ($i > 3) {
                        $member['additional'] = '';
                    }
                }
                array_push($families, $member);
            }
            if (empty($student->jclasses) || $student->jclasses[0]->current_lesson == '0') {
                $currentLession = '1';
            } else {
                $currentLession = $student->jclasses[0]->current_lesson;
            }
            $studyTime = $student->enrolled_date ? ($now->diff($student->enrolled_date))->m : 1;
            $families[0]['additional'] = $cvTemplateConfig['familyAdditional'][0] . "            ：" . $memberInJPRel;
            $families[1]['additional'] = $cvTemplateConfig['familyAdditional'][1] . "    ：みんなの日本語";
            $families[2]['additional'] = $cvTemplateConfig['familyAdditional'][2] . "    ：" . $studyTime . "ヶ月";
            $families[3]['additional'] = $cvTemplateConfig['familyAdditional'][3] . "        ：第" . $currentLession . "課";
            
            $this->tbs->MergeBlock('d', $families);

            $this->tbs->VarRef['serial'] = $query['serial'];
            $this->tbs->VarRef['created'] = $order->interview_date->subDays(14)->i18nFormat('yyyy年MM月dd日');
            $this->tbs->VarRef['studentNameJP'] = $fullname_kata;
            $this->tbs->VarRef['studentNameEN'] = $studentName_EN;
            $this->tbs->VarRef['birthday'] = $student->birthday->i18nFormat('yyyy年MM月dd日');
            $this->tbs->VarRef['age'] = ($now->diff($student->birthday))->y;
            $this->tbs->VarRef['gender'] = $genderJP[$student->gender];
            $this->tbs->VarRef['address'] = $address;
            $this->tbs->VarRef['livedJP'] = $yesNoJP[$student->is_lived_in_japan];

            $avatar = $student->image ?? 'students/no_img.png';
            $this->tbs->VarRef['avatar'] = ROOT . DS . 'webroot' . DS . 'img' . DS . $avatar;
            $this->tbs->VarRef['livingJP'] = "在日親戚    ：" . ($memberInJP == true ? "有" : "無");

            // convert strength
            $strengths = TableRegistry::get('Strengths')->find()->where(['del_flag' => FALSE])->toArray();
            $strengthArr = $this->checkData($student->strength, 'Điểm mạnh');
            if (!empty($strengthArr)) {
                $strengthArr = $this->convertTag($strengthArr, $strengths, 'Điểm mạnh (JP)');
            }
            $this->tbs->VarRef['strength'] = $strengthArr;

            // convert purpose
            $purposes = TableRegistry::get('Purposes')->find()->where(['del_flag' => FALSE])->toArray();
            $purposeArr = $this->checkData($student->purpose, 'Mục đích xuất khẩu lao động');
            if (!empty($purposeArr)) {
                $purposeArr = $this->convertTag($purposeArr, $purposes, 'Mục đích xuất khẩu lao động (JP)');
            }
            $this->tbs->VarRef['purpose'] = $purposeArr;

            // convert genitive
            $characteristics = TableRegistry::get('Characteristics')->find()->where(['del_flag' => FALSE])->toArray();
            $genitiveArr = $this->checkData($student->genitive, 'Tính cách');
            if (!empty($genitiveArr)) {
                $genitiveArr = $this->convertTag($genitiveArr, $characteristics, 'Tính cách (JP)');
            }
            $this->tbs->VarRef['genitive'] = $genitiveArr;

            // convert plan
            $afterPlans = TableRegistry::get('AfterPlans')->find()->where(['del_flag' => FALSE])->toArray();
            $afterPlanArr = $this->checkData($student->after_plan, 'Dự định sau khi về nước');
            if (!empty($afterPlanArr)) {
                $afterPlanArr = $this->convertTag($afterPlanArr, $afterPlans, 'Dự định sau khi về nước (JP)');
            }
            $this->tbs->VarRef['after_plan'] = $afterPlanArr;

            $this->tbs->VarRef['salary'] = $student->salary ?number_format($student->salary) : 0;
            $this->tbs->VarRef['saving'] = $this->checkData($student->saving_expected, 'Số tiền mong muốn');
            $this->tbs->VarRef['maritalStatus'] = $maritalStatus[$student->marital_status]['jp'];
            $this->tbs->VarRef['reh'] = $this->checkData($student->right_eye_sight_hospital, 'Thị lực mắt phải đo tại bệnh viện');
            $this->tbs->VarRef['leh'] = $this->checkData($student->left_eye_sight_hospital, 'Thị lực mắt trái đo tại bệnh viện');
            $this->tbs->VarRef['re'] = $this->checkData($student->right_eye_sight, 'Thị lực mắt phải');
            $this->tbs->VarRef['le'] = $this->checkData($student->left_eye_sight, 'Thị lực mắt trái');
            $this->tbs->VarRef['height'] = $this->checkData($student->height, 'Chiều cao');
            $this->tbs->VarRef['weight'] = $this->checkData($student->weight, 'Cân nặng');

            $preferred_hand = $this->checkData($student->preferred_hand, 'Tay thuận');
            $this->tbs->VarRef['preferred_hand'] = $preferred_hand  == "1" ? "右" : "左";
            
            $this->checkData($student->color_blind, 'Mù màu');
            $this->tbs->VarRef['color_blind'] = empty($student->color_blind) ? '' : $yesNoJP[$student->color_blind];

            $this->checkData($student->smoke, 'Hút thuốc');
            $this->tbs->VarRef['smoke'] = empty($student->smoke) ? '' : $smokedrink[$student->smoke]['jp'];

            $this->checkData($student->drink, 'Uống rượu');
            $this->tbs->VarRef['drink'] = empty($student->drink) ? '' : $smokedrink[$student->drink]['jp'];

            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['action' => 'index']);
            }

            $this->tbs->Show(OPENTBS_DOWNLOAD, $outputFileName);
            exit();
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }  
    }

    public function exportCover($id = null)
    {
        // load configure
        $coverConfig = Configure::read('coverTemplate');
        // load template
        $template = WWW_ROOT . 'document' . DS . $coverConfig['template'];
        $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

        $order = $this->Orders->get($id, [
            'contain' => ['Students',
            'Companies', 
            'Guilds',
            'Jobs', 
            ]]);
        $this->checkDeleteFlag($order, $this->Auth->user());

        $outputFileName = Text::insert($coverConfig['filename'], [
            'order' => $order->name, 
            ]);
        $now = Time::now();
        $guildJP = $this->checkData($order->guild->name_kanji, 'Tên phiên âm của nghiệp đoàn');
        $companyJP = $this->checkData($order->company->name_kanji, 'Tên phiên âm của công ty tiếp nhận');
        $departureDate = $this->checkData($order->departure_date, 'Ngày xuất cảnh dự kiến');
        $departureDate = new Time($order->departure_date);
        $this->tbs->VarRef['created'] = $now->i18nFormat('yyyy年M月d日');
        $this->tbs->VarRef['interview_date'] = $order->interview_date->i18nFormat('yyyy年M月d日');
        $this->tbs->VarRef['guild'] = $guildJP;
        $this->tbs->VarRef['company'] = $companyJP;
        $this->tbs->VarRef['job'] = $order->job->job_name_jp;
        $this->tbs->VarRef['total'] = count($order->students);
        $this->tbs->VarRef['departure_date'] = $departureDate->year . '年' . $departureDate->month;

        if (!empty($this->missingFields)) {
            $this->Flash->error(Text::insert($this->errorMessage['export'], [
                'fields' => $this->missingFields,
                ]), 
                [
                    'escape' => false,
                    'params' => ['showButtons' => true]
                ]);
            return $this->redirect(['action' => 'index']);
        }
        $this->tbs->Show(OPENTBS_DOWNLOAD, $outputFileName);
        exit;
    }

    public function exportSummary($id = null)
    {
        // load config
        $summaryConfig = Configure::read('orderSummary');
        $gender = Configure::read('gender');
        $studentStatus = Configure::read('studentStatus');
        $outputFileName = $summaryConfig['filename'];
        try {
            $order = $this->Orders->get($id, [
                'contain' => [
                    'Jobs',
                    'Companies',
                    'AdminCompanies',
                    'Guilds',
                    'Students' => function($q) {
                        return $q->where(['result' => '1']);
                    }
                ]
            ]);
            $adminCompany = $order->admin_company;
            $this->checkDeleteFlag($order, $this->Auth->user());
            $template = WWW_ROOT . 'document' . DS . $summaryConfig['template'];
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            $missingFields = [];
            $guildJP = $this->checkData($order->guild->name_kanji, 'Tên phiên âm của nghiệp đoàn');
            $guildVN = $this->checkData($order->guild->name_romaji, 'Tên nghiệp đoàn');
            $this->tbs->VarRef['adCompShortName'] = $adminCompany->short_name;
            $this->tbs->VarRef['guildJP'] = $guildJP;
            $this->tbs->VarRef['guildVN'] = $guildVN;

            $companyJP = $this->checkData($order->company->name_kanji, 'Tên phiên âm của công ty tiếp nhận');
            $companyVN = $order->company->name_romaji;
            $this->tbs->VarRef['companyJP'] = $companyJP;
            $this->tbs->VarRef['companyVN'] = $companyVN;

            $this->tbs->VarRef['jobJP'] = $order->job->job_name_jp;
            $this->tbs->VarRef['jobVN'] = $order->job->job_name;
                
            $this->tbs->VarRef['interviewDateJP'] = !empty($order->interview_date) ? $order->interview_date->i18nFormat('yyyy年M月d日') : 'N/A';
            $this->tbs->VarRef['interviewDateVN'] = !empty($order->interview_date) ? $order->interview_date->i18nFormat('dd/MM/yyyy') : 'N/A';

            $this->tbs->VarRef['tempDateJP'] = !empty($order->temporary_stay_date) ? $order->temporary_stay_date->i18nFormat('yyyy年M月d日') : 'N/A';
            $this->tbs->VarRef['tempDateVN'] = !empty($order->temporary_stay_date) ? $order->temporary_stay_date->i18nFormat('dd/MM/yyyy') : 'N/A';

            $this->tbs->VarRef['submittedDateJP'] = !empty($order->submitted_date) ? $order->submitted_date->i18nFormat('yyyy年M月d日') : 'N/A';
            $this->tbs->VarRef['submittedDateVN'] = !empty($order->submitted_date) ? $order->submitted_date->i18nFormat('dd/MM/yyyy') : 'N/A';

            $this->tbs->VarRef['visaDateJP'] = !empty($order->visa_apply_date) ? $order->visa_apply_date->i18nFormat('yyyy年M月d日') : 'N/A';
            $this->tbs->VarRef['visaDateVN'] = !empty($order->visa_apply_date) ? $order->visa_apply_date->i18nFormat('dd/MM/yyyy') : 'N/A';

            $this->tbs->VarRef['departureDateJP'] = !empty($order->departure) ? $order->departure->i18nFormat('yyyy年M月d日') : 'N/A';
            $this->tbs->VarRef['departureDateVN'] = !empty($order->departure) ? $order->departure->i18nFormat('dd/MM/yyyy') : 'N/A';

            $this->tbs->VarRef['nowJP'] = Time::now()->i18nFormat('yyyy年M月d日');

            $return_date_jp = $return_date_vn = 'N/A';
            if (!empty($order->departure) && !empty($order->work_time)) {
                $workTime = Configure::read('workTime');
                $return_date = $order->interview_date->addYear((int)$workTime[$order->work_time]);
                $return_date_jp = $return_date->i18nFormat('yyyy年M月');
                $return_date_vn = $return_date->i18nFormat('MM/yyyy');
            }
            $this->tbs->VarRef['returnDateJP'] = $return_date_jp;
            $this->tbs->VarRef['returnDateVN'] = $return_date_vn;

            $this->tbs->VarRef['jpairport'] = $order->japanese_airport ?? 'N/A';

            $listVN = [];
            foreach ($order->students as $key => $student) {
                $studentName_VN = mb_strtoupper($student->fullname);
                $studentVN = [
                    'no' => $key + 1,
                    'studentName' => $studentName_VN,
                    'birthday' => $student->birthday->i18nFormat('dd/MM/yyyy'),
                    'gender' => $gender[$student->gender],
                    'status' => $studentStatus[$student->status]
                ];
                array_push($listVN, $studentVN);
            }
            $this->tbs->MergeBlock('a', $listVN);            
            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['action' => 'index']);
            }
            $this->tbs->Show(OPENTBS_DOWNLOAD, $outputFileName);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }

    public function exportDispatchLetter($id = null)
    {
        // load config
        $dispatchLetterConfig = Configure::read('dispatchLetter');
        $gender = Configure::read('gender');
        $genderJP = Configure::read('genderJP');
        $outputFileName = $dispatchLetterConfig['filename'];
        try {
            $order = $this->Orders->get($id, [
                'contain' => [
                    'Jobs',
                    'Companies',
                    'Guilds',
                    'AdminCompanies',
                    'Students' => function($q) {
                        return $q->where(['result' => '1']);
                    }
                ]
            ]);
            $adminCompany = $order->admin_company; 
            $this->checkDeleteFlag($order, $this->Auth->user());
            $template = WWW_ROOT . 'document' . DS . $dispatchLetterConfig['template'];
            $missingFields = [];
            $guildJP = $this->checkData($order->guild->name_kanji, 'Tên phiên âm của nghiệp đoàn');
            $guildVN = $this->checkData($order->guild->name_romaji, 'Tên nghiệp đoàn');

            $guildLicenseNum = $this->checkData($order->guild->license_number, 'Số giấy phép của nghiệp đoàn');

            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            $guildDeputyJP = $this->checkData($order->guild->deputy_name_kanji, 'Tên phiên âm người đại diện nghiệp đoàn');
            $guildDeputyVN = $order->guild->deputy_name_romaji;

            $guildAddressJP = $this->checkData($order->guild->address_kanji, 'Địa chỉ phiên âm của nghiệp đoàn');
            $guildAddressVN = $order->guild->address_romaji;

            $guildPhone = $order->guild->phone_jp;

            $this->tbs->VarRef['adCompNameEN'] = $adminCompany->name_en;
            $this->tbs->VarRef['adCompNameVN'] = $adminCompany->name_vn;
            $this->tbs->VarRef['adCompShortName'] = $adminCompany->short_name;
            $this->tbs->VarRef['adCompLicense'] = $adminCompany->license;
            $this->tbs->VarRef['deputyRoleJP'] = $adminCompany->deputy_role_jp;
            $this->tbs->VarRef['deputyNameEN'] = $this->Util->convertV2E($adminCompany->deputy_name);
            $this->tbs->VarRef['deputyNameVN'] = $adminCompany->deputy_name;
            $this->tbs->VarRef['deputyRoleVN'] = $adminCompany->deputy_role_vn;
            $this->tbs->VarRef['dolabRoleJP'] = $adminCompany->dolab_role_jp;
            $this->tbs->VarRef['dolabNameEN'] = $this->Util->convertV2E($adminCompany->dolab_name);
            $this->tbs->VarRef['dolabRoleVN'] = $adminCompany->dolab_role_vn;
            $this->tbs->VarRef['dolabNameVN'] = $adminCompany->dolab_name;

            $this->tbs->VarRef['addressEN'] = $adminCompany->address_en;
            $this->tbs->VarRef['addressVN'] = $adminCompany->address_vn;
            $this->tbs->VarRef['phone'] = $adminCompany->phone_number;
            $this->tbs->VarRef['fax'] = $adminCompany->fax_number;

            $this->tbs->VarRef['guildJP'] = $guildJP;

            $this->tbs->VarRef['guildJP'] = $guildJP;
            $this->tbs->VarRef['guildVN'] = $guildVN;
            $this->tbs->VarRef['licenseNum'] = $guildLicenseNum;
            $this->tbs->VarRef['guildDeputyJP'] = $guildDeputyJP;
            $this->tbs->VarRef['guildDeputyVN'] = $guildDeputyVN;
            $this->tbs->VarRef['guildAddressJP'] = $guildAddressJP;
            $this->tbs->VarRef['guildAddressVN'] = $guildAddressVN;
            $this->tbs->VarRef['guildPhone'] = $guildPhone;

            $companyJP = $this->checkData($order->company->name_kanji, 'Tên phiên âm của công ty tiếp nhận');
            $companyVN = $order->company->name_romaji;

            $companyDeputyJP = $this->checkData($order->company->deputy_name_kanji, 'Tên phiên âm người đại diện công ty tiếp nhận');
            $companyDeputyVN = $order->company->deputy_name_romaji;

            $companyAddressJP = $this->checkData($order->company->address_kanji, 'Địa chỉ phiên âm của công ty tiếp nhận');
            $companyAddressVN = $order->company->address_romaji;

            $companyPhone = $order->company->phone_jp;

            $this->tbs->VarRef['companyJP'] = $companyJP;
            $this->tbs->VarRef['companyVN'] = $companyVN;
            $this->tbs->VarRef['companyDeputyJP'] = $companyDeputyJP;
            $this->tbs->VarRef['companyDeputyVN'] = $companyDeputyVN;
            $this->tbs->VarRef['companyAddressJP'] = $companyAddressJP;
            $this->tbs->VarRef['companyAddressVN'] = $companyAddressVN;
            $this->tbs->VarRef['companyPhone'] = $companyPhone;

            $this->tbs->VarRef['worktime'] = $order->work_time;
            $listJP = $listVN = [];
            $this->checkData($order->departure_date, 'Ngày xuất cảnh (dự kiến)');

            foreach ($order->students as $key => $student) {
                $studentName_VN = mb_strtoupper($student->fullname);
                $studentName_EN = $this->Util->convertV2E($studentName_VN);
                $departureDate = strtotime($order->departure_date);

                $studentJP = [
                    'no' => $key + 1,
                    'studentName' => $studentName_EN,
                    'birthday' => $student->birthday->i18nFormat('yyyy年M月d日'),
                    'gender' => $genderJP[$student->gender],
                    'job' => $order->job->job_name_jp,
                    'departureDate' => date('Y年n月', $departureDate)
                ];
                $studentVN = [
                    'no' => $key + 1,
                    'studentName' => $studentName_VN,
                    'birthday' => $student->birthday->i18nFormat('dd/MM/yyyy'),
                    'gender' => $gender[$student->gender],
                    'job' => $order->job->job_name,
                    'departureDate' => date('m/Y', $departureDate)
                ];
                array_push($listJP, $studentJP);
                array_push($listVN, $studentVN);
            }
            $this->tbs->MergeBlock('a', $listJP);
            $this->tbs->MergeBlock('b', $listVN);

            $vnDateFormatShort = Configure::read('vnDateFormatShort');
            $this->checkData($order->application_date, 'Ngày làm hồ sơ');
            $createdDayJP = $order->application_date ? $order->application_date->i18nFormat('yyyy年M月d日') : '';
            $createdDayVN =  Text::insert($vnDateFormatShort, [
                'day' => $order->application_date ? str_pad($order->application_date->day, 2, '0', STR_PAD_LEFT) : '', 
                'month' => $order->application_date ? str_pad($order->application_date->month, 2, '0', STR_PAD_LEFT) : '', 
                'year' => $order->application_date ? $order->application_date->year : '', 
                ]);
            $this->tbs->VarRef['createdDayJP'] = $createdDayJP;
            $this->tbs->VarRef['createdDayVN'] = $createdDayVN;
            
            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['action' => 'index']);
            }
            $this->tbs->Show(OPENTBS_DOWNLOAD, $outputFileName);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }

    public function exportDispatchLetterXlsx($id = null)
    {
        // load config
        $dispatchLetterXlsx = Configure::read('dispatchLetterXlsx');
        $cityJP = Configure::read('cityJP');

        try {
            // get data
            $order = $this->Orders->get($id, [
                'contain' => [
                    'Students' => function($q) {
                        return $q->where(['result' => '1']);
                    },
                    'Students.Addresses' => function($q) {
                        return $q->where(['Addresses.type' => '1']);
                    },
                    'Students.Cards' => function($q) {
                        return $q->where(['Cards.type' => '1']); # CMND
                    },
                    'Students.Addresses.Cities',
                    'Students.Addresses.Districts',
                    'Students.Addresses.Wards',
                    'Jobs',
                    'Companies',
                    'Guilds',
                    'AdminCompanies'
                ]
            ]);
            $adminCompany = $order->admin_company;
            $this->checkDeleteFlag($order, $this->Auth->user());
            // init worksheet
            $spreadsheet = $this->ExportFile->setXlsxProperties();
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(85);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

            $spreadsheet->getActiveSheet()->setShowGridLines(false);
            if (!empty($adminCompany->branch)) {
                $spreadsheet->getActiveSheet()->setCellValue('A1', mb_strtoupper($adminCompany->branch) . ' - ' . mb_strtoupper($adminCompany->short_name));
            } else {
                $spreadsheet->getActiveSheet()->setCellValue('A1', mb_strtoupper($adminCompany->short_name));
            }
            $spreadsheet->getActiveSheet()->getStyle('A1:A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ]);

            $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
            $spreadsheet->getActiveSheet()->mergeCells('A3:P3');
            $spreadsheet->getActiveSheet()->setCellValue('A3', 'ĐỀ NGHỊ CẤP THƯ PHÁI CỬ');
            $spreadsheet->getActiveSheet()->getStyle('A3:A3')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            $spreadsheet->getActiveSheet()->mergeCells('A4:P4');
            $spreadsheet->getActiveSheet()->setCellValue('A4', '(Kiêm biên bản giao nhận giấy tờ)');
            $spreadsheet->getActiveSheet()->getStyle('A4:A4')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $dear = $richText->createTextRun('Kính gửi:');
            $dear->getFont()->setBold(true)->setItalic(true)->setUnderline(true);
            $richText->createText(' '.  $adminCompany->name_vn);
            $spreadsheet->getActiveSheet()->setCellValue('A5', $richText);
            if (!empty($adminCompany->branch)) {
                $spreadsheet->getActiveSheet()->setCellValue('A6', $adminCompany->branch . ' xin đề nghị cấp thư phái cử cho các Tu nghiệp sinh Nhật bản theo danh sách sau:');
            } else {
                $spreadsheet->getActiveSheet()->setCellValue('A6', 'xin đề nghị cấp thư phái cử cho các Tu nghiệp sinh Nhật bản theo danh sách sau:');
            }
            
            $spreadsheet->getActiveSheet()
                ->mergeCells('A8:A9')->setCellValue('A8', 'STT')
                ->mergeCells('B8:B9')->setCellValue('B8', 'Họ và tên')
                ->mergeCells('C8:C9')->setCellValue('C8', 'Ngày sinh')
                ->mergeCells('D8:D9')->setCellValue('D8', 'CMND')
                ->mergeCells('E8:E9')->setCellValue('E8', 'Số điện thoại')
                ->mergeCells('F8:G8')->setCellValue('F8', 'Giới tính')->setCellValue('F9', 'Nam')->setCellValue('G9', 'Nữ')
                ->mergeCells('H8:J8')->setCellValue('H8', 'Quê quán')->setCellValue('H9', 'Xã')->setCellValue('I9', 'Huyện')->setCellValue('J9', 'Tỉnh,TP')
                ->mergeCells('K8:K9')->setCellValue('K8', 'Thời hạn HĐ')
                ->mergeCells('L8:L9')->setCellValue('L8', 'Nơi làm việc')
                ->mergeCells('M8:M9')->setCellValue('M8', 'Ngành nghề')
                ->mergeCells('N8:N9')->setCellValue('N8', 'Nghiệp đoàn')
                ->mergeCells('O8:O9')->setCellValue('O8', 'Công ty tiếp nhận')
                ->mergeCells('P8:P9')->setCellValue('P8', 'Chủ sử dụng');
            
            $spreadsheet->getActiveSheet()->getStyle('K8')->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(34);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(32);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(6);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(26);
            $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(26);
            $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(20);

            $listWorkers = [];
            $counter = 9;
            foreach ($order->students as $key => $student) {
                $counter++;
                $spreadsheet->getActiveSheet()->getRowDimension((string)$counter)->setRowHeight(54);
                if ($student->gender == 'M') {
                    $male = 'x';
                    $female = '';
                } else {
                    $male = '';
                    $female = 'x';
                }
                $ward = trim(str_replace($student->addresses[0]->ward->type, "", $student->addresses[0]->ward->name));
                $district = trim(str_replace($student->addresses[0]->district->type, "", $student->addresses[0]->district->name));
                if ($student->addresses[0]->city->type == "Tỉnh") {
                    $city = trim(str_replace($student->addresses[0]->city->type, "", $student->addresses[0]->city->name));
                } else {
                    $city = trim(str_replace("Thành phố", "", $student->addresses[0]->city->name));
                }
                $data = [
                    $key+1,
                    mb_strtoupper($student->fullname),
                    $student->birthday->i18nFormat('dd/MM/yyyy'),
                    $student->cards[0]->code,
                    $student->phone,
                    $male,
                    $female,
                    mb_strtoupper($ward),
                    mb_strtoupper($district),
                    mb_strtoupper($city),
                    str_pad($order->work_time, 2, '0', STR_PAD_LEFT),
                    mb_strtoupper($cityJP[$order->work_at]['rmj']),
                    mb_strtoupper($order->job->job_name),
                    mb_strtoupper($order->guild->name_romaji),
                    mb_strtoupper($order->company->name_romaji),
                    mb_strtoupper($order->company->deputy_name_romaji)
                ];
                array_push($listWorkers, $data);
            }
            // fill data to table
            $spreadsheet->getActiveSheet()->fromArray($listWorkers, NULL, 'A10');
            $spreadsheet->getActiveSheet()->getStyle('A8:P'. $counter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('A8:P'.$counter)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Style\Border::BORDER_THIN,
                    ]
                ],
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $spreadsheet->getActiveSheet()->getStyle('A8:P9')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);
            $spreadsheet->getActiveSheet()->getStyle('A10:A'.$counter)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);
            
            $footer = $counter+1;
            $this->checkData($order->application_date, 'Ngày làm hồ sơ');
            $day = $order->application_date ? str_pad($order->application_date->day, 2, '0', STR_PAD_LEFT) : '';
            $month = $order->application_date ? str_pad($order->application_date->month, 2, '0', STR_PAD_LEFT) : '';
            $year = $order->application_date ? $order->application_date->year : '';
            $spreadsheet->getActiveSheet()->mergeCells('A'.$footer.':P'.$footer)
                ->setCellValue('A'.$footer, 'TPHCM, ngày ' . $day . ' tháng ' . $month . ' năm ' . $year);
            $spreadsheet->getActiveSheet()->getStyle('A'.$footer)->getFont()->setItalic(true);
            $spreadsheet->getActiveSheet()->getStyle('A'.$footer)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $end = $footer + 2;
            $spreadsheet->getActiveSheet()->setCellValue('A'.$end, '(Phòng NV Cty đã nhận đủ hồ sơ theo DS trên)');
            $spreadsheet->getActiveSheet()->setSelectedCells('A1');

            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['action' => 'index']);
            }
            // export XLSX file for download
            $this->ExportFile->export($spreadsheet, $dispatchLetterXlsx['filename']);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
        
    }

    public function exportCandidates($id)
    {
        // get data
        $order = $this->Orders->get($id, [
            'contain' => [
                'Jobs',
                'Companies',
                'Guilds',
                'Students',
                'Students.InputTests' => ['sort' => ['InputTests.type' => 'ASC']],
                'Students.IqTests'
            ]
        ]);
        $this->checkDeleteFlag($order, $this->Auth->user());
        // init worksheet
        $spreadsheet = $this->ExportFile->setXlsxProperties();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(55);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(18);
        $spreadsheet->getActiveSheet()->setShowGridLines(false);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $spreadsheet->getActiveSheet()->mergeCells('A1:N1');
        $spreadsheet->getActiveSheet()->mergeCells('A2:N2');
        $spreadsheet->getActiveSheet()->mergeCells('A3:N3');
        $spreadsheet->getActiveSheet()->mergeCells('A4:N4');
        $spreadsheet->getActiveSheet()->setCellValue('A1', '技能実習生候補者名簿');
        $spreadsheet->getActiveSheet()->setCellValue('A2', '監理団体：' . $order->guild->name_kanji);
        $spreadsheet->getActiveSheet()->setCellValue('A3', '受入企業：' . $order->company->name_kanji);
        $spreadsheet->getActiveSheet()->setCellValue('A4', '受入職種：' . $order->job->job_name_jp);
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(100);
        
        $spreadsheet->getActiveSheet()->getStyle('A1:A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 48,
            ],
            'alignment' => [
                'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        $spreadsheet->getActiveSheet()->getStyle('A2:N4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 26,
            ],
            'alignment' => [
                'vertical' => Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(15);
        
        $listCandidatesXlsx = Configure::read('listCandidatesXlsx');
        $header = $listCandidatesXlsx['header'];
  
        for ($char = 'A'; $char <= 'O'; $char++) {
            if ($char == 'I') {
                $spreadsheet->getActiveSheet()->mergeCells('I6:J6');
                $spreadsheet->getActiveSheet()->setCellValue('I7', $header['I7']);
                $spreadsheet->getActiveSheet()->setCellValue('J7', $header['J7']);
            } else if ($char == 'J') {
                $spreadsheet->getActiveSheet()->getColumnDimension($char)->setWidth($header[$char]['width']);
                continue;
            } else {
                $spreadsheet->getActiveSheet()->mergeCells($char . '6:'. $char .'7');
            }
            $spreadsheet->getActiveSheet()->setCellValue($char.'6', $header[$char]['title']);
            $spreadsheet->getActiveSheet()->getColumnDimension($char)->setWidth($header[$char]['width']);
        }
        $spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(60);
        $spreadsheet->setActiveSheetIndex(0);

        $spreadsheet->getActiveSheet()->getStyle('A6:O7')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 20,
            ],
            'alignment' => [
                'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Style\Fill::FILL_SOLID,
                'color' => [
                    'rgb' => 'daeef3'
                ]
            ]
        ]);
        
        $listCandidates = [];
        $now = Time::now();
        $maritalStatus = Configure::read('maritalStatus');
        $maritalStatus = array_map('array_pop', $maritalStatus);

        $counter = 7;

        foreach ($order->students as $key => $student) {
            $noCell = $key + 1;
            $counter++;
            $error = '';
            $fullname = $student->fullname;
            $fullname_kata = $this->checkDataConcate($student->fullname_kata, 'Tên phiên âm');
            $studentName_VN = mb_strtoupper($fullname);
            $studentName_EN = $this->Util->convertV2E($studentName_VN);

            $nameCell = $fullname_kata . "\n" . $studentName_EN;
            $ageCell = ($now->diff($student->birthday))->y;
            $marriageCell = $student->marital_status ? $maritalStatus[$student->marital_status] : '';
            $data = [
                $noCell,
                $nameCell,
                $student->birthday->i18nFormat('yyyy年MM月dd日'),
                $ageCell,
                $marriageCell,
                $student->input_tests[2]->score ?? '',
                $student->input_tests[0]->score ?? '',
                $student->iq_tests[0]->total ?? '',
                $student->right_hand_force ?? '',
                $student->left_hand_force ?? '',
                $student->back_force ?? '',
                $student->blood_group ?? '',
            ];
            if (!empty($this->studentError)) {
                $this->missingFields .= '<li>' . $this->studentError . ' của lao động '.$fullname.'</li>';
                $this->studentError = '';
            }
            array_push($listCandidates, $data);
        }
        \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
        // fill data to table
        $spreadsheet->getActiveSheet()->fromArray($listCandidates, NULL, 'A8');

        $spreadsheet->getActiveSheet()->getStyle('A6:O'.$counter)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Style\Border::BORDER_THIN,
                ]
            ]
        ]);
        $spreadsheet->getActiveSheet()->getStyle('C8:O'.$counter)->applyFromArray([
            'alignment' => [
                'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $spreadsheet->getActiveSheet()->getStyle('B8:B'.$counter)->applyFromArray([
            'alignment' => [
                'horizontal' => Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => Style\Alignment::VERTICAL_CENTER,

            ],
        ]);
        $spreadsheet->getActiveSheet()->getStyle('A8:A'.$counter)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $spreadsheet->getActiveSheet()->freezePane('A8');

        if (!empty($this->missingFields)) {
            $this->Flash->error(Text::insert($this->errorMessage['export'], [
                'fields' => $this->missingFields,
                ]), 
                [
                    'escape' => false,
                    'params' => ['showButtons' => true, 'width' => 600]
                ]);
            return $this->redirect(['action' => 'index']);
        }
        // export XLSX file for download
        $this->ExportFile->export($spreadsheet, $listCandidatesXlsx['filename']);
        exit;
    }

    public function exportIqTest($id)
    {
        // load config
        $iqTestConfig = Configure::read('iqTestXlsx');
        try {
            // get data
            $order = $this->Orders->get($id, [
                'contain' => [
                    'Jobs',
                    'Companies',
                    'Guilds',
                    'Students',
                    'Students.IqTests',
                ]
            ]);
            $this->checkDeleteFlag($order, $this->Auth->user());
            // init worksheet
            $spreadsheet = $this->ExportFile->setXlsxProperties();
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(50);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(20);
            $spreadsheet->getActiveSheet()->setShowGridLines(false);
            $spreadsheet->getActiveSheet()->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(60);
            
            $spreadsheet->getActiveSheet()
                ->mergeCells('A1:AC2')->setCellValue('A1', '技能実習生候補者名簿')
                ->mergeCells('A3:AC3')->setCellValue('A3', '監理団体：' . $order->guild->name_kanji)
                ->mergeCells('A4:AC4')->setCellValue('A4', '受入企業：' . $order->company->name_kanji)
                ->mergeCells('A5:AC5')->setCellValue('A5', '受入職種：' . $order->job->job_name_jp);

            $spreadsheet->getActiveSheet()->getStyle('A1:A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 72,
                ],
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $spreadsheet->getActiveSheet()->getStyle('A3:A5')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 36,
                ],
                'alignment' => [
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ]
            ]);
            $spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(18);

            // set header
            $spreadsheet->getActiveSheet()
                ->mergeCells('A7:A8')->setCellValue('A7', 'No.')
                ->mergeCells('B7:B8')->setCellValue('B7', '氏名')
                ->mergeCells('C7:C8')->setCellValue('C7', '年齢')
                ->mergeCells('D7:O7')->setCellValue('D7', 'クレペリン検査　  もんだい　１')
                ->mergeCells('Q7:AB7')->setCellValue('Q7', 'クレペリン検査　　もんだい　２')
                ->mergeCells('AC7:AC8')->setCellValue('AC7', '合計');
            
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(4);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(36);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $spreadsheet->getActiveSheet()->getRowDimension('7')->setRowHeight(60);
            $spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(60);

            $spreadsheet->getActiveSheet()->fromArray(
                ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', NULL, '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                NULL,
                'D8'
            );

            $counter = 8;
            $now = Time::now();
            $listCandidates = [];
            foreach ($order->students as $key => $student) {
                $noCell = $key + 1;
                $counter++;
                $fullname = $student->fullname;
                $fullname_kata = $this->checkDataConcate($student->fullname_kata, 'Tên phiên âm');
                $studentName_VN = mb_strtoupper($fullname);
                $studentName_EN = $this->Util->convertV2E($studentName_VN);
    
                $nameCell = $fullname_kata . "\n" . $studentName_EN;
                $ageCell = ($now->diff($student->birthday))->y;
                $data = [
                    $noCell,
                    $nameCell,
                    $ageCell,
                    $student->iq_tests[0]->q1 ?? NULL,
                    $student->iq_tests[0]->q2 ?? NULL,
                    $student->iq_tests[0]->q3 ?? NULL,
                    $student->iq_tests[0]->q4 ?? NULL,
                    $student->iq_tests[0]->q5 ?? NULL,
                    $student->iq_tests[0]->q6 ?? NULL,
                    $student->iq_tests[0]->q7 ?? NULL,
                    $student->iq_tests[0]->q8 ?? NULL,
                    $student->iq_tests[0]->q9 ?? NULL,
                    $student->iq_tests[0]->q10 ?? NULL,
                    $student->iq_tests[0]->q11 ?? NULL,
                    $student->iq_tests[0]->q12 ?? NULL,
                    NULL,
                    $student->iq_tests[0]->q13 ?? NULL,
                    $student->iq_tests[0]->q14 ?? NULL,
                    $student->iq_tests[0]->q15 ?? NULL,
                    $student->iq_tests[0]->q16 ?? NULL,
                    $student->iq_tests[0]->q17 ?? NULL,
                    $student->iq_tests[0]->q18 ?? NULL,
                    $student->iq_tests[0]->q19 ?? NULL,
                    $student->iq_tests[0]->q20 ?? NULL,
                    $student->iq_tests[0]->q21 ?? NULL,
                    $student->iq_tests[0]->q22 ?? NULL,
                    $student->iq_tests[0]->q23 ?? NULL,
                    $student->iq_tests[0]->q24 ?? NULL,
                    $student->iq_tests[0]->total ?? NULL,
                ];
                array_push($listCandidates, $data);
            }
            // debug($counter);
            // exit;
            \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

            for ($i=9; $i <= $counter; $i++) { 
                if ($i % 2 == 0) {
                    $spreadsheet->getActiveSheet()->getStyle('A'.$i.':O'.$i)->applyFromArray([
                        'fill' => [
                            'fillType' => Style\Fill::FILL_SOLID,
                            'color' => [
                                'rgb' => 'daeef3'
                            ]
                        ]
                    ]);
                    $spreadsheet->getActiveSheet()->getStyle('Q'.$i.':AC'.$i)->applyFromArray([
                        'fill' => [
                            'fillType' => Style\Fill::FILL_SOLID,
                            'color' => [
                                'rgb' => 'daeef3'
                            ]
                        ]
                    ]);
                }
            }
            // fill data to table
            $spreadsheet->getActiveSheet()->fromArray($listCandidates, NULL, 'A9');
            $spreadsheet->getActiveSheet()->mergeCells('P7:P'.$counter);
            $spreadsheet->getActiveSheet()->getStyle('A7:AC'.$counter)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Style\Border::BORDER_THIN,
                    ]
                ]
            ]);
            $spreadsheet->getActiveSheet()->getStyle('A7:AC'.$counter)->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => Style\Border::BORDER_MEDIUM,
                    ]
                ]
            ]);
            $spreadsheet->getActiveSheet()->getStyle('A7:AC8')->applyFromArray([
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ],
                
            ]);
            $spreadsheet->getActiveSheet()->getStyle('A7:O8')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Style\Fill::FILL_SOLID,
                    'color' => [
                        'rgb' => 'daeef3'
                    ]
                ]
            ]);
            $spreadsheet->getActiveSheet()->getStyle('Q7:AC8')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Style\Fill::FILL_SOLID,
                    'color' => [
                        'rgb' => 'daeef3'
                    ]
                ]
            ]);

            $spreadsheet->getActiveSheet()->getStyle('C8:AC'.$counter)->applyFromArray([
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $spreadsheet->getActiveSheet()->getStyle('AC7:AC'.$counter)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);

            $spreadsheet->getActiveSheet()->getStyle('A7:A'.$counter)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $spreadsheet->getActiveSheet()->freezePane('A9');
            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true, 'width' => 600]
                    ]);
                return $this->redirect(['action' => 'index']);
            }
            // export XLSX file for download
            $this->ExportFile->export($spreadsheet, $iqTestConfig['filename']);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }

    public function exportDeclaration($id)
    {
        $declarationConfig = Configure::read('orderDeclaration');
        $vnDateFormatFull = Configure::read('vnDateFormatFull');
        $outputFileName = $declarationConfig['filename'];
        try {
            $order = $this->Orders->get($id, [
                'contain' => [
                    'Jobs',
                ]
            ]);
            $this->checkDeleteFlag($order, $this->Auth->user());
            $template = WWW_ROOT . 'document' . DS . $declarationConfig['template'];
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            $this->checkData($order->application_date, 'Ngày làm hồ sơ');
            $createdDayJP = $order->application_date ? $order->application_date->i18nFormat('yyyy 年 M 月 d 日') : '';
            $createdDayVN =  Text::insert($vnDateFormatFull, [
                'day' => $order->application_date ? str_pad($order->application_date->day, 2, '0', STR_PAD_LEFT) : '', 
                'month' => $order->application_date ? str_pad($order->application_date->month, 2, '0', STR_PAD_LEFT) : '', 
                'year' => $order->application_date ? $order->application_date->year : '', 
                ]);

            $this->tbs->VarRef['jobJP'] = $order->job->job_name_jp;
            $this->tbs->VarRef['jobVN'] = mb_strtolower($order->job->job_name);
            $this->tbs->VarRef['createdJP'] = $createdDayJP;
            $this->tbs->VarRef['createdVN'] = ucfirst($createdDayVN);

            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['action' => 'index']);
            }
            $this->tbs->Show(OPENTBS_DOWNLOAD, $outputFileName);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }

    public function exportSchedule($id)
    {
        $config = Configure::read('orderSchedule');
        $tableData = $config['tableData'];
        $outputFileName = $config['filename'];
        try { 
            $order = $this->Orders->get($id, ['contain' => [
                'AdminCompanies',
                'Students' => function($q) {
                    return $q->where(['result' => '1']);
                },
                'Schedules', 
                'Schedules.Holidays' => [
                    'sort' => ['Holidays.day' => 'ASC']
                ]
            ]]);
            $adminCompany = $order->admin_company;
            $this->checkData($order->departure_date, 'Ngày xuất cảnh dự kiến');
            $departureDate = new Time($order->departure_date);
            $template = WWW_ROOT . 'document' . DS . $config['template'];
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            $start = $order->application_date;
            $end = $order->schedule->end_date;
            $holidays = [];
            if (!empty($order->schedule->holidays)) {
                foreach ($order->schedule->holidays as $key => $holiday) {
                    $holidays[$holiday->day->i18nFormat('yyyy/M/d')] = $holiday->type;
                }
            }

            $this->tbs->VarRef['sy'] = $start->year;
            $this->tbs->VarRef['sm'] = $start->month;
            $this->tbs->VarRef['sd'] = $start->day;

            $this->tbs->VarRef['ey'] = $end->year;
            $this->tbs->VarRef['em'] = $end->month;
            $this->tbs->VarRef['ed'] = $end->day;

            $table = [];
            $counter = 1;
            while ($start <= $end) {
                $startTxt = $start->i18nFormat('yyyy/M/d');
                $row = [
                    'day' => $startTxt,
                ];
                $dayOfWeek = date('N', strtotime($start));
                if ($dayOfWeek == 6 || $dayOfWeek == 7 || array_key_exists($startTxt, $holidays)) {
                    if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                        $row['time'] = '休日';
                    }
                    if (array_key_exists($startTxt, $holidays)) {
                        if ($holidays[$startTxt] == 1) {
                            $row['time'] = '祝日';
                        } else {
                            $row['time'] = '休日';
                        }
                    }
                    $row['content'] = '';
                    $row['place'] = '';
                    $row['teacher'] = '';
                } else {
                    $row['time'] = '7:30〜16:30';
                    $row['content'] = $tableData[$counter]['content'];
                    if ($counter == 1) {
                        $row['place'] = $adminCompany->edu_center_name_jp;
                    } else {
                        $row['place'] = '//';
                    }
                    switch ($counter) {
                        case $counter < 20:
                            $row['teacher'] = $this->Util->convertV2E($order->schedule->teacher1) . '(日本語教師)';
                            break;
                        case $counter >= 20 && $counter < 23:
                            $row['teacher'] = $this->Util->convertV2E($order->schedule->teacher2) . '(日本語教師)';
                            break;
                        default:
                            $row['teacher'] = $this->Util->convertV2E($order->schedule->teacher3) . '(日本語教師)';
                            break;
                    }
                    $counter++;
                }
                $row['note'] = '';
                $start = $start->addDay(1);
                array_push($table, $row);
            }
            $students = [];
            if (!empty($order->students)) {
                foreach ($order->students as $key => $student) {
                    $studentName_VN = mb_strtoupper($student->fullname);
                    $studentName_EN = $this->Util->convertV2E($studentName_VN);
                    $row = [
                        'no' => $key + 1,
                        'fullname' => $studentName_EN,
                        'departure' => $departureDate->year . '/' . $departureDate->month
                    ];
                    array_push($students, $row);
                }
            }

            $this->tbs->MergeBlock('a', $table);
            $this->tbs->MergeBlock('b', $students);
            $this->tbs->VarRef['signRole'] = $adminCompany->signer_role_jp;
            $this->tbs->VarRef['signName'] = $this->Util->convertV2E($adminCompany->signer_name);
            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['action' => 'index']);
            }

            $this->tbs->Show(OPENTBS_DOWNLOAD, $outputFileName);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }

    public function exportScheduleRecord($id = null)
    {
        $config = Configure::read('orderScheduleRecord');
        $outputFileName = $config['filename'];
        try { 
            $order = $this->Orders->get($id, ['contain' => [
                'AdminCompanies',
                'Guilds',
                'Students' => function($q) {
                    return $q->where(['result' => '1']);
                },
                'Schedules', 
                'Schedules.Holidays' => [
                    'sort' => ['Holidays.day' => 'ASC']
                ]
            ]]);
            $adminCompany = $order->admin_company;
            $this->checkData($order->departure_date, 'Ngày xuất cảnh dự kiến');
            $guildName = $this->checkData($order->guild->name_kanji, 'Tên phiên âm của nghiệp đoàn');
            $guildAddress = $this->checkData($order->guild->address_kanji, 'Địa chỉ phiên âm của nghiệp đoàn');
            $deputy = $this->checkData($order->guild->deputy_name_kanji, 'Tên phiên âm của người đại diện nghiệp đoàn');
            $departureDate = new Time($order->departure_date);
            $template = WWW_ROOT . 'document' . DS . $config['template'];
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            $start = $order->application_date;
            $end = $order->schedule->end_date;
            $holidays = [];
            if (!empty($order->schedule->holidays)) {
                foreach ($order->schedule->holidays as $key => $holiday) {
                    $holidays[$holiday->day->i18nFormat('yyyy/M/d')] = $holiday->type;
                }
            }
            $this->tbs->VarRef['adCompName'] = $adminCompany->name_en;
            $this->tbs->VarRef['eduCenterAddr'] = mb_strtoupper($adminCompany->edu_center_address_en);

            $this->tbs->VarRef['guild'] = $guildName;
            $this->tbs->VarRef['guildAddress'] = $guildAddress;


            $this->tbs->VarRef['sy'] = $start->year;
            $this->tbs->VarRef['sm'] = $start->month;
            $this->tbs->VarRef['sd'] = $start->day;

            $this->tbs->VarRef['ey'] = $end->year;
            $this->tbs->VarRef['em'] = $end->month;
            $this->tbs->VarRef['ed'] = $end->day;
            $this->tbs->VarRef['deputy'] = $deputy;

            $this->tbs->VarRef['p1Start'] = $start->i18nFormat('yyyy年M月d日');

            $counter = 0;
            while ($start <= $end) {
                $startTxt = $start->i18nFormat('yyyy/M/d');
                
                $dayOfWeek = date('N', strtotime($start));
                if ($dayOfWeek != 6 && $dayOfWeek != 7 && !array_key_exists($startTxt, $holidays)) {
                    $counter++;
                    switch ($counter) {
                        case $counter == 19:
                            $this->tbs->VarRef['p1End'] = $start->i18nFormat('yyyy年M月d日');
                            $this->tbs->VarRef['p2Start'] = $start->addDay(1)->i18nFormat('yyyy年M月d日');
                            break;
                        case $counter == 22:
                            $this->tbs->VarRef['p2End'] = $start->i18nFormat('yyyy年M月d日');
                            $this->tbs->VarRef['p3Start'] = $start->addDay(1)->i18nFormat('yyyy年M月d日');
                            break;
                        case $counter == 25:
                            $this->tbs->VarRef['p3End'] = $start->i18nFormat('yyyy年M月d日');
                            break;
                    }
                }
                $start = $start->addDay(1);
            }

            $students = [];
            if (!empty($order->students)) {
                foreach ($order->students as $key => $student) {
                    $studentName_VN = mb_strtoupper($student->fullname);
                    $studentName_EN = $this->Util->convertV2E($studentName_VN);
                    $row = [
                        'no' => $key + 1,
                        'fullname' => $studentName_EN,
                        'departure' => $departureDate->year . '/' . $departureDate->month
                    ];
                    array_push($students, $row);
                }
            }
            $this->tbs->MergeBlock('a', $students);
            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['action' => 'index']);
            }

            $this->tbs->Show(OPENTBS_DOWNLOAD, $outputFileName);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }

    public function exportScheduleReport($id = null)
    {
        // load config
        $config = Configure::read('scheduleReportXlsx');
        try {
            $order = $this->Orders->get($id, ['contain' => ['Guilds', 'Schedules', 'AdminCompanies']]);
            $adminCompany = $order->admin_company;
            $start = $order->application_date;
            $end = $order->schedule->end_date;
            $guildName = $this->checkData($order->guild->name_kanji, 'Tên phiên âm của nghiệp đoàn');
            $adminCompanyBranchJP = $adminCompany->branch_jp;
            // init worksheet
            $spreadsheet = $this->ExportFile->setXlsxProperties();
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getDefaultStyle()->getFont()->setName('MS PMincho');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(12);
            $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(2.5);

            $spreadsheet->getActiveSheet()->mergeCells('A1:AH1')->setCellValue('A1', '本邦外に於ける講習実施報告書');
            $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(54);
            $spreadsheet->getActiveSheet()->getStyle('A1:A1')->applyFromArray([
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_TOP,
                ],
                'font' => [
                    'size' => 16,
                ],
            ]);
            
            $spreadsheet->getActiveSheet()->setCellValue('A2', $guildName);
            $spreadsheet->getActiveSheet()->setCellValue('P2', '御中');
            $spreadsheet->getActiveSheet()->setCellValue('B4', '別添名簿の講習受講者に対する講習は、計画通り実施したことを報告いたします。');
            $spreadsheet->getActiveSheet()->setCellValue('A6', '1.');
            $spreadsheet->getActiveSheet()
                ->setCellValueExplicit(
                        'A6',
                        '1.',
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    )
                ->setCellValue('B6', '講習実施施設')
                ->setCellValue('B7', '施設名：')
                ->mergeCells('F7:AH8')->setCellValue('F7', $adminCompany->name_en)
                ->setCellValue('F9', $adminCompanyBranchJP . '人材教育センター')
                ->setCellValue('B10', '所在地：')
                ->mergeCells('F10:AH11')->setCellValue('F10', mb_strtoupper($adminCompany->edu_center_address_en))
                ->setCellValueExplicit(
                    'A12',
                    '2.',
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                )
                ->setCellValue('B12', '講習実施期間')
                ->mergeCells('B13:D13')->setCellValue('B13', $start->year)
                ->setCellValue('E13', '年')
                ->mergeCells('F13:G13')->setCellValue('F13', $start->month)
                ->setCellValue('H13', '月')
                ->mergeCells('I13:J13')->setCellValue('I13', $start->day)
                ->setCellValue('K13', '日')
                ->mergeCells('L13:M13')->setCellValue('L13', '〜')
                ->mergeCells('N13:P13')->setCellValue('N13', $end->year)
                ->setCellValue('Q13', '年')
                ->mergeCells('R13:S13')->setCellValue('R13', $end->month)
                ->setCellValue('T13', '月')
                ->mergeCells('U13:V13')->setCellValue('U13', $end->day)
                ->setCellValue('W13', '日')
                ->setCellValueExplicit(
                    'A14',
                    '3.',
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                )
                ->setCellValue('B14', '講習実施時間')
                ->setCellValue('B15', '合計175時間')
                ->setCellValue('B16', '①')
                ->setCellValue('C16', '日本語133時間')
                ->setCellValue('B17', '②')
                ->setCellValue('C17', '専門用語・日本での生活一般に関する知識21時間')
                ->setCellValue('B18', '③')
                ->setCellValue('C18', '日本での円滑な技能等の修得に資する知識21時間')
                ->setCellValueExplicit(
                    'A19',
                    '4.',
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                )
                ->setCellValue('B19', '講習宿泊施設')
                ->setCellValue('B20', '施設名：')
                ->mergeCells('F20:AH21')->setCellValue('F20', $adminCompany->name_en)
                ->setCellValue('F22', $adminCompanyBranchJP . '人材教育センター所属寮')
                ->setCellValueExplicit(
                    'A23',
                    '5.',
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                )
                ->setCellValue('B23', '別添書類')
                ->setCellValue('B24', '①')
                ->setCellValue('C24', '「入国前講習実施記録」')
                ->setCellValue('B25', '②')
                ->setCellValue('C25', '「技能実習生一覧表」')
                ->mergeCells('Y26:AA26')->setCellValue('Y26', $end->year)
                ->setCellValue('AB26', '年')
                ->mergeCells('AC26:AD26')->setCellValue('AC26', $end->month)
                ->setCellValue('AE26', '月')
                ->mergeCells('AF26:AG26')->setCellValue('AF26', $end->day)
                ->setCellValue('AH26', '日')
                ->setCellValue('B27', '講習実施機関：')
                ->mergeCells('H27:AH28')->setCellValue('H27', $adminCompany->name_en)
                ->setCellValue('B30', '所在地：')
                ->mergeCells('H30:AH31')->setCellValue('H30', mb_strtoupper($adminCompany->edu_center_address_en))
                ->setCellValue('B32', '電話：')
                ->setCellValue('H32', $adminCompany->phone_number)
                ->setCellValue('B33', '責任者：')
                ->setCellValue('H33', $adminCompany->signer_role_jp .'    '. $this->Util->convertV2E($adminCompany->signer_name . '    ㊞'));
            if (!empty($adminCompanyBranchJP)) {
                $spreadsheet->getActiveSheet()
                    ->setCellValue('H29', 'ホーチミン市支部所属人材教育センター')
                    ->setCellValue('H30', mb_strtoupper($adminCompany->edu_center_address_en))
                    ->getStyle('H30')->getAlignment()->setWrapText(true);
            } else {
                $spreadsheet->getActiveSheet()
                    ->removeRow('29')
                    ->setCellValue('H29', mb_strtoupper($adminCompany->address_en))
                    ->getStyle('H29')->getAlignment()->setWrapText(true);
            }
            $spreadsheet->getActiveSheet()->getStyle('F7')->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('F10')->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('F20')->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('H27')->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('H30')->getAlignment()->setWrapText(true);

            $spreadsheet->getActiveSheet()->getStyle('A2:AH33')->applyFromArray([
                'alignment' => [
                    'vertical' => Style\Alignment::VERTICAL_TOP,
                ]
            ]);
            
            $spreadsheet->getActiveSheet()->setSelectedCells('A1');

            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['action' => 'index']);
            }
            // export XLSX file for download
            $this->ExportFile->export($spreadsheet, $config['filename']);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }

    public function exportFees($id = null)
    {
        $config = Configure::read('orderFees');
        $outputFileName = $config['filename'];
        $vnDateFormatFull = Configure::read('vnDateFormatFull');
        $query = $this->request->getQuery();
        try { 
            $order = $this->Orders->get($id, ['contain' => [
                'Companies',
                'AdminCompanies',
                'Guilds',
                'Schedules', 
                'Schedules.Holidays' => [
                    'sort' => ['Holidays.day' => 'ASC']
                ]
            ]]);
            $adminCompanies = $order->admin_company;
            $student = $this->Orders->Students->get($query['studentId']);
            $template = WWW_ROOT . 'document' . DS . $config['template'];
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            $guildKanji = $this->checkData($order->guild->name_kanji, 'Tên phiên âm của nghiệp đoàn');
            $companyKanji = $this->checkData($order->company->name_kanji, 'Tên phiên âm của công ty');
            $this->checkData($order->application_date, 'Ngày làm hồ sơ');
            $this->checkData($order->departure_date, 'Ngày xuất cảnh dự kiến');

            $departureDate = new Time($order->departure_date);

            $healthCheckDate2 = $departureDate->subDays(21);

            $dayOfWeek = date('N', strtotime($healthCheckDate2));
            if ($dayOfWeek > 3) {
                $healthCheckDate2 = $healthCheckDate2->subDays($dayOfWeek - 3);
            } else if ($dayOfWeek < 3) {
                $healthCheckDate2 = $healthCheckDate2->addDays(3 - $dayOfWeek);
            }
            $studentNameVN = mb_strtoupper($student->fullname);
            $studentNameEN = $this->Util->convertV2E($studentNameVN);

            $this->tbs->VarRef['adCompNameEN'] = $adminCompanies->name_en;
            $this->tbs->VarRef['adCompNameVN'] = $adminCompanies->name_vn;
            $this->tbs->VarRef['adCompShortName'] = $adminCompanies->short_name;

            $this->tbs->VarRef['f1VN'] = number_format($adminCompanies->basic_training_fee_vn);
            $this->tbs->VarRef['f1JP'] = number_format($adminCompanies->basic_training_fee_jp);

            $this->tbs->VarRef['f2VN'] = number_format($adminCompanies->training_fee_vn);
            $this->tbs->VarRef['f21VN'] = number_format(round($adminCompanies->training_fee_vn/3));
            $this->tbs->VarRef['f2JP'] = number_format($adminCompanies->training_fee_jp);
            $this->tbs->VarRef['f21JP'] = number_format(round($adminCompanies->training_fee_jp/3));

            $this->tbs->VarRef['f3VN'] = number_format($adminCompanies->oriented_fee_vn);
            $this->tbs->VarRef['f3JP'] = number_format($adminCompanies->oriented_fee_jp);

            $this->tbs->VarRef['f4VN'] = number_format($adminCompanies->documents_fee_vn);
            $this->tbs->VarRef['f4JP'] = number_format($adminCompanies->documents_fee_jp);

            $this->tbs->VarRef['f5p1VN'] = number_format($adminCompanies->health_test_fee_1_vn);
            $this->tbs->VarRef['f5p1JP'] = number_format($adminCompanies->health_test_fee_1_jp);
            $this->tbs->VarRef['f5p2VN'] = number_format($adminCompanies->health_test_fee_2_vn);
            $this->tbs->VarRef['f5p2JP'] = number_format($adminCompanies->health_test_fee_2_jp);
            
            $this->tbs->VarRef['f6VN'] = number_format($adminCompanies->dispatch_fee_vn);
            $this->tbs->VarRef['f6JP'] = number_format($adminCompanies->dispatch_fee_jp);

            $this->tbs->VarRef['f7VN'] = number_format($adminCompanies->accommodation_fee_vn);
            $this->tbs->VarRef['f7JP'] = number_format($adminCompanies->accommodation_fee_jp);

            $this->tbs->VarRef['f8p1VN'] = number_format($adminCompanies->visa_fee_1_vn);
            $this->tbs->VarRef['f8p1JP'] = number_format($adminCompanies->visa_fee_1_jp);
            $this->tbs->VarRef['f8p2VN'] = number_format($adminCompanies->visa_fee_2_vn);
            $this->tbs->VarRef['f8p2JP'] = number_format($adminCompanies->visa_fee_2_jp);

            $this->tbs->VarRef['f9VN'] = number_format($adminCompanies->foes_fee_vn);
            $this->tbs->VarRef['f9JP'] = number_format($adminCompanies->foes_fee_jp);

            $this->tbs->VarRef['f10VN'] = number_format($adminCompanies->other_fees_vn);
            $this->tbs->VarRef['f10JP'] = number_format($adminCompanies->other_fees_jp);

            $this->tbs->VarRef['f11VN'] = number_format($adminCompanies->total_fees_vn);
            $this->tbs->VarRef['f11JP'] = number_format($adminCompanies->total_fees_jp);

            $this->tbs->VarRef['signRoleJP'] = $adminCompanies->signer_role_jp;
            $this->tbs->VarRef['signRoleVN'] = $adminCompanies->signer_role_vn;
            $this->tbs->VarRef['signNameEN'] = $this->Util->convertV2E($adminCompanies->signer_name);
            $this->tbs->VarRef['signNameVN'] = $adminCompanies->signer_name;

            $this->tbs->VarRef['fullnameEN'] = $studentNameEN;
            $this->tbs->VarRef['fullnameVN'] = $studentNameVN;

            $this->tbs->VarRef['companyKanji'] = $companyKanji;
            $this->tbs->VarRef['companyRomaji'] = $order->company->name_romaji;

            $this->tbs->VarRef['guildKanji'] = $guildKanji;
            $this->tbs->VarRef['guildRomaji'] = $order->guild->name_romaji;

            $this->tbs->VarRef['startJP'] = $order->application_date->i18nFormat('yyyy年M月d日');
            $this->tbs->VarRef['startVN'] = ucfirst(Text::insert($vnDateFormatFull, [
                'day' => $order->application_date ? str_pad($order->application_date->day, 2, '0', STR_PAD_LEFT) : '', 
                'month' => $order->application_date ? str_pad($order->application_date->month, 2, '0', STR_PAD_LEFT) : '', 
                'year' => $order->application_date ? $order->application_date->year : '', 
                ]));

            $this->tbs->VarRef['health2JP'] = $healthCheckDate2->i18nFormat('yyyy年M月d日');
            $this->tbs->VarRef['health2VN'] = ucfirst(Text::insert($vnDateFormatFull, [
                'day' => str_pad($healthCheckDate2->day, 2, '0', STR_PAD_LEFT), 
                'month' => str_pad($healthCheckDate2->month, 2, '0', STR_PAD_LEFT), 
                'year' => $healthCheckDate2->year, 
                ]));

            $nextHealth =  $healthCheckDate2->addDays(1);
            $this->tbs->VarRef['nextHealthJP'] = $nextHealth->i18nFormat('yyyy年M月d日');
            $this->tbs->VarRef['nextHealthVN'] = ucfirst(Text::insert($vnDateFormatFull, [
                'day' => str_pad($nextHealth->day, 2, '0', STR_PAD_LEFT), 
                'month' => str_pad($nextHealth->month, 2, '0', STR_PAD_LEFT), 
                'year' => $nextHealth->year, 
                ]));
            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['action' => 'index']);
            }

            $this->tbs->Show(OPENTBS_DOWNLOAD, $outputFileName);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }

    public function exportVaf($id = null)
    {
        $config = Configure::read('orderVaf');
        $folderImgTemplate = Configure::read('folderImgTemplate');
        $outputFileName = $config['filename'];
        $query = $this->request->getQuery();
        try {
            $order = $this->Orders->get($id, ['contain' => [
                'Companies',
                'DisCompanies',
                'AdminCompanies',
                'Guilds',
            ]]);
            $this->checkData($order->dis_company, 'Công ty phái cử');
            $student = $this->Orders->Students->get($query['studentId'], [
                'contain' => [
                    'Addresses',
                    'Addresses.Cities',
                    'Addresses.Districts',
                    'Addresses.Wards',
                    'Cards'
                ]
            ]);
            $studentNameVN = mb_strtoupper($student->fullname);
            $studentNameEN = $this->Util->convertV2E($studentNameVN);
            $studentName = explode(' ', $studentNameEN);
            
            $surname = array_shift($studentName);
            $firstName = end($studentName);
            $studentGivenMiddleNames = trim(str_replace($surname, '', $studentNameEN));
            
            $outputFileName = Text::insert($config['filename'], [
                'firstName' => $firstName, 
            ]);

            $male = $female = $folderImgTemplate . DS . 'no_check.png';
            if ($student->gender == 'M') {
                $male = $folderImgTemplate . DS . 'check.png';
            } else {
                $female = $folderImgTemplate . DS . 'check.png';
            }

            $single = $married = $widowed = $divorced = $folderImgTemplate . DS . 'no_check.png';
            switch ($student->marital_status) {
                case 1:
                    $single = $folderImgTemplate . DS . 'check.png';
                    break;
                case 2:
                    $married = $folderImgTemplate . DS . 'check.png';
                    break;
                case 3:
                    $divorced = $folderImgTemplate . DS . 'check.png';
                    break;
            }

            $householdAddress = $currentAddress = NULL;
            foreach ($student->addresses as $key => $address) {
                if ($address->type == 1) {
                    $householdAddress = $address;
                } else {
                    $currentAddress = $address;
                }
            }
            
            $this->checkData($currentAddress->ward_id, 'Nơi ở hiện tại: Phường/Xã');
            $this->checkData($currentAddress->district_id, 'Nơi ở hiện tại: Quận/Huyện');
            $this->checkData($currentAddress->city_id, 'Nơi ở hiện tại: Tỉnh/Thành phố');

            $cmnd = $passport = NULL;
            foreach ($student->cards as $key => $card) {
                if ($card->type == 1) {
                    $cmnd = $card;
                } elseif ($card->type == 2) {
                    $passport = $card;
                } 
            }
            $this->checkData($cmnd->code, 'Chứng minh nhân dân');
            $this->checkData($passport->code, 'Passport');
            $template = WWW_ROOT . 'document' . DS . $config['template'];
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            $this->tbs->VarRef['firstName'] = $surname;
            $this->tbs->VarRef['givenMiddleNames'] = $studentGivenMiddleNames;
            $this->tbs->VarRef['bd'] = $student->birthday->i18nFormat('dd/MM/yyyy');
            $this->tbs->VarRef['householdAddress'] = $this->convertAddressToEng($householdAddress->city, 'city') . ', VIETNAM';
            $this->tbs->VarRef['currentAddress'] = trim(Text::insert(':ward, :district, :city', [
                'ward' => $this->convertAddressToEng($currentAddress->ward, 'ward'),
                'district' => $this->convertAddressToEng($currentAddress->district, 'district'),
                'city' => $this->convertAddressToEng($currentAddress->city, 'city')
            ]));
            $this->tbs->VarRef['cmndNo'] = $cmnd->code;
            $this->tbs->VarRef['ppNo'] = $passport->code;
            $this->tbs->VarRef['doi'] = $passport->from_date ? $passport->from_date->i18nFormat('dd/MM/yyyy') : '';
            $this->tbs->VarRef['doe'] = $passport->to_date ? $passport->to_date->i18nFormat('dd/MM/yyyy') : '';
            $this->tbs->VarRef['male'] = $male;
            $this->tbs->VarRef['female'] = $female;
            $this->tbs->VarRef['phone'] = $student->phone;

            $this->tbs->VarRef['single'] = $single;
            $this->tbs->VarRef['married'] = $married;
            $this->tbs->VarRef['widowed'] = $widowed;
            $this->tbs->VarRef['divorced'] = $divorced;

            $this->tbs->VarRef['cpName'] = $order->company->name_romaji;
            $this->tbs->VarRef['cpTel'] = $order->company->phone_jp;
            $this->tbs->VarRef['cpAddress'] = $order->company->address_romaji;

            $this->tbs->VarRef['disCpName'] = $order->dis_company ? $order->dis_company->name_kanji : '';
            $this->tbs->VarRef['disCpTel'] = $order->dis_company ? $order->dis_company->phone_vn : '';
            $this->tbs->VarRef['disCpAddress'] = $order->dis_company ? $order->dis_company->address_romaji : '';

            $this->tbs->VarRef['guildName'] = $order->guild->name_romaji;
            $this->tbs->VarRef['guildTel'] = $order->guild->phone_jp;
            $this->tbs->VarRef['guildAddress'] = $order->guild->address_romaji;

            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect($this->referer());
            }
            $this->tbs->Show(OPENTBS_DOWNLOAD, $outputFileName);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }

    public function exportCertificate($id)
    {
        $orderCertificateConfig = Configure::read('orderCertificate');
        $outputFileName = $orderCertificateConfig['filename'];
        $genderJP = Configure::read('genderJP');

        try {
            $order = $this->Orders->get($id, [
                'contain' => [
                    'Jobs',
                    'DisCompanies' => function ($q) {
                        return $q->where(['DisCompanies.del_flag' => FALSE]);
                    },
                    'Students' => function($q) {
                        return $q->where(['result' => '1']);
                    }
                ]
            ]);
            $this->checkDeleteFlag($order, $this->Auth->user());
            $template = WWW_ROOT . 'document' . DS . $orderCertificateConfig['template'];
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
            $firstStudentName = $order->students[0]->fullname;

            $disCompany = $this->checkData($order->dis_company, 'Công ty phái cử');
            $this->checkData($order->application_date, 'Ngày làm hồ sơ');
            $createdDayJP = $order->application_date ? $order->application_date->i18nFormat('yyyy年M月d日') : '';

            $this->tbs->VarRef['firstSName_VN'] = mb_strtoupper($firstStudentName);
            $this->tbs->VarRef['firstSName_EN'] = $this->Util->convertV2E($firstStudentName);
            $this->tbs->VarRef['remain'] = count($order->students) - 1;
            $this->tbs->VarRef['disCompany'] = mb_strtoupper($order->dis_company ? $order->dis_company->name_romaji : '');
            $this->tbs->VarRef['job'] = $order->job->job_name_jp;
            $this->tbs->VarRef['created'] = $createdDayJP;

            $deputy = '';
            if (!empty($order->dis_company)) {
                $deputy = $this->checkData($order->dis_company->deputy_name_romaji, 'Tên người đại diện công ty phái cử');
                $deputy = $this->Util->convertV2E($deputy);
            }
            $this->tbs->VarRef['deputy_EN'] = $deputy;

            $listStudents = [];
            foreach ($order->students as $key => $student) {
                $studentName_VN = mb_strtoupper($student->fullname);
                $studentName_EN = $this->Util->convertV2E($studentName_VN);

                $student = [
                    'no' => $key + 1,
                    'fullname' => $studentName_EN,
                    'birthday' => $student->birthday->i18nFormat('yyyy年M月d日'),
                    'gender' => $genderJP[$student->gender],
                ];
                array_push($listStudents, $student);
            }
            $this->tbs->MergeBlock('a', $listStudents);

            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['action' => 'index']);
            }
            $this->tbs->Show(OPENTBS_DOWNLOAD, $outputFileName);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }

    public function checkData($data, $field) 
    {
        if (empty($data)) {
            $this->missingFields .= '<li>'. $field . '</li>';
        }
        return $data;
    }

    public function checkDataConcate($data, $field) 
    {
        if (empty($data)) {
            if (!empty($this->studentError)) {
                $this->studentError .= ', ' . $field;
            } else {
                $this->studentError .= $field;
            }
        }
        return $data;
    }

    public function convertTag($data, $all, $label)
    {
        $data = explode(',', $data);
        array_shift($data);
        array_pop($data);
        $result = '';
        $total = count($data) - 1;
        foreach ($data as $key => $value) {
            $obj = $this->findObjectById($value, $all);
            if (!empty($obj)) {
                $this->checkData($obj->name_jp, $label);
                if (empty($obj->name_jp)) {
                    break;
                }
                if ($key == $total) {
                    $result .= $obj->name_jp;
                } else {
                    $result .= $obj->name_jp . ', ';
                }
            }
            
        }
        return $result;
    }

    public function findObjectById($id, $data)
    {
        foreach ($data as $key => $obj) {
            if ($obj->id == $id) {
                return $obj;
            }
        }
        return '';
    }

    public function convertAddressToEng($value, $type)
    {
        $result = '';
        if (!empty($value)) {
            try {
                $addressLevel = Configure::read('addressLevel');
                $result = $value;
                switch ($type) {
                    case 'city':
                        $result = $value->name;
                        $cityType = $value->type;
                        if ($cityType == 'Thành phố Trung ương') {
                            $result = $this->Util->convertV2E(str_replace("Thành phố", "", $result) . " " . $addressLevel['Thành phố']['en']);
                        } else {
                            $result = $this->Util->convertV2E(str_replace($cityType, "", $result) . " " . $addressLevel[$cityType]['en']);
                        }
                        break;
                    case 'district':
                        $result = $value->name;
                        $districtType = $value->type;
                        $district = trim(str_replace($districtType, "", $result));
                        if (is_numeric($district)) {
                            $result = $this->Util->convertV2E($addressLevel[$districtType]['en'] . " " . $district);
                        } else {
                            $result = $this->Util->convertV2E($district . " " . $addressLevel[$districtType]['en']);
                        }
                        break;
                    case 'ward':
                        $result = $value->name;
                        $wardType = $value->type;
                        $ward = trim(str_replace($wardType, '', $result));
    
                        if (is_numeric($ward)) {
                            $result = $this->Util->convertV2E($addressLevel[$wardType]['en'] . " " . $ward);
                        } else {
                            $result = $this->Util->convertV2E($ward . " " . $addressLevel[$wardType]['en']);
                        }
                        break;
                    case 'street':
                        $result = $this->Util->convertV2E($result);
                        str_replace('DUONG', '', $result);
                        str_replace('SO', '', $result);
                        break;
                }
            } catch (Exception $e) {
                Log::write('debug', $e);
            }
        }
        return $result;
    }
}

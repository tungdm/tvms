<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\Core\Exception\Exception;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Log\Log;


/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Util');
    }

    public function isAuthorized($user) 
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        $session = $this->request->session();
        if (isset($user['role'])) {
            return true;
        }
        return parent::isAuthorized($user);
    }
    /**
     * Displays a view
     *
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Network\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display(...$path)
    {
        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        // get data
        $orderTable = TableRegistry::get('Orders');
        $orderStudentsTable = TableRegistry::get('OrdersStudents');
        $studentTable = TableRegistry::get('Students');
        $now = Time::now();
        $year = $now->year;
        $month = $now->month;
        $currentMonth = $now->i18nFormat('yyyy-MM');
        $firstDayOfMonth = $currentMonth . '-01';
        $lastDayOfMonth = $this->Util->getLastDayOfMonth($firstDayOfMonth);

        // first row data
        $newOrder = $orderTable->find()->where(['created >=' => $firstDayOfMonth])->count();
        $newStudent = $studentTable->find()->where(['enrolled_date >=' => $firstDayOfMonth])->count();
        $returnStudent = $studentTable->find()->where(['return_date' => $currentMonth])->count();
        $newPassedCount = $orderStudentsTable->find()->contain(['Orders'])->where(['result' => '1', 'Orders.interview_date >=' => $firstDayOfMonth])->count();
        
        // second row data
        $northPopulation = $this->getAreaPopulation(['from' => '01', 'to' => '37']);
        $middlePopulation = $this->getAreaPopulation(['from' => '38', 'to' => '69']);
        $southPopulation = $this->getAreaPopulation(['from' => '70', 'to' => '96']);
        
        // third row data
        $totalStudent = $studentTable->find()->count();

        $totalPassed = $studentTable->find()->where(['status' => '3'])->count();
        $totalImmigrationCount = $studentTable->find()->where(['status' => '5'])->count();
        $rateImmi = round($totalImmigrationCount/$totalStudent, 4) * 100;
        
        $totalReturn = $studentTable->find()->where(['status IN' => ['5', '8']])->count();
        $totalWithdraw = $studentTable->find()->where(['status' => '7'])->count();
        $rateWithdraw = round($totalWithdraw/$totalStudent, 4) * 100;
        
        for ($i=1; $i < $month; $i++) {
            $pastMonth = $year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $firstDayOfMonthTmp = $pastMonth . '-01';
            $lastDayOfMonthTmp = $this->Util->getLastDayOfMonth($pastMonth);

            $monthlyNewOrder = $orderTable->find()
                ->where(function (QueryExpression $exp, Query $q) use ($firstDayOfMonthTmp, $lastDayOfMonthTmp) {
                    return $exp->between('interview_date', $firstDayOfMonthTmp, $lastDayOfMonthTmp, 'date');
                })->count();
            $monthlyNewStudent = $studentTable->find()
                ->where(function (QueryExpression $exp, Query $q) use($firstDayOfMonthTmp, $lastDayOfMonthTmp) {
                    return $exp->between('enrolled_date', $firstDayOfMonthTmp, $lastDayOfMonthTmp, 'date');
                })->count();
            $totalData[$pastMonth] = [
                'student' => $monthlyNewStudent,
                'order' => $monthlyNewOrder
            ];
        }
        $totalData[$currentMonth] = [
            'student' => $newStudent,
            'order' => $newOrder
        ];

        $data = [
            'currentMonth' => $currentMonth,
            'firstDayOfMonth' => $firstDayOfMonth,
            'lastDayOfMonth' => $lastDayOfMonth,
            'newOrder' => $newOrder,
            'newStudent' => $newStudent,
            'returnStudent' => $returnStudent,
            'newPassedCount' => $newPassedCount,
            'totalData' => $totalData,
            'totalPassed' => $totalPassed,
            'totalImmigrationCount' => $totalImmigrationCount,
            'rateImmi' => $rateImmi,
            'totalReturn' => $totalReturn,
            'totalWithdraw' => $totalWithdraw,
            'rateWithdraw' => $rateWithdraw,
            'northPopulation' => $northPopulation,
            'middlePopulation' => $middlePopulation,
            'southPopulation' => $southPopulation
        ];

        $this->set(compact('page', 'subpage', 'data'));
        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

    public function getPassedStudents()
    {
        $this->request->allowMethod('ajax');
        
        $resp = [
            'status' => 'error',
            'flash' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'icon' => 'fa fa-warning',
                'message' => $this->errorMessage['error']
            ]
        ];
        try {
            $now = Time::now();
            $currentMonth = $now->i18nFormat('yyyy-MM') . '-01';
            $orderStudentsTable = TableRegistry::get('OrdersStudents');
            $newlyPassed = $orderStudentsTable->find()
                            ->contain([
                                'Orders' => function($q) {
                                    return $q->where(['Orders.del_flag' => FALSE]);
                                },
                                'Students' => function($q) {
                                    return $q->where(['Students.del_flag' => FALSE]);
                                },
                                'Students.Addresses' => function($q) {
                                    return $q->where(['Addresses.type' => 1]);
                                },
                                'Students.Addresses.Cities'
                            ])
                            ->where(['result' => '1', 'Orders.interview_date >=' => $currentMonth])
                            ->order(['Orders.id' => 'ASC']);
            $newlyPassed->formatResults(function ($results) {
                return $results->map(function ($row) {
                    $jobId = $row['_matchingData']['Orders']['job_id'];
                    $row['hometown'] = $row['student']['addresses'][0]['city']['name'];
                    $row['north'] = $row['student']['addresses'][0]['city_id'] >= '01' && $row['student']['addresses'][0]['city_id'] <= '37';
                    return $row;
                });
            });
    
            $resp = [
                'status' => 'success',
                'data' => $newlyPassed
            ];
            Log::write('debug', $resp);
        } catch (Exception $e) {
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    protected function getAreaPopulation($range)
    {
        $addressTable = TableRegistry::get('Addresses');
        $population = $addressTable->find();
        $population
            ->select(['count' => $population->func()->count('student_id'), 'city_id', 'Cities.name'])
            ->matching('Cities')
            ->where(['Addresses.type' => '1'])
            ->where(function (QueryExpression $exp, Query $q) use ($range) {
                return $exp->between('city_id', $range['from'], $range['to']);
            })
            ->group('city_id')
            ->order(['count'=>'DESC'])
            ->limit(5)
            ->toArray();
        return $population;
    }
}

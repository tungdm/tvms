<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\Utility\Text;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Jtests Controller
 *
 * @property \App\Model\Table\JtestsTable $Jtests
 *
 * @method \App\Model\Entity\Jtest[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class JtestsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'kì thi';
        $this->loadComponent('SystemEvent');
        $this->loadComponent('ExportFile');
        $this->loadComponent('Util');
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

            // supervisory can set score before admin/full-access user close the test
            if ($action == 'setScore') {
                $target_id = $this->request->getParam('pass');
                if (!empty($target_id)) {
                    $target_id = $target_id[0];
                    $jtestContent = $this->Jtests
                            ->find()
                            ->contain([
                                'JtestContents' => function ($q) use ($target_id, $user) {
                                    return $q->where(['jtest_id' => $target_id, 'user_id' => $user['id']]);
                                }
                            ])->first();
                    $jtest = $this->Jtests->get($target_id);
                    if (empty($jtestContent) || $jtest->status == '5' || $jtest->del_flag == TRUE) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }

            // full-access user can do anything
            // read-only user can read data, export data
            if ($userPermission->action == 0 || ($userPermission->action == 1 && in_array($action, ['index', 'view', 'exportResult']))) {
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
        if (!empty($query)) {
            $allTest = $this->Jtests->find();

            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
            if (isset($query['test_date']) && !empty($query['test_date'])) {
                $test_date = $this->Util->convertDate($query['test_date']);
                $allTest->where(['test_date >=' => $test_date]);
            }
            if (isset($query['lesson_from']) && !empty($query['lesson_from'])) {
                $allTest->where(['lesson_from >=' => $query['lesson_from']]);
            }
            if (isset($query['lesson_to']) && !empty($query['lesson_to'])) {
                $allTest->where(['lesson_to <=' => $query['lesson_to']]);
            }
            if (isset($query['class_id']) && !empty($query['class_id'])) {
                $allTest->where(['jclass_id <=' => $query['class_id']]);
            }
            if (isset($query['status']) && !empty($query['status'])) {
                $now = Time::now()->i18nFormat('yyyy-MM-dd');

                switch ($query['status']) {
                    case "1":
                        $allTest->where(['test_date >' => $now]);
                        break;
                    case "2":
                        $allTest->where(['test_date' => $now]);
                        break;
                    case "3":
                        $allTest->where(['test_date <' => $now]);
                        break;
                    default:
                        $allTest->where(['status' => $query['status']]);
                        break;
                }
            }
            $allTest->order(['Jtests.created' => 'DESC']);
        } else {
            $query['records'] = 10;
            $allTest = $this->Jtests->find()->order(['Jtests.created' => 'DESC']);
        }

        if ($this->Auth->user('role_id') != 1) {
            // other user (not admin) can not view delete record
            $allTest->where(['Jtests.del_flag' => FALSE]);
        }

        $this->paginate = [
            'contain' => ['Jclasses', 'JtestContents', 'JtestContents.Users'],
            'limit' => $query['records']
        ];
        $jtests = $this->paginate($allTest);
        $jclasses = $this->Jtests->Jclasses->find('list');
        $this->set(compact('jtests', 'jclasses', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Jtest id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $jtest = $this->Jtests->get($id, [
            'contain' => [
                'Jclasses', 
                'Students',
                'JtestContents' => ['sort' => ['skill' => 'ASC']], 
                'JtestContents.Users',
                'CreatedByUsers',
                'ModifiedByUsers'
                ]
        ]);
        $this->checkDeleteFlag($jtest, $this->Auth->user());
        $this->set('jtest', $jtest);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $jtest = $this->Jtests->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            // create system event
            $event = $this->SystemEvent->create('THI TIẾNG NHẬT', $data['test_date']);
            $data['events'][0] = $event;
            $jtest = $this->Jtests->patchEntity($jtest, $data, ['associated' => ['JtestContents', 'Students', 'Events']]);
            $jtest = $this->Jtests->setAuthor($jtest, $this->Auth->user('id'), $this->request->getParam('action'));

            if ($this->Jtests->save($jtest)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $jtest->test_date
                ]));

                return $this->redirect(['action' => 'edit', $jtest->id]);
            }
            $this->Flash->error($this->errorMessage['add']);
        }
        $lessons = Configure::read('lessons');
        $jclasses = $this->Jtests->Jclasses->find()
            ->map(function ($row) use ($lessons) {
                $row->name = $row->name . ' (Đang học ' . $lessons[$row->current_lesson] . ')';
                return $row;
            })
            ->combine('id', 'name')
            ->toArray();
        $userTable = TableRegistry::get('Users');
        $teachers = $userTable->find('list')->where(['role_id' => '3']);
        $this->set(compact('jtest', 'jclasses', 'teachers'));
    }

    public function getStudents()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        try {
            if (isset($query['id']) && !empty($query['id'])) {
                // get current lesson
                $currentLesson = TableRegistry::get('Jclasses')->get($query['id'])->current_lesson;
                $stdClassTable = TableRegistry::get('JclassesStudents');
                $allStudents = $stdClassTable->find()
                    ->contain(['Students' => function($q) {
                        return $q->where(['status <' => '4']);
                    }])
                    ->where(['jclass_id' => $query['id']])->select(['student_id'])->toArray();
                $arr1 = [];
                foreach ($allStudents as $key => $value) {
                    array_push($arr1, $value->student_id);
                }

                $stdTestTable = TableRegistry::get('JtestsStudents');

                $stdTest = $stdTestTable->find()->where(['jtest_id' => $query['testId']])->select(['id', 'student_id'])->toArray();

                $arr2 = [];
                $arrId = [];
                foreach ($stdTest as $key => $value) {
                    array_push($arr2, $value->student_id);
                    array_push($arrId, array('id' => $value->id));
                }

                if (!empty(array_diff($arr1, $arr2))) {
                    $resp = [
                        'status' => 'changed',
                        'data' => $allStudents,
                        'currentLesson' => $currentLesson
                    ];
                } else {
                    $resp = [
                        'status' => 'unchanged',
                        'data' => $stdTest,
                        'ids' => $arrId,
                        'currentLesson' => $currentLesson
                    ];
                }
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    /**
     * Edit method
     *
     * @param string|null $id Jtest id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $jtest = $this->Jtests->get($id, [
            'contain' => ['Students', 'JtestContents', 'JtestContents.Users', 'Events']
        ]);
        if ($jtest->del_flag == TRUE || ($jtest->status == '5' && $this->Auth->user('role_id') != 1)) {
            $this->Flash->error($this->errorMessage['unAuthor']);
            return $this->redirect(['controller' => 'Pages', 'action' => 'display']);
        }
        $currentTestDate = $jtest->test_date->i18nFormat('yyyy-MM-dd');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $newTestDate = (new Time($data['test_date']))->i18nFormat('yyyy-MM-dd');
            if ($data['changed'] === "true") {
                // delete old student test data
                $result = $this->Jtests->JtestsStudents->deleteAll(['jtest_id' => $jtest->id]);
            }

            // update system event when test_date changed
            if ($currentTestDate !== $newTestDate) {
                $event = $this->SystemEvent->update($jtest->events[0]->id, $data['test_date']);
                $data['events'][0] = $event;
            }
            
            $jtest = $this->Jtests->patchEntity($jtest, $data);
            $jtest = $this->Jtests->setAuthor($jtest, $this->Auth->user('id'), $this->request->getParam('action'));

            if ($this->Jtests->save($jtest)) {
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity,
                    'name' => $jtest->test_date
                ]));

                return $this->redirect(['action' => 'edit', $jtest->id]);
            }
            $this->Flash->error(Text::insert($this->errorMessage['edit'], [
                'entity' => $this->entity,
                'name' => $currentTestDate
            ]));
        }
        $lessons = Configure::read('lessons');
        $jclasses = $this->Jtests->Jclasses->find()
            ->map(function ($row) use ($lessons) {
                $row->name = $row->name . ' (Đang học ' . $lessons[$row->current_lesson] . ')';
                return $row;
            })
            ->combine('id', 'name')
            ->toArray();
        $userTable = TableRegistry::get('Users');
        $teachers = $userTable->find('list')->where(['role_id' => '3']);
        $this->set(compact('jtest', 'jclasses', 'teachers'));
        $this->render('/Jtests/add');
    }

    public function setScore($id = null)
    {
        $jtest = $this->Jtests->get($id, [
            'contain' => ['Students', 'JtestContents', 'JtestContents.Users']
        ]);
        $currentUserId = $this->Auth->user('id');
        $skill = [];
        // admin or full-access user, get all skill of this test
        foreach ($jtest->jtest_contents as $key => $value) {
            array_push($skill, $value['skill']);
        }
        $permissionsTable = TableRegistry::get('Permissions');
        $userPermission = $permissionsTable->find()->where(['user_id' => $currentUserId, 'scope' => 'Jtests'])->first();
        $teacher = $this->Jtests->JtestContents->find()->where(['jtest_id' => $id, 'user_id' => $currentUserId])->toArray();
        if (!empty($teacher) && (!empty($userPermission) && $userPermission->action != 0)) { 
            // this case is for teacher with permission read-only
            $skill = []; // re-init skill
            foreach ($teacher as $key => $value) {
                array_push($skill, $value['skill']);
            }
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $jtest = $this->Jtests->patchEntity($jtest, $data, [
                'fieldList' => ['students'],
                'associated' => ['Students' => ['fieldList' => ['_joinData']]],
                ]);
            
            // update status
            if (empty($jtest->status)) {
                $jtest->status = '4'; // update scoring status
            }
            $skills = Configure::read('skills'); // load all skill for review
            $jtest = $this->Jtests->setAuthor($jtest, $this->Auth->user('id'), 'edit'); // update modified user
            if ($this->Jtests->save($jtest)) {
                $this->Flash->success($this->successMessage['setScore']);

                return $this->redirect(['action' => 'view', $jtest->id]);
            }
            $this->Flash->error($this->errorMessage['error']);
        }

        $this->set(compact('jtest', 'skill'));
    }

    public function finish($id = null)
    {
        $this->request->allowMethod(['post']);
        $jtest = $this->Jtests->get($id);
        if ($jtest->status !== '4') {
            $this->Flash->error($this->errorMessage['error']);
        } else {
            $jtest->status = '5'; // close test
            if ($this->Jtests->save($jtest)) {
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity, 
                    'name' => $jtest->test_date
                    ]));
            } else {
                $this->Flash->error($this->errorMessage['error']);
            }
        }
        return $this->redirect(['action' => 'index']);
    }
    /**
     * Delete method
     *
     * @param string|null $id Jtest id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $jtest = $this->Jtests->get($id);
        if ($jtest->del_flag) {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $jtest->test_date
                ]));
            return $this->redirect(['action' => 'index']);
        }

        $jtest->del_flag = TRUE;
        $jtest = $this->Jtests->setAuthor($jtest, $this->Auth->user('id'), 'edit'); // update modified user
        if ($this->Jtests->save($jtest)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $jtest->test_date
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $jtest->test_date
                ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $jtest = $this->Jtests->get($id);
        if (!$jtest->del_flag) {
            $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                'entity' => $this->entity,
                'name' => $jtest->test_date
                ]));
            return $this->redirect(['action' => 'index']);
        }

        $jtest->del_flag = FALSE;
        $jtest = $this->Jtests->setAuthor($jtest, $this->Auth->user('id'), 'edit'); // update modified user
        if ($this->Jtests->save($jtest)) {
            $this->Flash->success(Text::insert($this->successMessage['recover'], [
                'entity' => $this->entity, 
                'name' => $jtest->test_date
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                'entity' => $this->entity,
                'name' => $jtest->test_date
                ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function deleteSkill() 
    {
        $this->request->allowMethod('ajax');
        $recordId = $this->request->getData('id');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];

        try {
            $record = $this->Jtests->JtestContents->get($recordId);
            if (!empty($record) && $this->Jtests->JtestContents->delete($record)) {
                $skills = Configure::read('skills');
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => 'Đã xóa phần thi ' . $skills[$record->skill]
                    ]
                ];
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }

        return $this->jsonResponse($resp);
    }

    public function exportResult($id = null)
    {
        // load config
        $reportConfig = Configure::read('reportXlsx');
        $skills = Configure::read('skills');
        $lessons = Configure::read('lessons');
        $score = Configure::read('score');

        // get test data
        $jtest = $this->Jtests->get($id, [
            'contain' => [
                'Jclasses',
                'JtestContents',
                'JtestContents.Users',
                'Students'
            ]
        ]);

        $this->checkDeleteFlag($jtest, $this->Auth->user());
        
        // init worksheet
        $spreadsheet = $this->ExportFile->setXlsxProperties();
        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

        $activeSheet->setShowGridLines(false);
        $activeSheet->getSheetView()->setZoomScale(85);

        $activeSheet->setCellValue('A1', $reportConfig['branch']);
        $activeSheet->getStyle('A1:A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);
        $activeSheet
            ->mergeCells('A5:A6')->setCellValue('A5', 'STT')
            ->mergeCells('B5:B6')->setCellValue('B5', 'Họ tên');
        $activeSheet->getColumnDimension('A')->setWidth(6);
        $activeSheet->getColumnDimension('B')->setWidth(25);

        $col = 'C';
        $testContents = [];
        $teachers = [];

        if (!empty($jtest->jtest_contents)) {
            foreach ($jtest->jtest_contents as $key => $value) {
                array_push($teachers, $value->user->fullname);
                $testContents[$col] = [
                    'skill' => $value->skill,
                    'teacher' => $value->user->fullname,
                    'total' => 0,
                    'count' => 0
                ];
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', $skills[$value->skill]);
                $activeSheet->getColumnDimension($col)->setWidth(25);
                $col++;
            }
        }
        $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Tổng');
        $activeSheet->getColumnDimension($col)->setWidth(25);

        \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
        $activeSheet->getRowDimension('3')->setRowHeight(70);
        $activeSheet->mergeCells('A3:'.$col.'3');
        $activeSheet->setCellValue('A3', Text::insert($reportConfig['testTitle'], [
            'class' => $jtest->jclass->name,
            'testDate' => $jtest->test_date->i18nFormat('dd-MM-yyyy'),
            'testLessons' => mb_strtoupper($lessons[$jtest->lesson_from]) . ' - ' . mb_strtoupper($lessons[$jtest->lesson_to]) 
        ]));
        $activeSheet->getStyle('A3:A3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $listStudents = [];
        $counter = 6;
        foreach ($jtest->students as $key => $student) {
            $counter++;
            $data = [
                $key + 1,
                $student->fullname
            ];
            $joinData = $student->_joinData;
            $total = 0;
            foreach ($testContents as $key => $value) {
                $scoreCode = $score[$value['skill']];
                $testContents[$key]['total'] += $joinData[$scoreCode];
                $testContents[$key]['count']++;
                $total += $joinData[$scoreCode];
                array_push($data, $joinData[$scoreCode]);
            }
            array_push($data, $total);
            array_push($listStudents, $data);
        }

        $avgScore = [];
        $overallTotal = 0;

        foreach ($testContents as $key => $value) {
            $overallTotal += $value['total'];
            array_push($avgScore, round($value['total']/$value['count'], 1));
        }
        array_push($avgScore, round($overallTotal/(count($listStudents)*count($testContents)), 1));

        $counter++;
        $activeSheet->fromArray($listStudents, NULL, 'A7');
        $activeSheet->fromArray($avgScore, NULL, 'C'.$counter);
        $activeSheet->getStyle('C'.$counter.':'.$col.$counter)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $activeSheet->mergeCells('A'.$counter.':B'.$counter)->setCellValue('A'.$counter, 'ĐIỂM TRUNG BÌNH');

        $counter++;
        $activeSheet->mergeCells('A'.$counter.':B'.$counter)->setCellValue('A'.$counter, 'GIÁO VIÊN');
        $activeSheet->fromArray($teachers, NULL, 'C'.$counter);
        $activeSheet->getStyle('C'.$counter.':'.$col.$counter)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $activeSheet->getStyle('A5:'. $col . $counter)->applyFromArray([
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
        $activeSheet->getStyle('A5:'.$col.'6')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
        $activeSheet->getStyle('A7:A'.$counter)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $footer = $counter+1;
        $spreadsheet = $this->ExportFile->generateFooter($spreadsheet, $counter+1, $col);

        $spreadsheet->getActiveSheet()->freezePane('A7');
    
        // export XLSX file for download
        $this->ExportFile->export($spreadsheet, $reportConfig['filename']);
        exit;
    }
}

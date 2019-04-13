<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\I18n\Time;
use Cake\Log\Log;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
/**
 * JlptTests Controller
 *
 * @property \App\Model\Table\JlptTestsTable $JlptTests
 *
 * @method \App\Model\Entity\JlptTest[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class JlptTestsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'kì thi JLPT';
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
                    $jtestContent = $this->JlptTests
                            ->find()
                            ->contain([
                                'JlptContents' => function ($q) use ($target_id, $user) {
                                    return $q->where(['jlpt_test_id' => $target_id, 'user_id' => $user['id']]);
                                }
                            ])->first();
                    $jtest = $this->JlptTests->get($target_id);
                    if (empty($jtestContent) || $jtest->status == '5' || $jtest->del_flag == TRUE) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }

            // full-access user can do anything
            // read-only user can read data, export data
            if ($userPermission->action == 0 || ($userPermission->action == 1 && in_array($action, ['index', 'view', 'getStudents', 'exportReport']))) {
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
            $allTests = $this->JlptTests->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['level']) && !empty($query['level'])) {
                $allTests->where(['level' => $query['level']]);
            }
            if (isset($query['numOfStd']) && $query['numOfStd'] != NULL) {
                $allTests
                    ->select($this->JlptTests)
                    ->select(['student_count' => 'COUNT(Students.id)'])
                    ->leftJoinWith('Students')
                    ->group('JlptTests.id')
                    ->having(['student_count' => $query['numOfStd']]);
            }
            if (isset($query['test_date']) && !empty($query['test_date'])) {
                $test_date = $this->Util->convertDate($query['test_date']);
                $allTests->where(['test_date >=' => $test_date]);
            }
            if (isset($query['status']) && !empty($query['status'])) {
                $now = Time::now()->i18nFormat('yyyy-MM-dd');
                switch ($query['status']) {
                    case "1":
                        $allTests->where(['test_date >' => $now, 'JlptTests.status IS' => NULL]);
                        break;
                    case "2":
                        $allTests->where(['test_date' => $now, 'JlptTests.status IS' => NULL]);
                        break;
                    case "3":
                        $allTests->where(['test_date <' => $now, 'JlptTests.status IS' => NULL]);
                        break;
                    default:
                        $allTests->where(['JlptTests.status' => $query['status']]);
                        break;
                }
            }
            $allTests->order(['JlptTests.created' => 'DESC']);
        } else {
            $query['records'] = $this->defaultDisplay;
            $allTests = $this->JlptTests->find()->order(['JlptTests.created' => 'DESC']);
        }

        if ($this->Auth->user('role_id') != 1) {
            // other user (not admin) can not view delete record
            $allTests->where(['JlptTests.del_flag' => FALSE]);
        }

        $this->paginate = [
            'sortWhitelist' => ['test_date'],
            'contain' => ['Students', 'JlptContents'],
            'limit' => $query['records']
        ];

        $jlptTests = $this->paginate($allTests);

        // get report
        $langAbilityTable = TableRegistry::get('LanguageAbilities');
        $jlptCerts = $langAbilityTable->find()
            ->select([
                'count' => 'COUNT(LanguageAbilities.id)',
                'certificate'
            ])
            ->where(['lang_code' => 1, 'type' => 'internal'])
            ->group('certificate')
            ->toArray();
        
        $certCount = [];
        foreach ($jlptCerts as $key => $value) {
            $certCount[$value->certificate] = $value->count;
        }

        $this->set(compact('jlptTests', 'query', 'certCount'));
    }

    /**
     * View method
     *
     * @param string|null $id Jlpt Test id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $jtest = $this->JlptTests->get($id, [
            'contain' => [
                'Students' => ['sort' => ['result' => 'DESC']],
                'JlptContents' => ['sort' => ['skill' => 'ASC']], 
                'JlptContents.Users',
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
        $jlptTest = $this->JlptTests->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            // create system event
            $event = $this->SystemEvent->create('THI JLPT ' . $data['level'], $data['test_date']);
            $data['events'][0] = $event;
            $jlptTest = $this->JlptTests->patchEntity($jlptTest, $data, ['associated' => ['JlptContents', 'Students', 'Events']]);
            $jlptTest = $this->JlptTests->setAuthor($jlptTest, $this->Auth->user('id'), $this->request->getParam('action'));
            if ($this->JlptTests->save($jlptTest)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $jlptTest->test_date
                ]));
                return $this->redirect(['action' => 'edit', $jlptTest->id]);
            }
            $this->Flash->error($this->errorMessage['add']);
        }
        $userTable = TableRegistry::get('Users');
        $classTable = TableRegistry::get('Jclasses');

        $teachers = $userTable->find('list')->where(['role_id' => '3']);
        $jclasses = $classTable->find('list');
        $this->set(compact('jlptTest', 'teachers', 'jclasses'));
    }

    public function getStudents()
    {   
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $classTable = TableRegistry::get('Jclasses');
        $resp = [];
        try {
            if (isset($query['classId']) && !empty($query['classId'])) {
                $resp = $classTable->get($query['classId'], [
                    'contain' => [
                        'Students' => function($q) {
                            return $q->where(['status <' => '4']);
                        }]
                ]);
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
     * @param string|null $id Jlpt Test id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $jlptTest = $this->JlptTests->get($id, [
            'contain' => ['Students', 'Students.Jclasses', 'JlptContents', 'Events']
        ]);

        $this->checkDeleteFlag($jlptTest, $this->Auth->user());

        $currentTestDate = $jlptTest->test_date->i18nFormat('yyyy-MM-dd');;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            // debug($data);
            $newTestDate = (new Time($data['test_date']))->i18nFormat('yyyy-MM-dd');;

            if ($currentTestDate !== $newTestDate) {
                // update system event
                $event = $this->SystemEvent->update($jlptTest->events[0]->id, $data['test_date']);
                $data['events'][0] = $event;
            }

            $jlptTest = $this->JlptTests->patchEntity($jlptTest, $data);
            $jlptTest = $this->JlptTests->setAuthor($jlptTest, $this->Auth->user('id'), $this->request->getParam('action'));
            // debug($jlptTest);
            if ($this->JlptTests->save($jlptTest)) {
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity,
                    'name' => $jlptTest->test_date
                ]));
                return $this->redirect(['action' => 'edit', $jlptTest->id]);
            }
            $this->Flash->error(Text::insert($this->errorMessage['edit'], [
                'entity' => $this->entity,
                'name' => $currentTestDate
            ]));
        }
        $userTable = TableRegistry::get('Users');
        $classTable = TableRegistry::get('Jclasses');

        $teachers = $userTable->find('list')->where(['role_id' => '3']);
        $jclasses = $classTable->find('list');
        $this->set(compact('jlptTest', 'teachers', 'jclasses'));
        $this->render('/JlptTests/add');
    }

    public function setScore($id = null)
    {
        $jtest = $this->JlptTests->get($id, [
            'contain' => ['Students', 'JlptContents', 'JlptContents.Users']
        ]);
        $currentUserId = $this->Auth->user('id');
        $skill = [];
        // admin or full-access user, get all skill of this test
        foreach ($jtest->jlpt_contents as $key => $value) {
            array_push($skill, $value['skill']);
        }
        $permissionsTable = TableRegistry::get('Permissions');
        $userPermission = $permissionsTable->find()->where(['user_id' => $currentUserId, 'scope' => 'Jtests'])->first();
        $teacher = $this->JlptTests->JlptContents->find()->where(['jlpt_test_id' => $id, 'user_id' => $currentUserId])->toArray();
        if (!empty($teacher) && (!empty($userPermission) && $userPermission->action != 0)) { 
            // this case is for teacher with permission read-only
            $skill = []; // re-init skill
            foreach ($teacher as $key => $value) {
                array_push($skill, $value['skill']);
            }
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            foreach ($data['students'] as $key => $value) {
                $general_score = $this->updatePartialScore($value, $jtest, 'general_score', $key);
                $reading_score = $this->updatePartialScore($value, $jtest, 'reading_score', $key);
                $listening_score = $this->updatePartialScore($value, $jtest, 'listening_score', $key);
                
                // update test result
                $data['students'][$key]['_joinData']['result'] = $this->checkPass($general_score, $reading_score, $listening_score, $jtest->level) ? 'Y' : 'N';
            }
            // debug($data);
            
            $jtest = $this->JlptTests->patchEntity($jtest, $data, [
                'fieldList' => ['students'],
                'associated' => ['Students' => ['fieldList' => ['_joinData']]],
                ]);
            
            // update status
            if (empty($jtest->status)) {
                $jtest->status = '4'; // update scoring status
            }
            $skills = Configure::read('jlpt_skills'); // load all jlpt skill for review
            $jtest = $this->JlptTests->setAuthor($jtest, $this->Auth->user('id'), 'edit'); // update modified user
            if ($this->JlptTests->save($jtest)) {
                $this->Flash->success($this->successMessage['setScore']);
                return $this->redirect(['action' => 'view', $jtest->id]);
            }
            $this->Flash->error($this->errorMessage['error']);
        }

        $this->set(compact('jtest', 'skill'));
    }

    public function updatePartialScore($postData, $jtest, $scoreName, $key)
    {
        if (isset($postData['_joinData'][$scoreName]) && !empty($postData['_joinData'][$scoreName])) {
            return (int) $postData['_joinData'][$scoreName];
        } else {
            return $jtest->students[$key]['_joinData'][$scoreName] ?? 0;
        }
    }

    public function exportReport()
    {
        if ($this->request->is('post')) {
            $condition = $this->request->getData('jlpt');
            // debug($condition);
            
            $allTests = $this->JlptTests->find()->contain([
                'Students',
                'JlptContents' => ['sort' => ['skill' => 'ASC']],
            ]);

            if (!empty($condition['reportfrom']) && empty($condition['reportto'])) {
                // from specific day to now
                $from = $this->Util->reverseStr($condition['reportfrom']) . '-01';
                Log::write('debug', 'from:'.$from);
                $allTests->where(['test_date >=' => $from]);
            } else if (empty($condition['reportfrom']) && !empty($condition['reportto'])) {
                // from begining to specific day
                $reportto = $this->Util->reverseStr($condition['reportto']) . '-01';
                $to = $this->Util->getLastDayOfMonth($reportto);
                Log::write('debug', 'to:'.$to);
                $allTests->where(['test_date <=' => $to]);
            } else if (!empty($condition['reportfrom']) && !empty($condition['reportto'])) {
                // from specific day to specific day
                $from = $this->Util->reverseStr($condition['reportfrom']) . '-01';
                $reportto = $this->Util->reverseStr($condition['reportto']) . '-01';
                $to = $this->Util->getLastDayOfMonth($reportto);
                Log::write('debug', 'from:'.$from.', to:'.$to);
                $allTests->where(function (QueryExpression $exp, Query $q) use($from, $to) {
                    return $exp->between('test_date', $from, $to, 'date');
                });
            } else {
                // select all
                Log::write('debug', 'all');
            }

            if (!empty($condition['level'])) {
                $allTests->where(['level' => $condition['level']]);
            }

            $allTests->where(['JlptTests.del_flag' => FALSE]);

            // load config
            $jlptConfig = Configure::read('jlptReportXlsx');
            $skills = Configure::read('jlpt_skills');
            $levels = Configure::read('jlpt_levels');
            $jlptResult = Configure::read('jlptResult');

            // init worksheet
            $spreadsheet = $this->ExportFile->setXlsxProperties();
            $spreadsheet->setActiveSheetIndex(0);
            $activeSheet = $spreadsheet->getActiveSheet();
            $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

            $activeSheet->setShowGridLines(false);
            $activeSheet->getSheetView()->setZoomScale(85);
            $activeSheet->setCellValue('A1', $jlptConfig['branch']);
            $activeSheet->getStyle('A1:A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ]);

            \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
            $activeSheet
                ->mergeCells('A3:I3')->setCellValue('A3', $jlptConfig['header'])
                ->mergeCells('A5:A6')->setCellValue('A5', 'STT')
                ->mergeCells('B5:B6')->setCellValue('B5', 'Họ tên')
                ->mergeCells('C5:C6')->setCellValue('C5', 'Ngày thi')
                ->mergeCells('D5:D6')->setCellValue('D5', 'Trình độ')
                ->mergeCells('E5:G5')->setCellValue('E5', 'Điểm thành phần')
                ->setCellValue('E6', 'Kiến thức chung')
                ->setCellValue('F6', 'Đọc hiểu')
                ->setCellValue('G6', 'Nghe')
                ->mergeCells('H5:H6')->setCellValue('H5', 'Tổng')
                ->mergeCells('I5:I6')->setCellValue('I5', 'Kết quả');

            $activeSheet->getColumnDimension('A')->setWidth(6);
            $activeSheet->getColumnDimension('B')->setWidth(25);
            $activeSheet->getColumnDimension('C')->setWidth(15);
            $activeSheet->getColumnDimension('D')->setWidth(15);
            $activeSheet->getColumnDimension('E')->setWidth(15);
            $activeSheet->getColumnDimension('F')->setWidth(15);
            $activeSheet->getColumnDimension('G')->setWidth(15);
            $activeSheet->getColumnDimension('H')->setWidth(15);
            $activeSheet->getColumnDimension('I')->setWidth(15);
            $activeSheet->getRowDimension('3')->setRowHeight(85);
            
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
            $offset = 0;
            foreach ($allTests as $key => $jtest) {
                foreach ($jtest->students as $id => $student) {
                    if (empty($condition['result']) || $condition['result'] == $student->_joinData->result) {
                        $offset++;
                        $data = [
                            $offset,
                            $student->fullname,
                            $jtest->test_date->i18nFormat('dd-MM-yyyy'),
                            $jtest->level,
                            $student->_joinData->general_score ?? '',
                            $student->_joinData->reading_score ?? '',
                            $student->_joinData->listening_score ?? '',
                            $student->_joinData->total_score ?? '',
                            $student->_joinData->result ? $jlptResult[$student->_joinData->result] : '',
                        ];
                        array_push($listStudents, $data);
                        $counter++;
                    }
                }
            }
            // debug($listStudents);
            $activeSheet->fromArray($listStudents, NULL, 'A7');

            $activeSheet->getStyle('A5:I6')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);
            $activeSheet->getStyle('A7:A'.$counter)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);
            $activeSheet->getStyle('A5:I'.$counter)->applyFromArray([
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

            $spreadsheet = $this->ExportFile->generateFooter($spreadsheet, $counter+1, 'I');
            $spreadsheet->getActiveSheet()->freezePane('A7');
            // export XLSX file for download
            $this->ExportFile->export($spreadsheet, $jlptConfig['filename']);
            exit;
        }
    }

    public function exportResult($id = null)
    {
        // load config
        $jlptConfig = Configure::read('jlptResultXlsx');
        $skills = Configure::read('jlpt_skills');
        $level = Configure::read('jlpt_levels');
        $jlptResult = Configure::read('jlptResult');

        // get test data
        $jtest = $this->JlptTests->get($id, [
            'contain' => [
                'Students',
                'JlptContents' => ['sort' => ['skill' => 'ASC']],
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
        $activeSheet->setCellValue('A1', $jlptConfig['branch']);
        $activeSheet->getStyle('A1:A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
        $activeSheet
            ->mergeCells('A3:G3')->setCellValue('A3', Text::insert($jlptConfig['header'], [
                'level' => $jtest->level,
                'testDate' => $jtest->test_date->i18nFormat('dd-MM-yyyy'),
            ]))
            ->mergeCells('A5:A6')->setCellValue('A5', 'STT')
            ->mergeCells('B5:B6')->setCellValue('B5', 'Họ tên')
            ->mergeCells('C5:E5')->setCellValue('C5', 'Điểm thành phần')
            ->setCellValue('C6', 'Kiến thức chung')
            ->setCellValue('D6', 'Đọc hiểu')
            ->setCellValue('E6', 'Nghe')
            ->mergeCells('F5:F6')->setCellValue('F5', 'Tổng')
            ->mergeCells('G5:G6')->setCellValue('G5', 'Kết quả');
        $activeSheet->getColumnDimension('A')->setWidth(6);
        $activeSheet->getColumnDimension('B')->setWidth(25);
        $activeSheet->getColumnDimension('C')->setWidth(15);
        $activeSheet->getColumnDimension('D')->setWidth(15);
        $activeSheet->getColumnDimension('E')->setWidth(15);
        $activeSheet->getRowDimension('3')->setRowHeight(85);
        
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
            $data = [
                $key + 1,
                $student->fullname,
                $student->_joinData->general_score ?? '',
                $student->_joinData->reading_score ?? '',
                $student->_joinData->listening_score ?? '',
                $student->_joinData->total_score ?? '',
                $student->_joinData->result ? $jlptResult[$student->_joinData->result] : '',
            ];
            array_push($listStudents, $data);
            $counter++;
        }
        $activeSheet->fromArray($listStudents, NULL, 'A7');

        $activeSheet->getStyle('A5:G6')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
        $activeSheet->getStyle('A7:A'.$counter)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
        $activeSheet->getStyle('A5:G'.$counter)->applyFromArray([
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

        $spreadsheet = $this->ExportFile->generateFooter($spreadsheet, $counter+1, 'G');
        $spreadsheet->getActiveSheet()->freezePane('A7');

        // export XLSX file for download
        $this->ExportFile->export($spreadsheet, Text::insert($jlptConfig['filename'], [
            'level' => $jtest->level,
            'testDate' => $jtest->test_date->i18nFormat('yyyyMMdd'),
        ]));
        exit();
    }

    public function finish($id = null)
    {
        $this->request->allowMethod(['post']);
        $jtest = $this->JlptTests->get($id, ['contain' => 'Students']);

        // check del_flag and jlpt test status
        if (($jtest->del_flag == TRUE && $this->Auth->user('role_id') !== 1) || $jtest->status !== 4) {
            $this->Flash->error($this->errorMessage['unAuthor']);
            return $this->redirect(['controller' => 'Pages', 'action' => 'display']);
        }

        // create new internal certificate for passed students
        $certificates = [];
        foreach ($jtest->students as $key => $student) {
            if ($student->_joinData->result == 'Y') {
                $data = [
                    'student_id' => $student->id,
                    'lang_code' => '1', // Japanese
                    'certificate' => $jtest->level,
                    'type' => 'internal', // internal test
                    'from_date' => Time::now()->i18nFormat('yyyy-MM'),
                    'created_by' => $this->Auth->user('id')
                ];
                array_push($certificates, $data);
            }
        }
        $langTable = TableRegistry::get('LanguageAbilities');
        $entities = $langTable->newEntities($certificates);
        $langTable->getConnection()->transactional(function () use ($langTable, $entities) {
            foreach ($entities as $entity) {
                $langTable->save($entity, ['atomic' => false]);
            }
        });

        $jtest->status = 5; // update status
        $jtest = $this->JlptTests->setAuthor($jtest, $this->Auth->user('id'), 'edit');
        if ($this->JlptTests->save($jtest)) {
            $this->Flash->success(Text::insert($this->successMessage['edit'], [
                'entity' => $this->entity, 
                'name' => $jtest->test_date
                ]));
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Jlpt Test id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $jlptTest = $this->JlptTests->get($id);
        $jlptTest->del_flag = TRUE;
        $jlptTest = $this->JlptTests->setAuthor($jlptTest, $this->Auth->user('id'), 'edit');
        if ($this->JlptTests->save($jlptTest)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $jlptTest->test_date
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $jlptTest->test_date
                ]));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $jtest = $this->JlptTests->get($id);
        $jtest->del_flag = FALSE;
        $jtest = $this->JlptTests->setAuthor($jtest, $this->Auth->user('id'), 'edit'); // update modified user
        if ($this->JlptTests->save($jtest)) {
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

    public function deleteStudent()
    {
        $this->request->allowMethod('ajax');
        $studentId = $this->request->getData('studentId');
        $jlptId = $this->request->getData('jlptId');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];

        try {
            $table = TableRegistry::get('JlptTestsStudents');
            $record = $table->find()->where(['student_id' => $studentId, 'jlpt_test_id' => $jlptId])->contain(['Students'])->first();
            // $resp = ['record' => $record];
            if (!empty($record) && $table->delete($record)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => 'Đã xóa thành công '.$record->student->fullname.' ra khỏi kì thi JLPT'
                    ]
                ];
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }

        return $this->jsonResponse($resp);
    }

    public function checkPass($general_score, $reading_score, $listening_score, $level)
    {
        $total = $general_score + $reading_score + $listening_score;
        switch ($level) {
            case 'N5':
                return $total >= 80 && ($general_score + $reading_score) >= 38 && $listening_score >= 19;
                break;
            case 'N4':
                return $total >= 90 && ($general_score + $reading_score) >= 38 && $listening_score >= 19;
                break;
            case 'N3':
                return $total >= 95 && $general_score >= 19 && $reading_score >= 19 && $listening_score >= 19;
                break;
            case 'N2':
                return $total >= 90 && $general_score >= 19 && $reading_score >= 19 && $listening_score >= 19;
                break;
            case 'N1':
                return $total >= 100 && $general_score >= 19 && $reading_score >= 19 && $listening_score >= 19;
                break;
        }
        return FALSE;
    }
}

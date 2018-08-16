<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\Utility\Text;
use Cake\Core\Configure;
use PhpOffice\PhpSpreadsheet\Style;

/**
 * Jclasses Controller
 *
 * @property \App\Model\Table\JclassesTable $Jclasses
 *
 * @method \App\Model\Entity\Jclass[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class JclassesController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('ExportFile');
        $this->entity = 'lớp';
    }

    public function isAuthorized($user)
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        $session = $this->request->session();
        $permissionsTable = TableRegistry::get('Permissions');
        $userPermission = $permissionsTable->find()->where(['user_id' => $user['id'], 'scope' => $controller])->first();

        if (!empty($userPermission)) {
            if ($userPermission->action == 0 || ($userPermission->action == 1 && in_array($action, ['index', 'view']))) {
                $session->write($controller, $userPermission->action);
                return true;
            }

            // gvcn can access to edit action
            if ($action == 'edit') {
                $target_id = $this->request->getParam('pass');
                if (!empty($target_id)) {
                    $target_id = $target_id[0];
                    if ($this->Jclasses->get($target_id)->user_id == $user['id']) {
                        $session->write($controller, $userPermission->action);
                        return true;
                    }
                }
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
            $allClasses = $this->Jclasses->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
            if (isset($query['name']) && !empty($query['name'])) {
                $allClasses->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%' . $query['name'] . '%');
                });
            }
            if (isset($query['start']) && !empty($query['start'])) {
                $allClasses->where(['start >=' => $query['start']]);
            }
            if (isset($query['num_students']) && $query['num_students'] != NULL) {
                $allClasses
                    ->select($this->Jclasses)
                    ->select($this->Jclasses->Users)
                    ->select(['student_count' => 'COUNT(Students.id)'])
                    ->leftJoinWith('Students')
                    ->group('Jclasses.id')
                    ->having(['student_count' => $query['num_students']]);
            }
            if (isset($query['user_id']) && !empty($query['user_id'])) {
                $allClasses->where(['Users.id' => $query['user_id']]);
            }
            if (isset($query['current_lesson']) && !empty($query['current_lesson'])) {
                $allClasses->where(['current_lesson' => $query['current_lesson']]);
            }
        } else {
            $query['records'] = 10;
            $allClasses = $this->Jclasses->find()->order(['Jclasses.created' => 'DESC']);
        }
        
        $this->paginate = [
            'contain' => ['Users', 'Students'],
            'limit' => $query['records']
        ];
        $jclasses = $this->paginate($allClasses);
        $teachers = $this->Jclasses->Users->find('list')->where(['role_id' => '3']);
        $this->set(compact('jclasses', 'teachers', 'query'));
    }

    public function searchClass()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        if (isset($query['q']) && !empty($query['q'])) {
            $classes = $this->Jclasses
                ->find('list')
                ->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%'.$query['q'].'%');
                });
            $resp['items'] = $classes;
        }
        return $this->jsonResponse($resp);   
    }

    /**
     * View method
     *
     * @param string|null $id Jclass id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $jclass = $this->Jclasses->get($id, [
            'contain' => [
                'Students', 
                'Jtests', 
                'Users',
                'CreatedByUsers',
                'ModifiedByUsers'
                ]
        ]);

        $this->set('jclass', $jclass);
    }

    public function searchStudent()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        if (isset($query['q']) && !empty($query['q'])) {
            $studentTable = TableRegistry::get('Students');
            $students = $studentTable->find()
                ->leftJoinWith('Jclasses')
                ->select(['Jclasses.id', 'Students.id', 'Students.fullname'])
                ->where(['exempt <>' => 'Y'])
                ->andWhere(['Jclasses.id IS' => NULL])
                ->andWhere(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('fullname', '%'.$query['q'].'%');
                });
            $resp['items'] = $students->toArray();
        }
        return $this->jsonResponse($resp);        
    }

    public function getStudent()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        try {
            if (isset($query['id']) && !empty($query['id'])) {
                $resp = $this->Jclasses->Students->get($query['id']);
                $resp['enrolled_date'] = !empty($resp['enrolled_date']) ? $resp['enrolled_date']->i18nFormat('yyyy-MM-dd') : "N/A";
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $jclass = $this->Jclasses->newEntity();
        if ($this->request->is('post')) {
            $jclass = $this->Jclasses->patchEntity($jclass, $this->request->getData(), ['associated' => 'Students']);
            $jclass = $this->Jclasses->setAuthor($jclass, $this->Auth->user('id'), $this->request->getParam('action'));
            if ($this->Jclasses->save($jclass)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $jclass->name
                ]));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error($this->errorMessage['add']);
        }
        $teachers = $this->Jclasses->Users->find('list')->where(['role_id' => '3']);
        $classes = []; // dummy data
        $this->set(compact('jclass', 'teachers', 'classes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Jclass id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $jclass = $this->Jclasses->get($id, [
            'contain' => ['Students', 'Jtests', 'Users']
        ]);
        $className = $jclass->name;
        if ($this->request->is(['patch', 'post', 'put'])) {
            $jclass = $this->Jclasses->patchEntity($jclass, $this->request->getData());
            $jclass = $this->Jclasses->setAuthor($jclass, $this->Auth->user('id'), $this->request->getParam('action'));

            if ($this->Jclasses->save($jclass)) {
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity,
                    'name' => $jclass->name
                ]));

                return $this->redirect(['action' => 'edit', $jclass->id]);
            }
            $this->Flash->error(Text::insert($this->errorMessage['edit'], [
                'entity' => $this->entity,
                'name' => $className
            ]));
        }
        $teachers = $this->Jclasses->Users->find('list')->where(['role_id' => '3']);
        $classes = $this->Jclasses->find('list')->where(['id !=' => $id]);

        $this->set(compact('jclass', 'teachers', 'classes'));
        $this->render('/Jclasses/add');
    }

    /**
     * Delete method
     *
     * @param string|null $id Jclass id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $jclass = $this->Jclasses->get($id);
        if ($this->Jclasses->delete($jclass)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $jclass->name
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $jclass->name
                ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function deleteStudent()
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
            $table = TableRegistry::get('JclassesStudents');
            // $student = $table->get($recordId);
            $record = $table->find()->where(['JclassesStudents.id' => $recordId])->contain(['Students'])->first();

            if (!empty($record) && $table->delete($record)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => Text::insert($this->successMessage['delete'], [
                            'entity' => 'học viên', 
                            'name' => $record->student->fullname
                            ])
                    ]
                ];
            } else {
                $resp = [
                    'status' => 'error',
                    'alert' => [
                        'title' => 'Lỗi',
                        'type' => 'error',
                        'message' => Text::insert($this->errorMessage['delete'], [
                            'entity' => 'học viên', 
                            'name' => $record->student->fullname
                            ])
                    ]
                ];
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }

        return $this->jsonResponse($resp);
    }

    public function changeClass() {
        $this->request->allowMethod('ajax');
        $recordId = $this->request->getData('id');
        $newClassId = $this->request->getData('class'); 

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];

        try {
            $classTable = TableRegistry::get('Jclasses');
            $jclass = $classTable->get($newClassId);

            $table = TableRegistry::get('JclassesStudents');
            $record = $table->find()->where(['JclassesStudents.id' => $recordId])->contain(['Students'])->first();
            $record->jclass_id = $newClassId;

            if ($table->save($record)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => Text::insert($this->successMessage['changeClass'], [
                            'entity' => 'học viên', 
                            'name' => $record->student->fullname,
                            'class' => $jclass->name
                            ])
                    ]
                ];
            } else {
                $resp = [
                    'status' => 'error',
                    'alert' => [
                        'title' => 'Lỗi',
                        'type' => 'error',
                        'message' => Text::insert($this->errorMessage['changeClass'], [
                            'entity' => 'học viên', 
                            'name' => $record->student->fullname,
                            'class' => $jclass->name
                            ])
                    ]
                ];
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }

        return $this->jsonResponse($resp);
    }

    public function getClassTestInfo()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        try {
            if (isset($query['id']) && !empty($query['id'])) {
                $now = Time::now()->i18nFormat('yyyy-MM-dd');
                $testData = $this->Jclasses->Jtests->find()->where([
                    'jclass_id' => $query['id'],
                    'test_date >=' => $now 
                    ])->toArray();
                Log::write('debug', $testData);
                
                if (!empty($testData)) {
                    $resp = [
                        'info' => 'test'
                    ];
                } else {
                    $resp = [
                        'info' => 'not_test'
                    ];
                }
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function exportReport() {
        $allClasses = $this->Jclasses->find()->contain([
            'Users',
            'Students'
        ]);

        // load config
        $reportConfig = Configure::read('reportXlsx');
        $lessons = Configure::read('lessons');

        // init worksheet
        $spreadsheet = $this->ExportFile->setXlsxProperties();
        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(11);
        
        $activeSheet->setShowGridLines(false);
        $activeSheet->setCellValue('A1', $reportConfig['branch']);
        $activeSheet->getStyle('A1:A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        $activeSheet->getRowDimension('3')->setRowHeight(30);
        $activeSheet->mergeCells('A3:F3');
        $activeSheet->setCellValue('A3', $reportConfig['classTitle']);
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

        $activeSheet
            ->mergeCells('A5:A6')->setCellValue('A5', 'STT')
            ->mergeCells('B5:B6')->setCellValue('B5', 'Lớp học')
            ->mergeCells('C5:C6')->setCellValue('C5', 'Ngày bắt đầu')
            ->mergeCells('D5:D6')->setCellValue('D5', 'Sĩ số')
            ->mergeCells('F5:F6')->setCellValue('E5', 'Bài đang học')
            ->mergeCells('E5:E6')->setCellValue('F5', 'Giáo viên chủ nhiệm');

        $activeSheet->getColumnDimension('A')->setWidth(6);
        $activeSheet->getColumnDimension('B')->setWidth(15);
        $activeSheet->getColumnDimension('C')->setWidth(15);
        $activeSheet->getColumnDimension('D')->setWidth(8);
        $activeSheet->getColumnDimension('E')->setWidth(15);
        $activeSheet->getColumnDimension('F')->setWidth(25);

        $listClasses = [];
        $counter = 6;
        foreach ($allClasses as $key => $jclass) {
            $counter++;
            $data = [
                $key+1,
                $jclass->name,
                $jclass->start->i18nFormat('dd/MM/yyyy'),
                $jclass->students ? count($jclass->students) : '0',
                $lessons[$jclass->current_lesson],
                $jclass->user->fullname
            ];
            array_push($listClasses, $data);
        }
        // debug($listClasses);
        $activeSheet->fromArray($listClasses, NULL, 'A7');
        $activeSheet->getStyle('A5:F'.$counter)->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('A5:F'.$counter)->applyFromArray([
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
        $activeSheet->getStyle('A5:F6')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
        $activeSheet->getStyle('A7:A'.$counter)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
        $spreadsheet = $this->ExportFile->generateFooter($spreadsheet, $counter+1, 'F');
        $spreadsheet->getActiveSheet()->freezePane('A7');

        // export XLSX file for download
        $this->ExportFile->export($spreadsheet, $reportConfig['filename']);
        exit;
    }
}

<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\Core\Exception\Exception;
use Cake\I18n\Time;
use Cake\I18n\Number;
use Cake\Utility\Text;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Routing\Router;
use Cake\I18n\I18n;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
/**
 * Students Controller
 *
 * @property \App\Model\Table\StudentsTable $Students
 *
 * @method \App\Model\Entity\Student[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StudentsController extends AppController
{

    public function isAuthorized($user)
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        $session = $this->request->session();

        $permissionsTable = TableRegistry::get('Permissions');
        $userPermission = $permissionsTable->find()->where(['user_id' => $user['id'], 'scope' => $controller])->first();

        if (!empty($userPermission)) {
            if (in_array($action, ['addJob'])) {
                $orderPermission = $permissionsTable->find()->where(['user_id' => $user['id'], 'scope' => 'Orders'])->first();
                if (!empty($orderPermission) && $orderPermission->action == 0) {
                    // current user have full access in order field => they can modified job
                    return true;
                }
            }
            if ($userPermission->action == 0 
            || ($userPermission->action == 1 && (in_array($action, ['index', 'view', 'getStudent', 'getAllHistories']) || strpos($action, 'export') === 0))) {
                $session->write($controller, $userPermission->action);
                return true;
            }
        }
        return parent::isAuthorized($user);
    }

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('ExportFile');
        $this->loadComponent('Util');
        $this->loadModel('Notifications');
        $this->loadModel('NotificationSettings');
        $this->loadModel('Users');
        $this->entity = 'lao động';
        $this->missingFields = '';
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
            $allStudents = $this->Students->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['student_name']) && !empty($query['student_name'])) {
                $allStudents->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('fullname', '%'.$query['student_name'].'%');
                });
            }
            if (isset($query['zalo']) && !empty($query['zalo'])) {
                $allStudents->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('zalo', '%'.$query['zalo'].'%');
                });
            }
            if (isset($query['student_gender']) && !empty($query['student_gender'])) {
                $allStudents->where(['gender' => $query['student_gender']]);
            }
            if (isset($query['presenter']) && !empty($query['presenter'])) {
                $allStudents->where(['presenter_id' => $query['presenter']]);
            }
            if (isset($query['student_status']) && !empty($query['student_status'])) {
                $allStudents->where(['Students.status' => $query['student_status']]);
            }
            if (isset($query['enrolled_date']) && !empty($query['enrolled_date'])) {
                $enrolled_date = $this->Util->convertDate($query['enrolled_date']);
                $query['enrolled_date'] = date('d-m-Y', strtotime($enrolled_date));                
                $allStudents->where(['Students.enrolled_date >=' => $enrolled_date]);
            }
            if (isset($query['birthday']) && !empty($query['birthday'])) {
                $birthday = $this->Util->convertDate($query['birthday']);
                $query['birthday'] = date('d-m-Y', strtotime($birthday));                
                $allStudents->where(['Students.birthday' => $birthday]);
            }
            if (isset($query['hometown']) && !empty($query['hometown'])) {
                $allStudents->matching('Addresses', function($q) use ($query) {
                    return $q->where(['Addresses.city_id' => $query['hometown'], 'Addresses.type' => 1]);
                });
            }
            if (isset($query['interview_deposit']) && !empty($query['interview_deposit'])) {
                $allStudents->matching('InterviewDeposits', function($q) use ($query) {
                    return $q->where(['InterviewDeposits.status' => $query['interview_deposit']]);
                });
            }
            if (isset($query['return_date']) && !empty($query['return_date'])) {
                $allStudents->where(['Students.return_date' => $query['return_date']]);
            }
            if (!isset($query['sort'])) {
                $allStudents->order(['Students.created' => 'DESC']);
            }
        } else {
            $allStudents = $this->Students->find()->order(['Students.created' => 'DESC']);
            $query['records'] = $this->defaultDisplay;
        }
        if (isset($query['hometown']) && !empty($query['hometown'])) {
            $this->paginate = [
                'contain' => [
                    'Presenters',
                    'Addresses',
                    'Addresses.Cities',
                    'InterviewDeposits',
                ],
                'sortWhitelist' => ['fullname', 'enrolled_date', 'birthday'],
                'limit' => $query['records']
            ];
        } else {
            $this->paginate = [
                'contain' => [
                    'Presenters',
                    'Addresses' => function($q) {
                        return $q->where(['Addresses.type' => '1']);
                    },
                    'Addresses.Cities',
                    'InterviewDeposits',
                ],
                'sortWhitelist' => ['fullname', 'enrolled_date', 'birthday'],
                'limit' => $query['records']
            ];
        }
        $students = $this->paginate($allStudents);
        $cities = TableRegistry::get('Cities')->find('list')->cache('cities', 'long');
        $presenters = $this->Students->Presenters->find('list');
        $this->set(compact('students', 'query', 'cities', 'presenters'));
    }

    /**
     * View method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $student = $this->Students->get($id, [
            'contain' => [
                'Addresses' => ['sort' => ['Addresses.type' => 'ASC']],
                'Addresses.Cities',
                'Addresses.Districts',
                'Addresses.Wards',
                'Cards' => ['sort' => ['Cards.type' => 'ASC']], 
                'Families', 
                'Families.Jobs',
                'Educations',
                'Experiences',
                'Experiences.Jobs',
                'GeneralCosts',
                'PhysicalExams' => ['sort' => ['PhysicalExams.exam_date' => 'DESC']],
                'InterviewDeposits',
                'LanguageAbilities',
                'Documents',
                'Presenters',
                'InputTests' => ['sort' => ['InputTests.type' => 'ASC']],
                'IqTests',
                'Orders' => ['sort' => ['Orders.created' => 'DESC']],
                'Orders.Companies',
                'Orders.Guilds',
                'Histories' => function($q) {
                    return $q->where(['type' => 'main'])->order(['Histories.created' => 'DESC']);
                },
                'Histories.UsersCreatedBy',
                'Jtests',
                'Jclasses',
                'Jclasses.Users',
                'CreatedByUsers',
                'ModifiedByUsers'
            ]
        ]);
        $studentName_VN = mb_strtoupper($student->fullname);
        $studentName_EN = $this->Util->convertV2E($studentName_VN);

        $jtestScore = [];
        if (!empty($student->jtests)) {
            foreach ($student->jtests as $key => $value) {
                $testDate = $value->test_date->i18nFormat('yyyy-MM-dd');
                $vocScore = $value->_joinData->vocabulary_score;
                $graScore = $value->_joinData->grammar_score;
                $lisScore = $value->_joinData->listening_score;
                $conScore = $value->_joinData->conversation_score;

                $data = [
                    'date' => $testDate,
                    'vocabulary_score' => $vocScore,
                    'grammar_score' => $graScore,
                    'listening_score' => $lisScore,
                    'conversation_score' => $conScore,
                ];
                array_push($jtestScore, $data);
            }
        }
       
        $jobs = TableRegistry::get('Jobs')->find('list')->toArray();
        $cities = TableRegistry::get('Cities')->find('list')->cache('cities', 'long');
        $characteristics = TableRegistry::get('Characteristics')->find('list')->where(['del_flag' => FALSE])->toArray();
        $strengths = TableRegistry::get('Strengths')->find('list')->where(['del_flag' => FALSE])->toArray();
        $purposes = TableRegistry::get('Purposes')->find('list')->where(['del_flag' => FALSE])->toArray();
        $afterPlans = TableRegistry::get('AfterPlans')->find('list')->where(['del_flag' => FALSE])->toArray();
        $this->set(compact(['student', 'jobs', 'cities', 'studentName_VN', 'studentName_EN', 'jtestScore', 'characteristics', 'strengths', 'purposes', 'afterPlans']));
    }

    public function getStudent()
    {
        $this->request->allowMethod('ajax');
        $candidateId = $this->request->getQuery('id');
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
            $student = $this->Students->get($candidateId, [
                'contain' => [
                    'Addresses' => ['sort' => ['Addresses.type' => 'ASC']],
                    'Addresses.Cities',
                    'CreatedByUsers',
                    'ModifiedByUsers'
                    ]
                ]);
            
            $eduLevel = Configure::read('eduLevel');
            $eduLevel = array_map('array_shift', $eduLevel);
            $gender = Configure::read('gender');
            $yesNoQuestion = Configure::read('yesNoQuestion');

            $resp = [
                'status' => 'success',
                'data' => $student,
                'edu_level' => $eduLevel[$student->educational_level],
                'gender' => $gender[$student->gender],
                'birthday' => $student->birthday ? $student->birthday->i18nFormat('yyyy-MM-dd') : 'N/A',
                'appointment_date' => $student->appointment_date ? $student->appointment_date->i18nFormat('yyyy-MM-dd') : 'N/A',
                'exempt' => $yesNoQuestion[$student->exempt],
                'created' => $student->created->i18nFormat('dd-MM-yyyy HH:mm:ss'),
                'modified' => $student->modified ? $student->modified->i18nFormat('dd-MM-yyyy HH:mm:ss') : 'N/A'
            ];
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
        $this->autoRender = false;
        $student = $this->Students->newEntity();
        if ($this->request->is('post')) {
            $student = $this->Students->patchEntity($student, $this->request->getData(), ['associated' => ['Addresses']]);
            $student = $this->Students->setAuthor($student, $this->Auth->user('id'), $this->request->getParam('action'));

            // Get first key in studentStatus array
            $student->status = key(Configure::read('studentStatus'));

            if ($this->Students->save($student)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $student->fullname
                ]));
            } else {
                Log::write('debug', $student->errors());
                $this->Flash->error($this->errorMessage['add']);
            }
            return $this->redirect(['action' => 'index']);
            
        } else {
            throw new NotFoundException(__('Page not found'));
        }
    }

    public function addJob()
    {
        $this->request->allowMethod('ajax');
        $jobTable = TableRegistry::get('Jobs');
        $job = $jobTable->newEntity();
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
            $data = $this->request->getData();
            $checkExists = $jobTable->find()->where(['job_name' => $data['job_name']])->first();
            if (!empty($checkExists)) {
                $resp = [
                    'status' => 'error',
                    'flash' => [
                        'title' => 'Lỗi',
                        'type' => 'error',
                        'icon' => 'fa fa-warning',
                        'message' => Text::insert($this->errorMessage['addJob'], [
                            'name' => $data['job_name']
                            ])
                    ]
                ];
            } else {
                $job = $jobTable->patchEntity($job, $data);
                $job = $jobTable->setAuthor($job, $this->Auth->user('id'), 'add');
                if ($jobTable->save($job)) {
                    $resp = [
                        'status' => 'success',
                        'newJobId' => $job->id,
                        'flash' => [
                            'title' => 'Thành Công',
                            'type' => 'success',
                            'icon' => 'fa fa-check',
                            'message' => Text::insert($this->successMessage['add'], [
                                'entity' => 'nghề', 
                                'name' => $job->job_name
                                ])
                        ]
                    ];
                }
            }
        } catch (Exception $e) {
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function getHistory()
    {
        $this->request->allowMethod('ajax');
        $id = $this->request->getQuery('id');
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
            $history = $this->Students->Histories->get($id);
            $resp = [
                'status' => 'success',
                'history' => $history
            ];
        } catch (Exception $e) {
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function getAllHistories()
    {
        $this->request->allowMethod('ajax');
        $studentId = $this->request->getQuery('id');
        $type = $this->request->getQuery('type');
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
            $histories = $this->Students->Histories->find()
                ->contain(['UsersCreatedBy'])
                ->where(['student_id' => $studentId, 'type' => $type])
                ->order(['Histories.created' => 'DESC']);
            $histories->formatResults(function ($results) {
                return $results->map(function ($row) {
                    $row['controller'] = 'students';
                    $row['created'] = $row['created']->i18nFormat('dd-MM-yyyy HH:mm:ss');
                    $row['owner'] = $row['created_by'] == $this->Auth->user('id') ? true : false;
                    return $row;
                });
            });
            $student = $this->Students->get($studentId);
            $resp = [
                'status' => 'success',
                'histories' => $histories,
                'now' => Time::now()->i18nFormat('dd-MM-yyyy HH:mm:ss'),
                'student_created' => $student->created->i18nFormat('dd/MM/yyyy')
            ];
        } catch (Exception $e) {
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function addHistory()
    {
        $this->request->allowMethod('ajax');
        $data = $this->request->getData();
        $history = $this->Students->Histories->newEntity();
        $history = $this->Students->Histories->patchEntity($history, $data);
        $history = $this->Students->Histories->setAuthor($history, $this->Auth->user('id'), 'add');

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
            $student = $this->Students->get($data['student_id']);
            if ($this->Students->Histories->save($history)) {
                $history = $this->Students->Histories->get($history->id, ['contain' => ['UsersCreatedBy', 'UsersModifiedBy']]);
                $history->created = $history->created->i18nFormat('dd-MM-yyyy HH:mm:ss');
                $now = Time::now()->i18nFormat('dd-MM-yyyy HH:mm:ss');
                $resp = [
                    'status' => 'success',
                    'history' => $history,
                    'now' => $now,
                    'flash' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'icon' => 'fa fa-check',
                        'message' => Text::insert($this->successMessage['edit'], [
                            'entity' => $this->entity, 
                            'name' => $student->fullname
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

    public function editHistory($id = null)
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
            $history = $this->Students->Histories->find()->where([
                'created_by' => $this->Auth->user('id'), 
                'id' => $id
                ])->first();
            if (!empty($history)) {
                $data = $this->request->getData();
                $student = $this->Students->get($data['student_id']);

                $history = $this->Students->Histories->patchEntity($history, $data);
                $history = $this->Students->Histories->setAuthor($history, $this->Auth->user('id'), 'edit');

                if ($this->Students->Histories->save($history)) {
                    $resp = [
                        'status' => 'success',
                        'flash' => [
                            'title' => 'Thành Công',
                            'type' => 'success',
                            'icon' => 'fa fa-check',
                            'message' => Text::insert($this->successMessage['edit'], [
                                'entity' => $this->entity, 
                                'name' => $student->fullname
                                ])
                        ]
                    ];
                }
            }
        } catch (Exception $e) {
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function deleteHistory()
    {
        $this->request->allowMethod('ajax');
        $id = $this->request->getData('id');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lối',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        
        try {
            $history = $this->Students->Histories->find()->contain(['Students'])->where(['Histories.id' => $id, 'Histories.created_by' => $this->Auth->user('id')])->first();
            if (!empty($history) && $this->Students->Histories->delete($history)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => Text::insert($this->successMessage['edit'], [
                            'entity' => $this->entity, 
                            'name' => $history->student->fullname
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

    public function edit($id = null)
    {
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
            $student = $this->Students->get($id, [
                'contain' => [
                    'Addresses' => ['sort' => ['Addresses.type' => 'ASC']],
                    ]
                ]);
            $data = $this->request->getData();
            $student = $this->Students->patchEntity($student, $data, ['associated' => ['Addresses']]);
            $student = $this->Students->setAuthor($student, $this->Auth->user('id'), $this->request->getParam('action'));
            
            if ($this->Students->save($student)) {
                $resp = [
                    'status' => 'success',
                    'redirect' => Router::url(['action' => 'index']),
                ];
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity, 
                    'name' => $student->fullname
                    ]));
            }


        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function info($id = null)
    {
        $prevImage = NULL;
        $cmndImage1 = NULL;
        $cmndImage2 = NULL;
        $ppImage1 = NULL;
        $btnImage1 = NULL;
        $btnImage2 = NULL;
        $prevEnrolledDate = NULL;
        if (!empty($id)) {
            $student = $this->Students->get($id, [
                'contain' => [
                    'Addresses' => ['sort' => ['Addresses.type' => 'ASC']],
                    'Cards' => ['sort' => ['Cards.type' => 'ASC']], 
                    'Families', 
                    'Families.Jobs',
                    'Educations',
                    'Experiences',
                    'Experiences.Jobs',
                    'LanguageAbilities',
                    'Documents',
                    'InputTests',
                    'GeneralCosts',
                    'IqTests',
                    'PhysicalExams' => ['sort' => ['PhysicalExams.exam_date' => 'DESC']],
                    'InterviewDeposits',
                    'Histories' => function($q) {
                        return $q->where(['type' => 'main'])->order(['Histories.created' => 'DESC']);
                    },
                    'Histories.UsersCreatedBy',
                    ]
                ]);
            $action = 'edit';
            $prevImage = $student->image;
            $prevEnrolledDate = $student->enrolled_date;
            if (!empty($student->cards[0]->image1)) {
                $cmndImage1 = $student->cards[0]->image1;
            }
            if (!empty($student->cards[0]->image2)) {
                $cmndImage2 = $student->cards[0]->image2;
            }
            if (!empty($student->cards[1]->image1)) {
                $ppImage1 = $student->cards[1]->image1;
            }
            if (!empty($student->cards[3]->image1)) {
                $btnImage1 = $student->cards[3]->image1;
            }
            if (!empty($student->cards[3]->image1)) {
                $btnImage2 = $student->cards[3]->image2;
            }
        } else {
            $student = $this->Students->newEntity();
            $query = $this->request->getQuery();
            if (isset($query['candidateId']) && !empty($query['candidateId'])) {
                $candidate = $this->Students->Candidates->get($query['candidateId']);
                if (!$candidate->del_flag) {
                    $student->candidate_id = $candidate->id;
                    $student->fullname = $candidate->fullname;
                    $student->gender = $candidate->gender;
                    $student->phone = $candidate->phone;
                    $student->zalo = !empty($candidate->zalo_phone) ? $candidate->zalo_phone : $candidate->fb_name;
                    $student->birthday = $candidate->birthday;
                    $student->educational_level = $candidate->educational_level;
                    $student->presenter_id = 2; // Internet
                    $student->addresses = [
                        0 => [
                            'city_id' => $candidate->city_id,
                            'district_id' => NULL,
                        ]
                    ];
                }
            }
            
            $action = 'add';
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $data['lived_from'] = $this->Util->reverseStr($data['lived_from']);
            $data['lived_to'] = $this->Util->reverseStr($data['lived_to']);
            $student = $this->Students->patchEntity($student, $data, ['associated' => [
                'Addresses', 
                'Families', 
                'PhysicalExams',
                'InterviewDeposits',
                'GeneralCosts',
                'Cards', 
                'Educations',
                'Experiences',
                'LanguageAbilities',
                'Documents',
                'InputTests',
                'IqTests'
                ]]);
            
            $file_dir = WWW_ROOT . 'img' . DS . 'students';
            // save avatar image
            $student->image = $this->saveImage($data['student_avatar'], $file_dir, $prevImage);
            // save cmnd front image
            $student->cards[0]->image1 = $this->saveImage($data['cmnd_cropped_result_front'], $file_dir, $cmndImage1);
            // save cmnd back image
            $student->cards[0]->image2 = $this->saveImage($data['cmnd_cropped_result_back'], $file_dir, $cmndImage2);
            // save passport image
            $student->cards[1]->image1 = $this->saveImage($data['pp_cropped_result'], $file_dir, $ppImage1);
            // save btn images
            $student->cards[3]->image1 = $this->saveImage($data['btn_cropped_result_1'], $file_dir, $btnImage1);
            $student->cards[3]->image2 = $this->saveImage($data['btn_cropped_result_2'], $file_dir, $btnImage2);

            // setting expectation
            $expectJobs = $data['expectationJobs'];
            $expectStr = $this->convertTags($expectJobs);

            $student->expectation = $expectStr;
            // setting genitive
            $student->genitive = $this->convertTags($data['genitiveArr']);
            // setting strength
            $student->strength = $this->convertTags($data['strengthArr']);
            // setting purpose
            $student->purpose = $this->convertTags($data['purposeArr']);
            // setting after plan
            $student->after_plan = $this->convertTags($data['afterPlanArr']);

            $student = $this->Students->setAuthor($student, $this->Auth->user('id'), $action);
            try{
                // save to db
                if (isset($candidate) && !empty($candidate) && !$candidate->del_flag) {
                    $candidate->status = 4; // da ki ket
                    $this->Students->Candidates->save($candidate);
                }
                if ($this->Students->save($student)) {
                    if ($action == "add") {
                        $this->Flash->success(Text::insert($this->successMessage['add'], [
                            'entity' => $this->entity,
                            'name' => $student->fullname
                        ]));
                    } elseif ($action == "edit") {
                        $this->Flash->success(Text::insert($this->successMessage['edit'], [
                            'entity' => $this->entity,
                            'name' => $student->fullname
                        ]));
                    }
                    # create notification for enrolled date
                    if ($prevEnrolledDate != $student->enrolled_date) {
                        $setting = $this->NotificationSettings->get(4);
                        $receiversArr = explode(',', $setting->receivers_groups);
                        array_shift($receiversArr);
                        array_pop($receiversArr);
                        $now = Time::now()->addDays($setting->send_before)->i18nFormat('yyyy-MM-dd');
                        if ($student->enrolled_date->i18nFormat('yyyy-MM-dd') == $now) {
                            $data = [];
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
                            $entities = $this->Notifications->newEntities($data);
                            // save to db
                            $this->Notifications->saveMany($entities);
                        }
                    }
                    return $this->redirect(['action' => 'info', $student->id]);
                } else {
                    Log::write('debug', $student->errors());
                }
            } catch (Exception $e) {
                Log::write('debug', $e);
            }
            if ($action == "add") {
                $this->Flash->error($this->errorMessage['add']);
            } elseif ($action == "edit") {
                $this->Flash->error(Text::insert($this->errorMessage['edit'], [
                    'entity' => $this->entity,
                    'name' => $student->fullname
                ]));
            }
        }
        
        $presenters = TableRegistry::get('Presenters')->find('list');
        $jobs = TableRegistry::get('Jobs')->find('list')->where(['del_flag' => FALSE]);

        $cities = TableRegistry::get('Cities')->find('list');
        $districts = [];
        $wards = [];
        if (!empty($student->addresses)) {
            foreach ($student->addresses as $key => $value) {
                $districts[$key] = TableRegistry::get('Districts')->find('list')->where(['city_id' => $value['city_id']])->toArray();
                $wards[$key] = TableRegistry::get('Wards')->find('list')->where(['district_id' => $value['district_id']])->toArray();
            }
        }

        $characteristics = TableRegistry::get('Characteristics')->find('list')->where(['del_flag' => FALSE]);
        $strengths = TableRegistry::get('Strengths')->find('list')->where(['del_flag' => FALSE]);
        $purposes = TableRegistry::get('Purposes')->find('list')->where(['del_flag' => FALSE]);
        $afterPlans = TableRegistry::get('AfterPlans')->find('list')->where(['del_flag' => FALSE]);
        $this->set(compact(['student', 'presenters', 'jobs', 'cities', 'districts', 'wards', 'action', 'characteristics', 'strengths', 'purposes', 'afterPlans']));
    }

    public function getDistrict()
    {
        if ($this->request->is('ajax')) {
            $query = $this->request->getQuery();
            $resp = [];
            if (isset($query['city']) && !empty($query['city'])) {
                $districts = TableRegistry::get('Districts')->find('list')->where(['city_id' => $query['city']])->toArray();
                if (!empty($districts)) {
                    $resp = $districts;
                }
            }
            return $this->jsonResponse($resp);
        }
    }

    public function getWard()
    {
        if ($this->request->is('ajax')) {
            $query = $this->request->getQuery();
            $resp = [];
            if (isset($query['district']) && !empty($query['district'])) {
                $wards = TableRegistry::get('Wards')->find('list')->where(['district_id' => $query['district']])->toArray();
                if (!empty($wards)) {
                    $resp = $wards;
                }
            }
            return $this->jsonResponse($resp);
        }
    }

    public function checkDuplicate()
    {
        $this->request->allowMethod('ajax');
        $stdName = $this->request->getQuery('q');
        $query = $this->Students->find()->where(['fullname' => $stdName])->first();
        $resp = false;

        if (!empty($query)) {
            $resp = true;
        }     
        return $this->jsonResponse($resp);
    }

    /**
     * Delete method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $student = $this->Students->get($id);
        $studentName = $student->fullname;
        if ($this->Students->delete($student)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $studentName
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $studentName
            ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function deleteFamilyMember() 
    {
        $this->request->allowMethod('ajax');
        $memberId = $this->request->getData('id');
        $families = TableRegistry::get('Families');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        
        try {
            $member = $families->get($memberId);
            $student = $this->Students->get($member->student_id);
            $student = $this->Students->setAuthor($student, $this->Auth->user('id'), 'edit');

            if (!empty($member) && $families->delete($member) && $this->Students->save($student)) {
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

    public function deleteEducations()
    {
        $this->request->allowMethod('ajax');
        $eduId = $this->request->getData('id');
        $educations = TableRegistry::get('Educations');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        
        try {
            $eduHis = $educations->get($eduId);
            $student = $this->Students->get($eduHis->student_id);
            $student = $this->Students->setAuthor($student, $this->Auth->user('id'), 'edit');
            if (!empty($eduHis) && $educations->delete($eduHis) && $this->Students->save($student)) {
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

    public function deletePhysicalCalendar()
    {
        $this->request->allowMethod('ajax');
        $physcalendarId = $this->request->getData('calendarId');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        try {
            $physCal = $this->Students->PhysicalExams->get($physcalendarId);
            $student = $this->Students->get($physCal->student_id);

            $student = $this->Students->setAuthor($student, $this->Auth->user('id'), 'edit');

            if (!empty($physCal) &&  $this->Students->PhysicalExams->delete($physCal) && $this->Students->save($student)) {
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

    public function deleteExperience()
    {
        $this->request->allowMethod('ajax');
        $expId = $this->request->getData('id');
        $experiences = TableRegistry::get('Experiences');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        
        try {
            $exp = $experiences->get($expId);
            $student = $this->Students->get($exp->student_id);
            $student = $this->Students->setAuthor($student, $this->Auth->user('id'), 'edit');
            if (!empty($exp) && $experiences->delete($exp) && $this->Students->save($student)) {
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

    public function deleteLang()
    {
        $this->request->allowMethod('ajax');
        $langId = $this->request->getData('id');
        $langAblTbl = TableRegistry::get('LanguageAbilities');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        
        try {
            $langAbl = $langAblTbl->get($langId);
            $student = $this->Students->get($langAbl->student_id);
            $student = $this->Students->setAuthor($student, $this->Auth->user('id'), 'edit');
            if (!empty($langAbl) && $langAblTbl->delete($langAbl) && $this->Students->save($student)) {
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

    public function exportResume($id = null)
    {
        $orderId = $this->request->getQuery('order');
        // Load config
        $resumeConfig = Configure::read('resume');
        $country = Configure::read('country');
        $schoolTemplate = Configure::read('schoolTemplate');
        $eduLevel = Configure::read('eduLevel');
        $folderImgTemplate = Configure::read('folderImgTemplate');
        $language = Configure::read('language');
        $vnDateFormatFull = Configure::read('vnDateFormatFull');

        try {
            $student = $this->Students->get($id, [
                'contain' => [
                    'Addresses' => function($q) {
                        return $q->where(['Addresses.type' => '1']);
                    },
                    'Addresses.Cities',
                    'Addresses.Districts',
                    'Addresses.Wards',
                    'Educations',
                    'Experiences',
                    'Experiences.Jobs',
                    'LanguageAbilities' => function($q) {
                        return $q->where(['LanguageAbilities.type' => 'external']);
                    }
                ]
            ]);
            $order = $this->Students->Orders->get($orderId, ['contain' => 'Jobs']);
            $this->checkData($order->application_date, 'Ngày làm hồ sơ');

            $template = WWW_ROOT . 'document' . DS . $resumeConfig['template'];
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
            
            // Prepare data
            $now = Time::now();
            $studentName_VN = mb_strtoupper($student->fullname);
            $studentName_EN = $this->Util->convertV2E($studentName_VN);
            $studentName = explode(' ', $studentName_EN);
            $studentFirstName = array_pop($studentName);
            $output_file_name = Text::insert($resumeConfig['filename'], [
                'firstName' => $studentFirstName, 
                ]);
    
            $nation_VN = $country[$student->country]['vn'];
            $nation_JP = $country[$student->country]['jp'];
            
            $male = $female = $folderImgTemplate . DS . 'circle.png';
            if ($student->gender == 'M') {
                $female = $folderImgTemplate . DS . 'blank.png';
            } else {
                $male = $folderImgTemplate . DS . 'blank.png';
            }
    
            $marital_y = $marital_n = $folderImgTemplate . DS . 'circle.png';
            if ($student->marital_status == '2') {
                $marital_n = $folderImgTemplate . DS . 'blank.png';
            } else {
                $marital_y = $folderImgTemplate . DS . 'blank.png';
            }
    
            $mergedAdd = $this->mergeAddress($student->addresses[0]);
    
            $jplevel_JP = $jplevel_VN = $enlevel_JP = $enlevel_VN = "            ";
            if (!empty($student->language_abilities)) {
                foreach ($student->language_abilities as $key => $value) {
                    switch ($value->lang_code) {
                        case '1':
                            // japan
                            $jplevel_JP = $value->certificate . '相当';
                            $jplevel_VN = 'Tương đương ' . $value->certificate;
                            break;
                        case '2':
                            // english
                            $enlevel_JP = $value->certificate . '相当';
                            $enlevel_VN = 'Tương đương ' . $value->certificate;
                            break;
                    }
                }
            } else {
                $jplevel_JP = $resumeConfig['defaultLevel'] . '相当';
                $jplevel_VN = 'Tương đương ' . $resumeConfig['defaultLevel'];
            }
            $jplang = $enlang = $folderImgTemplate . DS . 'circle.png';
            if (empty(str_replace(" ", "", $jplevel_JP))) {
                $jplang = $folderImgTemplate . DS . 'blank.png';
            }
            if (empty(str_replace(" ", "", $enlevel_JP))) {
                $enlang = $folderImgTemplate . DS . 'blank.png';
            }
            $createdDayJP = '';
            $createdDayVN = '';

            if (!empty($order->application_date)) {
                $createdDayJP = $order->application_date->i18nFormat('yyyy年M月d日');
                $createdDayVN = ucfirst(Text::insert($vnDateFormatFull, [
                    'day' => str_pad($order->application_date->day, 2, '0', STR_PAD_LEFT), 
                    'month' => str_pad($order->application_date->month, 2, '0', STR_PAD_LEFT), 
                    'year' => $order->application_date->year, 
                    ]));
            }
            $this->tbs->VarRef['created_jp'] = $createdDayJP;
            $this->tbs->VarRef['created_vn'] = $createdDayVN;

            $this->tbs->VarRef['studentname_en'] = $studentName_EN;
            $this->tbs->VarRef['studentname_vn'] = $studentName_VN;
    
            $this->tbs->VarRef['nation_jp'] = $nation_JP;
            $this->tbs->VarRef['nation_vn'] = $nation_VN;
            
            $this->tbs->VarRef['male'] = $male;
            $this->tbs->VarRef['female'] = $female;
    
            $this->tbs->VarRef['maritalyes'] = $marital_y;  
            $this->tbs->VarRef['maritalno'] = $marital_n;
    
            $this->tbs->VarRef['jplevel_jp'] = $jplevel_JP;
            $this->tbs->VarRef['jplevel_vn'] = $jplevel_VN;
            $this->tbs->VarRef['enlevel_jp'] = $enlevel_JP;
            $this->tbs->VarRef['enlevel_vn'] = $enlevel_VN;
            $this->tbs->VarRef['jplang'] = $jplang;
            $this->tbs->VarRef['enlang'] = $enlang;
                
            $this->tbs->VarRef['bd_y'] = $student->birthday->year;
            $this->tbs->VarRef['bd_m'] = $student->birthday->month;
            $this->tbs->VarRef['bd_d'] = $student->birthday->day;
            $this->tbs->VarRef['bd_vn'] = ucfirst(Text::insert($vnDateFormatFull, [
                'day' => str_pad($student->birthday->day, 2, '0', STR_PAD_LEFT), 
                'month' => str_pad($student->birthday->month, 2, '0', STR_PAD_LEFT), 
                'year' => $student->birthday->year,
                ]));
    
            $this->tbs->VarRef['age'] = $order->application_date ? ($order->application_date->diff($student->birthday))->y : '';
    
            $this->tbs->VarRef['currentaddress_en'] = $mergedAdd['en'];
            $this->tbs->VarRef['currentaddress_vn'] = $mergedAdd['vn'];

            $this->tbs->VarRef['job_jp'] = $order->job->job_name_jp;
            $this->tbs->VarRef['job_vn'] = $order->job->job_name;
    
            $livedJapan_y = $livedJapan_n = $folderImgTemplate . DS . 'circle.png';
            if ($student->is_lived_in_japan === 'Y') {
                $livedJapan_n = $folderImgTemplate . DS . 'blank.png';
                $this->tbs->VarRef['lived_from'] = '     ' . $student->lived_from . '     ';
                $this->tbs->VarRef['lived_to'] = '     ' . $student->lived_to . '     ';
            } else {
                $livedJapan_y = $folderImgTemplate . DS . 'blank.png';
                $this->tbs->VarRef['lived_from'] = "                ";
                $this->tbs->VarRef['lived_to'] = "                ";
            }
    
            $this->tbs->VarRef['livedjapanyes'] = $livedJapan_y;
            $this->tbs->VarRef['livedjapanno'] = $livedJapan_n;
    
            $reject_y = $reject_n = $folderImgTemplate . DS . 'circle.png';
            if ($student->reject_stay === 'N') {
                $reject_y = $folderImgTemplate . DS . 'blank.png';
            } else {
                $reject_n = $folderImgTemplate . DS . 'blank.png';
            }
    
            $this->tbs->VarRef['reject_y'] = $reject_y;
            $this->tbs->VarRef['reject_n'] = $reject_n;
            
            $eduHis = [];
            if (empty($student->educations)) {
                $history = [
                    'title' => 'Quá trình học tập',
                    'time' => "",
                    'school' => ""
                ];
                $this->checkData('', 'Quá trình học tập');
                array_push($eduHis, $history);
            } else {
                foreach ($student->educations as $key => $value) {
                    $fromDate = new Time($value->from_date);
                    $toDate = new Time($value->to_date);
                    $history = [
                        'title' => 'Quá trình học tập',
                        'time' => $fromDate->year . '/' . $fromDate->month . ' ～ ' . $toDate->year . '/' . $toDate->month, 
                        'school' => Text::insert($schoolTemplate, [
                            'schoolNameEN' => $this->Util->convertV2E($value->school),
                            'eduLevelJP' => $eduLevel[$value->degree]['jp'],
                            'schoolNameVN' => $value->school,
                            'eduLevelVN' => $eduLevel[$value->degree]['vn']
                        ])
                    ];
                    array_push($eduHis, $history);
                }
            }            
            $this->tbs->MergeBlock('a', $eduHis);
            
            $expHis = [];
            if (empty($student->experiences)) {
                $history = [
                    'title' => "Quá trình công tác",
                    'time' => "",
                    'company' => ""
                ];
                $this->checkData('', 'Kinh nghiệm làm việc');            
                array_push($expHis, $history);
            }
            foreach ($student->experiences as $key => $value) {
                $companyStr = '';
                if (!empty($value->company_jp)) {
                    $companyStr = $value->company_jp . "\n";
                } elseif (!empty($value->company)) {
                    $companyStr = $value->company . "\n";
                }
                $companyStr .= !empty($value->job->job_name_jp) ? "（" . $value->job->job_name_jp . "）\n" : "";
    
                if (!empty($value->company_jp) && !empty($value->company)) {
                    $companyStr .= $value->company . "\n";
                }
                $companyStr .= !empty($value->job->job_name) ? "(" . $value->job->job_name . ")" : "";
                $fromDate = new Time($value->from_date);
                $toDate = new Time($value->to_date);
                $history = [
                    'title' => 'Quá trình công tác',
                    'time' => $fromDate->year . '/' . $fromDate->month . ' ～ ' . $toDate->year . '/' . $toDate->month,
                    'company' => $companyStr
            ];
                array_push($expHis, $history);
            }
            $this->tbs->MergeBlock('b', $expHis);
    
            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['controller' => 'Orders', 'action' => 'index']);
            }
    
            $this->tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['controller' => 'Orders', 'action' => 'index']);
        }
    }

    public function exportContract($id = null)
    {
        // load config
        $contractConfig = Configure::read('contract');
        $vnDateFormatFull = Configure::read('vnDateFormatFull');
        $vnDateFormatShort = Configure::read('vnDateFormatShort');
        // load template
        $lang = $this->request->getQuery('lang');
        $orderId = $this->request->getQuery('order');
        $filenameLang = 'filename_' . $lang;
        try {
            $template = WWW_ROOT . 'document' . DS . 'contract_'. $lang .'.docx';
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            $student = $this->Students->get($id, [
                'contain' => [
                    'Cards' => function($q) {
                        return $q->where(['Cards.type' => '1']);
                    },
                    'Orders',
                    'Orders.Jobs',
                    'Orders.Companies',
                    'Orders.Guilds',
                    'Addresses' => function($q) {
                        return $q->where(['Addresses.type' => '1']);
                    },
                    'Addresses.Cities',
                    'Addresses.Districts',
                    'Addresses.Wards',
                ]
            ]);

            $order = $this->Students->Orders->get($orderId, [
                'contain' => [
                    'Jobs',
                    'Companies',
                    'Guilds',
                    'Guilds.AdminCompanies',
                    'AdminCompanies'
                ]
            ]);
            $adminCompany = $order->admin_company;               
            $now = Time::now();
            $birthday = $student->birthday;
            $cmnd = $this->checkData($student->cards[0], 'Giấy chứng minh nhân dân');

            $mergeAddress = $this->mergeAddress($student->addresses[0]);

            $job = $order->job;

            $guild = $order->guild;

            $company = $order->company;
            
            $subsidy = '';
            $signingDate = '';
            if (!empty($guild->admin_companies)) {
                foreach ($guild->admin_companies as $value) {
                    if ($value->id == $adminCompany->id) {
                        $subsidy = $value->_joinData->subsidy;
                        $signingDate = $value->_joinData->signing_date;
                    }
                }
            }
            $subsidy = $subsidy ? Number::format($subsidy, ['locale' => 'ja_JP']) : '';
            $studentName_VN = mb_strtoupper($student->fullname);
            $studentName_EN = $this->Util->convertV2E($studentName_VN);
            $studentNameArr = explode(' ', $studentName_EN);
            $studentFirstName = array_pop($studentNameArr);
            $output_file_name = Text::insert($contractConfig[$filenameLang], [
                'firstName' => $studentFirstName, 
                ]);
            $this->checkData($subsidy, 'Tiền trợ cấp thực tập sinh của nghiệp đoàn');
            $this->checkData($order->application_date, 'Ngày làm hồ sơ');

            $cmnd_from_date = '';
            $licenseAt = $adminCompany->license_at;
            if ($lang == 'jp') {
                $adminCompanySignerName = $this->Util->convertV2E($adminCompany->signer_name);
                $studentName = $studentName_EN;
                $createdDay = $order->application_date ? $order->application_date->i18nFormat('yyyy年M月d日') : '';
                $birthday = $birthday->i18nFormat('yyyy年M月d日');
                $licenseAt = $licenseAt->i18nFormat('yyyy年M月d日');
                $adminCompanyFullName = $adminCompany->name_en;
                $adminCompanySignerRole = $adminCompany->signer_role_jp;
                $adminCompanyAddress = $adminCompany->address_en;
                if (!empty($cmnd) && !empty($cmnd->from_date)) {
                    $cmnd_from_date = $cmnd->from_date->i18nFormat('yyyy年M月d日');
                }
                $address = $mergeAddress['en'];
                $job = $job ? $job->job_name_jp : '';
                $this->checkData($job, 'Tên phiên âm nghề nghiệp phỏng vấn');

                if (!empty($guild)) {
                    $signingDate = $this->checkData($signingDate, 'Ngày ký kết hiệp định');
                    if (!empty($signingDate)) {
                        $signingDate = $signingDate->i18nFormat('yyyy年M月d日');
                    }
                }

                $guild = $guild ? $guild->name_kanji : '';
                $this->checkData($guild, 'Tên phiên âm nghiệp đoàn quản lý');

                $company = $company ? $company->name_kanji : '';
                $this->checkData($company, 'Tên phiên âm công ty tiếp nhận');
            } else {
                $adminCompanySignerName = $adminCompany->signer_name;
                $studentName = $studentName_VN;
                $licenseAt = $licenseAt->i18nFormat('dd/MM/yyyy');
                $adminCompanyFullName = $adminCompany->name_vn;
                $adminCompanySignerRole = $adminCompany->signer_role_vn;
                $adminCompanyAddress = $adminCompany->address_vn;
                if (empty($order->application_date)) {
                    $createdDay = '';
                } else {
                    $createdDay = Text::insert($vnDateFormatFull, [
                        'day' => str_pad($order->application_date->day, 2, '0', STR_PAD_LEFT), 
                        'month' => str_pad($order->application_date->month, 2, '0', STR_PAD_LEFT), 
                        'year' => $order->application_date->year, 
                        ]);
                }
                
                if (!empty($cmnd) && !empty($cmnd->from_date)) {
                    $cmnd_from_date = $cmnd->from_date->i18nFormat('dd/MM/yyyy');
                }
                
                $birthday = Text::insert($vnDateFormatShort, [
                    'day' => str_pad($birthday->day, 2, '0', STR_PAD_LEFT), 
                    'month' => str_pad($birthday->month, 2, '0', STR_PAD_LEFT), 
                    'year' => $birthday->year, 
                    ]);
                $address = $mergeAddress['vn'];
                $job = $job ? $job->job_name : '';
                $this->checkData($job, 'Tên nghề nghiệp phỏng vấn');

                if (!empty($guild)) {
                    $signingDate = $this->checkData($signingDate, 'Ngày ký kết hiệp định');
                    if (!empty($signingDate)) {
                        $signingDate = Text::insert($vnDateFormatFull, [
                            'day' => $signingDate->day, 
                            'month' => $signingDate->month, 
                            'year' => $signingDate->year, 
                            ]);
                    }
                }
                $guild = $guild ? $guild->name_romaji : '';
                $this->checkData($guild, 'Tên nghiệp đoàn quản lý');
                
                $company = $company ? $company->name_romaji : '';
                $this->checkData($company, 'Tên công ty tiếp nhận');
            }
            
            $this->tbs->VarRef['createdDay'] = $createdDay;
            $this->tbs->VarRef['adCompLicense'] = $adminCompany->license;
            $this->tbs->VarRef['adCompLicenseAt'] = $licenseAt;
            $this->tbs->VarRef['adCompShortName'] = $adminCompany->short_name;
            $this->tbs->VarRef['adCompName'] = $adminCompanyFullName;
            $this->tbs->VarRef['signerName'] = $adminCompanySignerName;
            $this->tbs->VarRef['signerRole'] = $adminCompanySignerRole;
            $this->tbs->VarRef['adCompAddress'] = $adminCompanyAddress;
            $this->tbs->VarRef['adCompPhone'] = $adminCompany->phone_number;
            $this->tbs->VarRef['adCompFax'] = $adminCompany->fax_number;

            $this->tbs->VarRef['year'] = $now->year;
            $this->tbs->VarRef['studentName'] = $studentName;
            $this->tbs->VarRef['birthday'] = $birthday;
            $this->tbs->VarRef['cmnd'] = $student->cards[0]->code;
            $this->tbs->VarRef['fromDay'] = $cmnd_from_date;
            $this->tbs->VarRef['address'] = $address;
            $this->tbs->VarRef['job'] = $job;
            $this->tbs->VarRef['guild'] = $guild;
            $this->tbs->VarRef['company'] = $company;
            $this->tbs->VarRef['subsidy'] = $subsidy;
            $this->tbs->VarRef['signingDate'] = $signingDate;

            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['controller' => 'Orders', 'action' => 'index']);
            }

            $this->tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['controller' => 'Orders', 'action' => 'index']);
        }
    }

    public function exportEduPlan($id = null)
    {
        // load config
        $eduPlanConfig = Configure::read('eduPlan');
        $jpKingYearName = Configure::read('jpKingYearName');
        $orderId = $this->request->getQuery('order');
        try {
            $template = WWW_ROOT . 'document' . DS . $eduPlanConfig['template'];
            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            $student = $this->Students->get($id, [
                'contain' => [
                    'Orders',
                    'Orders.Jobs',
                    'Orders.Companies',
                    'Orders.Guilds',
                ]
            ]);
            $order = $this->Students->Orders->get($orderId, [
                'contain' => [
                    'Companies',
                    'Guilds',
                    'AdminCompanies'
                ]
            ]);
            $adminCompany = $order->admin_company;
            $output_file_name = $eduPlanConfig['filename'];
            $studentName_VN = mb_strtoupper($student->fullname);
            $studentName_EN = $this->Util->convertV2E($studentName_VN);

            $company_name_kanji = $order->company->name_kanji;
            $this->checkData($company_name_kanji, 'Tên phiên âm công ty tiếp nhận');
            $this->tbs->VarRef['company'] = $company_name_kanji;

            $guild_name_kanji = $order->guild->name_kanji;
            $this->checkData($guild_name_kanji, 'Tên phiên âm nghiệp đoàn quản lý');
            $this->tbs->VarRef['guild'] = $guild_name_kanji;

            $this->tbs->VarRef['fullname'] = $studentName_EN;
            $this->tbs->VarRef['created'] = $order->application_date ? $order->application_date->i18nFormat('yyyy年M月d日') .'　'. $jpKingYearName : '';
            $this->checkData($order->application_date, 'Ngày làm hồ sơ');
            
            $this->tbs->VarRef['adCompName'] = $adminCompany->name_en;
            $this->tbs->VarRef['adCompShortName'] = $adminCompany->short_name;
            $this->tbs->VarRef['signerName'] = $this->Util->convertV2E($adminCompany->signer_name);
            $this->tbs->VarRef['signerRole'] = $adminCompany->signer_role_jp;
            
            if (!empty($this->missingFields)) {
                $this->Flash->error(Text::insert($this->errorMessage['export'], [
                    'fields' => $this->missingFields,
                    ]), 
                    [
                        'escape' => false,
                        'params' => ['showButtons' => true]
                    ]);
                return $this->redirect(['controller' => 'Orders', 'action' => 'index']);
            }

            $this->tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['controller' => 'Orders', 'action' => 'index']);
        }
    }

    public function exportCompanyCommitment($orderId = null)
    {
        $order = $this->Students->Orders->get($orderId, [
            'contain' => [
                'AdminCompanies'
            ]
        ]);
        $adminCompany = $order->admin_company;
        // load config
        $commitmentConfig = Configure::read('commitment');
        $jpKingYearName = Configure::read('jpKingYearName');
        $output_file_name = $commitmentConfig['filename'];

        $now = Time::now();
        $template = WWW_ROOT . 'document' . DS . $commitmentConfig['template'];
        $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
        $this->tbs->VarRef['created'] = $order->application_date ? $order->application_date->i18nFormat('yyyy年M月d日') .'　'. $jpKingYearName : '';
        $this->tbs->VarRef['adCompShortName'] = $adminCompany->short_name;
        $this->tbs->VarRef['adCompName'] = $adminCompany->name_en;
        $this->tbs->VarRef['deputyName'] = $this->Util->convertV2E($adminCompany->deputy_name);
        $this->tbs->VarRef['addressEN'] = $adminCompany->address_en;
        $this->tbs->VarRef['phone'] = $adminCompany->phone_number;
        $this->tbs->VarRef['email'] = $adminCompany->email;
        $this->tbs->VarRef['incorpDate'] = $adminCompany->incorporation_date->i18nFormat('yyyy年M月d日');
        $this->tbs->VarRef['signRole'] = $adminCompany->signer_role_jp;
        $this->tbs->VarRef['signName'] = $this->Util->convertV2E($adminCompany->signer_name);
        $this->tbs->VarRef['staffs'] = number_format($adminCompany->staffs_number);
        $this->tbs->VarRef['capitalVN'] = number_format($adminCompany->capital_vn);
        $this->tbs->VarRef['capitalJP'] = number_format($adminCompany->capital_jp);
        $this->tbs->VarRef['revVN'] = number_format($adminCompany->latest_revenue_vn);
        $this->tbs->VarRef['revJP'] = number_format($adminCompany->latest_revenue_jp);

        $this->checkData($order->application_date, 'Ngày làm hồ sơ');
        if (!empty($this->missingFields)) {
            $this->Flash->error(Text::insert($this->errorMessage['export'], [
                'fields' => $this->missingFields,
                ]), 
                [
                    'escape' => false,
                    'params' => ['showButtons' => true]
                ]);
            return $this->redirect(['controller' => 'Orders', 'action' => 'index']);
        }

        $this->tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

    public function exportXlsx() {
        $this->autoRender = false;
        
        // prepare data
        $students = $this->Students->find();
        $exportData = [];
        foreach ($students as $key => $student) {
            $birthday = Time::parse($student->birthday);
            $data = [
                $student->id, 
                '', 
                $student->fullname, 
                Date::formattedPHPToExcel($birthday->year, $birthday->month, $birthday->day)
            ];
            array_push($exportData, $data);
        }

        // init worksheet
        $spreadsheet = $this->ExportFile->setXlsxProperties();
        // set table header
        $header = ['Id', 'Code', 'Fullname', 'Birthday'];
        $spreadsheet->setActiveSheetIndex(0)->fromArray($header, NULL, 'A1');
        // fill data to table
        $spreadsheet->getActiveSheet()->fromArray($exportData, NULL, 'A2');
        // set filter
        $spreadsheet->getActiveSheet()->setAutoFilter($spreadsheet->getActiveSheet()->calculateWorksheetDimension());
        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();

        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->freezePane('A2');
        
        // set style
        $headerStyle = Configure::read('headerStyle');
        $tableStyle = Configure::read('tableStyle');
        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->applyFromArray($headerStyle);
        $spreadsheet->getActiveSheet()->getStyle('A1:D4')->applyFromArray($tableStyle);
        $spreadsheet->getActiveSheet()->getStyle('D2:D4')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);

        // rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Sample');

        // set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // export XLSX file for download
        $this->ExportFile->export($spreadsheet, 'demo.xlsx');
        exit;
    }

    public function exportReport() {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $zoomScale = 100;
            $allStudents = $this->Students->find()->contain([
                'Addresses' => function($q) {
                    return $q->where(['Addresses.type' => '1']);
                },
                'Addresses.Cities',
                'Presenters'
            ]);
            if (!empty($data['std'])) {
                $condition = $data['std'];
                if (isset($condition['order']) && !empty($condition['order'])) {
                    $zoomScale = $zoomScale - 5;
                    if (!empty($condition['order']['name'])) {
                        $zoomScale = $zoomScale - 5;
                        // select all students assigned to the order
                        $allStudents->contain([
                            'Orders' => function($q) use ($condition) {
                                return $q->where(['Orders.id' => $condition['order']['name']]);
                            }, 
                            'Orders.Jobs'
                            ]);
                        $allStudents->matching('Orders', function($q) use ($condition) {
                            return $q->where(['Orders.id' => $condition['order']['name']]);
                        });
                    } else {
                        // select all student passed the interview
                        $allStudents->contain([
                            'Orders' => function($q) {
                                return $q->where(['result' => '1']);
                            }, 
                            'Orders.Jobs'
                            ]);
                        $allStudents->matching('Orders', function($q) {
                            return $q->where([
                                'result' => '1',
                                ]);
                        });
                    }
                }
                
                if (isset($condition['company']) && !empty($condition['company'])) {
                    $zoomScale = $zoomScale - 5;
                    Log::write('debug', 'select all student with company info if passed the interview');
                    $allStudents->contain([
                        'Orders',
                        'Orders.Companies'
                        ]);
                    if (!empty($condition['company']['name'])) {
                        Log::write('debug', 'select all student in company ' . $condition['company']['name']);

                        // select all students passed the interview and belong to the query company
                        $allStudents->matching('Orders.Companies', function($q) use ($condition) {
                            return $q->where([
                                'Companies.id' => $condition['company']['name'],
                                // 'result' => '1'
                                ]);
                        });
                    }
                }

                if (isset($condition['guild']) && !empty($condition['guild'])) {
                    $zoomScale = $zoomScale - 5;
                    Log::write('debug', 'select all student with guild info if passed the interview');
                    $allStudents->contain([
                        'Orders', 
                        'Orders.Guilds'
                        ]);
                    if (!empty($condition['guild']['name'])) {
                        // select all students passed the interview and belong to the query guild
                        $allStudents->matching('Orders.Guilds', function($q) use ($condition) {
                            return $q->where([
                                'Guilds.id' => $condition['guild']['name'],
                                ]);
                        });
                    }
                }
                if (isset($condition['class']) && !empty($condition['class'])) {
                    $zoomScale = $zoomScale - 5;
                    $allStudents->contain('Jclasses');
                    if (!empty($condition['class']['name'])) {
                        $allStudents->matching('Jclasses', function($q) use ($condition) {
                            return $q->where(['Jclasses.id' => $condition['class']['name']]);
                        });
                    }
                }
                
                if (isset($condition['status']) && !empty($condition['status'])) {
                    $allStudents->where(['Students.status' => $condition['status']]);
                }
                if (isset($condition['presenter']) && !empty($condition['presenter'])) {
                    $allStudents->where(['Students.presenter_id' => (int)$condition['presenter']]);
                }
                if (isset($condition['city']) && !empty($condition['city'])) {
                    $allStudents->matching('Addresses', function($q) use ($condition) {
                        return $q->where(['Addresses.city_id' => $condition['city']]);
                    });
                }
                if (isset($condition['edulevel']) && !empty($condition['edulevel'])) {
                    $allStudents->where(['Students.educational_level' => $condition['edulevel']]);
                }
                if (isset($condition['gender']) && !empty($condition['gender'])) {
                    $allStudents->where(['Students.gender' => $condition['gender']]);
                }
                
                // enrolled date filter
                if (!empty($condition['reportfrom']) && empty($condition['reportto'])) {
                    $start = $this->Util->reverseStr($condition['reportfrom']) . '-01';
                    Log::write('debug', 'start:'.$start);
                    $allStudents->where(['Students.enrolled_date >=' => $start]);
                } else if (empty($condition['reportfrom']) && !empty($condition['reportto'])) {
                    $reportto = $this->Util->reverseStr($condition['reportto']) . '-01';
                    $end = $this->Util->getLastDayOfMonth($reportto);
                    Log::write('debug', 'end:'.$end);
                    $allStudents->where(['Students.enrolled_date <=' => $end]);
                } else if (!empty($condition['reportfrom']) && !empty($condition['reportto'])) {
                    $start = $this->Util->reverseStr($condition['reportfrom']) . '-01';
                    $reportto = $this->Util->reverseStr($condition['reportto']) . '-01';
                    $end = $this->Util->getLastDayOfMonth($reportto);
                    Log::write('debug', 'start:'.$start.', end:'.$end);
                    $allStudents->where(function (QueryExpression $exp, Query $q) use($start, $end) {
                        return $exp->between('Students.enrolled_date', $start, $end, 'date');
                    });
                }
            }
            // load config
            $reportConfig = Configure::read('reportXlsx');
            $studentStatus = Configure::read('studentStatus');
            $cityJP = Configure::read('cityJP');
            $lessons = Configure::read('lessons');
            $interviewResult = Configure::read('interviewResult');
            // init worksheet
            $spreadsheet = $this->ExportFile->setXlsxProperties();
            $spreadsheet->setActiveSheetIndex(0);
            $activeSheet = $spreadsheet->getActiveSheet();
            $activeSheet->getSheetView()->setZoomScale($zoomScale);
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

            $col = 'I';
            $activeSheet
                ->mergeCells('A5:A6')->setCellValue('A5', 'STT')
                ->mergeCells('B5:B6')->setCellValue('B5', 'Họ tên')
                ->mergeCells('C5:C6')->setCellValue('C5', 'Ngày sinh')
                ->mergeCells('D5:E5')->setCellValue('D5', 'Giới tính')->setCellValue('D6', 'Nam')->setCellValue('E6', 'Nữ')
                ->mergeCells('F5:F6')->setCellValue('F5', 'Quê quán')
                ->mergeCells('G5:G6')->setCellValue('G5', 'Ngày nhập học')
                ->mergeCells('H5:H6')->setCellValue('H5', 'Người giới thiệu')
                ->mergeCells('I5:I6')->setCellValue('I5', 'Trạng thái');
            if (isset($condition['order']) && !empty($condition['order'])) {
                $col++; // J
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Ngành nghề');
                $activeSheet->getColumnDimension($col)->setWidth(20);

                $col++; // K
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Ngày phỏng vấn');
                $activeSheet->getColumnDimension($col)->setWidth(16);

                $col++; // L
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Địa chỉ làm việc');
                $activeSheet->getColumnDimension($col)->setWidth(15);

                if (!empty($condition['order']['name'])) {
                    $col++; // M
                    $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Kết quả');
                    $activeSheet->getColumnDimension($col)->setWidth(7);
                }
            }
            if (isset($condition['company']) && !empty($condition['company'])) {
                $col++; // J | M | N
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Công ty');
                $activeSheet->getColumnDimension($col)->setWidth(20);
            }
            if (isset($condition['guild']) && !empty($condition['guild'])) {
                $col++; // J | N | O
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Nghiệp đoàn');
                $activeSheet->getColumnDimension($col)->setWidth(25);
            }
            if (isset($condition['class']) && !empty($condition['class'])) {
                $col++; // J | O | P
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Lớp học');
                $activeSheet->getColumnDimension($col)->setWidth(9);

                $col++; // J | P | Q
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Bài đang học');
                $activeSheet->getColumnDimension($col)->setWidth(13);
            }

            $activeSheet->getColumnDimension('A')->setWidth(6);
            $activeSheet->getColumnDimension('B')->setWidth(25);
            $activeSheet->getColumnDimension('C')->setWidth(12);
            $activeSheet->getColumnDimension('D')->setWidth(6);
            $activeSheet->getColumnDimension('E')->setWidth(6);
            $activeSheet->getColumnDimension('F')->setWidth(15);
            $activeSheet->getColumnDimension('G')->setWidth(15);
            $activeSheet->getColumnDimension('H')->setWidth(20);
            $activeSheet->getColumnDimension('I')->setWidth(12);

            $activeSheet->getRowDimension('3')->setRowHeight(30);
            $activeSheet->mergeCells('A3:'.$col.'3');
            $activeSheet->setCellValue('A3', $reportConfig['studentTitle']);
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
            foreach ($allStudents as $key => $student) {
                $counter++;
                if ($student->gender == 'M') {
                    $male = 'x';
                    $female = '';
                } else {
                    $male = '';
                    $female = 'x';
                }
                $data = [
                    $key+1,
                    $student->fullname,
                    $student->birthday ? $student->birthday->i18nFormat('dd/MM/yyyy') : '',
                    $male,
                    $female,
                    $student->addresses ? $student->addresses[0]->city->name : '',
                    $student->enrolled_date ? $student->enrolled_date->i18nFormat('dd/MM/yyyy') : '',
                    $student->presenter->name ?? '',
                    $studentStatus[$student->status]
                ];
                if (isset($condition['order']) && !empty($condition['order'])) {
                    $job = $student->orders ? $student->orders[0]->job->job_name : '';
                    $interviewDate = $student->orders ? $student->orders[0]->interview_date->i18nFormat('dd/MM/yyyy') : '';
                    $workAt = $student->orders ? $cityJP[$student->orders[0]->work_at]['rmj'] : '';
                    if (!empty($condition['order']['name'])) {
                        $result = $student->orders ? $interviewResult[$student->orders[0]->_joinData->result] : '';
                        array_push($data, $job, $interviewDate, $workAt, $result);
                    } else {
                        array_push($data, $job, $interviewDate, $workAt);
                    }
                }
                if (isset($condition['company']) && !empty($condition['company'])) {
                    $companyName = $student->orders ? $student->orders[0]->company->name_romaji : '';
                    array_push($data, $companyName);
                }
                if (isset($condition['guild']) && !empty($condition['guild'])) {
                    $guildName = $student->orders ? $student->orders[0]->guild->name_romaji : '';
                    array_push($data, $guildName);
                }
                if (isset($condition['class']) && !empty($condition['class'])) {
                    $className = $student->jclasses ? $student->jclasses[0]->name : '';
                    $currentLesson = $student->jclasses ? $lessons[$student->jclasses[0]->current_lesson] : '';
                    array_push($data, $className, $currentLesson);
                }
                array_push($listStudents, $data);
            }
            $activeSheet->fromArray($listStudents, NULL, 'A7');
            $activeSheet->getStyle('A5:'. $col . $counter)->getAlignment()->setWrapText(true);
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

    public function mergeAddress($address)
    {
        $addressLevel = Configure::read('addressLevel');
        $currentAddressTemplate = Configure::read('currentAddressTemplate');
        $currentCity = $address->city->name;
        $cityType = $address->city->type;
        if ($cityType == 'Thành phố Trung ương') {
            $currentCityEN = $this->Util->convertV2E(str_replace("Thành phố", "", $currentCity) . " " . $addressLevel['Thành phố']['en']);
        } else {
            $currentCityEN = $this->Util->convertV2E(str_replace($cityType, "", $currentCity) . " " . $addressLevel[$cityType]['en']);
        }

        $currentDistrict = $address->district->name;
        $districtType = $address->district->type;
        $currentDistrictTrim = trim(str_replace($districtType, "", $currentDistrict));
        if (is_numeric($currentDistrictTrim)) {
            $currentDistrictEN = $this->Util->convertV2E($addressLevel[$districtType]['en'] . " " . $currentDistrictTrim);
        } else {
            $currentDistrictEN = $this->Util->convertV2E($currentDistrictTrim . " " . $addressLevel[$districtType]['en']);
        }

        $currentWard = $address->ward->name;
        $wardType = $address->ward->type;
        $currentWardTrim = trim(str_replace($wardType, "", $currentWard));
        if (is_numeric($currentWardTrim)) {
            $currentWardEN = $this->Util->convertV2E($addressLevel[$wardType]['en'] . " " . $currentWardTrim);
        } else {
            $currentWardEN = $this->Util->convertV2E($currentWardTrim . " " . $addressLevel[$wardType]['en']);
        }

        $mergeAdd = [
            'vn' => Text::insert($currentAddressTemplate, [
                'ward' => $currentWard,
                'district' => $currentDistrict,
                'city' => $currentCity,
            ]),
            'en' => Text::insert($currentAddressTemplate, [
                'ward' => $currentWardEN,
                'district' => $currentDistrictEN,
                'city' => $currentCityEN,
            ])
        ];

        return $mergeAdd;
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

    public function convertTags($data)
    {
        $result = ',';
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (empty($value)) {                    
                    continue;
                }
                if (empty(array_values($value)[0])) {                    
                    continue;
                }
                $result = $result . (string)array_values($value)[0] . ',';                
            }
        }
        return $result;
    }

    public function saveImage($b64code, $file_dir, $prevImage)
    {
        $storedImage = $prevImage;
        if (!empty($b64code)) {
            $img = explode(',', $b64code);
            $imgData = base64_decode($img[1]);
            $filename = uniqid() . '.png';
            $file_dir = $file_dir . DS . $filename;
            file_put_contents($file_dir, $imgData);
            $storedImage = 'students/' . $filename;
        }
        return $storedImage;
    }
}

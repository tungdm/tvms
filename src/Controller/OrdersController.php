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

        if (!empty($userPermission) || $user['role_id'] == 1) {
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
            
            if ($user['role_id'] != 1 && 
                ($userPermission->action == 0 || 
                    ($userPermission->action == 1 && 
                        (in_array($action, ['index', 'view']) || strpos($action, 'export') === 0)))) {
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
                $query['records'] = 10;
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
            if (isset($query['guild_id']) && !empty($query['guild_id'])) {
                $allOrders->where(['Companies.guild_id' => $query['guild_id']]);
            }
            if (isset($query['company_id']) && !empty($query['company_id'])) {
                $allOrders->where(['company_id' => $query['company_id']]);
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
        } else {
            $query['records'] = 10;
            $allOrders = $this->Orders->find()->order(['Orders.interview_date' => 'DESC']);
        }

        $this->paginate = [
            'contain' => [
                'Companies', 
                'Companies.Guilds', 
                'Jobs'
            ],
            'sortWhitelist' => ['name', 'interview_date'],
            'limit' => $query['records'],
        ];

        $orders = $this->paginate($allOrders);
        
        $jobs = $this->Orders->Jobs->find('list');
        $companies = $this->Orders->Companies->find('list');
        $guilds = $this->Orders->Companies->Guilds->find('list');

        $this->set(compact('orders', 'jobs', 'companies', 'guilds', 'query'));
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
                'Companies', 
                'Companies.Guilds',
                'Jobs', 
                'Students',
                'CreatedByUsers',
                'ModifiedByUsers'
            ]
        ]);

        $this->set('order', $order);
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
        $companies = $this->Orders->Companies->find('list', ['limit' => 200]);
        $jobs = $this->Orders->Jobs->find('list', ['limit' => 200]);
        $this->set(compact('order', 'companies', 'jobs'));
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
                'Students', 
                'Students.Jclasses',
                'Students.Addresses' => function($q) {
                    return $q->where(['Addresses.type' => '1']);
                }, 
                'Students.Addresses.Cities',
                'Events'
            ]
        ]);
        $orderName = $order->name;
        $currentInterviewDate = $order->interview_date->i18nFormat('yyyy-MM-dd');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $data['departure_date'] = $this->Util->reverseStr($data['departure_date']);
            $newInterviewDate = $this->Util->convertDate($data['interview_date']);
            if ($currentInterviewDate !== $newInterviewDate) {
                // uppdate system event
                $event = $this->SystemEvent->update($order->events[0]->id, $data['interview_date']);
                $data['events'][0] = $event;
            }
            $order = $this->Orders->patchEntity($order, $data, ['associated' => ['Students', 'Events']]);
            $order = $this->Orders->setAuthor($order, $this->Auth->user('id'), $this->request->getParam('action'));
            
            if ($this->Orders->save($order)) {
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
        $companies = $this->Orders->Companies->find('list', ['limit' => 200]);
        $jobs = $this->Orders->Jobs->find('list', ['limit' => 200]);
        $this->set(compact('order', 'companies', 'jobs'));
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
        if (!empty($order->students)) {
            foreach ($order->students as $key => $student) {
                $student->status = '2';
                $student->return_date = '';
            }
            if ($this->Orders->Students->saveMany($order->students) && $this->Orders->delete($order)) {
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
            if ($this->Orders->delete($order)) {
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

    public function close($id = null)
    {
        $this->request->allowMethod(['post']);
        $order = $this->Orders->get($id);
        if ($order->status !== '4') {
            $this->Flash->error($this->errorMessage['unAuthor']);
        } else {
            $order->status = '5'; // close order
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

            $candidateName = $interview->student->fullname;
            if (!empty($interview) && $table->delete($interview) && $this->Orders->save($order)) {
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
                ->find('list', ['limit' => 200])
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
                    'LanguageAbilities',
                    'Families',
                    'Families.Jobs',
                    'Jclasses',
                ]
            ]);
            $studentName_VN = mb_strtoupper($student->fullname);
            $studentName_EN = $this->Util->convertV2E($studentName_VN);
            $studentName = explode(' ', $studentName_EN);
            $studentFirstName = array_pop($studentName);
            $output_file_name = Text::insert($cvTemplateConfig['filename'], [
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
                        'schoolJP' => $eduLevel[$value->degree]['jp'] . "校卒業" . $specialized,
                    ];
                    array_push($eduHis, $history);

                    // certificate
                    if (!empty($value->certificate)) {
                        $certificate = [];
                        $certificateDate = new Time($value->certificate);
                        $data = [
                            'year' => $certificateDate->year,
                            'month' => $certificateDate->month,
                            'certificate' => $eduLevel[$value->degree]['jp'] . "校卒業証明書"
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
            $this->tbs->VarRef['created'] = $now->subDays(14)->i18nFormat('yyyy年MM月dd日');
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
            $this->tbs->VarRef['strength'] = $this->checkData($student->strength, 'Điểm mạnh');
            $this->tbs->VarRef['purpose'] = $this->checkData($student->purpose, 'Mục đích xuất khẩu lao động');
            $this->tbs->VarRef['genitive'] = $this->checkData($student->genitive, 'Tính cách');
            $this->tbs->VarRef['salary'] = $this->checkData($student->salary, 'Thu nhập hiện tại');
            $this->tbs->VarRef['saving'] = $this->checkData($student->saving_expected, 'Số tiền mong muốn');
            $this->tbs->VarRef['maritalStatus'] = $maritalStatus[$student->marital_status]['jp'];
            $this->tbs->VarRef['after_plan'] = $this->checkData($student->after_plan, 'Dự định sau khi về nước');
            $this->tbs->VarRef['reh'] = $this->checkData($student->right_eye_sight_hospital, 'Thị lực mắt phải đo tại bệnh viện');
            $this->tbs->VarRef['leh'] = $this->checkData($student->left_eye_sight_hospital, 'Thị lực mắt trái đo tại bệnh viện');
            $this->tbs->VarRef['re'] = $this->checkData($student->right_eye_sight, 'Thị lực mắt phải');
            $this->tbs->VarRef['le'] = $this->checkData($student->left_eye_sight, 'Thị lực mắt trái');
            $this->tbs->VarRef['height'] = $student->height;
            $this->tbs->VarRef['weight'] = $student->weight;

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

            $this->tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
            exit();
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
        $output_file_name = $dispatchLetterConfig['filename'];
        $order = $this->Orders->get($id, [
            'contain' => [
                'Jobs',
                'Companies',
                'Companies.Guilds',
                'Students' => function($q) {
                    return $q->where(['result' => '1']);
                }
            ]
        ]);

        try {
            $template = WWW_ROOT . 'document' . DS . $dispatchLetterConfig['template'];
            $missingFields = [];
            $guildJP = $this->checkData($order->company->guild->name_kanji, 'Tên phiên âm của nghiệp đoàn');
            $guildVN = $this->checkData($order->company->guild->name_romaji, 'Tên nghiệp đoàn');

            $guildLicenseNum = $this->checkData($order->company->guild->license_number, 'Số giấy phép của nghiệp đoàn');

            $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            $guildDeputyJP = $this->checkData($order->company->guild->deputy_name_kanji, 'Tên phiên âm người đại diện nghiệp đoàn');
            $guildDeputyVN = $order->company->guild->deputy_name_romaji;

            $guildAddressJP = $this->checkData($order->company->guild->address_kanji, 'Địa chỉ phiên âm của nghiệp đoàn');
            $guildAddressVN = $order->company->guild->address_romaji;

            $guildPhone = $order->company->guild->phone_jp;

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
            $this->tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
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
                    'Students.Addresses.Cities',
                    'Students.Addresses.Districts',
                    'Students.Addresses.Wards',
                    'Jobs',
                    'Companies',
                    'Companies.Guilds'
                ]
            ]);
            // init worksheet
            $spreadsheet = $this->ExportFile->setXlsxProperties();
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(85);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

            $spreadsheet->getActiveSheet()->setShowGridLines(false);
            $spreadsheet->getActiveSheet()->setCellValue('A1', 'CHI NHÁNH CÔNG TY VINAGIMEX., JSC (TP HCM)');
            $spreadsheet->getActiveSheet()->getStyle('A1:A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ]);

            $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
            $spreadsheet->getActiveSheet()->mergeCells('A3:M3');
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

            $spreadsheet->getActiveSheet()->mergeCells('A4:M4');
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
            $richText->createText(' Công ty CP XNK tổng hợp và CGCN Việt Nam');
            $spreadsheet->getActiveSheet()->setCellValue('A5', $richText);
            $spreadsheet->getActiveSheet()->setCellValue('A6', 'Chi nhánh TP HCM xin đề nghị cấp thư phái cử cho các Tu nghiệp sinh Nhật bản theo danh sách sau:');
            
            $spreadsheet->getActiveSheet()
                ->mergeCells('A8:A9')->setCellValue('A8', 'STT')
                ->mergeCells('B8:B9')->setCellValue('B8', 'Họ và tên')
                ->mergeCells('C8:C9')->setCellValue('C8', 'Ngày sinh')
                ->mergeCells('D8:E8')->setCellValue('D8', 'Giới tính')->setCellValue('D9', 'Nam')->setCellValue('E9', 'Nữ')
                ->mergeCells('F8:H8')->setCellValue('F8', 'Quê quán')->setCellValue('F9', 'Xã')->setCellValue('G9', 'Huyện')->setCellValue('H9', 'Tỉnh,TP')
                ->mergeCells('I8:I9')->setCellValue('I8', 'Thời hạn HĐ')
                ->mergeCells('J8:J9')->setCellValue('J8', 'Nơi làm việc')
                ->mergeCells('K8:K9')->setCellValue('K8', 'Ngành nghề')
                ->mergeCells('L8:L9')->setCellValue('L8', 'Nghiệp đoàn')
                ->mergeCells('M8:M9')->setCellValue('M8', 'Ghi chú');
            
            $spreadsheet->getActiveSheet()->getStyle('I8')->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(34);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(32);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(6);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(26);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(10);

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
                    $male,
                    $female,
                    mb_strtoupper($ward),
                    mb_strtoupper($district),
                    mb_strtoupper($city),
                    str_pad($order->work_time, 2, '0', STR_PAD_LEFT),
                    mb_strtoupper($cityJP[$order->work_at]['rmj']),
                    mb_strtoupper($order->job->job_name),
                    mb_strtoupper($order->company->guild->name_romaji),
                ];
                array_push($listWorkers, $data);
            }
            // fill data to table
            $spreadsheet->getActiveSheet()->fromArray($listWorkers, NULL, 'A10');
            $spreadsheet->getActiveSheet()->getStyle('A8:M'. $counter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('A8:M'.$counter)->applyFromArray([
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
            $spreadsheet->getActiveSheet()->getStyle('A8:M9')->applyFromArray([
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
            $spreadsheet->getActiveSheet()->mergeCells('A'.$footer.':M'.$footer)
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
                'Companies.Guilds',
                'Students',
                'Students.InputTests' => ['sort' => ['InputTests.type' => 'ASC']],
                'Students.IqTests'
            ]
        ]);
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
        $spreadsheet->getActiveSheet()->setCellValue('A2', '監理団体：' . $order->company->guild->name_kanji);
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
}

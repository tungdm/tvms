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
use Cake\Utility\Text;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
            if ($userPermission->action == 0 || ($userPermission->action == 1 && in_array($action, ['index', 'view']))) {
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
        $this->entity = 'lao động';
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
                $query['records'] = 10;
            }
            if (isset($query['code']) && !empty($query['code'])) {
                $allStudents->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('code', '%'.$query['code'].'%');
                });
            }
            if (isset($query['fullname']) && !empty($query['fullname'])) {
                $allStudents->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('fullname', '%'.$query['fullname'].'%');
                });
            }
            if (isset($query['email']) && !empty($query['email'])) {
                $allStudents->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('email', '%'.$query['email'].'%');
                });
            }
            if (isset($query['gender']) && !empty($query['gender'])) {
                $allStudents->where(['gender' => $query['gender']]);
            }

            if (isset($query['phone']) && !empty($query['phone'])) {
                $allStudents->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('phone', '%'.$query['phone'].'%');
                });
            }
            if (isset($query['status']) && !empty($query['status'])) {
                $allStudents->where(['status' => $query['status']]);
            }
        } else {
            $allStudents = $this->Students->find()->order(['Students.created' => 'DESC']);;
            $query['records'] = 10;
        }
        $this->paginate = [
            'sortWhitelist' => ['code', 'fullname', 'email', 'phone'],
            'limit' => $query['records']
        ];
        $students = $this->paginate($allStudents);
        $cities = TableRegistry::get('Cities')->find('list')->cache('cities', 'long');
        $this->set(compact('students', 'query', 'cities'));
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
                'LanguageAbilities',
                'Documents',
                'Presenters'
            ]
        ]);
        $jobs = TableRegistry::get('Jobs')->find('list')->toArray();
        $cities = TableRegistry::get('Cities')->find('list')->cache('cities', 'long');
        $this->set(compact(['student', 'jobs', 'cities']));
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

            //Get first key in studentStatus array
            $student->status = key(Configure::read('studentStatus'));

            if ($this->Students->save($student)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $student->fullname
                ]));
            } else {
                $this->Flash->error($this->errorMessage['add']);
            }
            return $this->redirect(['action' => 'index']);
            
        } else {
            throw new NotFoundException(__('Page not found'));
        }
    }

    public function info($id = null)
    {
        $prevImage = NULL;
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
                    'Documents'
                    ]
                ]);
            $action = 'edit';
            $prevImage = $student->image;
        } else {
            $student = $this->Students->newEntity();
            $action = 'add';
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $student = $this->Students->patchEntity($student, $data, ['associated' => [
                'Addresses', 
                'Families', 
                'Cards', 
                'Educations',
                'Experiences',
                'LanguageAbilities',
                'Documents'
                ]]);
            
            // save image
            $b64code = $data['b64code'];
            if (!empty($b64code)) {
                $img = explode(',', $b64code);
                $imgData = base64_decode($img[1]);
                $filename = uniqid() . '.png';
                $file_dir = WWW_ROOT . 'img' . DS . 'students' . DS . $filename;
                file_put_contents($file_dir, $imgData);
                $student->image = 'students/' . $filename;
            } else {
                $student->image = $prevImage;
            }

            // setting expectation
            $expectJobs = $data['expectationJobs'];
            $expectStr = ',';
            if (!empty($expectStr)) {
                foreach ($expectJobs as $key => $value) {
                    if (empty($value)) {                    
                        continue;
                    }
                    $expectStr = $expectStr . (string)array_values($value)[0] . ',';                
                }
            }
            $student->expectation = $expectStr;
            $student = $this->Students->setAuthor($student, $this->Auth->user('id'), $action);

            // setting student code if first init
            if (empty($student->code)) {
                $lastestCode = $this->Students->find()->order(['id' => 'DESC'])->first();
                $parsingCode = Text::tokenize($lastestCode, '-');
                $codeTemplate = Configure::read('studentCodeTemplate');
                $now = Time::now()->i18nFormat('yyyyMMdd');
                if ($now === $parsingCode[1]) {
                    $counter = (int)$parsingCode[2] + 1;
                    $counter = str_pad((string)$counter, 3, '0', STR_PAD_LEFT);
                } else {
                    $counter = '001';
                }
                $newCode = Text::insert($codeTemplate, [
                    'date' => $now, 
                    'counter' => $counter
                    ]);
                $student->code = $newCode;
            }

            try{
                // save to db
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
                    return $this->redirect(['action' => 'info', $student->id]);
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
        
        //TODO: get data from db
        $presenters = TableRegistry::get('Presenters')->find('list');
        $jobs = TableRegistry::get('Jobs')->find('list');

        $cities = TableRegistry::get('Cities')->find('list')->cache('cities', 'long');
        $districts = [];
        $wards = [];
        if (!empty($student->addresses)) {
            foreach ($student->addresses as $key => $value) {
                $districts[$key] = TableRegistry::get('Districts')->find('list')->where(['city_id' => $value->city_id])->toArray();
                $wards[$key] = TableRegistry::get('Wards')->find('list')->where(['district_id' => $value->district_id])->toArray();
            }
        }
        $this->set(compact(['student', 'presenters', 'jobs', 'cities', 'districts', 'wards', 'action']));
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
                } else {
                    //TODO: Blacklist current user
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
                } else {
                    //TODO: Blacklist current user
                }
            }
            return $this->jsonResponse($resp);
        }
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
            $memberName = $member->fullname;
            if (!empty($member) && $families->delete($member)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => Text::insert($this->successMessage['delete'], [
                            'entity' => 'thành viên', 
                            'name' => $memberName
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

    public function deleteEducations()
    {
        $this->request->allowMethod('ajax');
        $eduId = $this->request->getData('id');
        $educations = TableRegistry::get('Educations');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Error',
                'type' => 'error',
                'message' => __('The euducation history could not be deleted. Please, try again.')
            ]
        ];
        
        try {
            $eduHis = $educations->get($eduId);
            if (!empty($eduHis) && $educations->delete($eduHis)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('The education history has been deleted.')
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
                'title' => 'Error',
                'type' => 'error',
                'message' => __('The working experience could not be deleted. Please, try again.')
            ]
        ];
        
        try {
            $exp = $experiences->get($expId);
            if (!empty($exp) && $experiences->delete($exp)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('The working experience has been deleted.')
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
                'title' => 'Error',
                'type' => 'error',
                'message' => __('The ability could not be deleted. Please, try again.')
            ]
        ];
        
        try {
            $langAbl = $langAblTbl->get($langId);
            if (!empty($langAbl) && $langAblTbl->delete($langAbl)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('The ability has been deleted.')
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
        // Load config
        $resumeConfig = Configure::read('resume');
        $country = Configure::read('country');
        $addressENLevel = Configure::read('addressENLevel');
        $currentAddressTemplate = Configure::read('currentAddressTemplate');
        $schoolTemplate = Configure::read('schoolTemplate');
        $eduLevel = Configure::read('eduLevel');
        $folderImgTemplate = Configure::read('folderImgTemplate');
        $language = Configure::read('language');

        $student = $this->Students->get($id, [
            'contain' => [
                'Addresses' => function($q) {
                    return $q->where(['Addresses.type' => '2']);
                },
                'Addresses.Cities',
                'Addresses.Districts',
                'Addresses.Wards',
                'Educations',
                'Experiences',
                'Experiences.Jobs',
                'LanguageAbilities'
                ]
            ]);

        // $template = WWW_ROOT . 'document' . DS . 'resume_template.docx';
        $template = WWW_ROOT . 'document' . DS . 'resume.docx';
        $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
        
        // Prepare data
        $now = Time::now();
        $studentName_VN = mb_strtoupper($student->fullname);
        $studentName_EN = $this->convertV2E($studentName_VN);
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

        $currentCity = $student->addresses[0]->city->name;
        $cityType = $student->addresses[0]->city->type;
        if ($cityType == 'Thành phố Trung ương') {
            $currentCityEN = $this->convertV2E(str_replace("Thành phố", "", $currentCity) . " " . $addressENLevel['Thành phố']);
        } else {
            $currentCityEN = $this->convertV2E(str_replace($cityType, "", $currentCity) . " " . $addressENLevel[$cityType]);
        }

        $currentDistrict = $student->addresses[0]->district->name;
        $districtType = $student->addresses[0]->district->type;
        $currentDistrictEN = $this->convertV2E(str_replace($districtType, "", $currentDistrict) . " " . $addressENLevel[$districtType]);
        
        $currentWard = $student->addresses[0]->ward->name;
        $wardType = $student->addresses[0]->ward->type;
        $currentWardEN = $this->convertV2E(str_replace($wardType, "", $currentWard) . " " . $addressENLevel[$wardType]);

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
        }
        $jplang = $enlang = $folderImgTemplate . DS . 'circle.png';
        if (empty(str_replace(" ", "", $jplevel_JP))) {
            $jplang = $folderImgTemplate . DS . 'blank.png';
        }
        if (empty(str_replace(" ", "", $enlevel_JP))) {
            $enlang = $folderImgTemplate . DS . 'blank.png';
        }
        
        $this->tbs->VarRef['y'] = $now->year;
        $this->tbs->VarRef['m'] = $now->month;
        $this->tbs->VarRef['d'] = $now->day;
        $this->tbs->VarRef['studentname_en'] = $studentName_EN;
        $this->tbs->VarRef['studentname_vn'] = $studentName_VN;

        $this->tbs->VarRef['nation_jp'] = $nation_JP;
        $this->tbs->VarRef['nation_vn'] = $nation_VN;
        
        $this->tbs->VarRef['male'] = $male;
        $this->tbs->VarRef['female'] = $female;

        $this->tbs->VarRef['maritalyes'] = $marital_y;  
        $this->tbs->VarRef['maritalno'] = $marital_n;

        $this->tbs->VarRef['jplevel_jp'] = $jplevel_JP;
        $this->tbs->VarRef['jplevel_vn'] = $jplevel_JP;
        $this->tbs->VarRef['enlevel_jp'] = $enlevel_JP;
        $this->tbs->VarRef['enlevel_vn'] = $enlevel_VN;
        $this->tbs->VarRef['jplang'] = $jplang;
        $this->tbs->VarRef['enlang'] = $enlang;
            
        $this->tbs->VarRef['bd_y'] = $student->birthday->year;
        $this->tbs->VarRef['bd_m'] = $student->birthday->month;
        $this->tbs->VarRef['bd_d'] = $student->birthday->day;

        $this->tbs->VarRef['age'] = ($now->diff($student->birthday))->y;

        $this->tbs->VarRef['currentaddress_en'] = Text::insert($currentAddressTemplate, [
            'ward' => $currentWardEN,
            'district' => $currentDistrictEN,
            'city' => $currentCityEN,
        ]);
        $this->tbs->VarRef['currentaddress_vn'] = Text::insert($currentAddressTemplate, [
            'ward' => $currentWard,
            'district' => $currentDistrict,
            'city' => $currentCity,
        ]);

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
            array_push($eduHis, $history);
        } else {
            foreach ($student->educations as $key => $value) {
                $history = [
                    'title' => 'Quá trình học tập',
                    'time' => $value->from_date . ' ～ ' . $value->to_date,
                    'school' => Text::insert($schoolTemplate, [
                        'schoolNameEN' => $this->convertV2E($value->school),
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

            $history = [
                'title' => 'Quá trình công tác',
                'time' => $value->from_date . ' ～ ' . $value->to_date,
                'company' => $companyStr
            ];
            array_push($expHis, $history);
        }
        $this->tbs->MergeBlock('b', $expHis);

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
                $student->code, 
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
        // add some data
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
        $this->ExportFile->export($spreadsheet);
        exit;
    }

    public function convertV2E ($str)
    {
        if (!$str) {
            return false;
        }
        $str = trim($str);
        $str = mb_strtoupper($str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        return $str;
    }
}

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
        Log::write('debug', $userPermission);

        if (!empty($userPermission)) {
            if ($userPermission->action == 0 || ($userPermission->action == 1 && in_array($action, ['index', 'view']))) {
                $session->write($controller, $userPermission->action);
                return true;
            }
        }
        return parent::isAuthorized($user);
    }

    // public function beforeFilter(Event $event)
    // {
    //     $this->Security->setConfig('unlockedActions', ['info']);
    // }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {   
        $query = $this->request->getQuery();
        
        $allStudents = $this->Students->find();
        if (!empty($query)) {
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
            $this->paginate = [
                'sortWhitelist' => ['code', 'fullname', 'email', 'gender', 'phone', 'status'],
                'limit' => (int)$query['records']
            ];
            
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
            $query['records'] = 10;
            $this->paginate = [
                'sortWhitelist' => ['code', 'fullname', 'email', 'gender', 'phone', 'status'],
                'limit' => $query['records']
            ];
        }
        // $students = $this->paginate($this->Students->find()->order(['id' => 'DESC']));
        $students = $this->paginate($allStudents);
        $this->set(compact('students', 'query'));
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
            'contain' => []
        ]);

        $this->set('student', $student);
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
                $this->Flash->success(__('The student has been saved.'));
            } else {
                $this->Flash->error(__('The student could not be saved. Please, try again.'));
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
                    'Experiences.Jobs'
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
                'Experiences'
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
                            
                    $this->Flash->success(__('The student has been saved.'));
                    return $this->redirect(['action' => 'info', $student->id]);
                }
            } catch (Exception $e) {
                Log::write('debug', $e);
            }
            
            $this->Flash->error(__('The student could not be saved. Please, try again.'));
        }
        
        //TODO: get data from db
        $presenters = [];
        $jobs = TableRegistry::get('Jobs')->find('list');

        $this->set(compact(['student', 'presenters', 'jobs']));
    }

    public function getDistrict()
    {
        if ($this->request->is('ajax')) {
            $query = $this->request->getQuery();
            $resp = [];
            if (isset($query['city']) && !empty($query['city'])) {
                $district = Configure::read('district');
                if (array_key_exists($query['city'], $district)) {
                    $resp = $district[$query['city']];
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
                $ward = Configure::read('ward');
                if (array_key_exists($query['district'], $ward)) {
                    $resp = $ward[$query['district']];
                } else {
                    //TODO: Blacklist current user
                }
            }
            return $this->jsonResponse($resp);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $student = $this->Students->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $student = $this->Students->patchEntity($student, $this->request->getData());
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student could not be saved. Please, try again.'));
        }
        $this->set(compact('student'));
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
        if ($this->Students->delete($student)) {
            $this->Flash->success(__('The student has been deleted.'));
        } else {
            $this->Flash->error(__('The student could not be deleted. Please, try again.'));
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
                'title' => 'Error',
                'type' => 'error',
                'message' => __('The member could not be deleted. Please, try again.')
            ]
        ];
        
        try {
            $member = $families->get($memberId);
            if (!empty($member) && $families->delete($member)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('The member has been deleted.')
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
}

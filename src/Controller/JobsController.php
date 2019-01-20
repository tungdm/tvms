<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Utility\Text;


/**
 * Jobs Controller
 *
 * @property \App\Model\Table\JobsTable $Jobs
 *
 * @method \App\Model\Entity\Job[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class JobsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'nghề nghiệp';
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

            if ($userPermission->action == 0 || ($userPermission->action == 1 && in_array($action, ['index', 'view']))) {
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
            $allJobs = $this->Jobs->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
            if (isset($query['f_job_name']) && !empty($query['f_job_name'])) {
                $allJobs->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('job_name', '%'.$query['f_job_name'].'%');
                });
            }
            if (isset($query['f_job_name_jp']) && !empty($query['f_job_name_jp'])) {
                $allJobs->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('job_name_jp', '%'.$query['f_job_name_jp'].'%');
                });
            }
            if (isset($query['f_created_by']) && !empty($query['f_created_by'])) {
                $allJobs->where(['Jobs.created_by' => $query['f_created_by']]);
            }
            if (isset($query['f_modified_by']) && !empty($query['f_modified_by'])) {
                $allJobs->where(['Jobs.modified_by' => $query['f_modified_by']]);
            }
            $allJobs->order(['Jobs.created' => 'DESC']);
        } else {
            $query['records'] = 10;
            $allJobs = $this->Jobs->find()->order(['Jobs.created' => 'DESC']);
        }
        $this->paginate = [
            'sortWhitelist' => ['job_name','job_name_jp'],
            'contain' => [
                'CreatedByUsers',
                'ModifiedByUsers'
            ],
            'limit' => $query['records']
        ];
        if ($this->Auth->user('role_id') != 1) {
            // other user (not admin) can not view delete record
            $allJobs->where(['Jobs.del_flag' => FALSE]);
        }
        $jobs = $this->paginate($allJobs);

        $usersTable = TableRegistry::get('Users');
        $allUsers = $usersTable->find('list');

        $this->set(compact('jobs', 'allUsers', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Job id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
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
            $job = $this->Jobs->get($id, [
                'contain' => [
                    'CreatedByUsers',
                    'ModifiedByUsers'
                ]
            ]);
            if (!$job->del_flag || $this->Auth->user('role_id') == 1) {
                $resp = [
                    'status' => 'success',
                    'data' => $job,
                    'created' => $job->created ? $job->created ->i18nFormat('dd-MM-yyyy HH:mm:ss') : '',
                    'modified' => $job->modified ? $job->modified->i18nFormat('dd-MM-yyyy HH:mm:ss') : ''
                ];
            }
        }
        catch (Exception $e) {
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
        $this->request->allowMethod('ajax');
        $job = $this->Jobs->newEntity();
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
            $checkExists = $this->Jobs->find()->where(['job_name' => $data['job_name']])->first();
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
                $job = $this->Jobs->patchEntity($job, $data);
                $job = $this->Jobs->setAuthor($job, $this->Auth->user('id'), $this->request->getParam('action'));

                if ($this->Jobs->save($job)) {
                    $resp = [
                        'status' => 'success',
                        'redirect' => Router::url(['action' => 'index']),
                    ];
                    $this->Flash->success(Text::insert($this->successMessage['add'], [
                        'entity' => $this->entity,
                        'name' => $job->job_name
                    ]));
                } else {
                    Log::write('debug', $job->errors());
                    
                }
            }
        } catch (Exception $e) {
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    /**
     * Edit method
     *
     * @param string|null $id Job id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $data = $this->request->getData();
        $job = $this->Jobs->get($data['id'], [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $job = $this->Jobs->patchEntity($job, $data);
            $job = $this->Jobs->setAuthor($job, $this->Auth->user('id'), $this->request->getParam('action'));

            if ($this->Jobs->save($job)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $job->job_name
                    ]));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error($this->errorMessage['error']);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Job id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $job = $this->Jobs->get($id);
        $job->del_flag = TRUE;
        $job = $this->Jobs->setAuthor($job, $this->Auth->user('id'), 'edit');
        if ($this->Jobs->save($job)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $job->job_name
                ]));
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }

        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $job = $this->Jobs->get($id);
        $job->del_flag = FALSE;
        $job = $this->Jobs->setAuthor($job, $this->Auth->user('id'), 'edit');

        if ($this->Jobs->save($job)) {
            $this->Flash->success(Text::insert($this->successMessage['recover'], [
                'entity' => $this->entity, 
                'name' => $job->job_name
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                'entity' => $this->entity,
                'name' => $job->job_name
                ]));
        }
        return $this->redirect(['action' => 'index']);
    }
}

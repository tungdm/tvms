<?php
namespace App\Controller;

use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Core\Configure;
use App\Controller\AppController;    
use Cake\Log\Log;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;


/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        Log::write('debug', 'initialize usercontroller');
        
        $this->Auth->allow(['login', 'logout']);
    }

    public function isAuthorized($user) 
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        $session = $this->request->session();
        Log::write('debug', $user);
        
        if (isset($user['permissions'])) {
            foreach ($user['permissions'] as $key => $value) {
                if ($controller == $value['scope']) {
                    // Check if user try to edit admin info
                    if (in_array($action, ['edit', 'delete'])) {
                        $target_id = $this->request->getParam('pass')[0];
                        $target_user = $this->Users->get($target_id, ['contain' => 'Roles']);
                        if ($target_user->role->name == 'admin') {
                            Log::write('alert', 'User "' . $user['username'] . '" try to modify user "' . $target_user->username . '" with admin role');
                            return false;
                        }
                    }

                    if ($value['action'] == 0 || ($value['action'] == 1 && in_array($action, ['index', 'view']))) {
                        $session->write($controller, $value['action']);
                        return true;
                    }
                    return false;
                }
            }
        }
        
        return parent::isAuthorized($user);
    }

    public function login() 
    {
        $rememberMe = $this->request->getCookie('rememberMe');

        if ($this->request->is('post') || isset($rememberMe)) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error('Your username or password is incorrect.');
        }
        $this->viewBuilder()->autoLayout(false);
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }


    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $query = $this->request->getQuery();
        
        $this->paginate = [
            'contain' => ['Roles', 'Profiles', 'Profiles.Jobs'],
            'sortWhitelist' => ['username', 'email', 'gender', 'phone', 'fullname', 'job_id', 'role_id'],
            'limit' => 3
        ];
        $allUsers = $this->Users->find();
        if (!empty($query)) {
            $condition = [];
            if (isset($query['username']) && !empty($query['username'])) {
                $allUsers->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('username', '%'.$query['username'].'%');
                });
            }
            
            if (isset($query['email']) && !empty($query['email'])) {
                $allUsers->matching('Profiles', function(Query $q) use ($query) {
                    return $q->where(['Profiles.email LIKE' => '%'.$query['email'].'%']);
                });
            }

            if (isset($query['gender']) && !empty($query['gender'])) {
                $allUsers->matching('Profiles', function(Query $q) use ($query) {
                    return $q->where(['Profiles.gender' => $query['gender']]);
                });
            }

            if (isset($query['job_id']) && !empty($query['job_id'])) {
                $allUsers->matching('Profiles', function(Query $q) use ($query) {
                    return $q->where(['Profiles.job_id' => $query['job_id']]);
                });
            }

            if (isset($query['phone']) && !empty($query['phone'])) {
                $allUsers->matching('Profiles', function(Query $q) use ($query) {
                    return $q->where(['Profiles.phone LIKE' => '%'.$query['phone'].'%']);
                });
            }

            if (isset($query['fullname']) && !empty($query['fullname'])) {
                $allUsers->matching('Profiles', function(Query $q) use ($query) {
                    return $q->where(['Profiles.fullname LIKE' => '%'.$query['fullname'].'%']);
                });
            }

            if (isset($query['role_id']) && !empty($query['role_id'])) {
                $allUsers->matching('Profiles', function(Query $q) use ($query) {
                    return $q->where(['role_id' => $query['role_id']]);
                });
            }
        }

        $users = $this->paginate($allUsers);
        $roles = $this->Users->Roles->find('list');
        $jobs = TableRegistry::get('Jobs')->find('list');
        $this->set(compact('users', 'roles', 'jobs', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);
        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // $template = ROOT . DS . 'webroot' . DS . 'document' . DS . 'demo.docx';
        // $this->tbs->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
        // $output_file_name = 'output.docx';

        // $username = $this->Auth->user('username');
        // $now = Time::now()->i18nFormat('yyyy 年 MM 月 dd 日');
        // $fullname = 'Dang Minh Tung';
        // $fullname_vn = 'Đặng Minh Tùng';
        // $nation_jp = 'ベトナム';
        // $nation_vn = 'Việt Nam';
        // $male_cb = '&#9745;'; //check
        // $female_cb = '&#9744;'; //non-check
        // $notfa_cb = '&#x25A0;'; //check
        // $fa_cb = '&#9744;'; //non-check
        // $language_jp = 'ベトナム語';
        // $language_vn = 'Tiếng Việt';
        
        
        // $this->tbs->VarRef['now'] = $now;
        // $this->tbs->VarRef['yourname'] = $username;
        // $this->tbs->VarRef['username'] = strtoupper($fullname);
        // $this->tbs->VarRef['username_vn'] = strtoupper($fullname_vn);
        // $this->tbs->VarRef['nation_jp'] = $nation_jp;
        // $this->tbs->VarRef['nation_vn'] = $nation_vn;
        // $this->tbs->VarRef['male_cb'] = html_entity_decode($male_cb);
        // $this->tbs->VarRef['female_cb'] = html_entity_decode($female_cb);
        // $this->tbs->VarRef['notfa_cb'] = html_entity_decode($notfa_cb);
        // $this->tbs->VarRef['fa_cb'] = html_entity_decode($fa_cb);
        // $this->tbs->VarRef['language_jp'] = $language_jp;
        // $this->tbs->VarRef['language_vn'] = $language_vn;
        // $this->tbs->VarRef['imgpath'] = ROOT . DS . 'webroot' . DS . 'img' . DS . 'admin.jpg';

        // $this->tbs->Show(OPENTBS_FILE, $output_file_name);
        // $this->tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        // exit();

        $user = $this->Users->newEntity();
        if ($this->request->is('ajax')) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['associated' => ['Profiles', 'Permissions']]);
            $user = $this->Users->setAuthor($user, $this->Auth->user('id'), $this->request->getParam('action'));
            if ($this->Users->save($user)) {
                $resp = [
                    'status' => 'success',
                    'redirect' => Router::url(['action' => 'index']),
                    'flash' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('The user has been saved.')
                    ]
                ];
                $this->Flash->success(__('The user has been saved.'));
                // return $this->redirect(['action' => 'index']);
            } else {
                $resp = [
                    'status' => 'error',
                    'flash' => [
                        'title' => 'Error',
                        'type' => 'error',
                        'message' => __('The user could not be saved. Please, try again.')
                    ]
                ];
            }
            
            // $this->Flash->error(__('The user could not be saved. Please, try again.'));
            return $this->jsonResponse($resp);
        }
        // get role
        $userRole = $this->Auth->user('role')['name'];
        if ($userRole == 'admin') {
            $roles = $this->Users->Roles->find('list');
        } else {
            $roles = $this->Users->Roles->find('list')->where(['name !=' => 'admin'])->all();
        }
        $jobs = $this->Users->Profiles->Jobs->find('list');
        
        $this->set(compact('user', 'roles', 'jobs'));
    }

    public function deletePermission()
    {
        $this->request->allowMethod('ajax');
        $id = $this->request->getData('id');
        $permissions = TableRegistry::get('Permissions');
        $permission = $permissions->get($id);
        if ($permissions->delete($permission)) {
            $resp = [
                'status' => 'success',
                'alert' => [
                    'title' => 'Success',
                    'type' => 'success',
                    'message' => __('The permission has been deleted.')
                ]
            ];
        } else {
            $resp = [
                'status' => 'error',
                'alert' => [
                    'title' => 'Error',
                    'type' => 'error',
                    'message' => __('The permission could not be deleted. Please, try again.')
                ]
            ];
        }
        return $this->jsonResponse($resp);
    }
    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Profiles', 'Profiles.Jobs', 'Permissions']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            debug($this->request->getData());
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fieldList' => ['role_id', 'permissions'],
                'associated' => [
                    'Profiles' => [
                        'fieldList' => ['fullname', 'email', 'job_id', 'phone']
                    ], 
                    'Permissions' => [
                        'fieldList' => ['scope', 'action']
                    ]
                ]
            ]);
            // $user = $this->Users->patchEntity($user, $this->request->getData(), ['associated' => ['Profiles', 'Permissions']]);
            debug($user);
            $user = $this->Users->setAuthor($user, $this->Auth->user('id'), $this->request->getParam('action'));
            // debug($user->errors());

            //TODO: Blacklist user
            
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        // get role
        $userRole = $this->Auth->user('role')['name'];
        if ($userRole == 'admin') {
            $roles = $this->Users->Roles->find('list');
        } else {
            $roles = $this->Users->Roles->find('list')->where(['name !=' => 'admin'])->all();
        }
        $jobs = $this->Users->Profiles->Jobs->find('list');

        $this->set(compact('user', 'roles', 'jobs'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}

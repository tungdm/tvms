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
use Cake\Utility\Text;
use Cake\Auth\DefaultPasswordHasher;


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
        
        // case: allow user update their profile
        $target_id = $this->request->getParam('pass');
        if (!empty($target_id)) {
            $target_id = $target_id[0];
        }
        if ($action === 'edit' && ($target_id == $this->Auth->user('id'))) {
            return true;
        }
        
        $permissionsTable = TableRegistry::get('Permissions');
        $userPermission = $permissionsTable->find()->where(['user_id' => $user['id'], 'scope' => $controller])->first();

        // case: check permission on specific scope
        if (!empty($userPermission)) {
            // Check if user try to edit admin info
            if (in_array($action, ['edit', 'delete'])) {
                $target_user = $this->Users->get($target_id, ['contain' => 'Roles']);
                if ($target_user->role->name == 'admin') {
                    // TODO: Blacklist user
                    Log::write('warning', 'User "' . $user['username'] . '" try to modify user "' . $target_user->username . '" with admin role');
                    return false;
                }
            }

            if ($userPermission->action == 0 || ($userPermission->action == 1 && in_array($action, ['index', 'view']))) {
                $session->write($controller, $userPermission->action);
                return true;
            }
            return false;
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
        
        $allUsers = $this->Users->find();
        if (!empty($query)) {
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
            $this->paginate = [
                'contain' => ['Roles'],
                'sortWhitelist' => ['username', 'email', 'gender', 'phone', 'fullname', 'role_id'],
                'limit' => $query['records']
            ];
            if (isset($query['username']) && !empty($query['username'])) {
                $allUsers->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('username', '%'.$query['username'].'%');
                });
            }
            
            if (isset($query['email']) && !empty($query['email'])) {
                $allUsers->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('email', '%'.$query['email'].'%');
                });
            }

            if (isset($query['gender']) && !empty($query['gender'])) {
                $allUsers->where(['gender' => $query['gender']]);
            }

            if (isset($query['phone']) && !empty($query['phone'])) {
                $allUsers->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('phone', '%'.$query['phone'].'%');
                });
            }

            if (isset($query['fullname']) && !empty($query['fullname'])) {
                $allUsers->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('fullname', '%'.$query['fullname'].'%');
                });
            }

            if (isset($query['role']) && !empty($query['role'])) {
                $allUsers->where(['role_id' => $query['role']]);
            }
        } else {
            $query['records'] = 10;
            $this->paginate = [
                'contain' => ['Roles'],
                'sortWhitelist' => ['username', 'email', 'gender', 'phone', 'fullname', 'role_id'],
                'limit' => $query['records']
            ];
        }

        $users = $this->paginate($allUsers);
        $roles = $this->Users->Roles->find('list');

        // get role for edit permission
        $currentUser = $this->Auth->user();
        if ($currentUser['role_id'] == 1) {
            $roles4Edit = $roles;
        } else {
            $roles4Edit = $roles->where(['name !=' => 'admin'])->all();
        }
        $this->set(compact('users', 'roles', 'roles4Edit', 'query'));
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
        $currentUser = $this->Auth->user();
        $currentUserRole = $currentUser['role']['name'];
        $user = $this->Users->newEntity();

        if ($this->request->is('ajax')) {
            $resp = [
                'status' => 'error',
                'flash' => [
                    'title' => 'Error',
                    'type' => 'error',
                    'message' => __('The user could not be saved. Please, try again.')
                ]
            ];

            $data = $this->request->getData();
            // finalcheck: only admin can create an admin
            if ($data['role_id'] == '1' && $currentUserRole != 'admin') {
                //TODO: Blacklist current user
                $msgTemplate = Configure::read('blackListTemplate');
                $msg = Text::insert($msgTemplate, [
                    'username' => $currentUser['username'], 
                    'error' => 'try to create an admin'
                    ]);
                Log::write('warning', $msg);
                return $this->jsonResponse($resp);
            }
            $user = $this->Users->patchEntity($user, $data, ['associated' => ['Permissions']]);

            // set password
            $user->password = Configure::read('passwordDefault');
            
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
            }

            return $this->jsonResponse($resp);
        }
        // get role for display
        if ($currentUserRole == 'admin') {
            $roles = $this->Users->Roles->find('list');
        } else {
            $roles = $this->Users->Roles->find('list')->where(['name !=' => 'admin'])->all();
        }
        
        $this->set(compact('user', 'roles'));
    }

    public function editPermission()
    {
        $this->request->allowMethod('ajax');
        $resp = [];
        
        if ($this->request->is('get')) {
            $id = $this->request->getQuery('id');
            $permissionsTable = TableRegistry::get('Permissions');
            $permissions = $permissionsTable->find()->where(['user_id' => $id])->toArray();

            $resp = [
                'permissions' => $permissions
            ];
        } else if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            Log::write('debug', $data);
            $resp = [
                'status' => 'error',
                'flash' => [
                    'title' => 'Error',
                    'type' => 'error',
                    'message' => __('The permission could not be changed. Please, try again.')
                ]
            ];

            $currentUser = $this->Auth->user();
            $currentUserRole = $currentUser['role_id'];
            // finalcheck: only admin can create an admin
            if ($data['role_id'] == '1' && $currentUserRole != 1) {
                //TODO: Blacklist current user
                $msgTemplate = Configure::read('blackListTemplate');
                $msg = Text::insert($msgTemplate, [
                    'username' => $currentUser['username'], 
                    'error' => 'try to change user role to admin'
                    ]);
                Log::write('warning', $msg);
                return $this->jsonResponse($resp);
            }

            if (isset($data['id']) && !empty($data['id'])) {
                $targetId = $data['id'];
                $user = $this->Users->get($targetId);
                
                // case: manager update user permission
                $user = $this->Users->patchEntity($user, $data, [
                    'fieldList' => ['role_id', 'permissions'],
                    'associated' => [
                        'Permissions' => [
                            'fieldList' => ['id', 'scope', 'action']
                        ]
                    ]
                ]);
                Log::write('debug', $user);
                
                $user = $this->Users->setAuthor($user, $this->Auth->user('id'), 'edit');
                
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
                    $this->Flash->success(__('The permission has been changed.'));
                }
            }
        }
        return $this->jsonResponse($resp);
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
            'contain' => ['Permissions', 'Roles']
        ]);
        $prevImage = $user->image;
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            
            if ($this->Auth->user('id') == $id) {
                // case: user update their profile
                $user = $this->Users->patchEntity($user, $data, [
                    'fieldList' => ['phone', 'fullname', 'email', 'birthday'],
                ]);
            }
            
            // save image
            $b64code = $data['b64code'];
            if (!empty($b64code)) {
                $img = explode(',', $b64code);
                $imgData = base64_decode($img[1]);
                $filename = uniqid() . '.png';
                $file_dir = WWW_ROOT . 'img' . DS . 'users' . DS . $filename;
                file_put_contents($file_dir, $imgData);
                $image_path = 'users/' . $filename;
                $user->image = $image_path;
            } else {
                $user->image = $prevImage;
            }

            $user = $this->Users->setAuthor($user, $this->Auth->user('id'), $this->request->getParam('action'));

            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                
                if ($this->Auth->user('id') == $id) {
                    $this->Auth->setUser($user);
                }

                return $this->redirect(['action' => 'edit', $user->id]);
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

        $this->set(compact('user', 'roles'));
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

    public function changePassword() {
        $this->request->allowMethod('ajax');
        $resp = [
            'status' => 'error',
            'flash' => [
                'title' => 'Error',
                'type' => 'error',
                'message' => __('The password could not be updated. Please, try again.')
            ]
        ];

        $data = $this->request->getData();
        $currentPassword = $this->Users->find('password', ['userId' => $this->Auth->user('id')])->first();

        // check confirm password
        $hasher = new DefaultPasswordHasher();
        if ($hasher->check($data['current-password'], $currentPassword->password)) {
            // update password
            $user = $this->Users->get($this->Auth->user('id'));
            $user->password = $data['new-password'];
            
            if ($this->Users->save($user)) {
                // logout user
                $resp = [
                    'status' => 'success',
                    'redirect' => Router::url(['action' => 'logout']),
                    'flash' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('The password has been updated. Please, login again.')
                    ]
                ];
                $this->Flash->success(__('The password has been updated. Please, login again.'));
            }
        }
        return $this->jsonResponse($resp);
    }
}

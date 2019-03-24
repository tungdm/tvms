<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Utility\Text;


/**
 * NotificationSettings Controller
 *
 * @property \App\Model\Table\NotificationSettingsTable $NotificationSettings
 *
 * @method \App\Model\Entity\NotificationSetting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NotificationSettingsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'thiết lập';
    }

    public function isAuthorized($user)
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        $session = $this->request->session();
        $permissionsTable = TableRegistry::get('Permissions');
        $userPermission = $permissionsTable->find()->where(['user_id' => $user['id'], 'scope' => $controller])->first();
        if (in_array($action, ['changeState', 'deleteNotification', 'viewTopNoti', 'viewAll'])) {
            return true;
        }
        if (!empty($userPermission)) {
            // only admin can recover deleted record
            if ($action == 'recover') {
                return false;
            }
            // full-access user can do anything
            // read-only user can read data
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
            $allSettings = $this->NotificationSettings->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['f_title']) && !empty($query['f_title'])) {
                $allSettings->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('title', '%'.$query['f_title'].'%');
                });
            }
            if (isset($query['f_modified_by']) && !empty($query['f_modified_by'])) {
                $allSettings->where(['NotificationSettings.modified_by' => $query['f_modified_by']]);
            }
            $allSettings->order(['NotificationSettings.created' => 'DESC']);
        } else {
            $query['records'] = $this->defaultDisplay;
            $allSettings = $this->NotificationSettings->find()->order(['NotificationSettings.created' => 'DESC']);
        }
        $this->paginate = [
            'contain' => ['CreatedByUsers', 'ModifiedByUsers'],
            'limit' => $query['records']
        ];
        if ($this->Auth->user('role_id') != 1) {
            // other user (not admin) can not view delete record
            $allSettings->where(['NotificationSettings.del_flag' => FALSE]);
        }

        $settings = $this->paginate($allSettings);

        $allUsers = TableRegistry::get('Users')->find('list');
        $groups = TableRegistry::get('Roles')->find('list');
        $this->set(compact('settings', 'allUsers', 'query', 'groups'));
    }

    /**
     * View method
     *
     * @param string|null $id Notification Setting id.
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
            $setting = $this->NotificationSettings->get($id, [
                'contain' => [
                    'CreatedByUsers',
                    'ModifiedByUsers'
                ]
            ]);
            if (!$setting->del_flag || $this->Auth->user('role_id') == 1) {
                $groups = TableRegistry::get('Roles')->find('list')->toArray();
                $receiversArr = explode(',', $setting->receivers_groups);
                array_shift($receiversArr);
                array_pop($receiversArr);
                $receiversStr = '';
                $total = count($receiversArr);
                foreach ($receiversArr as $key => $value) {
                    if ($key == $total - 1) {
                        $receiversStr .= $groups[$value];
                    } else {
                        $receiversStr .= $groups[$value] . ', ';
                    }
                }
                $resp = [
                    'status' => 'success',
                    'data' => $setting,
                    'receivers' => $receiversStr,
                    'receiversArr' => $receiversArr,
                    'created' => $setting->created ? $setting->created ->i18nFormat('dd-MM-yyyy HH:mm:ss') : '',
                    'modified' => $setting->modified ? $setting->modified->i18nFormat('dd-MM-yyyy HH:mm:ss') : ''
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
     * Edit method
     *
     * @param string|null $id Notification Setting id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $data = $this->request->getData();
        $setting = $this->NotificationSettings->get($data['id']);
        $this->checkDeleteFlag($setting, $this->Auth->user());
        $setting = $this->NotificationSettings->patchEntity($setting, $data);
        $setting->receivers_groups = $this->convertTags($data['groups']);
        $setting = $this->NotificationSettings->setAuthor($setting, $this->Auth->user('id'), 'edit');
        if ($this->NotificationSettings->save($setting)) {
            $this->Flash->success(Text::insert($this->successMessage['edit'], [
                'entity' => $this->entity,
                'name' => $setting->title
            ]));
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Notification Setting id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $setting = $this->NotificationSettings->get($id);
        $setting->del_flag = TRUE;
        $setting = $this->NotificationSettings->setAuthor($setting, $this->Auth->user('id'), 'edit');

        if ($this->NotificationSettings->save($setting)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $setting->title
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $setting->title
                ]));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $setting = $this->NotificationSettings->get($id);
        $setting->del_flag = FALSE;
        $setting = $this->NotificationSettings->setAuthor($setting, $this->Auth->user('id'), 'edit');

        if ($this->NotificationSettings->save($setting)) {
            $this->Flash->success(Text::insert($this->successMessage['recover'], [
                'entity' => $this->entity, 
                'name' => $setting->title
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                'entity' => $this->entity,
                'name' => $setting->title
                ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function changeState()
    {
        $this->request->allowMethod('ajax');
        $resp = [
            'status' => 'error',
            'flash' => [
                'title' => 'Lối',
                'type' => 'error',
                'icon' => 'fa fa-warning',
                'message' => $this->errorMessage['error']
            ]
        ];

        $notiId = $this->request->getData('id');
        $notificationTable = TableRegistry::get('Notifications');
        try {
            $currentUser = $this->Auth->user('id');
            $noti = $notificationTable->get($notiId);
            $noti->is_seen = TRUE;
            if ($notificationTable->save($noti)) {
                $unreadMsg = $notificationTable->find()->where(['user_id' => $currentUser, 'is_seen' => FALSE])->count();
                $resp = [
                    'status' => 'success',
                    'unreadMsg' => $unreadMsg,
                ];
            } 
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function deleteNotification()
    {
        $this->request->allowMethod('ajax');
        $resp = [
            'status' => 'error',
            'flash' => [
                'title' => 'Lối',
                'type' => 'error',
                'icon' => 'fa fa-warning',
                'message' => $this->errorMessage['error']
            ]
        ];

        $notiId = $this->request->getData('id');
        $notificationTable = TableRegistry::get('Notifications');
        try {
            $currentUser = $this->Auth->user('id');
            $noti = $notificationTable->find()->where(['id' => $notiId, 'user_id' => $currentUser])->first();
            if ($notificationTable->delete($noti)) {
                $unreadMsg = $notificationTable->find()->where(['user_id' => $currentUser, 'is_seen' => FALSE])->count();
                $resp = [
                    'status' => 'success',
                    'unreadMsg' => $unreadMsg,
                    'flash' => [
                        'title' => 'Thành công',
                        'type' => 'success',
                        'icon' => 'fa fa-check',
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

    public function viewTopNoti()
    {
        $this->request->allowMethod('ajax');
        $resp = [
            'status' => 'error',
            'flash' => [
                'title' => 'Lối',
                'type' => 'error',
                'icon' => 'fa fa-warning',
                'message' => $this->errorMessage['error']
            ]
        ];
        try {
            $currentUser = $this->Auth->user('id');
            $notificationTable = TableRegistry::get('Notifications');
            $notifications = $notificationTable->find()->where(['user_id' => $currentUser])->limit(5)->order(['created' => 'DESC'])->toArray();
            $resp = [
                'status' => 'success',
                'notifications' => $notifications
            ];
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }


    public function viewAll()
    {
        $this->request->allowMethod('ajax');
        $resp = [
            'status' => 'error',
            'flash' => [
                'title' => 'Lối',
                'type' => 'error',
                'icon' => 'fa fa-warning',
                'message' => $this->errorMessage['error']
            ]
        ];
        try {
            $currentUser = $this->Auth->user('id');
            $notificationTable = TableRegistry::get('Notifications');
            $notifications = $notificationTable->find()->where(['user_id' => $currentUser])->toArray();
            $resp = [
                'status' => 'success',
                'notifications' => $notifications
            ];
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
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
}

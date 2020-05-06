<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Log\Log;

/**
 * Purposes Controller
 *
 * @property \App\Model\Table\PurposesTable $Purposes
 *
 * @method \App\Model\Entity\Purpose[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PurposesController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'mục đích';
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
            
            // full-access user can do anything
            // read-only user can read data, export data
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
            $allPurposes = $this->Purposes->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['purpose_name']) && !empty($query['purpose_name'])) {
                $allPurposes->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%'.$query['purpose_name'].'%');
                });
            }
            if (isset($query['purpose_name_jp']) && !empty($query['purpose_name_jp'])) {
                $allPurposes->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name_jp', '%'.$query['purpose_name_jp'].'%');
                });
            }
            if (isset($query['created_by']) && !empty($query['created_by'])) {
                $allPurposes->where(['Purposes.created_by' => $query['created_by']]);
            }
            if (isset($query['modified_by']) && !empty($query['modified_by'])) {
                $allPurposes->where(['Purposes.modified_by' => $query['modified_by']]);
            }
            if (!isset($query['sort'])) {
                $allPurposes->order(['Purposes.created' => 'DESC']);
            }
        } else {
            $query['records'] = $this->defaultDisplay;
            $allPurposes = $this->Purposes->find()->order(['Purposes.created' => 'DESC']);
        }
        $this->paginate = [
            'contain' => ['CreatedByUsers', 'ModifiedByUsers'],
            'limit' => $query['records']
        ];
        if ($this->Auth->user('role_id') != 1) {
            // other user (not admin) can not view delete record
            $allPurposes->where(['Purposes.del_flag' => FALSE]);
        }
        $purposes = $this->paginate($allPurposes);
        $usersTable = TableRegistry::get('Users');
        $allUsers = $usersTable->find('list');
        $this->set(compact('purposes', 'allUsers', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Purpose id.
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
            $purpose = $this->Purposes->get($id, [
                'contain' => [
                    'CreatedByUsers',
                    'ModifiedByUsers'
                ]
            ]);
            if (!$purpose->del_flag || $this->Auth->user('role_id') == 1) {
                $resp = [
                    'status' => 'success',
                    'data' => $purpose,
                    'created' => $purpose->created ? $purpose->created ->i18nFormat('dd-MM-yyyy HH:mm:ss') : '',
                    'modified' => $purpose->modified ? $purpose->modified->i18nFormat('dd-MM-yyyy HH:mm:ss') : ''
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
        $purpose = $this->Purposes->newEntity();
        if ($this->request->is('post')) {
            $purpose = $this->Purposes->patchEntity($purpose, $this->request->getData());
            $purpose = $this->Purposes->setAuthor($purpose, $this->Auth->user('id'), 'add');
            if ($this->Purposes->save($purpose)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $purpose->name
                ]));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error($this->errorMessage['error']);
        }
        $this->set(compact('purpose'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Purpose id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $data = $this->request->getData();
        $purpose = $this->Purposes->get($data['id']);
        $this->checkDeleteFlag($purpose, $this->Auth->user());

        $purpose = $this->Purposes->patchEntity($purpose, $this->request->getData());
        $purpose = $this->Purposes->setAuthor($purpose, $this->Auth->user('id'), 'edit');
        if ($this->Purposes->save($purpose)) {
            $this->Flash->success(Text::insert($this->successMessage['edit'], [
                'entity' => $this->entity,
                'name' => $purpose->name
            ]));
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Purpose id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $purpose = $this->Purposes->get($id);
        $purpose->del_flag = TRUE;
        $purpose = $this->Purposes->setAuthor($purpose, $this->Auth->user('id'), 'edit');

        if ($this->Purposes->save($purpose)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $purpose->name
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $purpose->name
                ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $purpose = $this->Purposes->get($id);
        $purpose->del_flag = FALSE;
        $purpose = $this->Purposes->setAuthor($purpose, $this->Auth->user('id'), 'edit');

        if ($this->Purposes->save($purpose)) {
            $this->Flash->success(Text::insert($this->successMessage['recover'], [
                'entity' => $this->entity, 
                'name' => $purpose->name
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                'entity' => $this->entity,
                'name' => $purpose->name
                ]));
        }
        return $this->redirect(['action' => 'index']);
    }
}

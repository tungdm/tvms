<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Log\Log;
/**
 * Characteristics Controller
 *
 * @property \App\Model\Table\CharacteristicsTable $Characteristics
 *
 * @method \App\Model\Entity\Characteristic[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CharacteristicsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'tính cách';
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
            $allChars = $this->Characteristics->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['char_name']) && !empty($query['char_name'])) {
                $allChars->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%'.$query['char_name'].'%');
                });
            }
            if (isset($query['char_name_jp']) && !empty($query['char_name_jp'])) {
                $allChars->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name_jp', '%'.$query['char_name_jp'].'%');
                });
            }
            if (isset($query['created_by']) && !empty($query['created_by'])) {
                $allChars->where(['Characteristics.created_by' => $query['created_by']]);
            }
            if (isset($query['modified_by']) && !empty($query['modified_by'])) {
                $allChars->where(['Characteristics.modified_by' => $query['modified_by']]);
            }
            if (!isset($query['sort'])) {
                $allChars->order(['Characteristics.created' => 'DESC']);
            }
        } else {
            $query['records'] = $this->defaultDisplay;
            $allChars = $this->Characteristics->find()->order(['Characteristics.created' => 'DESC']);
        }
        $this->paginate = [
            'contain' => ['CreatedByUsers', 'ModifiedByUsers'],
            'limit' => $query['records']
        ];
        if ($this->Auth->user('role_id') != 1) {
            // other user (not admin) can not view delete record
            $allChars->where(['Characteristics.del_flag' => FALSE]);
        }
        $characteristics = $this->paginate($allChars);
        $usersTable = TableRegistry::get('Users');
        $allUsers = $usersTable->find('list');
        $this->set(compact('characteristics', 'allUsers', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Characteristic id.
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
            $characteristic = $this->Characteristics->get($id, [
                'contain' => [
                    'CreatedByUsers',
                    'ModifiedByUsers'
                ]
            ]);
            if (!$characteristic->del_flag || $this->Auth->user('role_id') == 1) {
                $resp = [
                    'status' => 'success',
                    'data' => $characteristic,
                    'created' => $characteristic->created ? $characteristic->created ->i18nFormat('dd-MM-yyyy HH:mm:ss') : '',
                    'modified' => $characteristic->modified ? $characteristic->modified->i18nFormat('dd-MM-yyyy HH:mm:ss') : ''
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
        $characteristic = $this->Characteristics->newEntity();
        if ($this->request->is('post')) {
            $characteristic = $this->Characteristics->patchEntity($characteristic, $this->request->getData());
            $characteristic = $this->Characteristics->setAuthor($characteristic, $this->Auth->user('id'), 'add');
            if ($this->Characteristics->save($characteristic)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $characteristic->name
                ]));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error($this->errorMessage['error']);
        }
        $this->set(compact('characteristic'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Characteristic id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $data = $this->request->getData();
        $characteristic = $this->Characteristics->get($data['id']);
        $this->checkDeleteFlag($characteristic, $this->Auth->user());
        $characteristic = $this->Characteristics->patchEntity($characteristic, $data);
        $characteristic = $this->Characteristics->setAuthor($characteristic, $this->Auth->user('id'), 'edit');
        if ($this->Characteristics->save($characteristic)) {
            $this->Flash->success(Text::insert($this->successMessage['edit'], [
                'entity' => $this->entity,
                'name' => $characteristic->name
            ]));
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Characteristic id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $characteristic = $this->Characteristics->get($id);
        $characteristic->del_flag = TRUE;
        $characteristic = $this->Characteristics->setAuthor($characteristic, $this->Auth->user('id'), 'edit');

        if ($this->Characteristics->save($characteristic)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $characteristic->name
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $characteristic->name
                ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $characteristic = $this->Characteristics->get($id);
        $characteristic->del_flag = FALSE;
        $characteristic = $this->Characteristics->setAuthor($characteristic, $this->Auth->user('id'), 'edit');

        if ($this->Characteristics->save($characteristic)) {
            $this->Flash->success(Text::insert($this->successMessage['recover'], [
                'entity' => $this->entity, 
                'name' => $characteristic->name
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                'entity' => $this->entity,
                'name' => $characteristic->name
                ]));
        }
        return $this->redirect(['action' => 'index']);
    }
}

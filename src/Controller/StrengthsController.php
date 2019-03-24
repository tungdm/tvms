<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Log\Log;

/**
 * Strengths Controller
 *
 * @property \App\Model\Table\StrengthsTable $Strengths
 *
 * @method \App\Model\Entity\Strength[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StrengthsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'chuyên môn';
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
            $allStrengths = $this->Strengths->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['strength_name']) && !empty($query['strength_name'])) {
                $allStrengths->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%'.$query['strength_name'].'%');
                });
            }
            if (isset($query['strength_name_jp']) && !empty($query['strength_name_jp'])) {
                $allStrengths->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name_jp', '%'.$query['strength_name_jp'].'%');
                });
            }
            if (isset($query['created_by']) && !empty($query['created_by'])) {
                $allStrengths->where(['Strengths.created_by' => $query['created_by']]);
            }
            if (isset($query['modified_by']) && !empty($query['modified_by'])) {
                $allStrengths->where(['Strengths.modified_by' => $query['modified_by']]);
            }
            $allStrengths->order(['Strengths.created' => 'DESC']);
        } else {
            $query['records'] = $this->defaultDisplay;
            $allStrengths = $this->Strengths->find()->order(['Strengths.created' => 'DESC']);
        }
        $this->paginate = [
            'contain' => ['CreatedByUsers', 'ModifiedByUsers'],
            'limit' => $query['records']
        ];
        if ($this->Auth->user('role_id') != 1) {
            // other user (not admin) can not view delete record
            $allStrengths->where(['Strengths.del_flag' => FALSE]);
        }
        $strengths = $this->paginate($allStrengths);
        $usersTable = TableRegistry::get('Users');
        $allUsers = $usersTable->find('list');
        $this->set(compact('strengths', 'allUsers', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Strength id.
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
            $strength = $this->Strengths->get($id, [
                'contain' => [
                    'CreatedByUsers',
                    'ModifiedByUsers'
                ]
            ]);
            if (!$strength->del_flag || $this->Auth->user('role_id') == 1) {
                $resp = [
                    'status' => 'success',
                    'data' => $strength,
                    'created' => $strength->created ? $strength->created->i18nFormat('dd-MM-yyyy HH:mm:ss') : '',
                    'modified' => $strength->modified ? $strength->modified->i18nFormat('dd-MM-yyyy HH:mm:ss') : ''
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
        $strength = $this->Strengths->newEntity();
        if ($this->request->is('post')) {
            $strength = $this->Strengths->patchEntity($strength, $this->request->getData());
            $strength = $this->Strengths->setAuthor($strength, $this->Auth->user('id'), 'add');
            if ($this->Strengths->save($strength)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $strength->name
                ]));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error($this->errorMessage['error']);
        }
        $this->set(compact('strength'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Strength id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $data = $this->request->getData();
        $strength = $this->Strengths->get($data['id']);
        $this->checkDeleteFlag($strength, $this->Auth->user());
        $strength = $this->Strengths->patchEntity($strength, $data);
        $strength = $this->Strengths->setAuthor($strength, $this->Auth->user('id'), 'edit');
        if ($this->Strengths->save($strength)) {
            $this->Flash->success(Text::insert($this->successMessage['edit'], [
                'entity' => $this->entity,
                'name' => $strength->name
            ]));
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Strength id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $strength = $this->Strengths->get($id);
        $strength->del_flag = TRUE;
        $strength = $this->Strengths->setAuthor($strength, $this->Auth->user('id'), 'edit');

        if ($this->Strengths->save($strength)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $strength->name
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $strength->name
                ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $strength = $this->Strengths->get($id);
        $strength->del_flag = FALSE;
        $strength = $this->Strengths->setAuthor($strength, $this->Auth->user('id'), 'edit');

        if ($this->Strengths->save($strength)) {
            $this->Flash->success(Text::insert($this->successMessage['recover'], [
                'entity' => $this->entity, 
                'name' => $strength->name
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                'entity' => $this->entity,
                'name' => $strength->name
                ]));
        }

        return $this->redirect(['action' => 'index']);
    }
}

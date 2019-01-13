<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Log\Log;
/**
 * AfterPlans Controller
 *
 * @property \App\Model\Table\AfterPlansTable $AfterPlans
 *
 * @method \App\Model\Entity\AfterPlan[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AfterPlansController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'dự định';
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
            $allPlans = $this->AfterPlans->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
            if (isset($query['plan_name']) && !empty($query['plan_name'])) {
                $allPlans->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%'.$query['plan_name'].'%');
                });
            }
            if (isset($query['plan_name_jp']) && !empty($query['plan_name_jp'])) {
                $allPlans->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name_jp', '%'.$query['plan_name_jp'].'%');
                });
            }
            if (isset($query['created_by']) && !empty($query['created_by'])) {
                $allPlans->where(['AfterPlans.created_by' => $query['created_by']]);
            }
            if (isset($query['modified_by']) && !empty($query['modified_by'])) {
                $allPlans->where(['AfterPlans.modified_by' => $query['modified_by']]);
            }
        } else {
            $query['records'] = 10;
            $allPlans = $this->AfterPlans->find()->order(['AfterPlans.created' => 'DESC']);
        }
        $this->paginate = [
            'contain' => ['CreatedByUsers', 'ModifiedByUsers'],
            'limit' => $query['records']
        ];
        if ($this->Auth->user('role_id') != 1) {
            // other user (not admin) can not view delete record
            $allPlans->where(['AfterPlans.del_flag' => FALSE]);
        }
        $plans = $this->paginate($allPlans);
        $usersTable = TableRegistry::get('Users');
        $allUsers = $usersTable->find('list');
        $this->set(compact('plans', 'allUsers', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id After Plan id.
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
            $afterPlan = $this->AfterPlans->get($id, [
                'contain' => [
                    'CreatedByUsers',
                    'ModifiedByUsers'
                ]
            ]);
            if (!$afterPlan->del_flag || $this->Auth->user('role_id') == 1) {
                $resp = [
                    'status' => 'success',
                    'data' => $afterPlan,
                    'created' => $afterPlan->created ? $afterPlan->created ->i18nFormat('dd-MM-yyyy HH:mm:ss') : '',
                    'modified' => $afterPlan->modified ? $afterPlan->modified->i18nFormat('dd-MM-yyyy HH:mm:ss') : ''
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
        $afterPlan = $this->AfterPlans->newEntity();
        if ($this->request->is('post')) {
            $afterPlan = $this->AfterPlans->patchEntity($afterPlan, $this->request->getData());
            $afterPlan = $this->AfterPlans->setAuthor($afterPlan, $this->Auth->user('id'), 'add');

            if ($this->AfterPlans->save($afterPlan)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $afterPlan->name
                ]));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error($this->errorMessage['error']);
        }
        $this->set(compact('afterPlan'));
    }

    /**
     * Edit method
     *
     * @param string|null $id After Plan id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $data = $this->request->getData();
        $afterPlan = $this->AfterPlans->get($data['id']);
        $this->checkDeleteFlag($afterPlan, $this->Auth->user());
        $afterPlan = $this->AfterPlans->patchEntity($afterPlan, $data);
        $afterPlan = $this->AfterPlans->setAuthor($afterPlan, $this->Auth->user('id'), 'edit');
        if ($this->AfterPlans->save($afterPlan)) {
            $this->Flash->success(Text::insert($this->successMessage['edit'], [
                'entity' => $this->entity,
                'name' => $afterPlan->name
            ]));
            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error($this->errorMessage['error']);
    }

    /**
     * Delete method
     *
     * @param string|null $id After Plan id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $afterPlan = $this->AfterPlans->get($id);
        $afterPlan->del_flag = TRUE;
        $afterPlan = $this->AfterPlans->setAuthor($afterPlan, $this->Auth->user('id'), 'edit');

        if ($this->AfterPlans->save($afterPlan)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $afterPlan->name
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $afterPlan->name
                ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $afterPlan = $this->AfterPlans->get($id);
        $afterPlan->del_flag = FALSE;
        $afterPlan = $this->AfterPlans->setAuthor($afterPlan, $this->Auth->user('id'), 'edit');

        if ($this->AfterPlans->save($afterPlan)) {
            $this->Flash->success(Text::insert($this->successMessage['recover'], [
                'entity' => $this->entity, 
                'name' => $afterPlan->name
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                'entity' => $this->entity,
                'name' => $afterPlan->name
                ]));
        }
        return $this->redirect(['action' => 'index']);
    }
}

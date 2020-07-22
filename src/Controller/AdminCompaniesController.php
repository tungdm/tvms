<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Utility\Text;
use Cake\I18n\Number;


/**
 * AdminCompanies Controller
 *
 * @property \App\Model\Table\AdminCompaniesTable $AdminCompanies
 *
 * @method \App\Model\Entity\AdminCompany[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AdminCompaniesController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'Công ty';
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
            if (in_array($action, ['recover', 'delete'])) {
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
            $adminCompanies = $this->AdminCompanies->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['f_alias']) && !empty($query['f_alias'])) {
                $adminCompanies->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('alias', '%'.$query['f_alias'].'%');
                });
            }
            if (isset($query['f_deputy_name']) && !empty($query['f_deputy_name'])) {
                $adminCompanies->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('deputy_name', '%'.$query['f_deputy_name'].'%');
                });
            }
            if (isset($query['f_phone_number']) && !empty($query['f_phone_number'])) {
                $adminCompanies->where(['phone_number' => $query['f_phone_number']]);
            }
            if (isset($query['f_email']) && !empty($query['f_email'])) {
                $adminCompanies->where(['AdminCompanies.email' => $query['f_email']]);
            }
            if (!isset($query['sort'])) {
                $adminCompanies->order(['AdminCompanies.created' => 'DESC']);
            }
        } else {
            $query['records'] = $this->defaultDisplay;
            $adminCompanies = $this->AdminCompanies->find()->order(['AdminCompanies.created' => 'DESC']);
        }
        
        $this->paginate = [
            'sortWhitelist' => ['alias', 'deputy_name'],
            'limit' => $query['records']
        ];
        if ($this->Auth->user('role_id') != 1) {
            // other user (not admin) can not view delete record
            $allJobs->where(['AdminCompanies.deleted' => FALSE]);
        }
        $adminCompanies = $this->paginate($adminCompanies);
        $this->set(compact('adminCompanies', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Admin Company id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $adminCompany = $this->AdminCompanies->get($id);
        $this->set(compact('adminCompany'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $adminCompany = $this->AdminCompanies->newEntity();
        if ($this->request->is('post')) {
            $adminCompany = $this->AdminCompanies->patchEntity($adminCompany, $this->request->getData());
            $adminCompany = $this->AdminCompanies->setAuthor($adminCompany, $this->Auth->user('id'), 'add');
            if ($this->AdminCompanies->save($adminCompany)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $adminCompany->alias
                ]));
                return $this->redirect(['action' => 'edit', $adminCompany->id]);
            } else {
                Log::write('debug', $adminCompany->errors());
                $this->Flash->error($this->errorMessage['add']);
            }
        }
        $this->set(compact('adminCompany'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Admin Company id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {   
        $adminCompany = $this->AdminCompanies->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $adminCompany = $this->AdminCompanies->patchEntity($adminCompany, $data);
            $adminCompany = $this->AdminCompanies->setAuthor($adminCompany, $this->Auth->user('id'), 'edit');
            if ($this->AdminCompanies->save($adminCompany)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $adminCompany->alias
                    ]));

                return $this->redirect(['action' => 'edit', $adminCompany->id]);
            }
            $this->Flash->error($this->errorMessage['error']);
        }
        $this->set(compact('adminCompany'));
        $this->render('/AdminCompanies/add');
    }

    /**
     * Delete method
     *
     * @param string|null $id Admin Company id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $adminCompany = $this->AdminCompanies->get($id);
        $adminCompany->deleted = TRUE;
        $adminCompany = $this->AdminCompanies->setAuthor($adminCompany, $this->Auth->user('id'), 'edit');
        if ($this->AdminCompanies->save($adminCompany)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $adminCompany->alias
                ]));
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }
        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $adminCompany = $this->AdminCompanies->get($id);
        $adminCompany->deleted = FALSE;
        $adminCompany = $this->AdminCompanies->setAuthor($adminCompany, $this->Auth->user('id'), 'edit');
        if ($this->AdminCompanies->save($adminCompany)) {
            $this->Flash->success(Text::insert($this->successMessage['recover'], [
                'entity' => $this->entity, 
                'name' => $adminCompany->alias
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                'entity' => $this->entity,
                'name' => $adminCompany->alias
                ]));
        }
        return $this->redirect(['action' => 'index']);
    }
}

<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Log\Log;
use Cake\Utility\Text;

/**
 * Installments Controller
 *
 * @property \App\Model\Table\InstallmentsTable $Installments
 *
 * @method \App\Model\Entity\Installment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InstallmentsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->entity = 'đợt thu phí';
    }

    public function isAuthorized($user)
    {
        if ($this->Auth->user('role')['name'] != 'admin') {
            return false;
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
            $allInstallments = $this->Installments->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['name']) && !empty($query['name'])) {
                $allInstallments->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%'.$query['name'].'%');
                });
            }
            if (isset($query['f_admin_company']) && !empty($query['f_admin_company'])) {
                $allInstallments->where(['Installments.admin_company_id' => $query['f_admin_company']]);
            }
            if (isset($query['f_created_by']) && !empty($query['f_created_by'])) {
                $allInstallments->where(['Installments.created_by' => $query['f_created_by']]);
            }
            if (isset($query['f_modified_by']) && !empty($query['f_modified_by'])) {
                $allInstallments->where(['Installments.modified_by' => $query['f_modified_by']]);
            }
            if (!isset($query['sort'])) {
                $allInstallments->order(['Installments.created' => 'DESC']);
            }
        }
        else {
            $query['records'] = $this->defaultDisplay;
            $allInstallments = $this->Installments->find()->order(['Installments.created' => 'DESC']);
        }
        $this->paginate = [
            'contain' => [
                'AdminCompanies',
                'CreatedByUsers',
                'ModifiedByUsers'
            ],
            'sortWhitelist' => ['name'],
            'limit' => $query['records'],
        ];

        $installments = $this->paginate($allInstallments);
        $adminCompanies = TableRegistry::get('AdminCompanies')->find('list')->where(['deleted' => FALSE])->toArray();
        $usersTable = TableRegistry::get('Users');
        $allUsers = $usersTable->find('list');
        $this->set(compact('installments', 'allUsers', 'adminCompanies', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Installment id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $installment = $this->Installments->get($id, [
            'contain' => [
                'AdminCompanies',
                'InstallmentFees' => ['sort' => ['name_romaji' => 'ASC']], 
                'InstallmentFees.Guilds'
            ]
        ]);
        $sbInstallmentActive = true;
        $this->set(compact('installment', 'sbInstallmentActive'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $installment = $this->Installments->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $installment = $this->Installments->patchEntity($installment, $data, ['associated' => ['InstallmentFees']]);
            $installment = $this->Installments->setAuthor($installment, $this->Auth->user('id'), $this->request->getParam('action'));
            if ($this->Installments->save($installment)) {
                $this->Flash->success($this->successMessage['addNoName']);
                return $this->redirect(['action' => 'edit', $installment->id]);
            }
            $this->Flash->error($this->errorMessage['add']);
        }
        $adminCompanies = TableRegistry::get('AdminCompanies')->find('list')->where(['deleted' => FALSE])->toArray();
        $guilds = TableRegistry::get('Guilds')->find('list');
        $sbInstallmentActive = true;
        $this->set(compact('installment', 'guilds', 'adminCompanies', 'sbInstallmentActive'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Installment id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $installment = $this->Installments->get($id, [
            'contain' => [
                'AdminCompanies',
                'InstallmentFees' => ['sort' => ['name_romaji' => 'ASC']], 
                'InstallmentFees.Guilds'
                ]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $installment = $this->Installments->patchEntity($installment, $data);
            $installment = $this->Installments->setAuthor($installment, $this->Auth->user('id'), $this->request->getParam('action'));
            if ($this->Installments->save($installment)) {
                $this->Flash->success($this->successMessage['addNoName']);
                return $this->redirect(['action' => 'edit', $id]);
            }
            $this->Flash->error($this->errorMessage['error']);
        }
        $adminCompanies = TableRegistry::get('AdminCompanies')->find('list')->where(['deleted' => FALSE])->toArray();
        $guilds = TableRegistry::get('Guilds')->find('list');
        $sbInstallmentActive = true;
        $this->set(compact('installment', 'guilds', 'adminCompanies', 'sbInstallmentActive'));
        $this->render('/Installments/add');

    }

    public function export($id = null)
    {
        
    }


    /**
     * Delete method
     *
     * @param string|null $id Installment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $installment = $this->Installments->get($id);
        if ($this->Installments->delete($installment)) {
            $this->Flash->success($this->successMessage['deleteNoName']);
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }
        return $this->redirect(['action' => 'index']);
    }

    public function deleteFees($id = null)
    {
        $this->request->allowMethod('ajax');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        try {
            $fees = $this->Installments->InstallmentFees->get($id);
            Log::write('debug', $fees);
            if ($this->Installments->InstallmentFees->delete($fees)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
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
}
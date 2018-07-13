<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\I18n\Time;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 *
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{
    
    public function isAuthorized($user)
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        $session = $this->request->session();
        $permissionsTable = TableRegistry::get('Permissions');
        $userPermission = $permissionsTable->find()->where(['user_id' => $user['id'], 'scope' => $controller])->first();

        if (!empty($userPermission)) {
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
        $now = Time::now()->i18nFormat('yyyy-MM-dd');
        
        if (!empty($query)) {
            $allOrders = $this->Orders->find();

            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
            if (isset($query['name']) && !empty($query['name'])) {
                $allOrders->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%' . $query['name'] . '%');
                });
            }
            if (isset($query['interview_date']) && !empty($query['interview_date'])) {
                $allOrders->where(['interview_date' => $query['interview_date']]);
            }
            if (isset($query['work_at']) && !empty($query['work_at'])) {
                $allOrders->where(['work_at' => $query['work_at']]);
            }
            if (isset($query['guild_id']) && !empty($query['guild_id'])) {
                $allOrders->where(['Companies.guild_id' => $query['guild_id']]);
            }
            if (isset($query['company_id']) && !empty($query['company_id'])) {
                $allOrders->where(['company_id' => $query['company_id']]);
            }
            if (isset($query['status']) && !empty($query['status'])) {
                switch ($query['status']) {
                    case "1":
                        $allOrders->where(['interview_date >' => $now]);
                        break;
                    case "2":
                        $allOrders->where(['interview_date' => $now]);
                        break;
                    case "3":
                        $allOrders->where(['interview_date <' => $now]);
                        break;
                    case "4":
                        $allOrders->where(['status' => "4"]);
                        break;
                }
            }
        } else {
            $query['records'] = 10;
            $allOrders = $this->Orders->find()->order(['Orders.created' => 'DESC']);
        }

        $this->paginate = [
            'contain' => [
                'Companies', 
                'Companies.Guilds', 
                'Jobs'
            ],
            'sortWhitelist' => ['name', 'interview_date'],
            'limit' => $query['records'],
        ];

        $orders = $this->paginate($allOrders);
        
        $jobs = $this->Orders->Jobs->find('list');
        $companies = $this->Orders->Companies->find('list');
        $guilds = $this->Orders->Companies->Guilds->find('list');

        $this->set(compact('orders', 'jobs', 'companies', 'guilds', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => ['Companies', 'Jobs', 'Students']
        ]);

        $this->set('order', $order);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $order = $this->Orders->newEntity();
        if ($this->request->is('post')) {
            $order = $this->Orders->patchEntity($order, $this->request->getData(), ['associated' => 'Students']);
            $order = $this->Orders->setAuthor($order, $this->Auth->user('id'), $this->request->getParam('action'));
            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The order has been saved.'));

                return $this->redirect(['action' => 'edit', $order->id]);
            }
            $this->Flash->error(__('The order could not be saved. Please, try again.'));
        }
        $companies = $this->Orders->Companies->find('list', ['limit' => 200]);
        $jobs = $this->Orders->Jobs->find('list', ['limit' => 200]);
        $this->set(compact('order', 'companies', 'jobs'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => ['Students']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $order = $this->Orders->patchEntity($order, $data, ['associated' => 'Students']);
            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The order has been saved.'));

                return $this->redirect(['action' => 'edit', $order->id]);
            }
            $this->Flash->error(__('The order could not be saved. Please, try again.'));
        }
        $companies = $this->Orders->Companies->find('list', ['limit' => 200]);
        $jobs = $this->Orders->Jobs->find('list', ['limit' => 200]);
        $this->set(compact('order', 'companies', 'jobs'));
        $this->render('/Orders/add');
    }

    /**
     * Delete method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $order = $this->Orders->get($id);
        if ($this->Orders->delete($order)) {
            $this->Flash->success(__('The order has been deleted.'));
        } else {
            $this->Flash->error(__('The order could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function close($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $order = $this->Orders->get($id);
            $order->status = '4'; // close constant
            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The order has been close.'));
            } else {
                $this->Flash->error(__('The order could not be close. Please, try again.'));
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->redirect(['action' => 'index']);
    }

    public function deleteCandidate()
    {
        $this->request->allowMethod('ajax');
        $interviewId = $this->request->getData('id');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Error',
                'type' => 'error',
                'message' => __('The candidate could not be deleted. Please, try again.')
            ]
        ];

        try {
            $table = TableRegistry::get('OrdersStudents');
            $interview = $table->get($interviewId);

            if (!empty($interview) && $table->delete($interview)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('The candidate has been deleted.')
                    ]
                ];
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }

        return $this->jsonResponse($resp);
    }

    public function searchCandidate()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        if (isset($query['q']) && !empty($query['q'])) {
            $students = $this->Orders->Students
                ->find('list', ['limit' => 200])
                ->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('fullname', '%'.$query['q'].'%');
                });
            $resp['items'] = $students;
        }
        return $this->jsonResponse($resp);        
    }

    public function recommendCandidate()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $candidates = $this->Orders->Students->find();
        if (!empty($query)) {
            if (isset($query['ageFrom']) 
                && isset($query['ageTo']) 
                && !empty($query['ageFrom'])
                && !empty($query['ageTo'])
                ) {
                $candidates->where(function (QueryExpression $exp, Query $q) use ($query) {
                    $now = Time::now();
                    $interval = (int) $query['ageTo'] - (int) $query['ageFrom'];
                    $minDate = $now->subYears((int) $query['ageTo'])->i18nFormat('yyyy-MM-dd');
                    $maxDate = $now->addYears($interval)->i18nFormat('yyyy-MM-dd');
                    return $exp->between('birthday', $minDate, $maxDate, 'date');
                });
            }

            if (isset($query['height']) && $query['height'] !== 0) {
                $candidates->where(['height >=' => (float) $query['height']]);
            }

            if (isset($query['weight']) && $query['weight'] !== 0) {
                $candidates->where(['weight >=' => (float) $query['weight']]);
            }

            if (isset($query['job']) && !empty($query['job'])) {
                $candidates->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('expectation', '%,'.$query['job'].',%');
                });
            }

            $now = Time::now();
            $candidates->formatResults(function ($results) use ($now) {
                return $results->map(function ($row) use ($now) {
                    $age = ($now->diff($row['birthday']))->y;
                    $row['age'] = $age;
                    return $row;
                });
            });
        }
        
        $resp = [
            'candidates' => $candidates
        ];
        
        return $this->jsonResponse($resp); 
    }

    public function getCandidate()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];

        try {
            if (isset($query['id']) && !empty($query['id'])) {
                $student = $this->Orders->Students->get($query['id']);
                $now = Time::now();
                $age = ($now->diff($student['birthday']))->y;
                $student['age'] = $age;
                $resp = $student;
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        
        return $this->jsonResponse($resp);   
    }
}

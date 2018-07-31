<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\I18n\Time;
use Cake\Utility\Text;
use Cake\I18n\Number;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 *
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{
    
    public function initialize()
    {
        parent::initialize();
        $this->entity = 'đơn hàng';
        $this->loadComponent('SystemEvent');
    }

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
            if (isset($query['created']) && !empty($query['created'])) {
                $allOrders->where(['Orders.created >=' => $query['created']]);
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
            'contain' => [
                'Companies', 
                'Companies.Guilds',
                'Jobs', 
                'Students'
                ]
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
            $data = $this->request->getData();
            // create system event
            $event = $this->SystemEvent->create('PHỎNG VẤN', $data['interview_date']);
            $data['events'][0] = $event;
            $order = $this->Orders->patchEntity($order, $data, ['associated' => ['Students', 'Events']]);
            $order = $this->Orders->setAuthor($order, $this->Auth->user('id'), $this->request->getParam('action'));

            if ($this->Orders->save($order)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $order->name
                ]));

                return $this->redirect(['action' => 'edit', $order->id]);
            }
            $this->Flash->error($this->errorMessage['add']);
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
            'contain' => ['Students', 'Events']
        ]);
        $orderName = $order->name;
        $currentInterviewDate = $order->interview_date;
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $newInterviewDate = new Time($data['interview_date']);
            if ($currentInterviewDate !== $newInterviewDate) {
                // uppdate system event
                $event = $this->SystemEvent->update($order->events[0]->id, $data['interview_date']);
            }
            $data['events'][0] = $event;
            $order = $this->Orders->patchEntity($order, $data, ['associated' => ['Students', 'Events']]);
            
            if ($this->Orders->save($order)) {
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity,
                    'name' => $order->name
                ]));
                return $this->redirect(['action' => 'edit', $order->id]);
            }

            $this->Flash->error(Text::insert($this->errorMessage['edit'], [
                'entity' => $this->entity,
                'name' => $orderName
            ]));
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
        $orderName = $order->name;
        if ($this->Orders->delete($order)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $orderName
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $orderName
                ]));
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
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];

        try {
            $table = TableRegistry::get('OrdersStudents');
            $interview = $table->find()->where(['OrdersStudents.id' => $interviewId])->contain(['Students'])->first();
            $candidateName = $interview->student->fullname;
            if (!empty($interview) && $table->delete($interview)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => Text::insert($this->successMessage['delete'], [
                            'entity' => 'ứng viên', 
                            'name' => $candidateName
                            ])
                    ]
                ];
            } else {
                $resp = [
                    'status' => 'error',
                    'alert' => [
                        'title' => 'Lỗi',
                        'type' => 'error',
                        'message' => Text::insert($this->errorMessage['delete'], [
                            'entity' => 'ứng viên', 
                            'name' => $candidateName
                            ])
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
                })
                ->where(['status <' => '3']);
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
                    $minDate = Time::now()->subYears((int) $query['ageTo'])->year . '-01-01';
                    $maxDate = Time::now()->subYears((int) $query['ageFrom'])->year . '-01-01';
                    return $exp->between('birthday', $minDate, $maxDate, 'date');
                });
            }

            if (isset($query['height']) && (int)$query['height'] !== 0) {
                Log::write('debug', $query['height']);
                $candidates->where(['height >=' => (float) $query['height']]);
            }

            if (isset($query['weight']) && (int)$query['weight'] !== 0) {
                Log::write('debug', $query['weight']);
                $candidates->where(['weight >=' => (float) $query['weight']]);
            }

            if (isset($query['job']) && !empty($query['job'])) {
                $candidates->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('expectation', '%,'.$query['job'].',%');
                });
            }
            $candidates->where(['status <' => '3']);
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

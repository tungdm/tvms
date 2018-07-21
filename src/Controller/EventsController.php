<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Exception\Exception;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\I18n\Time;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;


/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 *
 * @method \App\Model\Entity\Event[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EventsController extends AppController
{
    
    public function initialize()
    {
        parent::initialize();
        $this->entity = 'sự kiện';
    }

    public function isAuthorized($user)
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        $session = $this->request->session();
        $permissionsTable = TableRegistry::get('Permissions');
        $userPermission = $permissionsTable->find()->where(['user_id' => $user['id'], 'scope' => $controller])->first();

        // case: check permission on specific scope
        if (!empty($userPermission)) {
            return true;
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
        $controller = $this->request->getParam('controller');
        $eventScope = Configure::read('eventScope');
        $permissionsTable = TableRegistry::get('Permissions');
        $currentUser = $this->Auth->user();

        $userPermission = $permissionsTable->find()
            ->where([
                'user_id' => $currentUser['id'],
                'scope' => $controller,
                'action' => '0'
                ])
            ->first();
        $currentUserRole = $currentUser['role_id'];
        if ($currentUserRole != 1 && empty($userPermission)) {
            // not admin or full access user
            $eventScope = [
                '1' => 'Chỉ mình tôi'
            ];
        }
        $this->set(compact('eventScope'));
    }

    public function getEvents() 
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $currentUserId = $this->Auth->user('id');
        $resp = [];
        try {
            $events = $this->Events->find()
            ->where([
                'start >=' => $query['start'],
                'end <=' => $query['end'],
            ])
            ->where(function ($exp) use ($currentUserId) {
                $orCondition = $exp->or_(['user_id' => $currentUserId])->eq('scope', '2');
                return $exp->add($orCondition);
            });
            $events = $events->toArray();
            foreach ($events as $event) {
                $editable = false;
                if ($event->scope === "1" || $event->user_id == $currentUserId) {
                    $editable = true;
                }
                $data = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start' => $event->start,
                    'end' => $event->end,
                    'allDay' => $event->all_day === "true" ? true : false,
                    'scope' => $event->scope,
                    'backgroundColor' => $event->color,
                    'borderColor' => $event->color,
                    'editable' => $editable
                ];
                array_push($resp, $data);
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function getEvent()
    {
        $this->request->allowMethod('ajax');
        $id = $this->request->getQuery('id');
        $resp = [];
        try {
            $event = $this->Events->find()
                ->where(['Events.id' => $id])
                ->select($this->Events)
                ->select($this->Events->Orders)
                ->select($this->Events->Orders->Companies)
                ->select($this->Events->Orders->Companies->Guilds)
                ->select($this->Events->Orders->Jobs)
                ->select($this->Events->Users)
                ->select($this->Events->Jtests)
                ->select($this->Events->Jtests->Jclasses)
                ->select($this->Events->Jtests->JtestContents->Users)
                ->contain([
                    'Users', 
                    'Orders', 
                    'Orders.Students', 
                    'Orders.Companies', 
                    'Orders.Companies.Guilds', 
                    'Orders.Jobs',
                    'Jtests',
                    'Jtests.Jclasses',
                    'Jtests.JtestContents.Users'
                    ])
                ->first();
            $cityJP = Configure::read('cityJP');
            $cityJP = array_map('array_shift', $cityJP);
            $yesNoQuestion = Configure::read('yesNoQuestion');
            $interviewType = Configure::read('interviewType');
            $skills = Configure::read('skills');
            $lessons = Configure::read('lessons');

            if (!empty($event->order)) {
                $event->order->work_at = $cityJP[$event->order->work_at];
                $event->order->skill_test = $yesNoQuestion[$event->order->skill_test];
                $event->order->interview_type = $interviewType[$event->order->interview_type];
            } else if (!empty($event->jtest)) {
                $event->jtest->lesson_from = $lessons[$event->jtest->lesson_from];
                $event->jtest->lesson_to = $lessons[$event->jtest->lesson_to];
                foreach ($event->jtest->jtest_contents as $key => $value) {
                    $event->jtest->jtest_contents[$key]->skill = $skills[$value->skill];
                }
            }
            $resp = $event;
            $resp['all_day'] == "true" ? true : false;
            $resp['owner'] = empty($event->user_id) ? 'Thông báo từ hệ thống' : $event->user->fullname;
        } catch (Exception $e) {
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
        $this->request->allowMethod('ajax');
        $resp = [];
        if ($this->request->is('post')) {
            $resp = [
                'status' => 'error',
                'flash' => [
                    'title' => 'Lỗi',
                    'type' => 'error',
                    'icon' => 'fa fa-warning',
                    'message' => $this->errorMessage['add']
                ]
            ];

            $event = $this->Events->newEntity();
            $data = $this->request->getData();
            
            $currentUser = $this->Auth->user();
            $currentUserRole = $currentUser['role_id'];
            $permissionsTable = TableRegistry::get('Permissions');
            $controller = $this->request->getParam('controller');
            $userPermission = $permissionsTable->find()
                ->where([
                    'user_id' => $currentUser['id'],
                    'scope' => $controller,
                    'action' => '0'
                    ])
                ->first();
            if ($data['scope'] === "2" && $currentUserRole != 1 && empty($userPermission)) {
                //TODO: Blacklist current user
                $msgTemplate = Configure::read('blackListTemplate');
                $msg = Text::insert($msgTemplate, [
                    'username' => $currentUser['username'], 
                    'error' => 'try to create global event'
                    ]);
                Log::write('warning', $msg);
                return $this->jsonResponse($resp); 
            }
            $data['user_id'] = $this->Auth->user('id');            
            $data['start'] = new Time($data['start']);
            $data['end'] = new Time($data['end']);

            $event = $this->Events->patchEntity($event, $data);
            $event = $this->Events->setAuthor($event, $this->Auth->user('id'), $this->request->getParam('action'));
            
            if ($this->Events->save($event)) {
                $resp = [
                    'status' => 'success',
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start,
                    'end' => $event->end,
                    'allDay' => $event->all_day == "true" ? true: false,
                    'backgroundColor' => $event->color,
                    'borderColor' => $event->color,
                    'flash' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'icon' => 'fa fa-check-circle-o',
                        'message' => Text::insert($this->successMessage['add'], [
                            'entity' => $this->entity,
                            'name' => $event->title
                        ])
                    ]
                ];
            }
        }
        return $this->jsonResponse($resp);
    }

    /**
     * Edit method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->request->allowMethod('ajax');
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
            $event = $this->Events->get($id, [
                'contain' => []
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();
                $data['start'] = new Time($data['start']);
                $data['end'] = new Time($data['end']);
    
                $event = $this->Events->patchEntity($event, $data);
                $event = $this->Events->setAuthor($event, $this->Auth->user('id'), $this->request->getParam('action'));
                
                if ($this->Events->save($event)) {
                    $resp = [
                        'status' => 'success',
                        'id' => $event->id,
                        'title' => $event->title,
                        'start' => $event->start,
                        'end' => $event->end,
                        'allDay' => $event->all_day === "true" ? true: false,
                        'backgroundColor' => $event->color,
                        'borderColor' => $event->color,
                        'flash' => [
                            'title' => 'Thành Công',
                            'type' => 'success',
                            'icon' => 'fa fa-check-circle-o',
                            'message' => Text::insert($this->successMessage['edit'], [
                                'entity' => $this->entity,
                                'name' => $event->title
                            ])
                        ]
                    ];
                } else {
                    Log::write('debug', $event->errors());
                }
            }
        }  catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function editDuration($id = null)
    {
        $this->request->allowMethod('ajax');
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
            $event = $this->Events->get($id, [
                'contain' => []
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();
                Log::write('debug', $data);
                $data['start'] = new Time($data['start']);
                $data['end'] = new Time($data['end']);
    
                $event = $this->Events->patchEntity($event, $data);
                $event = $this->Events->setAuthor($event, $this->Auth->user('id'), 'edit');
                if ($this->Events->save($event)) {
                    $resp = [
                        'status' => 'success',
                        'id' => $event->id,
                        'title' => $event->title,
                        'start' => $event->start,
                        'end' => $event->end,
                        'allDay' => $event->all_day == "true" ? true: false,
                        'backgroundColor' => $event->color,
                        'borderColor' => $event->color,
                        'flash' => [
                            'title' => 'Thành Công',
                            'type' => 'success',
                            'icon' => 'fa fa-check-circle-o',
                            'message' => Text::insert($this->successMessage['edit'], [
                                'entity' => $this->entity,
                                'name' => $event->title
                            ])
                        ]
                    ];
                }
            }
        }  catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    /**
     * Delete method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod('ajax');
        $resp = [];

        if ($this->request->is(['post', 'delete'])) {
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
                $event = $this->Events->get($id);
                $eventTitle = $event->title;
                if (!empty($event) && $this->Events->delete($event)) {
                    $resp = [
                        'status' => 'success',
                        'flash' => [
                            'title' => 'Thành Công',
                            'type' => 'success',
                            'icon' => 'fa fa-check-circle-o',
                            'message' => Text::insert($this->successMessage['delete'], [
                                'entity' => $this->entity,
                                'name' => $eventTitle
                            ])
                        ]
                    ];
                }
            } catch (Exception $e) {
                //TODO: blacklist user
                Log::write('debug', $e);
            }
        }
        return $this->jsonResponse($resp);
    }
}

<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Exception\Exception;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\I18n\Time;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 *
 * @method \App\Model\Entity\Event[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EventsController extends AppController
{
    public function isAuthorized($user)
    {
        // all authorized user can access event
        return true;
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $eventScope = Configure::read('eventScope');

        $currentUser = $this->Auth->user();
        $currentUserRole = $currentUser['role_id'];
        if ($currentUserRole != 1) {
            // not admin
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
            Log::write('debug', $events);
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

        Log::write('debug', $id);

        $resp = [];
        try {
            $event = $this->Events->get($id, ['contain' => 'Users']);
            Log::write('debug', $event);
            $resp = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'allDay' => $event->all_day == "true" ? true: false,
                'scope' => $event->scope,
                'start' => $event->start,
                'end' => $event->end,
                'backgroundColor' => $event->color,
                'borderColor' => $event->color,
                'owner' => $event->user->fullname
            ];
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
            ];
            $event = $this->Events->newEntity();
            $data = $this->request->getData();
            Log::write('debug', $data);
            
            $currentUser = $this->Auth->user();
            $currentUserRole = $currentUser['role_id'];
            if ($data['scope'] === "2" && $currentUserRole != 1) {
                //TODO: Blacklist current user
                $msgTemplate = Configure::read('blackListTemplate');
                $msg = Text::insert($msgTemplate, [
                    'username' => $currentUser['username'], 
                    'error' => 'try to create global eventn'
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
                    ];
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
                'alert' => [
                    'title' => 'Error',
                    'type' => 'error',
                    'message' => __('The event could not be deleted. Please, try again.')
                ]
            ];
            try {
                $event = $this->Events->get($id);
                if (!empty($event) && $this->Events->delete($event)) {
                    $resp = [
                        'status' => 'success',
                        'alert' => [
                            'title' => 'Success',
                            'type' => 'success',
                            'message' => __('The event has been deleted.')
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

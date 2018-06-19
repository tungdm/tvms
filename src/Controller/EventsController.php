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
                '1' => 'Only me'
            ];
        }
        $this->set(compact('eventScope'));
    }

    public function getEvents() 
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $currentUserId = $this->Auth->user('id');
        Log::write('debug', $currentUserId);
        
        $resp = [];
        try {
            $events = $this->Events->find()->where([
                'start >=' => $query['start'],
                'end <=' => $query['end'],
                'user_id' => $currentUserId
            ])->toArray();
            foreach ($events as $value) {
                $data = [
                    'id' => $value->id,
                    'title' => $value->title,
                    'start' => $value->start,
                    'end' => $value->end,
                    'allDay' => $value->all_day == 1 ? true: false,
                    'backgroundColor' => $value->color,
                    'borderColor' => $value->color,
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
            $event = $this->Events->get($id);
            $resp = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'allDay' => $event->all_day == 1 ? true: false,
                'scope' => $event->scope,
                'start' => $event->start,
                'end' => $event->end,
                'backgroundColor' => $event->color,
                'borderColor' => $event->color,
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
            $event = $this->Events->newEntity();
            $data = $this->request->getData();
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
                    'allDay' => $event->all_day == 1 ? true: false,
                    'backgroundColor' => $event->color,
                    'borderColor' => $event->color,
                ];
            } else {
                $resp = [
                    'status' => 'error',
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
        $event = $this->Events->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $users = $this->Events->Users->find('list', ['limit' => 200]);
        $this->set(compact('event', 'users'));
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
        $this->request->allowMethod(['post', 'delete']);
        $event = $this->Events->get($id);
        if ($this->Events->delete($event)) {
            $this->Flash->success(__('The event has been deleted.'));
        } else {
            $this->Flash->error(__('The event could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

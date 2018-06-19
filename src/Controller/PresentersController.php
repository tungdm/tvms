<?php
namespace App\Controller;

use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Core\Configure;
use App\Controller\AppController;    
use Cake\Log\Log;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;

/**
 * Presenters Controller
 *
 * @property \App\Model\Table\PresentersTable $Presenters
 *
 * @method \App\Model\Entity\Presenter[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PresentersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $query = $this->request->getQuery();
        
        $this->paginate = [
            'sortWhitelist' => ['name', 'address', 'phone','type'],
            'limit' => 10
        ];
        $allPresenters = $this->Presenters->find();
        if (!empty($query)) {
            if (isset($query['name']) && !empty($query['name'])) {
                $allPresenters->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%'.$query['name'].'%');
                });
            }
            if (isset($query['address']) && !empty($query['address'])) {
                $allPresenters->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('address', '%'.$query['address'].'%');
                });
            }
            if (isset($query['phone']) && !empty($query['phone'])) {
                $allPresenters->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('phone', '%'.$query['phone'].'%');
                });
            }
            if (isset($query['type']) && !empty($query['type'])) {
                $allPresenters->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('type', '%'.$query['type'].'%');
                });
            }
        }

        $presenters = $this->paginate($allPresenters);
        $this->set(compact('presenters', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Presenter id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $presenter = $this->Presenters->get($id, [
            'contain' => []
        ]);

        $this->set('presenter', $presenter);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $presenter = $this->Presenters->newEntity();
        if ($this->request->is('post')) {
            $presenter = $this->Presenters->patchEntity($presenter, $this->request->getData());
            if ($this->Presenters->save($presenter)) {
                $this->Flash->success(__('The presenter has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The presenter could not be saved. Please, try again.'));
        }
        
    }

    /**
     * Edit method
     *
     * @param string|null $id Presenter id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            $resp = [];
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();
                $presenter = $this->Presenters->get($data['id'], [
                    'contain' => []
                ]);
                $presenter = $this->Presenters->patchEntity($presenter, $data, [
                    'fieldList' => ['name', 'address', 'phone','type']
                ]);
                $presenter = $this->Presenters->setAuthor($presenter, $this->Auth->user('id'), $this->request->getParam('action'));
                if ($this->Presenters->save($presenter)) {
                    $resp = [
                        'status' => 'success',
                        'redirect' => Router::url(['action' => 'index']),
                        'flash' => [
                            'title' => 'Success',
                            'type' => 'success',
                            'message' => __('Chỉnh sửa thành công !')
                        ]
                    ];
                    $this->Flash->success(__('Thông tin đã được thay đổi.'));
                } else {
                    $resp = [
                        'status' => 'error',
                        'flash' => [
                            'title' => 'Error',
                            'type' => 'error',
                            'message' => __('Thao tác chưa thành công. Xin vui lòng thử lại !')
                        ]
                    ];
                }
            } else {
                $presenterId = $this->request->getQuery('id');
                // get guild data
                $presenter = $this->Presenters->get($presenterId, [
                    'contain' => []
                ]);
                $resp = $presenter;
            }
            return $this->jsonResponse($resp);
        } else {
            //TODO: throw 404 page not found
            
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Presenter id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $presenter = $this->Presenters->get($id);
        if ($this->Presenters->delete($presenter)) {
            $this->Flash->success(__('The presenter has been deleted.'));
        } else {
            $this->Flash->error(__('The presenter could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
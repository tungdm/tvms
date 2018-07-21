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
use Cake\Utility\Text;


/**
 * Presenters Controller
 *
 * @property \App\Model\Table\PresentersTable $Presenters
 *
 * @method \App\Model\Entity\Presenter[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PresentersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->entity = 'cộng tác viên';
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
        
        if (!empty($query)) {
            $allPresenters = $this->Presenters->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
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
        } else {
            $query['records'] = 10;
            $allPresenters = $this->Presenters->find()->order(['created' => 'DESC']);
        }

        $this->paginate = [
            'sortWhitelist' => ['name', 'address', 'phone'],
            'limit' => $query['records']
        ];

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
    public function view()
    {
        $this->request->allowMethod('ajax');
        $presenterId = $this->request->getQuery('id');
        $resp = [];

        try {
            $presenter = $this->Presenters->get($presenterId);
            $resp = $presenter;
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
        $presenter = $this->Presenters->newEntity();
        if ($this->request->is('post')) {
            $presenter = $this->Presenters->patchEntity($presenter, $this->request->getData());
            if ($this->Presenters->save($presenter)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $presenter->name
                ]));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error($this->errorMessage['add']);
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
                    ];
                    $this->Flash->success(Text::insert($this->successMessage['edit'], [
                        'entity' => $this->entity,
                        'name' => $presenter->name
                    ]));
                } else {
                    $resp = [
                        'status' => 'error',
                        'flash' => [
                            'title' => 'Lỗi',
                            'type' => 'error',
                            'message' => Text::insert($this->errorMessage['edit'], [
                                'entity' => $this->entity,
                                'name' => $presenter->name
                            ])
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
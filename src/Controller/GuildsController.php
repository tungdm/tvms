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
 * Guilds Controller
 *
 * @property \App\Model\Table\GuildsTable $Guilds
 *
 * @method \App\Model\Entity\Guild[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GuildsController extends AppController
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

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'nghiệp đoàn';
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
            $allGuilds = $this->Guilds->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
            if (isset($query['name']) && !empty($query['name'])) {
                $allGuilds->where(function (QueryExpression $exp, Query $q) use ($query) {
                    $orConditions = $exp->or_(function ($or) use ($query) {
                        return $or->like('Guilds.name_kanji', '%'. trim($query['name']) .'%')
                            ->like('Guilds.name_romaji', '%'. trim($query['name']) .'%');
                    });
                    return $exp->add($orConditions);
                });
            }
            if (isset($query['address']) && !empty($query['address'])) {
                $allGuilds->where(function (QueryExpression $exp, Query $q) use ($query) {
                    $orConditions = $exp->or_(function ($or) use ($query) {
                        return $or->like('Guilds.address_romaji', '%'.$query['address'].'%')
                            ->like('Guilds.address_kanji', '%'.$query['address'].'%');
                    });
                    return $exp->add($orConditions);
                });
            }
            if (isset($query['phone_vn']) && !empty($query['phone_vn'])) {
                $allGuilds->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('phone_vn', '%'.$query['phone_vn'].'%');
                });
            }
            if (isset($query['phone_jp']) && !empty($query['phone_jp'])) {
                $allGuilds->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('phone_jp', '%'.$query['phone_jp'].'%');
                });
            }
        } else {
            $query['records'] = 10;
            $allGuilds = $this->Guilds->find()->order(['Guilds.created' => 'DESC']);
        }
        $this->paginate = [
            'sortWhitelist' => ['name_romaji','name_kanji', 'address_romaji', 'address_kanji', 'phone_vn', 'phone_jp'],
            'limit' => $query['records']
        ];
        $guilds = $this->paginate($allGuilds);
        $this->set(compact('guilds', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Guild id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view()
    {
        $this->request->allowMethod('ajax');
        $guildId = $this->request->getQuery('id');
        $resp = [];

        try {
            $guild = $this->Guilds->get($guildId);
            $resp = $guild;
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
        $guild = $this->Guilds->newEntity();
        if($this->request->is('ajax')) {
            $resp = [];
            $guild = $this->Guilds->patchEntity($guild, $this->request->getData());
            $guild = $this->Guilds->setAuthor($guild, $this->Auth->user('id'), $this->request->getParam('action'));
            if($this->Guilds->save($guild)) {
                $resp = [
                    'status' => 'success',
                    'redirect' => Router::url(['action' => 'index']),
                ];    
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $guild->name_romaji
                ]));
            }
        } else {
            $resp = [
                'status' => 'error',
                'flash' => [
                    'title' => 'Lỗi',
                    'type' => 'error',
                    'icon' => 'fa fa-warning',
                    'message' => $this->errorMessage['add']
                ]
            ];
        }
        return $this->jsonResponse($resp);
    }

    /**
     * Edit method
     *
     * @param string|null $id Guild id.
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
                $guild = $this->Guilds->get($data['id'], [
                    'contain' => []
                ]);
                $guildName = $guild->name_romaji;

                $guild = $this->Guilds->patchEntity($guild, $data);
                $guild = $this->Guilds->setAuthor($guild, $this->Auth->user('id'), $this->request->getParam('action'));
                if ($this->Guilds->save($guild)) {
                    $resp = [
                        'status' => 'success',
                        'redirect' => Router::url(['action' => 'index']),
                    ];
                    $this->Flash->success(Text::insert($this->successMessage['edit'], [
                        'entity' => $this->entity, 
                        'name' => $guild->name_romaji
                        ]));
                } else {
                    $resp = [
                        'status' => 'error',
                        'flash' => [
                            'title' => 'Lỗi',
                            'type' => 'error',
                            'icon' => 'fa fa-warning',
                            'message' => Text::insert($this->errorMessage['edit'], [
                                'entity' => $this->entity,
                                'name' => $guildName
                            ])
                        ]
                    ];
                }
            } else {
                $guildId = $this->request->getQuery('id');
                // get guild data
                $guild = $this->Guilds->get($guildId, [
                    'contain' => []
                ]);
                $resp = $guild;
            }
            return $this->jsonResponse($resp);
        } else {
            //TODO: throw 404 page not found
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Guild id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $guild = $this->Guilds->get($id);
        $guildName = $guild->name_romaji;
        if ($this->Guilds->delete($guild)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $guildName
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $guildName
            ]));
        }

        return $this->redirect(['action' => 'index']);
    }
}



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
 * Companies Controller
 *
 * @property \App\Model\Table\CompaniesTable $Companies
 *
 * @method \App\Model\Entity\Company[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CompaniesController extends AppController
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
        $this->entity = 'công ty';
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
            $allCompanies = $this->Companies->find();

            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
            if (isset($query['name']) && !empty($query['name'])) {
                $allCompanies->where(function (QueryExpression $exp, Query $q) use ($query) {
                    $orConditions = $exp->or_(function ($or) use ($query) {
                        return $or->like('Companies.name_kanji', '%'.$query['name'].'%')
                            ->like('Companies.name_romaji', '%'.$query['name'].'%');
                    });
                    return $exp->add($orConditions);
                });
            }
            if (isset($query['address']) && !empty($query['address'])) {
                $allCompanies->where(function (QueryExpression $exp, Query $q) use ($query) {
                    $orConditions = $exp->or_(function ($or) use ($query) {
                        return $or->like('Companies.address_romaji', '%'.$query['address'].'%')
                            ->like('Companies.address_kanji', '%'.$query['address'].'%');
                    });
                    return $exp->add($orConditions);
                });
            }
            if (isset($query['guild']) && !empty($query['guild'])) {
                $allCompanies->where(['guild_id' => $query['guild']]);
            }
            if (isset($query['phone_vn']) && !empty($query['phone_vn'])) {
                $allCompanies->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('Companies.phone_vn', '%'.$query['phone_vn'].'%');
                });
            }
            if (isset($query['phone_jp']) && !empty($query['phone_jp'])) {
                $allCompanies->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('Companies.phone_jp', '%'.$query['phone_jp'].'%');
                });
            }
        } else {
            $query['records'] = 10;
            $allCompanies = $this->Companies->find()->order(['Companies.created' => 'DESC']);
        }

        $this->paginate = [
            'sortWhitelist' => ['name_romaji','name_kanji','address_romaji', 'address_kanji', 'phone_vn','phone_jp'],
            'contain' => ['Guilds'],
            'limit' => $query['records']
        ];

        $companies = $this->paginate($allCompanies);
        $guilds = $this->Companies->Guilds->find('list');

        $this->set(compact('companies', 'guilds', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->request->allowMethod('ajax');
        $companyId = $this->request->getQuery('id');
        $resp = [];

        try {
            $guild = $this->Companies->get($companyId, [
                'contain' => ['Guilds']
            ]);
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
        $company = $this->Companies->newEntity();
        if ($this->request->is('post')) {
            $company = $this->Companies->patchEntity($company, $this->request->getData());
            if ($this->Companies->save($company)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $company->name_romaji
                ]));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error($this->errorMessage['add']);
        }
        $this->set(compact('company'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Company id.
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
                $company = $this->Companies->get($data['id'], [
                    'contain' => []
                ]);
                $company = $this->Companies->patchEntity($company, $data);
                $company = $this->Companies->setAuthor($company, $this->Auth->user('id'), $this->request->getParam('action'));
                if ($this->Companies->save($company)) {
                    $resp = [
                        'status' => 'success',
                        'redirect' => Router::url(['action' => 'index']),
                    ];
                    $this->Flash->success(Text::insert($this->successMessage['edit'], [
                        'entity' => $this->entity,
                        'name' => $company->name_romaji
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
                                'name' => $company->name_romaji
                            ])
                        ]
                    ];
                }
            } else {
                $companyId = $this->request->getQuery('id');
                // get guild data
                $company = $this->Companies->get($companyId, [
                    'contain' => []
                ]);
                $resp = $company;
            }
            return $this->jsonResponse($resp);
        } else {
            //TODO: throw 404 page not found
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $company = $this->Companies->get($id);
        $companyName = $company->name_romaji;
        if ($this->Companies->delete($company)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $companyName
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $companyName
            ]));
        }

        return $this->redirect(['action' => 'index']);
    }
}

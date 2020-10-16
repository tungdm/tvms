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
            // only admin can recover deleted record
            if ($action == 'recover') {
                return false;
            }
            if ($userPermission->action == 0 || ($userPermission->action == 1 && in_array($action, ['index', 'view', 'searchGuild']))) {
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
                $query['records'] = $this->defaultDisplay;
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
            if (!isset($query['sort'])) {
                $allGuilds->order(['Guilds.created' => 'DESC']);
            }
        } else {
            $query['records'] = $this->defaultDisplay;
            $allGuilds = $this->Guilds->find()->order(['Guilds.created' => 'DESC']);
        }
        $deleted = false;
        if (isset($query['deleted']) && $this->Auth->user('role_id') == 1) {
            $deleted = $query['deleted'];
        }
        $allGuilds->where(['Guilds.del_flag' => $deleted]);
        $query['deleted'] = $deleted;

        $this->paginate = [
            'sortWhitelist' => ['name_romaji','name_kanji', 'address_romaji', 'address_kanji', 'phone_vn', 'phone_jp'],
            'limit' => $query['records']
        ];
        $guilds = $this->paginate($allGuilds);
        $companies = $this->Guilds->Companies->find('list')
                    ->where([
                        'type' => '2',
                        'del_flag' => FALSE
                        ])
                    ->order(['name_romaji' => 'ASC']); // cty tiep nhan
        $allCompanies = $this->Guilds->Companies->find('list')
                    ->where(['type' => '2'])
                    ->order(['name_romaji' => 'ASC']); // cty tiep nhan
        $this->set(compact('guilds', 'companies', 'allCompanies', 'query'));
    }

    public function searchGuild()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        if (isset($query['q']) && !empty($query['q'])) {
            $guilds = $this->Guilds
                ->find('list')
                ->where(['del_flag' => FALSE])
                ->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name_romaji', '%'.$query['q'].'%');
                });
            $resp['items'] = $guilds;
        }
        return $this->jsonResponse($resp);   
    }

    public function view($id = null)
    {
        $guild = $this->Guilds->get($id, [
            'contain' => [
                'CreatedByUsers',
                'ModifiedByUsers',
                'Companies' => ['sort' => ['Companies.name_romaji' => 'ASC']],
                'AdminCompanies' => function($q) {
                    return $q->where(['AdminCompanies.deleted' => FALSE])->order(['AdminCompanies.alias' => 'ASC']);
                },
                // 'InstallmentFees' => ['sort' => ['Installments.name' => 'ASC']],
                'InstallmentFees' => ['sort' => [
                    // 'Installments.created' => 'DESC',
                    'InstallmentFees.invoice_date' => 'DESC',
                    'InstallmentFees.installment_id' => 'ASC'
                ]],
                'InstallmentFees.Installments'
            ]
        ]);
        $this->checkDeleteFlag($guild, $this->Auth->user());
        $this->set(compact('guild'));
    }


    /**
     * View method
     *
     * @param string|null $id Guild id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewJson()
    {
        $this->request->allowMethod('ajax');
        $guildId = $this->request->getQuery('id');
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
            $guild = $this->Guilds->get($guildId, [
                'contain' => [
                    'CreatedByUsers',
                    'ModifiedByUsers',
                    'Companies' => function($q) {
                        return $q->where(['Companies.del_flag' => FALSE]);
                    },
                    'AdminCompanies' => function($q) {
                        return $q->where(['AdminCompanies.deleted' => FALSE])->order(['AdminCompanies.alias' => 'ASC']);
                    },
                ]
            ]);
            $resp = [
                'status' => 'success',
                'data' => $guild,
                'created' => $guild->created->i18nFormat('dd-MM-yyyy HH:mm:ss'),
                'modified' => $guild->modified->i18nFormat('dd-MM-yyyy HH:mm:ss')
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
        $guild = $this->Guilds->newEntity();
        $companies = $this->Guilds->Companies->find('list')
                    ->where([
                        'type' => '2',
                        'del_flag' => FALSE
                        ])
                    ->order(['name_romaji' => 'ASC']); // cty tiep nhan
        $adminCompanies = $this->Guilds->AdminCompanies->find('list')
                    ->where(['deleted' => FALSE])
                    ->order(['alias' => 'ASC']);
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $guild = $this->Guilds->patchEntity($guild, $data, ['associated' => ['Companies', 'AdminCompanies']]);
            $guild = $this->Guilds->setAuthor($guild, $this->Auth->user('id'), 'add');
            if ($this->Guilds->save($guild)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $guild->name_romaji
                ]));
            } else {
                $this->Flash->error($this->errorMessage['add']);
            }
            return $this->redirect(['action' => 'index']);
        }
        $this->set(compact('guild', 'companies', 'adminCompanies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Guild id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $companies = $this->Guilds->Companies->find('list')
                    ->where([
                        'type' => '2', // cty tiep nhan
                        'del_flag' => FALSE
                        ])
                    ->order(['name_romaji' => 'ASC']); 
        $adminCompanies = $this->Guilds->AdminCompanies->find('list')
                    ->where(['deleted' => FALSE])
                    ->order(['alias' => 'ASC']);
        // get guild data
        $guild = $this->Guilds->get($id, [
            'contain' => [
                'Companies' => function($q) {
                    return $q->where(['Companies.del_flag' => FALSE])->order(['Companies.name_romaji' => 'ASC']);
                },
                'AdminCompanies' => function($q) {
                    return $q->where(['AdminCompanies.deleted' => FALSE])->order(['AdminCompanies.alias' => 'ASC']);
                }
            ]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $delCompanies = [];
            if (!empty($guild->companies)) {
                foreach ($guild->companies as $key => $company) {
                    if ($company->_joinData->del_flag) {
                        $delCom = [
                            'id' => $company->id,
                            '_joinData' => [
                                'modified_by' => $this->Auth->user('id'),
                                'id' => $company->_joinData->id,
                                'del_flag' => TRUE
                            ]
                        ];
                        array_push($data['companies'], $delCom);
                    }
                }
            }
            $guild = $this->Guilds->patchEntity($guild, $data, ['associated' => ['Companies', 'AdminCompanies']]);
            $guild = $this->Guilds->setAuthor($guild, $this->Auth->user('id'), 'edit');
            if ($this->Guilds->save($guild)) {
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity,
                    'name' => $guild->name_romaji
                ]));
                return $this->redirect(['action' => 'edit', $id]);
            }
            $this->Flash->error($this->errorMessage['error']);
        }
        $this->set(compact('guild', 'companies', 'adminCompanies'));
        $this->render('/Guilds/add');
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
        $guild = $this->Guilds->get($id, ['contain' => 'Companies']);
        $data = [
            'del_flag' => TRUE, // guild del_flag
        ];
        // if (!empty($guild->companies)) {
        //     foreach ($guild->companies as $key => $company) {
        //         $data['companies'][$key]['id'] = $company->id;
        //         $data['companies'][$key]['_joinData']['del_flag'] = TRUE;
        //         $data['companies'][$key]['_joinData']['modified_by'] = $this->Auth->user('id');
        //     }
        // }

        $guild = $this->Guilds->patchEntity($guild, $data, ['associated' => ['Companies']]);
        $guild = $this->Guilds->setAuthor($guild, $this->Auth->user('id'), 'edit');
        if ($this->Guilds->save($guild)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $guild->name_romaji
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $guild->name_romaji
            ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $guild = $this->Guilds->get($id, ['contain' => 'Companies']);
        $data = [
            'del_flag' => FALSE, // guild del_flag
        ];
        if (!empty($guild->companies)) {
            foreach ($guild->companies as $key => $company) {
                $data['companies'][$key]['id'] = $company->id;
                $data['companies'][$key]['_joinData']['del_flag'] = FALSE;
                $data['companies'][$key]['_joinData']['modified_by'] = $this->Auth->user('id');
            }
        }
        $guild = $this->Guilds->patchEntity($guild, $data, ['associated' => ['Companies']]);
        $guild = $this->Guilds->setAuthor($guild, $this->Auth->user('id'), 'edit');
        if ($this->Guilds->save($guild)) {
            $this->Flash->success(Text::insert($this->successMessage['recover'], [
                'entity' => $this->entity, 
                'name' => $guild->name_romaji
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['recover'], [
                'entity' => $this->entity,
                'name' => $guild->name_romaji
            ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function deleteGuildCompany()
    {
        $this->request->allowMethod('ajax');
        
        $recordId = $this->request->getData('recordId');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];

        $table = TableRegistry::get('GuildsCompanies');
        $record = $table->get($recordId);
        $guildId = $record->guild_id;
        $guild = $this->Guilds->get($guildId);
        $guild = $this->Guilds->setAuthor($guild, $this->Auth->user('id'), 'edit');
        if ($table->delete($record) && $this->Guilds->save($guild)) {
            $resp = [
                'status' => 'success',
                'alert' => [
                    'title' => 'Thành Công',
                    'type' => 'success',
                    'message' => 'Đã xóa thành công dữ liệu'
                ]
            ];
        }

        return $this->jsonResponse($resp);
    }

    public function deleteGuildAdminCompany($id = null)
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
            $table = TableRegistry::get('GuildsAdminCompanies');
            $record = $table->get($id);
            $guildId = $record->guild_id;
            $guild = $this->Guilds->get($guildId);
            $guild = $this->Guilds->setAuthor($guild, $this->Auth->user('id'), 'edit');
            if ($table->delete($record) && $this->Guilds->save($guild)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => 'Đã xóa thành công dữ liệu'
                    ]
                ];
            }
    
        } catch (Exception $e) {
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    public function migrate()
    {
        $guilds = $this->Guilds->find('all')->where(['del_flag' => false]);
        foreach ($guilds as $key => $guild) {
            $data = [
                'admin_companies' => [
                    (int) 1 => [
                        'id' => '1',
                        '_joinData' => [
                            'subsidy' => $guild->subsidy,
                            'first_three_years_fee' => $guild->first_three_years_fee,
                            'two_years_later_fee' => $guild->two_years_later_fee,
                            'pre_training_fee' => $guild->pre_training_fee
                        ]
                    ]
                ]
            ];
            $guild = $this->Guilds->patchEntity($guild, $data, ['associated' => ['AdminCompanies']]);
            $this->Guilds->save($guild);
        }
        return $this->redirect(['action' => 'index']);
    }

    public function recoverCompany()
    {
        $this->request->allowMethod('ajax');
        
        $recordId = $this->request->getData('recordId');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];

        $table = TableRegistry::get('GuildsCompanies');
        $record = $table->get($recordId);
        $record->del_flag = FALSE;
        $record->modified_by = $this->Auth->user('id');

        $guildId = $record->guild_id;
        $guild = $this->Guilds->get($guildId);
        $guild = $this->Guilds->setAuthor($guild, $this->Auth->user('id'), 'edit');

        $companyId = $record->company_id;
        $company = $this->Guilds->Companies->get($companyId);
        if ($company->del_flag) {
            $company->del_flag = FALSE;
            $company = $this->Guilds->Companies->setAuthor($company, $this->Auth->user('id'), 'edit');
        }

        if ($table->save($record) && $this->Guilds->save($guild) && $this->Guilds->Companies->save($company)) {
            $resp = [
                'status' => 'success',
                'admin' => $this->Auth->user('role_id') == 1,
                'alert' => [
                    'title' => 'Thành Công',
                    'type' => 'success',
                    'message' => 'Đã phục hồi thành công dữ liệu'
                ]
            ];
        }

        return $this->jsonResponse($resp);
    }
}



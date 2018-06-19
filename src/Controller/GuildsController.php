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
 * Guilds Controller
 *
 * @property \App\Model\Table\GuildsTable $Guilds
 *
 * @method \App\Model\Entity\Guild[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GuildsController extends AppController
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
            'sortWhitelist' => ['name_romaji','name_kanji', 'address_romaji', 'address_kanji', 'phone_vn', 'phone_jp'],
            'limit' => 10
        ];
        $allGuilds = $this->Guilds->find();
        if (!empty($query)) {
            if (isset($query['name_romaji']) && !empty($query['name_romaji'])) {
                $allGuilds->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name_romaji', '%'.$query['name_romaji'].'%');
                });
            }
            if (isset($query['name_kanji']) && !empty($query['name_kanji'])) {
                $allGuilds->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name_kanji', '%'.$query['name_kanji'].'%');
                });
            }
            if (isset($query['address_romaji']) && !empty($query['address_romaji'])) {
                $allGuilds->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('address_romaji', '%'.$query['address_romaji'].'%');
                });
            }
            if (isset($query['address_kanji']) && !empty($query['address_kanji'])) {
                $allGuilds->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('address_kanji', '%'.$query['address_kanji'].'%');
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
        }

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
    public function view($id = null)
    {
        $guild = $this->Guilds->get($id, [
            'contain' => []
        ]);

        $this->set('guild', $guild);
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
            // debug($guild);
            Log::write('debug', 'Guild: '.$guild);
            
            if($this->Guilds->save($guild)) {
                $resp = [
                    'status' => 'success',
                    'redirect' => Router::url(['action' => 'index']),
                    'flash' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('Thông tin thêm mới đã được lưu.')
                    ]
                ];    
                $this->Flash->success(__('Lưu thành công.'));
            }
        } else {
            $resp = [
                'status' => 'error',
                'flash' => [
                    'title' => 'Error',
                    'type' => 'error',
                    'message' => __('Đã có lỗi xảy ra, vui lòng thử lại.')
                ]
            ];
        }
        return $this->jsonResponse($resp);
        //$this->set(compact('guild'));
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
                $guild = $this->Guilds->patchEntity($guild, $data, [
                    'fieldList' => ['name_romaji','name_kanji', 'address_romaji', 'address_kanji', 'phone_vn','phone_jp']
                ]);
                $guild = $this->Guilds->setAuthor($guild, $this->Auth->user('id'), $this->request->getParam('action'));
                if ($this->Guilds->save($guild)) {
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

        // $guild = $this->Guilds->get($id, [
        //     'contain' => []
        // ]);
        // if ($this->request->is(['patch', 'post', 'put'])) {
        //     $guild = $this->Guilds->patchEntity($guild, $this->request->getData(), [
        //         'fieldList' => ['name', 'address_romaji','address_kanji','phone']
        //     ]);
        //     if ($this->Guilds->save($guild)) {
        //         $this->Flash->success(__('The guild has been saved.'));

        //         return $this->redirect(['action' => 'index']);
        //     }
        //     $this->Flash->error(__('The guild could not be saved. Please, try again.'));
        // }
        // $this->set(compact('guild'));
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
        if ($this->Guilds->delete($guild)) {
            $this->Flash->success(__('The guild has been deleted.'));
        } else {
            $this->Flash->error(__('The guild could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}



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
 * Companies Controller
 *
 * @property \App\Model\Table\CompaniesTable $Companies
 *
 * @method \App\Model\Entity\Company[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CompaniesController extends AppController
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
            'sortWhitelist' => ['name_romaji','name_kanji','address_romaji', 'address_kanji', 'phone_vn','phone_jp'],
            'limit' => 10
        ];
        $allCompanies = $this->Companies->find();
        if (!empty($query)) {
            if (isset($query['name_romaji']) && !empty($query['name_romaji'])) {
                $allPresenters->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name_romaji', '%'.$query['name_romaji'].'%');
                });
            }
            if (isset($query['name_kanji']) && !empty($query['name_kanji'])) {
                $allPresenters->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name_kanji', '%'.$query['name_kanji'].'%');
                });
            }
            if (isset($query['address_romaji']) && !empty($query['address_romaji'])) {
                $allPresenters->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('address_romaji', '%'.$query['address_romaji'].'%');
                });
            }
            if (isset($query['address_romaji']) && !empty($query['address_kanji'])) {
                $allPresenters->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('address_kanji', '%'.$query['address_kanji'].'%');
                });
            }
            if (isset($query['phone_vn']) && !empty($query['phone_vn'])) {
                $allPresenters->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('phone_vn', '%'.$query['phone_vn'].'%');
                });
            }
            if (isset($query['phone_jp']) && !empty($query['phone_jp'])) {
                $allPresenters->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('phone_jp', '%'.$query['phone_jp'].'%');
                });
            }
        }

        $companies = $this->paginate($allCompanies);
        $this->set(compact('companies', 'query'));
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
        $company = $this->Companies->get($id, [
            'contain' => ['Guilds']
        ]);

        $this->set('company', $company);
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
                $this->Flash->success(__('The company has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The company could not be saved. Please, try again.'));
        }
        $guilds = $this->Companies->Guilds->find('list', ['limit' => 200]);
        $this->set(compact('company', 'guilds'));
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
                $company = $this->Companies->patchEntity($company, $data, [
                    'fieldList' => ['name_romaji','name_kanji','address_romaji', 'address_kanji', 'phone_vn','phone_jp']
                ]);
                $company = $this->Companies->setAuthor($company, $this->Auth->user('id'), $this->request->getParam('action'));
                if ($this->Companies->save($company)) {
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
        if ($this->Companies->delete($company)) {
            $this->Flash->success(__('The company has been deleted.'));
        } else {
            $this->Flash->error(__('The company could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

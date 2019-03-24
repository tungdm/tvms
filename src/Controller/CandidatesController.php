<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Log\Log;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
/**
 * Candidates Controller
 *
 * @property \App\Model\Table\CandidatesTable $Candidates
 *
 * @method \App\Model\Entity\Candidate[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidatesController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'ứng viên';
        $this->loadComponent('Util');
    }

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
            $allCandidates = $this->Candidates->find();

            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['candidate_name']) && !empty($query['candidate_name'])) {
                $allCandidates->where(function (QueryExpression $exp, Query $q) use ($query) {
                    $orConditions = $exp->or_(function ($or) use ($query) {
                        return $or->like('fullname', '%'.$query['candidate_name'].'%')
                            ->like('fb_name', '%'.$query['candidate_name'].'%');
                    });
                    return $exp->add($orConditions);
                });
            }
            if (isset($query['contact_date']) && !empty($query['contact_date'])) {
                $contactDate = $this->Util->convertDate($query['contact_date']);
                $allCandidates->where(['contact_date >=' => $contactDate]);
            }
            if (isset($query['phone']) && !empty($query['phone'])) {
                $allCandidates->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('phone', '%'.$query['phone'].'%');
                });
            }
            if (isset($query['source']) && !empty($query['source'])) {
                $allCandidates->where(['source' => $query['source']]);
                
            }
            if (isset($query['status']) && !empty($query['status'])) {
                switch ($query['status']) {
                    case 3:
                        $allCandidates->where(['potential' => TRUE]);
                        break;
                    default:
                        $allCandidates->where(['status' => $query['status']]);
                        break;
                }
            }
            $allCandidates->order(['Candidates.created' => 'DESC']);
        } else {
            $query['records'] = $this->defaultDisplay;
            $allCandidates = $this->Candidates->find()->order(['Candidates.created' => 'DESC']);
        }
        if ($this->Auth->user('role_id') != 1) {
            // other user (not admin) can not view delete record
            $allCandidates->where(['Candidates.del_flag' => FALSE]);
        }
        $this->paginate = [
            'contain' => ['Cities'],
            'limit' => $query['records']
        ];
        $candidates = $this->paginate($allCandidates);
        $this->set(compact('candidates', 'query'));
    }

    /**
     * View method
     *
     * @param string|null $id Candidate id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $candidate = $this->Candidates->get($id, [
            'contain' => [
                'Cities', 
                'ConsultantNotes' => [
                    'sort' => ['ConsultantNotes.consultant_date' => 'DESC']
                ],
                'ConsultantNotes.Users',
                'CreatedByUsers',
                'ModifiedByUsers'
            ]
        ]);
        if ($this->Auth->user('role_id') != 1 && $candidate->del_flag) {
            throw new NotFoundException();
        }
        $this->set('candidate', $candidate);
    }

    public function viewStudent($id = null)
    {
        $candidate = $this->Candidates->get($id);
        $studentTable = TableRegistry::get('Students');
        $student = $studentTable->find()->where(['candidate_id' => $id])->first();

        if ($this->Auth->user('role_id') != 1 && ($candidate->del_flag || $student->del_flag)) {
            throw new NotFoundException();
        }
        return $this->redirect(['controller' => 'Students', 'action' => 'view', $student->id]);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidate = $this->Candidates->newEntity();
        if ($this->request->is('post')) {
            $candidate = $this->Candidates->patchEntity($candidate, $this->request->getData());
            $candidate = $this->Candidates->setAuthor($candidate, $this->Auth->user('id'), 'add');
            if ($this->Candidates->save($candidate)) {
                $this->Flash->success($this->successMessage['addNoName']);
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error($this->errorMessage['add']);
        }
        $cities = TableRegistry::get('Cities')->find('list');
        $this->set(compact('candidate', 'cities'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidate = $this->Candidates->get($id, [
            'contain' => [
                'ConsultantNotes' => [
                    'sort' => ['ConsultantNotes.consultant_date' => 'DESC']
                ],
                'ConsultantNotes.Users'
            ]
        ]);
        if ($candidate->del_flag) {
            throw new NotFoundException();
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (isset($data['potential']) && $data['potential'] == 'on') {
                $data['potential'] = TRUE;
            }
            if (isset($data['consultant_notes']) && count($data['consultant_notes']) > 0) {
                $data['status'] = 2; // Đã tư vấn
            } else {
                $data['status'] = 1; // Chưa tư vấn
            }
            $candidate = $this->Candidates->patchEntity($candidate, $data, ['associated' => 'ConsultantNotes']);
            $candidate = $this->Candidates->setAuthor($candidate, $this->Auth->user('id'), 'edit');

            if ($this->Candidates->save($candidate)) {
                $this->Flash->success($this->successMessage['addNoName']);
                return $this->redirect(['action' => 'edit', $id]);
            }
            $this->Flash->error($this->errorMessage['error']);
        }
        $consultantUser = TableRegistry::get('Users')->find('list')->where(['role_id' => 4]); // nhan vien tuyen dung
        $cities = TableRegistry::get('Cities')->find('list');
        $this->set(compact('candidate', 'cities', 'consultantUser'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidate = $this->Candidates->get($id);
        $candidate->del_flag = TRUE;
        $candidate = $this->Candidates->setAuthor($candidate, $this->Auth->user('id'), 'edit');
        $candidateName = $candidate->source == 1 ? $candidate->fb_name : $candidate->fullname;
        if ($this->Candidates->save($candidate)) {
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $candidateName
                ]));
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }

        return $this->redirect(['action' => 'index']);
    }

    public function recover($id = null)
    {
        $this->request->allowMethod(['post']);
        $candidate = $this->Candidates->get($id);
        $candidate->del_flag = FALSE;
        $candidate = $this->Candidates->setAuthor($candidate, $this->Auth->user('id'), 'edit');
        $candidateName = $candidate->source == 1 ? $candidate->fb_name : $candidate->fullname;
        if ($this->Candidates->save($candidate)) {
            $this->Flash->success(Text::insert($this->successMessage['recover'], [
                'entity' => $this->entity, 
                'name' => $candidateName
                ]));
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }

        return $this->redirect(['action' => 'index']);
    }

    public function deleteConsultantNote()
    {
        $this->request->allowMethod('ajax');
        $consultantId = $this->request->getData('consultantId');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        try {
            $consultant = $this->Candidates->ConsultantNotes->get($consultantId);
            $candidate = $this->Candidates->get($consultant->candidate_id, ['contain' => ['ConsultantNotes']]);
            if (count($candidate->consultant_notes) == 1) {
                $data = ['status' => 1];
                $candidate = $this->Candidates->patchEntity($candidate, $data);
            }
            $candidate = $this->Candidates->setAuthor($candidate, $this->Auth->user('id'), 'edit');

            if (!empty($consultant) &&  $this->Candidates->ConsultantNotes->delete($consultant) && $this->Candidates->save($candidate)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => $this->successMessage['deleteNoName']
                    ]
                ];
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }
}

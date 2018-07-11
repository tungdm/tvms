<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Core\Configure;

/**
 * Jtests Controller
 *
 * @property \App\Model\Table\JtestsTable $Jtests
 *
 * @method \App\Model\Entity\Jtest[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class JtestsController extends AppController
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

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Jclasses', 'JtestContents', 'JtestContents.Users']
        ];
        $jtests = $this->paginate($this->Jtests);
        $jclasses = $this->Jtests->Jclasses->find('list');
        $this->set(compact('jtests', 'jclasses'));
    }

    /**
     * View method
     *
     * @param string|null $id Jtest id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $jtest = $this->Jtests->get($id, [
            'contain' => ['Jclasses', 'Students', 'JtestAttendances', 'JtestContents', 'JtestContents.Users']
        ]);

        $this->set('jtest', $jtest);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $jtest = $this->Jtests->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $jtest = $this->Jtests->patchEntity($jtest, $data, ['associated' => ['JtestContents', 'Students']]);
            if ($this->Jtests->save($jtest)) {
                $this->Flash->success(__('The test has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The test could not be saved. Please, try again.'));
        }
        $jclasses = $this->Jtests->Jclasses->find('list');
        $userTable = TableRegistry::get('Users');
        $teachers = $userTable->find('list')->where(['role_id' => '3']);        
        $this->set(compact('jtest', 'jclasses', 'teachers'));
    }

    public function getStudents()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        try {
            if (isset($query['id']) && !empty($query['id'])) {
                $stdClassTable = TableRegistry::get('JclassesStudents');
                $allStudents = $stdClassTable->find()->contain(['Students'])->where(['jclass_id' => $query['id']])->select(['student_id'])->toArray();
                $arr1 = [];
                foreach ($allStudents as $key => $value) {
                    array_push($arr1, $value->student_id);
                }
                Log::write('debug', $arr1);

                $stdTestTable = TableRegistry::get('JtestsStudents');
                $stdTest = $stdTestTable->find()->where(['jtest_id' => $query['testId']])->select(['id', 'student_id'])->toArray();
                $arr2 = [];
                $arrId = [];
                foreach ($stdTest as $key => $value) {
                    array_push($arr2, $value->student_id);
                    array_push($arrId, array('id' => $value->id));
                }
                Log::write('debug', $arr2);

                if (!empty(array_diff($arr1, $arr2))) {
                    $resp = [
                        'status' => 'changed',
                        'data' => $allStudents
                    ];
                } else {
                    $resp = [
                        'status' => 'unchanged',
                        'data' => $stdTest,
                        'ids' => $arrId
                    ];
                }
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    /**
     * Edit method
     *
     * @param string|null $id Jtest id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $jtest = $this->Jtests->get($id, [
            'contain' => ['Students', 'JtestContents', 'JtestContents.Users']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if ($data['changed'] === "true") {
                // delete old student test data
                $result = $this->Jtests->JtestsStudents->deleteAll(['jtest_id' => $jtest->id]);
            }
            $jtest = $this->Jtests->patchEntity($jtest, $data);
            if ($this->Jtests->save($jtest)) {
                $this->Flash->success(__('The test has been saved.'));

                return $this->redirect(['action' => 'edit', $jtest->id]);
            }
            $this->Flash->error(__('The test could not be saved. Please, try again.'));
        }
        $jclasses = $this->Jtests->Jclasses->find('list');
        $userTable = TableRegistry::get('Users');
        $teachers = $userTable->find('list')->where(['role_id' => '3']);        
        $this->set(compact('jtest', 'jclasses', 'teachers'));
        $this->render('/Jtests/add');
    }

    public function setScore($id = null)
    {
        $jtest = $this->Jtests->get($id, [
            'contain' => ['Students', 'JtestContents', 'JtestContents.Users']
        ]);
        $currentUserId = $this->Auth->user('id');
        $skill = '';
        $teacher = $this->Jtests->JtestContents->find()->where(['jtest_id' => $id, 'user_id' => $currentUserId])->toArray();
        if (empty($teacher)) {
            $this->Flash->error(__('You do not have permission for this action.'));
            return $this->redirect(['action' => 'index']); 
        } else {
            $skill = $teacher[0]['skill'];
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $jtest = $this->Jtests->patchEntity($jtest, $data, [
                'fieldList' => ['students'],
                'associated' => ['Students' => ['fieldList' => ['_joinData']]],
                ]);
            if ($this->Jtests->save($jtest)) {
                $this->Flash->success(__('The score has been saved.'));

                return $this->redirect(['action' => 'setScore', $jtest->id]);
            }
            $this->Flash->error(__('The score could not be saved. Please, try again.'));
        }

        $this->set(compact('jtest', 'skill'));

    }

    /**
     * Delete method
     *
     * @param string|null $id Jtest id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $jtest = $this->Jtests->get($id);
        if ($this->Jtests->delete($jtest)) {
            $this->Flash->success(__('The test has been deleted.'));
        } else {
            $this->Flash->error(__('The test could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function deleteSkill() {
        $this->request->allowMethod('ajax');
        $recordId = $this->request->getData('id');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Error',
                'type' => 'error',
                'message' => __('The skill could not be removed from this test. Please, try again.')
            ]
        ];

        try {
            $record = $this->Jtests->JtestContents->get($recordId);
            if (!empty($record) && $this->Jtests->JtestContents->delete($record)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('The skill has been remove from this test.')
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

<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\Utility\Text;


/**
 * Jtests Controller
 *
 * @property \App\Model\Table\JtestsTable $Jtests
 *
 * @method \App\Model\Entity\Jtest[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class JtestsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->entity = 'kì thi';
        $this->loadComponent('SystemEvent');
    }

    public function isAuthorized($user)
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        $session = $this->request->session();
        $permissionsTable = TableRegistry::get('Permissions');
        $userPermission = $permissionsTable->find()->where(['user_id' => $user['id'], 'scope' => $controller])->first();

        if (!empty($userPermission) || $user['role_id'] == 1) {
            if ($action == 'edit') {
                $now = Time::now()->i18nFormat('yyyy-MM-dd');
                $target_id = $this->request->getParam('pass');
                if (!empty($target_id)) {
                    $target_id = $target_id[0];
                    $testDate = $this->Jtests->get($target_id)->test_date;
                    if ($testDate <= $now) {
                        // can not modified
                        return false;
                    }
                }
            }

            if ($user['role_id'] != 1 && ($userPermission->action == 0 || ($userPermission->action == 1 && in_array($action, ['index', 'view'])))) {
                $session->write($controller, $userPermission->action);
                return true;
            }

            // supervisory can access to set score action
            if ($action == 'setScore') {
                Log::write('debug', 'user set score');
                $target_id = $this->request->getParam('pass');
                if (!empty($target_id)) {
                    $target_id = $target_id[0];
                    $jtest = $this->Jtests
                            ->find()
                            ->contain([
                                'JtestContents' => function ($q) use ($target_id, $user) {
                                    return $q->where(['jtest_id' => $target_id, 'user_id' => $user['id']]);
                                }
                            ])->first();
                    if (!empty($jtest) && $jtest->status !== '5') {
                        return true;
                    }
                }
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
            $allTest = $this->Jtests->find();

            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = 10;
            }
            if (isset($query['test_date']) && !empty($query['test_date'])) {
                $allTest->where(['test_date >=' => $query['test_date']]);
            }
            if (isset($query['lesson_from']) && !empty($query['lesson_from'])) {
                $allTest->where(['lesson_from >=' => $query['lesson_from']]);
            }
            if (isset($query['lesson_to']) && !empty($query['lesson_to'])) {
                $allTest->where(['lesson_to <=' => $query['lesson_to']]);
            }
            if (isset($query['class_id']) && !empty($query['class_id'])) {
                $allTest->where(['jclass_id <=' => $query['class_id']]);
            }
            if (isset($query['status']) && !empty($query['status'])) {
                $now = Time::now()->i18nFormat('yyyy-MM-dd');

                switch ($query['status']) {
                    case "1":
                        $allTest->where(['test_date >' => $now]);
                        break;
                    case "2":
                        $allTest->where(['test_date' => $now]);
                        break;
                    case "3":
                        $allTest->where(['test_date <' => $now]);
                        break;
                    case "4":
                        $allTest->where(['status' => "4"]);
                        break;
                }
            }
        } else {
            $query['records'] = 10;
            $allTest = $this->Jtests->find()->order(['Jtests.created' => 'DESC']);
        }

        $this->paginate = [
            'contain' => ['Jclasses', 'JtestContents', 'JtestContents.Users'],
            'limit' => $query['records']
        ];
        $jtests = $this->paginate($allTest);
        $jclasses = $this->Jtests->Jclasses->find('list');
        $this->set(compact('jtests', 'jclasses', 'query'));
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
            'contain' => [
                'Jclasses', 
                'Students', 
                'JtestContents' => ['sort' => ['skill' => 'ASC']], 
                'JtestContents.Users',
                'CreatedByUsers',
                'ModifiedByUsers'
                ]
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
            // create system event
            $event = $this->SystemEvent->create('THI TIẾNG NHẬT', $data['test_date']);
            $data['events'][0] = $event;
            $jtest = $this->Jtests->patchEntity($jtest, $data, ['associated' => ['JtestContents', 'Students', 'Events']]);
            $jtest = $this->Jtests->setAuthor($jtest, $this->Auth->user('id'), $this->request->getParam('action'));

            // update flag
            $flag = '';
            foreach ($data['jtest_contents'] as $key => $value) {
                $flag = $flag . $value['skill'];
            }
            $jtest->flag = $flag;
            if ($this->Jtests->save($jtest)) {
                $this->Flash->success(Text::insert($this->successMessage['add'], [
                    'entity' => $this->entity,
                    'name' => $jtest->test_date
                ]));

                return $this->redirect(['action' => 'edit', $jtest->id]);
            }
            $this->Flash->error($this->errorMessage['add']);
        }
        $lessons = Configure::read('lessons');
        $jclasses = $this->Jtests->Jclasses->find()
            ->map(function ($row) use ($lessons) {
                $row->name = $row->name . ' (Đang học ' . $lessons[$row->current_lesson] . ')';
                return $row;
            })
            ->combine('id', 'name')
            ->toArray();
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
                // get current lesson
                $currentLesson = TableRegistry::get('Jclasses')->get($query['id'])->current_lesson;
                $stdClassTable = TableRegistry::get('JclassesStudents');
                $allStudents = $stdClassTable->find()->contain(['Students'])->where(['jclass_id' => $query['id']])->select(['student_id'])->toArray();
                $arr1 = [];
                foreach ($allStudents as $key => $value) {
                    array_push($arr1, $value->student_id);
                }

                $stdTestTable = TableRegistry::get('JtestsStudents');

                $stdTest = $stdTestTable->find()->where(['jtest_id' => $query['testId']])->select(['id', 'student_id'])->toArray();

                $arr2 = [];
                $arrId = [];
                foreach ($stdTest as $key => $value) {
                    array_push($arr2, $value->student_id);
                    array_push($arrId, array('id' => $value->id));
                }

                if (!empty(array_diff($arr1, $arr2))) {
                    $resp = [
                        'status' => 'changed',
                        'data' => $allStudents,
                        'currentLesson' => $currentLesson
                    ];
                } else {
                    $resp = [
                        'status' => 'unchanged',
                        'data' => $stdTest,
                        'ids' => $arrId,
                        'currentLesson' => $currentLesson
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
            'contain' => ['Students', 'JtestContents', 'JtestContents.Users', 'Events']
        ]);
        $currentTestDate = $jtest->test_date;
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $newTestDate = new Time($data['test_date']);
            if ($data['changed'] === "true") {
                // delete old student test data
                $result = $this->Jtests->JtestsStudents->deleteAll(['jtest_id' => $jtest->id]);
            }
            if ($currentTestDate !== $newTestDate) {
                // uppdate system event
                $event = $this->SystemEvent->update($jtest->events[0]->id, $data['test_date']);
            }
            $data['events'][0] = $event;
            
            $jtest = $this->Jtests->patchEntity($jtest, $data);
            $jtest = $this->Jtests->setAuthor($jtest, $this->Auth->user('id'), $this->request->getParam('action'));

            if ($this->Jtests->save($jtest)) {
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity,
                    'name' => $jtest->test_date
                ]));

                return $this->redirect(['action' => 'edit', $jtest->id]);
            }
            $this->Flash->error(Text::insert($this->errorMessage['edit'], [
                'entity' => $this->entity,
                'name' => $currentTestDate
            ]));
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
            $this->Flash->error($this->errorMessage['unAuthor']);
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $skill = $teacher[0]['skill'];
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $jtest = $this->Jtests->patchEntity($jtest, $data, [
                'fieldList' => ['students'],
                'associated' => ['Students' => ['fieldList' => ['_joinData']]],
                ]);
            
            // update flag
            $jtest->flag = str_replace($skill, '', $jtest->flag);
            if (empty($jtest->flag)) {
                $jtest->status = '4'; // finish scoring
            }
            $skills = Configure::read('skills');
            if ($this->Jtests->save($jtest)) {
                $this->Flash->success(Text::insert($this->successMessage['setScore'], [
                    'skill' => $skills[$skill]
                ]));

                return $this->redirect(['action' => 'view', $jtest->id]);
            }
            $this->Flash->error(Text::insert($this->errorMessage['setScore'], [
                'skill' => $skills[$skill]
            ]));
        }

        $this->set(compact('jtest', 'skill'));
    }

    public function finish($id = null)
    {
        $this->request->allowMethod(['post']);
        $jtest = $this->Jtests->get($id);
        if ($jtest->status !== '4') {
            $this->Flash->error($this->errorMessage['unAuthor']);
        } else {
            $jtest->status = '5'; // close test
            if ($this->Jtests->save($jtest)) {
                $this->Flash->success(Text::insert($this->successMessage['edit'], [
                    'entity' => $this->entity, 
                    'name' => $jtest->test_date
                    ]));
            } else {
                $this->Flash->error($this->errorMessage['error']);
            }
        }
        return $this->redirect(['action' => 'index']);
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
            $this->Flash->success(Text::insert($this->successMessage['delete'], [
                'entity' => $this->entity, 
                'name' => $jtest->test_date
                ]));
        } else {
            $this->Flash->error(Text::insert($this->errorMessage['delete'], [
                'entity' => $this->entity,
                'name' => $jtest->test_date
                ]));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function deleteSkill() {
        $this->request->allowMethod('ajax');
        $recordId = $this->request->getData('id');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];

        try {
            $record = $this->Jtests->JtestContents->get($recordId);
            if (!empty($record) && $this->Jtests->JtestContents->delete($record)) {
                $skills = Configure::read('skills');
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => 'Đã xóa phần thi ' . $skills[$record->skill]
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

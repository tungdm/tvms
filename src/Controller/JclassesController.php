<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Log\Log;

/**
 * Jclasses Controller
 *
 * @property \App\Model\Table\JclassesTable $Jclasses
 *
 * @method \App\Model\Entity\Jclass[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class JclassesController extends AppController
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
        $query = $this->request->getQuery();
        if (!empty($query)) {

        } else {
            $query['records'] = 10;
            $allOrders = $this->Jclasses->find()->order(['Jclasses.created' => 'DESC']);
        }
        
        $this->paginate = [
            'contain' => ['Users', 'Students']
        ];
        $jclasses = $this->paginate($this->Jclasses);
        $teachers = $this->Jclasses->Users->find('list')->where(['role_id' => '3']);
        $this->set(compact('jclasses', 'teachers'));
    }

    /**
     * View method
     *
     * @param string|null $id Jclass id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $jclass = $this->Jclasses->get($id, [
            'contain' => ['Users']
        ]);

        $this->set('jclass', $jclass);
    }

    public function searchStudent()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        if (isset($query['q']) && !empty($query['q'])) {
            $studentTable = TableRegistry::get('Students');
            $students = $studentTable->find()
                ->leftJoinWith('Jclasses')
                ->select(['Jclasses.id', 'Students.id', 'Students.fullname'])
                ->where(['Jclasses.id IS' => NULL])
                ->andWhere(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('fullname', '%'.$query['q'].'%');
                });
            $resp['items'] = $students->toArray();
        }
        return $this->jsonResponse($resp);        
    }

    public function getStudent()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery();
        $resp = [];
        try {
            if (isset($query['id']) && !empty($query['id'])) {
                $resp = $this->Jclasses->Students->get($query['id']);
                $resp['enrolled_date'] = !empty($resp['enrolled_date']) ? $resp['enrolled_date']->i18nFormat('yyyy-MM-dd') : "N/A";
            }
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
        $jclass = $this->Jclasses->newEntity();
        if ($this->request->is('post')) {
            $jclass = $this->Jclasses->patchEntity($jclass, $this->request->getData(), ['associated' => 'Students']);
            $jclass = $this->Jclasses->setAuthor($jclass, $this->Auth->user('id'), $this->request->getParam('action'));
            if ($this->Jclasses->save($jclass)) {
                $this->Flash->success(__('The class has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class could not be saved. Please, try again.'));
        }
        $teachers = $this->Jclasses->Users->find('list')->where(['role_id' => '3']);
        $classes = []; // dummy data
        $this->set(compact('jclass', 'teachers', 'classes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Jclass id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $jclass = $this->Jclasses->get($id, [
            'contain' => ['Students']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $jclass = $this->Jclasses->patchEntity($jclass, $this->request->getData());
            if ($this->Jclasses->save($jclass)) {
                $this->Flash->success(__('The class has been saved.'));

                return $this->redirect(['action' => 'edit', $jclass->id]);
            }
            $this->Flash->error(__('The class could not be saved. Please, try again.'));
        }
        $teachers = $this->Jclasses->Users->find('list')->where(['role_id' => '3']);
        $classes = $this->Jclasses->find('list')->where(['id !=' => $id]);

        $this->set(compact('jclass', 'teachers', 'classes'));
        $this->render('/Jclasses/add');
    }

    /**
     * Delete method
     *
     * @param string|null $id Jclass id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $jclass = $this->Jclasses->get($id);
        if ($this->Jclasses->delete($jclass)) {
            $this->Flash->success(__('The class has been deleted.'));
        } else {
            $this->Flash->error(__('The class could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function deleteStudent()
    {
        $this->request->allowMethod('ajax');
        $recordId = $this->request->getData('id');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Error',
                'type' => 'error',
                'message' => __('The student could not be removed from this class. Please, try again.')
            ]
        ];

        try {
            $table = TableRegistry::get('JclassesStudents');
            $student = $table->get($recordId);

            if (!empty($student) && $table->delete($student)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('The student has been remove from this class.')
                    ]
                ];
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }

        return $this->jsonResponse($resp);
    }

    public function changeClass() {
        $this->request->allowMethod('ajax');
        $recordId = $this->request->getData('id');
        $newClassId = $this->request->getData('class'); 

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Error',
                'type' => 'error',
                'message' => __('The student could not be move to the new class. Please, try again.')
            ]
        ];

        try {
            $table = TableRegistry::get('JclassesStudents');
            $student = $table->get($recordId);
            $student->jclass_id = $newClassId;
            if ($table->save($student)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Success',
                        'type' => 'success',
                        'message' => __('The student has been move to the new class.')
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

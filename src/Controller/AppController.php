<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Network\Exception\NotFoundException;
use Cake\Event\Event;
use Cake\Routing\Router;
use clsTinyButStrong;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->successMessage = Configure::read('successMessage');
        $this->errorMessage = Configure::read('errorMessage');
        $this->defaultDisplay = Configure::Read('defaultDisplay');

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        if (!$this->request->is('ajax')) {
            $this->loadComponent('Security');
            $this->loadComponent('Csrf');
        }
        $this->loadComponent('Auth', [
            'authorize' => ['Controller'],
            // 'authenticate' => [
                // 'RememberMe.Cookie' => [
                //     'userModel' => 'Users',
                //     'fields' => ['username' => 'username'],
                //     'cookie' => [
                //         'name' => 'rememberMe',
                //         'expires' => '+30 days',
                //         'secure' => false,
                //         'httpOnly' => true,
                //     ],
                // ],
            // ],
            'authenticate' => [
                'Cookie' => [
                    'finder' => 'auth'
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'authError' => $this->errorMessage['unAuthor']
            // If unauthorized, return them to page they were just on
            // 'unauthorizedRedirect' => $this->referer()
        ]);

        //Init TBS
        include_once ROOT . DS . 'vendor' . DS . 'tbs' . DS . 'tbs_class.php';
        include_once ROOT . DS . 'vendor' . DS . 'tbs' . DS . 'tbs_plugin_opentbs.php';
        $this->tbs = new clsTinyButStrong;
        $this->tbs->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
    }

    public function isAuthorized($user)
    {
        // Admin can access every action
        if (isset($user['role']) && $user['role_id'] == 1) {
            return true;
        }
        if ($this->request->is('ajax')) {
            $this->response->type('json');
            $this->response->body(json_encode([
                'status' => 'error', 
                'flash' => [
                    'title' => 'Lỗi',
                    'type' => 'error',
                    'icon' => 'fa fa-warning',
                    'message' => $this->errorMessage['unAuthor']
                ]
                ]));
            $this->response->send();
            exit();
        }

        // Default deny
        return false;
    }

    public function jsonResponse($data)
    {
        $response = $this->response;
        $response = $response->withType('application/json')->withStringBody(json_encode($data));
        return $response;
    }

    public function checkDeleteFlag($entity, $user)
    {
        if ($entity->del_flag == TRUE && $user['role_id'] !== 1) {
            throw new NotFoundException();
        }
    }

    public function beforeRender(Event $event)
    {
        $currentUser = $this->Auth->user('id');
        $unreadMsg = TableRegistry::get('Notifications')->find()->where(['user_id' => $currentUser, 'is_seen' => FALSE])->count();
        $this->set(compact('unreadMsg'));
    }
}

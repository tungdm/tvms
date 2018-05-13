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
use Cake\Event\Event;
use clsTinyButStrong;

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

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        $this->loadComponent('Csrf');
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

                // 'Form' => [
                //     'field' => [
                //         'username' => 'username',
                //         'password' => 'password'
                //     ],
                //     'finder' => 'auth'
                // ]
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
            // If unauthorized, return them to page they were just on
            'unauthorizedRedirect' => $this->referer()
        ]);

        //Init TBS
        require_once ROOT . DS . 'vendor' . DS . 'tbs' . DS . 'tbs_class.php';
        require_once ROOT . DS . 'vendor' . DS . 'tbs' . DS . 'tbs_plugin_opentbs.php';
        $this->tbs = new clsTinyButStrong;
        $this->tbs->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
    }

    public function isAuthorized($user)
    {
        // Admin can access every action
        if (isset($user['role']) && $user['role']['name'] === 'admin') {
            return true;
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
}

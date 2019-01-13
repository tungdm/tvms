<?php

namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Datasource\EntityInterface;
use Cake\Controller\Component\AuthComponent;
use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\Log\Log;
use Cake\Utility\Hash;
use Cake\Utility\Security;
use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenTime;
use InvalidArgumentException;

class CookieAuthenticate extends BaseAuthenticate {

    public static $userTokenFieldName = 'remember_me_token';

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->config([
            'fields' => [
                'username' => 'username',
            ],
            'inputKey' => 'remember_me',
            'always' => false,
            'dropExpiredToken' => true,
            'cookie' => [
                'name' => 'rememberMe',
                'expires' => '+30 days',
                'secure' => false,
                'httpOnly' => true,
            ],
            'tokenStorageModel' => 'RememberMeTokens',
            'userModel' => 'Users',
            'scope' => [],
            'contain' => null,
        ]);
        parent::__construct($registry, $config);
    }

    public function implementedEvents()
    {
        return [
            'Auth.afterIdentify' => 'afterIdentify',
            'Auth.logout' => 'logout'
        ];
    }

    public function afterIdentify(Event $event, array $user)
    {
        $authComponent = $event->getSubject();

        if (!$user) {
            Log::write('debug', 'Authen failed');
            
            // when authenticate failed, clear cookie token.
            $authComponent->response = $this->setCookie($authComponent->response, '');
            return;
        }

        if ($this->getConfig('dropExpiredToken')) {            
            // drop expired token
            $tokenTable = TableRegistry::get($this->getConfig('tokenStorageModel'));
            $tokenTable->dropExpired($this->getConfig('userModel'));
        }

        if ($this->getConfig('always') || $authComponent->request->getData($this->getConfig('inputKey')) == 'true') {
            // save token            
            $token = $this->saveToken($user, $this->generateToken($user));
            Log::write('debug', 'User checked remember_me => save token: ' . $token);
            
            if ($token) {
                // write cookie
                $authComponent->response = $this->setLoginTokenToCookie($authComponent->response, $user, $token);
                // set token to user
                $user[static::$userTokenFieldName] = $token->toArray();

                return $user;
            }
        }
    }

    public function logout(Event $event, array $user)
    {
        $authComponent = $event->getSubject();
        $authComponent->response = $this->setCookie($authComponent->response, '');

        // drop token
        $this->dropToken($user);

        return true;
    }

    protected function dropToken(array $user)
    {
        $token = $this->getTokenFromUserArray($user);
        $tokenTable = TableRegistry::get($this->getConfig('tokenStorageModel'));

        if (!$token) {
            return false;
        }

        return $tokenTable->delete($token);
    }

    protected function getTokenFromUserArray(array $user)
    {
        if (empty($user[static::$userTokenFieldName])) {
            return null;
        }

        $tokenTable = TableRegistry::get($this->getConfig('tokenStorageModel'));
        $token = $tokenTable->find()
            ->where([
                'id' => $user[static::$userTokenFieldName]['id'],
            ])
            ->first();

        return $token;
    }

    public function authenticate(ServerRequest $request, Response $response)
    {
        Log::write('debug', 'Test log from custom authen');
        return $this->getUser($request);
    }

    public function getUser(ServerRequest $request)
    {
        if (!$this->checkFields($request)) {
            Log::write('debug', 'Cookie do not exists, first-time login');
            Log::write('debug', isset($request->data['username']));
            if (isset($request->data['username']) && isset($request->data['password'])) {
                $user = $this->_findUser($request->data['username'], $request->data['password']);
                if ($user['del_flag']) {
                    return false;
                }
                return $user;
            } else {
                return false;
            }
        }
        $cookieParams = $this->decodeCookie($this->getCookie($request));
        $user = $this->findUserAndTokenBySeries($cookieParams['username'], $cookieParams['series']);
        if (empty($user)) {
            Log::write('debug', 'Cannot find user with by cookie token');            
            return false;
        }
        if (!$this->verifyToken($user, $cookieParams['token'])) {
            Log::write('debug', 'Cookie token is invalid, drop it');            
            
            $this->dropInvalidToken($user);

            return false;
        }
        // remove password field
        $userArray = Hash::remove($user->toArray(), $this->getConfig('fields.password'));

        return $userArray;
    }

    protected function findUserAndTokenBySeries($username, $series, $token = null)
    {
        $query = $this->_query($username);

        if (!empty($query->clause('select'))) {
            $tokenTable = TableRegistry::get($this->getConfig('tokenStorageModel'));
            $query->select($tokenTable);
        }

        $query->matching('RememberMeTokens', function (Query $q) use ($series) {
            return $q->where(['RememberMeTokens.series' => $series]);
        });

        $user = $query->first();

        if (!$user) {
            return null;
        }

        // change mappging
        $matchingData = $user->get('_matchingData');
        $user->set(static::$userTokenFieldName, $matchingData['RememberMeTokens']);
        $user->unsetProperty('_matchingData');

        return $user;
    }

    protected function checkFields(ServerRequest $request)
    {
        $cookie = $this->getCookie($request);
        
        if (empty($cookie) || !is_string($cookie)) {
            return false;
        }

        $decoded = $this->decodeCookie($cookie);
        if (empty($decoded['username']) || empty($decoded['series']) || empty($decoded['token'])) {
            return false;
        }

        return true;
    }

    protected function getCookie(ServerRequest $request)
    {
        return $request->getCookie($this->getConfig('cookie.name'));
    }

    protected function setCookie(Response $response, $cookie)
    {
        $config = $this->getConfig('cookie');
        $expires = new FrozenTime($config['expires']);
        $config['value'] = $cookie;
        $config['expire'] = $expires->format('U');

        return $response->withCookie($this->getConfig('cookie.name'), $config);
    }

    public function decodeCookie($cookie)
    {
        return json_decode(Security::decrypt($cookie, Security::salt()), true);
    }

    protected function saveToken(array $user, $token)
    {
        $userModel = $this->getConfig('userModel');
        $userTable =TableRegistry::get($this->getConfig('userModel'));
        $tokenTable = TableRegistry::get($this->getConfig('tokenStorageModel'));

        $entity = null;
        $id = Hash::get($user, static::$userTokenFieldName . '.id');
        $expires = new FrozenTime($this->getConfig('cookie.expires'));

        Log::write('debug', 'now: ' . FrozenTime::now());
        Log::write('debug', 'expires: ' . $expires);

        if ($id) {
            // update token
            $entity = $tokenTable->get($id);
            $tokenTable->patchEntity($entity, [
                'token' => $token,
                'expires' => $expires,
            ]);
        } else {
            // new token
            $entity = $tokenTable->newEntity([
                'model' => $userModel,
                'user_id' => $user[$userTable->getPrimaryKey()],
                'series' => $this->generateToken($user),
                'token' => $token,
                'expires' => $expires,
            ]);
        }
        return $tokenTable->save($entity);
    }

    protected function generateToken(array $user)
    {
        $prefix = bin2hex(Security::randomBytes(16));
        $token = Security::hash($prefix . serialize($user));

        return $token;
    }

    public function encryptToken($username, $series, $token)
    {
        return Security::encrypt(json_encode(compact('username', 'series', 'token')), Security::salt());
    }

    protected function setLoginTokenToCookie(Response $response, array $user, $token)
    {
        if (isset($user[$this->getConfig('fields.username')])) {
            // write cookie
            $username = $user[$this->getConfig('fields.username')];
            $cookieToken = $this->encryptToken($username, $token->series, $token->token);
            $response = $this->setCookie($response, $cookieToken);
        }

        return $response;
    }

    protected function verifyToken(EntityInterface $user, $verifyToken)
    {
        $token = $this->getTokenFromUserEntity($user);

        if ($token->token !== $verifyToken) {
            return false;
        }

        if (FrozenTime::now()->gt($token->expires)) {
            return false;
        }

        return true;
    }

    protected function getTokenFromUserEntity(EntityInterface $user)
    {
        if (empty($user->{static::$userTokenFieldName})) {
            throw new InvalidArgumentException('user entity has not matching token data.');
        }

        return $user->{static::$userTokenFieldName};
    }

    protected function dropInvalidToken(EntityInterface $user)
    {
        $token = $this->getTokenFromUserEntity($user);

        return $this->getTokensTable()->delete($token);
    }

}


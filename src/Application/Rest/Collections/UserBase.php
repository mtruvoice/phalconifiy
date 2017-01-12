<?php

namespace Phalconify\Application\Rest\Collections;

use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceValidator;
use Phalconify\Database\Mongo;
use Phalconify\Utils\Strings as StringUtils;
use Phalconify\Application\Rest\Http\Request as Request;
use Phalconify\Application\Rest\Auth\Helpers\User as UserHelper;

class UserBase extends Base
{
    use StringUtils;

    /**
     * Roles available.
     */
    const ROLE_GUEST = 'Guest';
    const ROLE_ADMIN = 'Admin';
    const ROLE_USER = 'User';

    /*
    ** String Constants
    */
    const STATUS_ACTIVE = 'Active';

    /*
    ** Sort and filter constants
    */
    const DEFAULT_SORTBY = 'email';

    /**
     * Email.
     *
     * @var string
     */
    public $email;

    /**
     * Role.
     *
     * @var string
     */
    public $role;

    /**
     * Password.
     *
     * @var string
     */
    public $password;

    /**
     * Status.
     *
     * @var string
     */
    public $status;

    /**
     * Token.
     *
     * @var string
     */
    public $token;

    /**
     * Token expiration.
     *
     * @var int
     */
    public $tokenExpiry;

    /**
     * Last seen.
     *
     * @var int
     */
    public $lastSeen;

    /*
    ** Get user with credentials or token, which ever is present
    */
    public static function getUser($encryptionKey = '')
    {
        // Initialise
        $user = false;
        $request = new Request();
        $credentials = $request->getBasicAuth();

        // No credentials sent in request
        if ($credentials === null) {
            return self::getUserWithToken();
        } else {
            return self::getUserWithCredentials($credentials, $encryptionKey);
        }

        // This should never be executed
        return false;
    }

    /*
    ** Get user with credentials
    */
    public static function getUserWithCredentials(array $credentials, $encryptionKey)
    {
        // Username and password must be present
        if (!isset($credentials['username']) || !isset($credentials['password'])) {
            return false;
        }

        // Lookup user based on credentials
        $user = self::findFirst([
            'conditions' => ['email' => $credentials['username']],
        ]);

        // User not found
        if ($user === false) {
            return false;
        }

        // User found, check password
        if (UserHelper::passwordsMatch($user->password, $credentials['password'], $encryptionKey) !== true) {
            return false;
        }

        // Return the user
        return $user;
    }

    /*
    ** Get user with token
    */
    public static function getUserWithToken()
    {
        // Create request instance
        $request = new Request();

        // Check for a token
        $token = $request->getAuthToken();

        // Token does not exist
        if ($token === false) {
            return false;
        }

        // Lookup user based on token
        $user = self::findFirst([
            'conditions' => ['token' => $token],
        ]);

        // Validate token expiry
        if ($user && isset($user->tokenExpiry)) {
            if ($user->tokenExpiry < time()) {
                return false;
            }
        } else {
            return false;
        }

        // User found
        return $user;
    }

    /*
    ** Reset the users token
    */
    public function resetToken()
    {
        $this->token = '';
        $this->tokenExpiry = '';
    }

    /*
    ** Reset the users password
    */
    public function resetPassword()
    {
        $password = $this->createRandomString();
        // Encrypt password
        $di = \Phalcon\DI::getDefault();
        $cryptor = new \Phalcon\Crypt();
        $this->password = $cryptor->encryptBase64($password, $di['phalconify-config']->encryption->key);
        $this->save();

        return $password;
    }

    /*
    ** Model Hook - After Validation On Create
    */
    public function afterValidationOnCreate()
    {
        // Add default properties
        $this->resetToken();

        // Encrypt the password
        $di = \Phalcon\DI::getDefault();
        $cryptor = new \Phalcon\Crypt();
        $this->password = $cryptor->encryptBase64($this->password, $di['phalconify-config']->encryption->key);
        
        $this->dateCreated = time();
    }

    /*
    ** Model Hook - Before Save
    */
    public function beforeSave()
    {
        if ($this->status == 'Inactive') {
            $this->resetToken();
        }
    }

    /*
    ** Model Validation
    */
    public function validation()
    {
        // Ensure email is present
        $this->validate(
            new PresenceValidator([
                    'field' => 'email',
                    'message' => 'Email address is required.',
                ]
            )
        );

        // Ensure email is valid
        $this->validate(
            new EmailValidator([
                    'field' => 'email',
                    'message' => 'Email address is not valid.',
                ]
            )
        );

        // Ensure email does not already exists
        if (isset($this->_id) === true) {
            $exists = self::findFirst([
                [
                    '_id' => [
                        '$ne' => Mongo::getId($this->_id),
                    ],
                    'email' => $this->email,
                ],
            ]);
        } else {
            $exists = self::findFirst([
                ['email' => $this->email],
            ]);
        }
        if ($exists !== false) {
            $message = new \Phalcon\Mvc\Model\Message(
                'This email address is already being used.',
                'email',
                'InvalidValue'
            );
            $this->appendMessage($message);

            return false;
        }

        // Ensure role is present
        $this->validate(
            new PresenceValidator([
                    'field' => 'role',
                    'message' => 'Role is required.',
                ]
            )
        );

        // Ensure status is present
        $this->validate(
            new PresenceValidator([
                    'field' => 'status',
                    'message' => 'Status is required.',
                ]
            )
        );

        return $this->validationHasFailed() != true;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setTokenExpiry($tokenExpiry = 3600)
    {
        $this->tokenExpiry += $tokenExpiry;
    }

    public function getTokenExpiry()
    {
        return $this->tokenExpiry;
    }

    /*
    ** Update the token expiry
    */
    public function updateTokenExpiry($append = 3600)
    {
        $now = time();
        $this->lastSeen = $now;
        $this->tokenExpiry = $now + $append;

        return $this->save();
    }

    public function setLastSeen($lastSeen = null)
    {
        $this->lastSeen = $lastSeen ?? time();
    }

    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }
}

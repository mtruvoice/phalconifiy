<?php

namespace Phalconify\Application\Rest\Controllers;

use Phalconify\Application\Rest\Http\Request as Request;
use Phalconify\Application\Rest\Http\Response\Json as JsonResponse;
use Phalconify\Application\Rest\Auth\Helpers\User as UserHelper;

/**
 * Implements a user collection.
 */
class UsersController extends CRUD
{
    /**
     * {@inheritdoc}
     */
    protected function getCollection()
    {
        return UserHelper::getUsersCollection();
    }

    /**
     * Resets the users password and emails them the new one.
     *
     * Request Method: DELETE
     */
    public function passwordReset($userId)
    {
        $userDocument = $this->getCollection()::findById($userId);
        if ($userDocument) {
            $request = $this->getDI()->get('phalconify-request');
            $userDocument->loadFromBody($request);

            // Encrypt password
            $di = \Phalcon\DI::getDefault();
            $cryptor = new \Phalcon\Crypt();
            $userDocument->password = $cryptor->encryptBase64($userDocument->password, $di['config']->encryption->key);
            $userDocument->save();

            return JsonResponse::success([], $userDocument);
        }

        return JsonResponse::error([JsonResponse::ERROR_RECORD_NOT_FOUND]);
    }
}

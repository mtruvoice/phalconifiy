<?php

namespace Phalconify\Application\Rest\Controllers;

use Phalconify\Application\Rest\Http\Response\Json as JsonResponse;
use Phalconify\Application\Rest\Auth\Agent as AuthAgent;
use Phalconify\Application\Rest\Auth\Helpers\User as UserHelper;

class AuthController extends \Phalcon\Mvc\Controller
{
    public function login()
    {
        // Get dependency injection service
        $di = $this->getDI();

        // Create a User helper instance
        $helper = new UserHelper();

        // Check if the dependency injection service contains a user
        if ($helper->hasValidUser($di) !== true) {
            return JsonResponse::error([AuthAgent::ERROR_INVALID_CREDENTIALS]);
        }

        // Successful login - generate a token
        $token = UserHelper::generateToken();

        // Update user with token and expiry
        $user = $di['user'];
        $user->setToken($token);
        $user->setTokenExpiry();
        $user->setLastSeen();
        $user->save();

        // Generate payload
        $payload = [
            'token' => $user->token,
            'userData' => $user,
        ];

        return JsonResponse::success([], $payload);
    }
}

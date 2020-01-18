<?php

namespace Chat\Controllers\Traits;

use Chat\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait InteractsWithAuthorizedUser
{
    /**
     * @param Request $request
     * @return User
     */
    protected function getAuthorizedUser(Request $request): User
    {
        if (!$identifier = $request->header('Authorization')) {
            throw new BadRequestHttpException(
                'Authorization is required'
            );
        }

        return User::searchBy($identifier);
    }

}
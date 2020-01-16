<?php

namespace Chat\Controllers;

use Chat\Encryptions\Encrypter;
use Chat\Entities\UserObject;
use Chat\Models\User;
use Chat\Transformers\UserTransformer;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /** @var string */
    private const DEFAULT_NAME = 'unknown';

    /**
     * @param Request $request
     * @return array
     *
     * @throws \Exception
     */
    public function create(Request $request)
    {
        // parse inputs
        $name = $request->get('name') ?? self::DEFAULT_NAME;
        $uuid = $request->get('uuid') ?? Encrypter::salt(false);

        // create user
        $user = User::createNewUser(new UserObject($name, $uuid));

        // return response
        return (new UserTransformer())->transform($user);
    }


}
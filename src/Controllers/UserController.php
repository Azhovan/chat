<?php

namespace Chat\Controllers;

use Chat\Encryptions\EncryptFactory;
use Chat\Entities\UserObject;
use Chat\Models\User;
use Chat\Transformers\UserTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Default name for user if no name passed.
     *
     * @var string
     */
    private const ANONYMOUS_NAME = 'anonymous';

    /**
     * Data wrapper for user.
     *
     * @var UserTransformer
     */
    private $transformer;

    /**
     * UserController constructor.
     *
     * @param UserTransformer $transformer
     */
    public function __construct(?UserTransformer $transformer)
    {
        //Todo: add this to the container
        $this->transformer = $transformer ?? new UserTransformer();
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @throws Exception
     */
    public function create(Request $request): Response
    {
        $name = $request->get('name') ?? self::ANONYMOUS_NAME;
        $uuid = $request->get('uuid') ?? EncryptFactory::generateUuid();

        $user = User::createNewUser(
            new UserObject($name, $uuid)
        );

        return $this->response(
            $this->transformer->transform($user)
        );
    }

    /**
     * Fetch user information by an identifier (id or uuid)
     *
     * @param int $identifier
     * @return Response
     */
    public function show(int $identifier): Response
    {
        try {

            $user = User::searchBy($identifier);

            return $this->response(
                $this->transformer->transform($user)
            );

        } catch (ModelNotFoundException $e) {
            return $this->response(
                [], Response::HTTP_BAD_REQUEST
            );
        }
    }


}
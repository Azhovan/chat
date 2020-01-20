<?php

namespace Chat\Controllers;

use Chat\Controllers\Traits\InteractsWithAuthorizedUser;
use Chat\Encryptions\EncryptFactory;
use Chat\Entities\UserObject;
use Chat\Models\User;
use Chat\Transformers\MessageTransformer;
use Chat\Transformers\UserTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends Controller
{
    use InteractsWithAuthorizedUser;

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
     * Data wrapper for user.
     *
     * @var MessageTransformer
     */
    private $messageTransformer;

    /**
     * UserController constructor.
     *
     * @param UserTransformer $transformer
     * @param MessageTransformer $msgTransfer
     */
    public function __construct(UserTransformer $transformer, MessageTransformer $msgTransfer)
    {
        $this->transformer = $transformer;
        $this->messageTransformer = $msgTransfer;
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
            $this->transformer->transform($user),
            Response::HTTP_CREATED
        );
    }

    /**
     * Fetch user information by an identifier (id or uuid)
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        try {
            // fetch the user by token
            // token is user identifier (uuid or id)
            // if user not found, error will be returned
            $user = $this->getAuthorizedUser($request);

            return $this->response(
                $this->transformer->transform($user)
            );

        } catch (\Throwable $e) {
            return $this->response(
                [], Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function conversations(Request $request): Response
    {
        try {
            $user = $this->getAuthorizedUser($request);
            $messages = $user->getConversations();
            return $this->response($messages);
        } catch (InvalidArgumentException | ModelNotFoundException | BadRequestHttpException $e) {
            return $this->response([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


}
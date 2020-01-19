<?php

namespace Chat\Controllers;


use Chat\Controllers\Traits\InteractsWithAuthorizedUser;
use Chat\Models\Conversation;
use Chat\Transformers\ConversationTransformer;
use Chat\Transformers\MessageTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ConversationController extends Controller
{
    use InteractsWithAuthorizedUser;

    /**
     * Data wrapper for user.
     *
     * @var MessageTransformer
     */
    private $messageTransformer;

    /**
     * @var MessageTransformer
     */
    private $conversationTransformer;


    /**
     * ConversationController constructor.
     *
     * @param MessageTransformer $msgTransformer
     * @param ConversationTransformer $csnTransformer
     */
    public function __construct(MessageTransformer $msgTransformer, ConversationTransformer $csnTransformer)
    {
        $this->messageTransformer = $msgTransformer;
        $this->conversationTransformer = $csnTransformer;
    }

    /**
     * Create a new conversation
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function create(Request $request): Response
    {
        try {
            $this->getAuthorizedUser($request);

            $conversation = Conversation::init();

            return $this->response(
                $this->conversationTransformer->transform($conversation),
                Response::HTTP_CREATED
            );
        } catch (InvalidArgumentException | ModelNotFoundException | BadRequestHttpException $e) {
            return $this->response(
                [$e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }


    /**
     * Send message to a conversation
     *
     * @param Request $request
     * @param int $id conversation id
     * @return Response
     *
     * @throws Exception
     */
    public function sendMessage(Request $request, int $id): Response
    {
        try {
            $validation = $this->validate($request->all(), [
                'message' => 'required'
            ]);

            if ($validation->fails()) {
                throw new BadRequestHttpException(
                    'message field is required'
                );
            }
            // fetch the user by token
            // token is user identifier (uuid or id)
            // if user not found, error will be returned
            $user = $this->getAuthorizedUser($request);
            $conversation = $this->getValidConversation($id);

            // send the message to conversation
            $message = $user->sendMessage(
                $conversation, $request->get('message')
            );

            // message object is returned
            return $this->response(
                $this->messageTransformer->transform($message)
            );

        } catch (InvalidArgumentException | ModelNotFoundException | BadRequestHttpException $e) {
            return $this->response([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return Conversation
     */
    private function getValidConversation(int $id): Conversation
    {
        if (!$conversation = Conversation::find($id)) {
            throw new ModelNotFoundException(sprintf('invalid conversation id is given: %d',
                $id
            ));
        }
        return $conversation;
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws Exception
     */
    public function readMessage(Request $request, int $id): Response
    {
        try {
            // fetch the user by token
            // token is user identifier (uuid or id)
            // if user not found, error will be returned
            $user = $this->getAuthorizedUser($request);
            $conversation = $this->getValidConversation($id);
            $messages = $user->readMessagesFrom($conversation);

            // message object is returned
            return $this->response(
                $this->messageTransformer->transform($messages)
            );

        } catch (InvalidArgumentException | ModelNotFoundException | BadRequestHttpException $e) {
            return $this->response([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}
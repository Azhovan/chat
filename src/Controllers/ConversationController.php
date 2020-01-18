<?php

namespace Chat\Controllers;


use Chat\Models\Conversation;
use Chat\Models\User;
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
        //Todo add these transformers to container
        $this->messageTransformer = $msgTransformer ?? new MessageTransformer;
        $this->conversationTransformer = $csnTransformer ?? new ConversationTransformer;
    }

    /**
     * Create a new conversation
     *
     * @return Response
     * @throws Exception
     */
    public function create(): Response
    {
        $conversation = Conversation::init();

        return $this->response(
            ($this->conversationTransformer)->transform($conversation)
        );
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
                'access_token' => 'required',
                'message' => 'required'
            ]);

            if ($validation->fails()) {
                throw new BadRequestHttpException('access_token and message is required');
            }

            // if the conversation does not exist, immediately fail
            if (!$conversation = Conversation::find($id)) {
                throw new ModelNotFoundException('invalid conversation id is given.');
            }

            // fetch the user by token
            // token is user identifier (uuid or id)
            $user = User::searchBy($request->get('access_token'));

            // send the message to conversation
            $message = $user->sendMessage($conversation, $request->get('message'));

            // message object is returned
            return $this->response($this->messageTransformer->transform($message));

        } catch (InvalidArgumentException | ModelNotFoundException | BadRequestHttpException $e) {
            return $this->response([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}
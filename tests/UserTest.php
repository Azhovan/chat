<?php

namespace Tests;

use Chat\Database\Database;
use Chat\Entities\UserObject;
use Chat\Models\Conversation;
use Chat\Models\User;
use Chat\Models\UserConversation;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @var Database
     */
    private $database;

    public function setUp(): void
    {
        parent::setUp();
        $this->database = new Database();
        $this->database->establishConnection();
    }

    /**
     * @test
     */
    public function create_user_by_using_value_object()
    {
        $userObject = new UserObject('James Gosling', '1234gh21');
        $user = User::createNewUser($userObject);

        $this->assertSame('James Gosling', $user->name);
        $this->assertSame('1234gh21', $user->uuid);
    }

    /**
     * @test
     */
    public function users_can_send_message_to_other_users()
    {
        $user1 = User::createNewUser(
            new UserObject('James Gosling', '1234gh21')
        );
        $user2 = User::createNewUser(
            new UserObject('Ken Thompson', '767pvw111')
        );

        // initialize the conversation
        $conversation = Conversation::init();

        // send message from user1 to user2
        $user1->sendMessage(
            $conversation,
            'Hi there, what are you up today?'
        );

        // send message from
        $user2->sendMessage(
            $conversation,
            'Hi body, I\'m gonna create some thing awesome code!'
        );

        // find user1 conversation in db
        $row1 = UserConversation::where(function ($query) use ($user1, $conversation) {
            $query->where('user_id', $user1->id);
            $query->where('conversation_id', $conversation->id);
        })->get();

        // find user2 conversation in db
        $row2 = UserConversation::where(function ($query) use ($user2, $conversation) {
            $query->where('user_id', $user2->id);
            $query->where('conversation_id', $conversation->id);
        })->get();

        $this->assertNotNull($row1);
        $this->assertNotNull($row2);
    }

    /** @test */
    public function users_can_fetch_their_conversation_through_relation()
    {
        $user = User::createNewUser(
            new UserObject('James Gosling', '1234gh21')
        );

        $conversation = Conversation::init();
        // 3 messages
        $user->sendMessage($conversation, 'Hi there, what are you up today?');
        $user->sendMessage($conversation, 'today I need some rest. it was long week');
        $user->sendMessage($conversation, ':)');

        $allConversations = $user->chats()->get();
        $this->assertCount(3, $allConversations);
    }

    /** @test
     */
    public function users_can_get_their_chat_message_by_ascending_order()
    {
        $user1 = User::createNewUser(
            new UserObject('James Gosling', '1234gh21')
        );
        $user2 = User::createNewUser(
            new UserObject('Ken Thompson', '767pvw111')
        );

        // initialize the conversation
        $conversation = Conversation::init();

        $user1->sendMessage($conversation, 'user1 message 1');
        $user2->sendMessage($conversation, 'user2 message 2');
        $user1->sendMessage($conversation, 'user1 message 3');
        $user1->sendMessage($conversation, 'user1 message 4');

        $expected = [
            ['user_id' => $user1->id, 'message'=> 'user1 message 1'],
            ['user_id' => $user2->id, 'message'=> 'user2 message 2'],
            ['user_id' => $user1->id, 'message'=> 'user1 message 3'],
            ['user_id' => $user1->id, 'message'=> 'user1 message 4'],
        ];

        $actual = $user1->readMessages($conversation);

        foreach ($expected as $key => $item) {
            $this->assertSame(
                $actual[$key]['message'], $item['message']
            );
            $this->assertSame(
                (int)$actual[$key]['user_id'], (int)$item['user_id']
            );
        }

    }

}
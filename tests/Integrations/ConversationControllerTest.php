<?php

namespace Tests\Integrations;

use Chat\Entities\UserObject;
use Chat\Models\Conversation;
use Chat\Models\User;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ConversationControllerTest extends TestCase
{
    /**
     * @var Client
     */
    private $httpClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpClient = new Client([
            'base_uri' => getenv('APP_URI'),
        ]);
    }

    /** @test */
    public function existing_user_can_initialize_conversation()
    {
        $token = User::createNewUser(new UserObject('name', '1234'))->id;

        $response = $this->httpClient->post("/conversations", [
            'headers' => [
                'Authorization' => $token
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('conversation_id', $data);
    }

    /** @test */
    public function existing_user_can_send_message_to_specific_conversation()
    {
        $user = User::createNewUser(new UserObject('name', '1234'));
        $conversation = Conversation::init();
        $response = $this->httpClient->post("/conversations/" . $conversation->id . "/messages", [
            'json' => [
                'message' => 'hello body. such a nice weather ha?'
            ],
            'headers' => [
                'Authorization' => $user->id,
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user->id, $data['user_id']);
        $this->assertEquals($user->name, $data['user_name']);
        $this->assertEquals($conversation->id, $data['conversation_id']);
    }

    /** @test */
    public function existing_user_can_get_all_messages_from_specific_conversation()
    {
        $user = User::createNewUser(new UserObject('name', '1234'));
        $conversation = Conversation::init();
        $user->sendMessage($conversation, 'message 1');
        $user->sendMessage($conversation, 'message 2');
        $user->sendMessage($conversation, 'message 3');

        $response = $this->httpClient->get('conversations/' . $conversation->id . '/messages', [
            'headers' => [
                'Authorization' => $user->id
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(3, $data);
        $this->assertEquals('message 1', $data[0]['message']);
        $this->assertEquals('message 2', $data[1]['message']);
        $this->assertEquals('message 3', $data[2]['message']);
    }

    /** @test */
    public function if_user_does_exist_error_message_is_returned()
    {
        $conversation = Conversation::init();

        $this->expectException(\GuzzleHttp\Exception\ClientException::class);
        $response = $this->httpClient->get('conversations/' . $conversation->id . '/messages', [
            'headers' => [
                'Authorization' => -1
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /** @test */
    public function if_conversation_does_exist_error_message_is_returned()
    {
        $user = User::createNewUser(new UserObject('name', '1234'));

        $this->expectException(\GuzzleHttp\Exception\ClientException::class);
        $response = $this->httpClient->get('conversations/99999/messages', [
            'headers' => [
                'Authorization' => -1
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }


}
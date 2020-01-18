<?php

namespace Tests\Integrations;

use Chat\Entities\UserObject;
use Chat\Models\User;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
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
    public function can_get_user_information()
    {
        $user = User::createNewUser(
            new UserObject('new user name', '123456789')
        );

        $response = $this->httpClient->get("/users/" . $user->id);
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('123456789', $data['user_uuid']);
        $this->assertEquals($user->id, $data['user_id']);
        $this->assertEquals($user->name, $data['user_name']);
    }

    /** @test */
    public function can_creat_user_by_valid_data()
    {
        $response = $this->httpClient->post('/users', [
            'json' => [
                'name' => 'Elon mask',
                'uuid' => '999999'
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Elon mask', $data['user_name']);
        $this->assertEquals('999999', $data['user_uuid']);
    }

    /** @test */
    public function can_create_anonymous_user()
    {
        $response = $this->httpClient->post('/users');
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('anonymous', $data['user_name']);
    }

    /** @test */
    public function if_user_does_not_exist_404_is_returned()
    {
        $this->expectException(\GuzzleHttp\Exception\ClientException::class);
        $response = $this->httpClient->get("/users/2323232");

        $this->assertEquals(400, $response->getStatusCode());
    }


}
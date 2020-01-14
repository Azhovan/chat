<?php

namespace Tests;

use Chat\Database\Database;
use Chat\Models\Conversation;
use PHPUnit\Framework\TestCase;

class ConversationTest extends TestCase
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

    /** @test */
    public function conversations_have_different_encryption_key()
    {
        $conversation1 = Conversation::init();
        $conversation2 = Conversation::init();

        $this->assertTrue(
            $conversation1->encryption_key != $conversation2->encryption_key
        );
    }

}
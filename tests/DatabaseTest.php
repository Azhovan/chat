<?php

namespace Tests;

use Chat\Database\Database;
use Chat\Model\User;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @test 
     */
    public function can_establish_connection_to_database()
    {
        $this->database = new Database();
        $this->database->establishConnection();
        $this->assertNotNull($this->database);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function can_insert_data_into_tables_use_model()
    {
        User::insert([
            'name' => 'John doe',
            'uuid' => $uuid = random_bytes(16),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->assertNotNull(User::where('uuid', $uuid)->get());
    }

}
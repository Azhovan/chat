<?php

namespace Tests;

use Chat\Database\Database;
use Chat\Models\User;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
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
    public function can_establish_connection_to_database()
    {
        $this->assertNotNull($this->database);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function can_insert_raw_data_into_tables_use_model()
    {
        User::create([
            'name' => 'John doe',
            'uuid' => $uuid = random_bytes(16),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->assertNotNull(User::where('uuid', $uuid)->get());
    }

}
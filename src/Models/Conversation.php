<?php

namespace Chat\Models;

use Chat\Encryptions\Encrypter;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    /**
     * @inherit
     */
    protected $guarded = ['id'];

    /**
     * Initialize the conversation
     *
     * @return Conversation
     * @throws \Exception
     */
    public static function init(): Conversation
    {
        return self::create([
            'encryption_key' => Encrypter::salt()
        ]);
    }

}
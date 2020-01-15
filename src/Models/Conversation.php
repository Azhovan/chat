<?php

namespace Chat\Models;

use Chat\Encryptions\Encrypter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Every conversation belongs to particular chat
     *
     * @return BelongsTo
     */
    public function chats(): BelongsTo
    {
        $this->belongsTo(UserConversation::class);
    }

}
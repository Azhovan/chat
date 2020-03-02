<?php

namespace Chat\Models;

use Chat\Encryptions\Encrypter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Conversation
 * @package Chat\Models
 *
 * @property $id
 * @property $encryption_key
 * @property $created_at
 * @property $updated_at
 */
class Conversation extends Model
{
    /**
     * @inheritdoc
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
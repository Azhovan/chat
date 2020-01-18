<?php

namespace Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserConversation
 * @package Chat\Models
 *
 * @property $id
 * @property $user_id
 * @property $conversation_id
 * @property $message
 * @property $created_at
 * @property $updated_at
 */
class UserConversation extends Model
{

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

}
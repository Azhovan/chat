<?php

namespace Chat\Models;

use Carbon\Carbon;
use Chat\Encryptions\EncryptFactory;
use Chat\Entities\UserObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{

    /**
     * @inheritDoc
     */
    protected $guarded = ['id'];

    /**
     * Create a new user
     *
     * @param UserObject $user
     * @return User
     * @throws \Exception
     */
    public static function createNewUser(UserObject $user): User
    {
        return User::create([
            'name' => $user->getName(),
            'uuid' => $user->getUuid()
        ]);
    }

    /**
     * @param Conversation $conversation
     * @param string $message
     * @return bool
     * @throws \Exception
     */
    public function sendMessage(Conversation $conversation, string $message): bool
    {
        $encryptedMessage = EncryptFactory::create($conversation->encryption_key, $message);

        return $this->conversations()->insert([
            'user_id' => $this->id,
            'conversation_id' => $conversation->id,
            'message' => $encryptedMessage,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Every user can have many conversation
     *
     * @return HasMany
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(UserConversation::class);
    }

}
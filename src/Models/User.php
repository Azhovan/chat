<?php

namespace Chat\Models;

use Carbon\Carbon;
use Chat\Encryptions\EncryptFactory;
use Chat\Entities\UserObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;

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
     * Send an encrypted message to given conversation
     *
     * @param Conversation $conversation
     * @param string $message
     * @return bool
     * @throws \Exception
     */
    public function sendMessage(Conversation $conversation, string $message): bool
    {
        $encryptedMessage = EncryptFactory::encrypt($conversation->encryption_key, $message);

        return $this->chats()->insert([
            'user_id' => $this->id,
            'conversation_id' => $conversation->id,
            'message' => $encryptedMessage,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Every user may have many chats
     *
     * @return HasMany
     */
    public function chats(): HasMany
    {
        return $this->hasMany(UserConversation::class);
    }

    /**
     * Read all messages from a given conversation
     * This could be a group chat or just one-to-one chat
     *
     * @param Conversation $conversation
     * @return array
     * @throws \Exception
     */
    public function readMessages(Conversation $conversation): array
    {
        // fetch all messages which were exchanged during the
        // conversation between all users
        $chats = UserConversation::where(function ($query) use ($conversation) {
            $query->where('conversation_id', $conversation->id);
        })->orderBy('updated_at')
            ->get()
            ->toArray();

        $encryptionKey = $conversation->encryption_key;

        foreach ($chats as $key => $chat) {
            $chats[$key]['message'] = EncryptFactory::decrypt($encryptionKey, $chat['message']);
        }

        return $chats;
    }

}
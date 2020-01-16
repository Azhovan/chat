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
     *
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
     *
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
     *
     * @throws \Exception
     */
    public function readMessagesFrom(Conversation $conversation): array
    {
        // fetch all messages which were exchanged during the
        // single conversation between all users
        // result is sorted based on created_at, this lets us have return accurate result even if user will be able
        // to change the message (if we have edit message feature in our app)
        $chats = UserConversation::where(function ($query) use ($conversation) {
            $query->where('conversation_id', $conversation->id);
        })->orderBy('created_at')
            ->get()
            ->toArray();

        // preparing to use it in decryptAll method
        $chats = array($chats);
        return EncryptFactory::decryptAll($chats);
    }

    /**
     * Get all the conversation of user
     *
     * @return array|\Illuminate\Support\Collection
     *
     * @throws \Exception
     */
    public function getConversations(): array
    {
        $conversations = $this->chats()
            ->groupBy('conversation_id')
            ->get()->pluck('conversation_id');

        $chats = UserConversation::whereIn('conversation_id', $conversations)
            ->orderBy('conversation_id')
            ->get()
            ->groupBy('conversation_id')
            ->toArray();

        return EncryptFactory::decryptAll($chats);
    }

}
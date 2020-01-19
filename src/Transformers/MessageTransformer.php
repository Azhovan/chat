<?php

namespace Chat\Transformers;

use Chat\Encryptions\EncryptFactory;
use Chat\Models\UserConversation;

class MessageTransformer extends BaseTransformer
{

    /**
     * @param UserConversation|array $message
     * @return array
     *
     * @throws \Exception
     */
    public function transform($message): array
    {
        // one single message
        // in this case I will wrap the data into proper format
        if ($message instanceof UserConversation) {
            return $this->getMessageBag($message);
        }

        return is_array($message) ? array_shift($message) : [];
    }

    /**
     * @param UserConversation $message
     * @return array
     * @throws \Exception
     */
    private function getMessageBag(UserConversation $message): array
    {
        $key = $message->conversation->encryption_key;

        return [
            'user_id' => $message->user_id,
            'user_name' => $message->user->name,
            'conversation_id' => $message->conversation_id,
            'message' => EncryptFactory::decrypt($key, $message->message),
            'created_at' => $this->formatDate($message->created_at),
            'updated_at' => $this->formatDate($message->updated_at)
        ];
    }
}


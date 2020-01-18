<?php

namespace Chat\Transformers;

use Chat\Encryptions\EncryptFactory;
use Chat\Models\UserConversation;

class MessageTransformer extends BaseTransformer
{
    /**
     * @param UserConversation $message
     * @return array
     *
     * @throws \Exception
     */
    public function transform(UserConversation $message): array
    {
        $key = $message->conversation->encryption_key;

        return [
            'user_id' => $message->user_id,
            'conversation_id' => $message->conversation_id,
            'message' => EncryptFactory::decrypt($key, $message->message),
            'created_at' => $this->formatDate($message->created_at),
            'updated_at' => $this->formatDate($message->updated_at)
        ];
    }

}


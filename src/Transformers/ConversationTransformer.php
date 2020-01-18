<?php

namespace Chat\Transformers;

use Chat\Models\Conversation;

class ConversationTransformer extends BaseTransformer
{
    /**
     * @param Conversation $conversation
     * @return array
     */
    public function transform(Conversation $conversation): array
    {
        return [
            'conversation_id' => $conversation->id,
            'created_at' => $this->formatDate($conversation->created_at),
            'updated_at' => $this->formatDate($conversation->updated_at)
        ];
    }

}


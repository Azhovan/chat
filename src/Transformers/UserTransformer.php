<?php

namespace Chat\Transformers;

use Chat\Models\User;

class UserTransformer extends BaseTransformer
{

    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_uuid' => $user->uuid,
            'created_at' => $this->formatDate($user->created_at),
            'updated_at' => $this->formatDate($user->updated_at)
        ];
    }
}

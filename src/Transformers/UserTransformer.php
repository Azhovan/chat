<?php

namespace Chat\Transformers;

use Carbon\Carbon;
use Chat\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{

    /**
     * @var array
     */
    protected $availableIncludes = [
        'conversations'
    ];

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

    /**
     * @param string $date
     * @return string
     */
    private function formatDate(string $date): string
    {
        return Carbon::parse($date)->format('Y-m-d h:i:s');
    }

//    public function includeConversations(User $user)
//    {
//        return $this->item($user->getConversations());
//    }

}
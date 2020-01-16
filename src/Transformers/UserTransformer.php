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
            'id' => $user->id,
            'name' => $user->name,
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d h:i:s')
        ];
    }

//    public function includeConversations(User $user)
//    {
//        return $this->item($user->getConversations());
//    }

}
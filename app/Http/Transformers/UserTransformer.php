<?php


namespace App\Http\Transformers;


use App\Models\User;


class UserTransformer extends Transformer
{

    /**
     * Apply the transformer to a single item
     *
     * @param User $item
     * @return array
     */
    protected function transformItem($item): array
    {
        $result =  [
            'id' => (int)$item->id,
            'email' => self::castNullable($item->email, 'string'),
            'first_name' => (string)$item->first_name,
            'last_name' => (string)$item->last_name,
            'date_of_birth' => $item->date_of_birth ? (string)$item->date_of_birth->format('Y-m-d') : null,
            'email_verified_at' => self::castNullable($item->email_verified_at, 'string'),
            'account_type' => (string)ucfirst($item->account_type),
            'role' => (string) $item->roles()->first()->name,
            'status' => (string)ucfirst($item->status),
            'created_at' => (string)$item->created_at->toISOString(),
        ];

        if ($item->relationLoaded('profile')) {
            $result['profile'] = app(ProfileTransformer::class)->transform($item->profile);
        }

        return $result;
    }


}

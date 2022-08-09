<?php


namespace App\Http\Transformers;


use App\Models\Profile;


class ProfileTransformer extends Transformer
{
    /**
     * Apply the transformer to a single item
     *
     * @param Profile $item
     * @return array
     */
    protected function transformItem($item): array
    {
        return [
            'photo' => $item->photoUrl(),
            'description' => (string)$item->description,
            'phone_number' => (string)$item->phone_number,
            'gender' => (string)$item->gender,
        ];
    }


}

<?php

namespace App\Http\Transformers;

use App\Models\Setting;

class CategoryTransformer extends Transformer
{
    /**
     * Apply the transformer to a single item
     *
     * @param Setting $item
     * @return array
     */
    protected function transformItem($item): array
    {
        $result = [
            'id' => (int)$item->id,
            'name' => (string)$item->name,
            'description' => (string)$item->description,
        ];

        return $result;
    }


}

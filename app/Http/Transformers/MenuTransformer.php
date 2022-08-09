<?php

namespace App\Http\Transformers;

use App\Models\Setting;

class MenuTransformer extends Transformer
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
            'order' => (int)$item->order,
        ];

        if ($item->relationLoaded('page')) {
            $result['page'] = app(PageTransformer::class)->transform($item->page);
        }

        return $result;
    }


}

<?php

namespace App\Http\Transformers;

use App\Models\Setting;

class PageTransformer extends Transformer
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
            'title' => (string)$item->title,
            'content' => (string)$item->content,
            'slug' => (string)$item->slug,
            'created_at' => (string)$item->created_at->toISOString(),
        ];

        return $result;
    }


}

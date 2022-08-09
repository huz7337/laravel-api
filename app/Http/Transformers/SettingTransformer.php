<?php

namespace App\Http\Transformers;

use App\Models\Setting;

class SettingTransformer extends Transformer
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
            'value' => (string)$item->value,
            'default' => (boolean)$item->default,
            'model' => (string)$item->model,
            'created_at' => (string)$item->created_at->toISOString(),
        ];

        return $result;
    }


}

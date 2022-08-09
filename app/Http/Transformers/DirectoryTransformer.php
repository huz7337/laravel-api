<?php


namespace App\Http\Transformers;

use Illuminate\Database\Eloquent\Model;


class DirectoryTransformer extends Transformer
{

    /**
     * Apply the transformer to a single item
     *
     * @param Model $item
     * @return array
     */
    protected function transformItem($item): array
    {
        return [
            'id' => (int)$item->id,
            'name' => (string)ucfirst($item->name),
        ];
    }


}

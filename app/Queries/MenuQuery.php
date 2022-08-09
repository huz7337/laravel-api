<?php

namespace App\Queries;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder;

class MenuQuery extends Query
{
    /**
     * Available sort options
     * @var array|string[]
     */
    public static array $sort = [
        'id' => 'id',
        'order' => 'order',
    ];

    /**
     * Get query
     * @return Builder
     */
    public function query(): Builder
    {
        $query = Menu::query();

        $sort = $this->getSorting();
        $query->orderBy(...$sort);

        return $query;
    }

}

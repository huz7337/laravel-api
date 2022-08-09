<?php

namespace App\Queries;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class CategoriesQuery extends Query
{
    /**
     * Available sort options
     * @var array|string[]
     */
    public static array $sort = [
        'id' => 'id'
    ];

    /**
     * Get query
     * @return Builder
     */
    public function query(): Builder
    {
        $query = Category::query();

        $sort = $this->getSorting();
        $query->orderBy(...$sort);

        return $query;
    }

}

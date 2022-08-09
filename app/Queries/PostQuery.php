<?php

namespace App\Queries;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class PostQuery extends Query
{
    /**
     * Available sort options
     * @var array|string[]
     */
    public static array $sort = [
        'id' => 'id',
    ];

    /**
     * Get query
     * @return Builder
     */
    public function query(): Builder
    {
        $query = Post::query();

        $sort = $this->getSorting();
        $query->orderBy(...$sort);

        return $query;
    }

}

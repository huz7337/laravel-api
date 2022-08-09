<?php

namespace App\Queries;

use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;

class PagesQuery extends Query
{
    /**
     * Available sort options
     * @var array|string[]
     */
    public static array $sort = [
        'id' => 'id',
        'title' => 'title',
    ];

    /**
     * Get query
     * @return Builder
     */
    public function query(): Builder
    {
        $query = Page::query();

        // search
        $query->when(
            $search = $this->getFilter('search'),
            fn(Builder $q) => $q->where(function (Builder $q) use ($search) {
                $q->orWhere('title', 'like', "%$search%");
            })
        );

        $sort = $this->getSorting();
        $query->orderBy(...$sort);

        return $query;
    }

}

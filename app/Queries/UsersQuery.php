<?php

namespace App\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UsersQuery extends Query
{
    /**
     * Available sort options
     * @var array|string[]
     */
    public static array $sort = [
        'id' => 'id',
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'email' => 'email',
    ];

    /**
     * Get query
     * @return Builder
     */
    public function query(): Builder
    {
        $query = User::query();

        // search
        $query->when(
            $search = $this->getFilter('search'),
            fn(Builder $q) => $q->where(function (Builder $q) use ($search) {
                $q->orWhere('first_name', 'like', "%$search%");
                $q->orWhere('last_name', 'like', "%$search%");
            })
        );

        $sort = $this->getSorting();
        $query->orderBy(...$sort);

        return $query;
    }

}

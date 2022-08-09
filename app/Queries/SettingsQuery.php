<?php

namespace App\Queries;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;

class SettingsQuery extends Query
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
        $query = Setting::query();

        // filter by default settings
        $query->when(
            $default = $this->getFilter('default'),
            fn(Builder $q) => $q->where('default', $default)
        );

        $sort = $this->getSorting();
        $query->orderBy(...$sort);

        return $query;
    }

}

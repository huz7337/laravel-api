<?php


namespace App\Queries;


use Illuminate\Database\Eloquent\Builder;

abstract class Query
{

    /**
     * Available sort fields
     * @var array
     */
    public static array $sort = [];

    /**
     * Filters to apply on the query
     * @var array
     */
    protected array $filters = [];


    /**
     * Relations to load for the model
     * @var array
     */
    protected array $with = [];


    /**
     * Query constructor.
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }


    /**
     * Generate a query object
     * @return static
     */
    public static function make()
    {
        return new static(...func_get_args());
    }


    /**
     * Add relations to load
     * @param array $relations
     * @return $this
     */
    public function with(array $relations): self
    {
        $this->with += $relations;

        return $this;
    }


    /**
     * Get sort parameters
     * @return array
     */
    protected function getSorting(): array
    {
        return [
            static::$sort[$this->filters['sort_column'] ?? static::$sort['id']],
            $this->filters['sort_direction'] ?? 'desc'
        ];
    }


    /**
     * Check if a filter exists
     * @param string $key
     * @return bool
     */
    protected function hasFilter(string $key)
    {
        return !empty($this->filters[$key]);
    }


    /**
     * Get the value of a filter
     * @param string $key
     * @return mixed|null
     */
    protected function getFilter(string $key)
    {
        return $this->filters[$key] ?? null;
    }


    /**
     * Get query
     * @return Builder
     */
    abstract public function query(): Builder;

}

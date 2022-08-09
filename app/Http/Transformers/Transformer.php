<?php


namespace App\Http\Transformers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

abstract class Transformer
{
    protected array $merge = [];
    private ?array $show = null;
    private ?array $hide = null;

    public static function castNullable($var, $type)
    {
        if (is_null($var)) {
            return null;
        }
        settype($var, $type);

        return $var;
    }

    /**
     * @param Model | LengthAwarePaginator | iterable | null $item
     * @param null $merge
     * @return array
     */
    public function transform($item, $merge = null): ?array
    {
        if (null === $item) {
            return null;
        }


        $method = $this->getTransformMethod($item);
        $transformed = $this->{$method}($item);


        return array_merge($transformed, (array)$merge);
    }

    /**
     * @param $item
     * @return string
     */
    private function getTransformMethod($item): string
    {
        if ($item instanceof Model) {
            return 'transformSingleItem';
        }
        if ($item instanceof LengthAwarePaginator) {
            return 'transformPagination';
        }

        if (is_iterable($item)) {
            return 'transformCollection';
        }

        throw new \InvalidArgumentException(
            "The attribute must be an instance of Model, LengthAwarePaginator or iterable"
        );

    }

    public function setMergeItem(array $merge)
    {
        $this->merge = $merge;

        return $this;
    }

    public function show(array $fields): Transformer
    {
        $this->show = $fields;

        return $this;
    }

    public function hide(array $fields): Transformer
    {
        $this->hide = $fields;

        return $this;
    }

    protected function isFieldRequested(string $field): bool
    {
        if (in_array($field, $this->hide ?? [])) {
            return false;
        }

        if (!empty($this->show)) {
            return in_array($field, $this->show);
        }

        return true;
    }

    /**
     * Apply the transformer to a collection of items
     *
     * @param LengthAwarePaginator $object
     * @return array
     */
    private function transformPagination(LengthAwarePaginator $object): array
    {
        return [
            'currentPage' => $object->currentPage(),
            'perPage' => $object->perPage(),
            'lastPage' => $object->lastPage(),
            'total' => $object->total(),
            'items' => $this->transformCollection($object->items())
        ];
    }

    /**
     * Apply the transformer to a collection of items
     *
     * @param iterable $items
     * @return array
     */
    private function transformCollection(iterable $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = array_merge($this->transformSingleItem($item), $this->merge);
        }

        return array_merge($result);
    }

    private function transformSingleItem($item): array
    {
        $transformed = $this->transformItem($item);

        if (null !== $this->show) {
            $transformed = array_filter(
                $transformed,
                fn($key) => in_array($key, $this->show),
                ARRAY_FILTER_USE_KEY
            );
        }

        if (null !== $this->hide) {
            $transformed = array_filter(
                $transformed,
                fn($key) => !in_array($key, $this->hide),
                ARRAY_FILTER_USE_KEY
            );
        }

        return $transformed;
    }

    /**
     * Apply the transformer to a single item
     *
     * @param Model $item
     * @return array
     */
    protected abstract function transformItem($item): array;


}

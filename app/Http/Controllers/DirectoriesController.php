<?php

namespace App\Http\Controllers;

use App\Http\Transformers\DirectoryTransformer;
use App\Queries\UsersQuery;
use Illuminate\Support\Facades\DB;

class DirectoriesController extends Controller
{

    /**
     * @var DirectoryTransformer
     */
    protected DirectoryTransformer $_transformer;

    /**
     * UsersController constructor.
     * @param DirectoryTransformer $transformer
     */
    public function __construct(DirectoryTransformer $transformer)
    {
        $this->_transformer = $transformer;
    }

    /**
     * Return the list of coaches for filters
     * @param ListDirectoryRequest $request
     * @return mixed
     */
    public function coaches(ListDirectoryRequest $request)
    {
        $params = $request->validated();
        $params['sort_column'] = 'name';
        $params['sort_direction'] = 'asc';

        $items = UsersQuery::make($params)->coaches()->query()
            ->select(DB::raw("`first_name` ||  ' ' || `last_name` as name"), 'users.id as id')
            ->get();
        return response()->success($this->_transformer->transform($items));
    }
}

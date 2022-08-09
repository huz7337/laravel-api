<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\ListCategoriesRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Transformers\CategoryTransformer;
use App\Models\Category;
use App\Models\User;
use App\Queries\CategoriesQuery;
use App\Services\CategoryService;

class CategoryController extends Controller
{

    /**
     * @var CategoryTransformer
     */
    protected CategoryTransformer $_transformer;

    /**
     * @var CategoryService
     */
    protected CategoryService $_service;

    /**
     * Constructor.
     * @param CategoryService $service
     */
    public function __construct(CategoryTransformer $transformer, CategoryService $service)
    {
        $this->_transformer = $transformer;
        $this->_service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index(ListCategoriesRequest $request)
    {
        $params = $request->validated();

        $items = CategoriesQuery::make($params)->query()->paginate(
            $params['per_page'] ?? config('filters.per_page')
        );

        return response()->success($this->_transformer->transform($items));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCategoryRequest $request
     * @return Category
     */
    public function store(CreateCategoryRequest $request)
    {
        $params = $request->validated();

        $category = $this->_service->createCategory($params);

        return response()->success($this->_transformer->transform($category));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCategoryRequest $request
     * @param Category $category
     * @return mixed
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $params = $request->validated();

        $category = $this->_service->updateCategory($category, $params);

        return response()->success($this->_transformer->transform($category));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return mixed
     */
    public function destroy(Category $category)
    {
        $this->authorize(User::PERMISSION_CATEGORY_DELETE);

        $category->delete();

        return response()->message(__('The category :id has been removed.', ['id' => $category->id]));
    }
}

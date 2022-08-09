<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListPagesRequest;
use App\Http\Requests\CreatePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Transformers\PageTransformer;
use App\Models\Page;
use App\Models\User;
use App\Queries\PagesQuery;
use App\Services\PagesService;

class PageController extends Controller
{
    /**
     * @var PageTransformer
     */
    protected PageTransformer $_transformer;

    /**
     * @var PagesService
     */
    protected PagesService $_service;

    /**
     * Constructor.
     * @param PagesService $service
     */
    public function __construct(PageTransformer $transformer, PagesService $service)
    {
        $this->_transformer = $transformer;
        $this->_service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index(ListPagesRequest $request)
    {
        $params = $request->validated();

        $items = PagesQuery::make($params)->query()->paginate(
            $params['per_page'] ?? config('filters.per_page')
        );

        return response()->success($this->_transformer->transform($items));
    }

    /**
     * Display the specified resource.
     *
     * @param Page $page
     * @return mixed
     */
    public function show(Page $page)
    {
        return response()->success($this->_transformer->transform($page));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePageRequest $request
     * @return mixed
     */
    public function store(CreatePageRequest $request)
    {
        $params = $request->validated();

        $page = $this->_service->createPage($params);

        return response()->success($this->_transformer->transform($page));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePageRequest $request
     * @param Page $page
     * @return mixed
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $params = $request->validated();

        $page = $this->_service->updatePage($page, $params);

        return response()->success($this->_transformer->transform($page));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Page $page
     * @return mixed
     */
    public function destroy(Page $page)
    {
        $this->authorize(User::PERMISSION_PAGE_DELETE);

        $page->delete();

        return response()->message(__('The page :id has been removed.', ['id' => $page->id]));
    }
}

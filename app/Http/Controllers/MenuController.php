<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMenuRequest;
use App\Http\Requests\ListMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Transformers\MenuTransformer;
use App\Models\Menu;
use App\Models\User;
use App\Queries\MenuQuery;
use App\Services\MenuService;

class MenuController extends Controller
{

    /**
     * @var MenuTransformer
     */
    protected MenuTransformer $_transformer;

    /**
     * @var MenuService
     */
    protected MenuService $_service;

    /**
     * Constructor.
     * @param MenuService $service
     */
    public function __construct(MenuTransformer $transformer,MenuService $service)
    {
        $this->_transformer = $transformer;
        $this->_service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index(ListMenuRequest $request)
    {
        $params = $request->validated();

        $items = MenuQuery::make($params)->query()->paginate(
            $params['per_page'] ?? config('filters.per_page')
        );

        return response()->success($this->_transformer->transform($items->load('page')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateMenuRequest $request
     * @return mixed
     */
    public function store(CreateMenuRequest $request)
    {
        $params = $request->validated();

        $menu = $this->_service->createMenuItem($params);

        return response()->success($this->_transformer->transform($menu->load('page')));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMenuRequest $request
     * @param Menu $menu
     * @return mixed
     */
    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $params = $request->validated();

        $menu = $this->_service->updateMenuItem($menu, $params);

        return response()->success($this->_transformer->transform($menu->load('page')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Menu $menu
     * @return mixed
     */
    public function destroy(Menu $menu)
    {
        $this->authorize(User::PERMISSION_MENU_DELETE);

        $menu->delete();

        return response()->message(__('The menu item :id has been removed.', ['id' => $menu->id]));
    }
}

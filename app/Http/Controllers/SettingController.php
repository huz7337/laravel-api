<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSettingRequest;
use App\Http\Requests\ListSettinsRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Transformers\SettingTransformer;
use App\Models\Setting;
use App\Models\User;
use App\Queries\SettingsQuery;
use App\Services\SettingService;
use Illuminate\Routing\Controller as BaseController;

class SettingController extends BaseController
{
    /**
     * @var SettingTransformer
     */
    protected SettingTransformer $_transformer;

    /**
     * @var SettingService
     */
    protected SettingService $_service;

    /**
     * Constructor.
     * @param SettingService $service
     */
    public function __construct(SettingTransformer $transformer, SettingService $service)
    {
        $this->_transformer = $transformer;
        $this->_service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index(ListSettinsRequest $request)
    {
        $params = $request->validated();

        $items = SettingsQuery::make($params)->query()->paginate(
            $params['per_page'] ?? config('filters.per_page')
        );

        return response()->success($this->_transformer->transform($items));
    }

    /**
     * Show the user's profile
     * @param Setting $setting
     * @return mixed
     */
    public function show(Setting $setting)
    {
        return response()->success($this->_transformer->transform($setting));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateSettingRequest $request
     * @return mixed
     */
    public function store(CreateSettingRequest $request)
    {
        $params = $request->validated();

        $setting = $this->_service->createSetting($params);
        return response()->success($this->_transformer->transform($setting));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSettingRequest $request
     * @param Setting $setting
     * @return mixed
     */
    public function update(UpdateSettingRequest $request, Setting $setting)
    {
        $params = $request->validated();

        $setting = $this->_service->updateSetting($setting, $params);
        return response()->success($this->_transformer->transform($setting));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Setting $item
     * @return mixed
     */
    public function destroy(Setting $setting)
    {
        $this->authorize(User::PERMISSION_SETTING_DELETE);

        $setting->delete();

        return response()->message(__('The category :id has been removed.', ['id' => $setting->id]));
    }
}

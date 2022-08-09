<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    /**
     * Add a new page
     * @param array $data
     * @return Setting
     */
    public function createSetting(array $data): Setting
    {
        $setting = Setting::create($data);

        return $setting;
    }

    /**
     * Update new page
     * @param Setting $item
     * @param array $data
     * @return Setting
     */
    public function updateSetting(Setting $item, array $data): Setting
    {
        $item->update($data);

        return $item;
    }

}

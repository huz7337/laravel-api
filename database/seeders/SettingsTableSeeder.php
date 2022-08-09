<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'name' => 'home',
                'value' => 1,
                'default' => 1,
                'model' => 'page',
            ],
            [
                'name' => 'site-name',
                'value' => 'Web site',
                'default' => 1,
            ],
        ];


        foreach ($settings as $setting) {
            $this->addSetting($setting);
        }
    }

    /**
     * Add a super-admin user
     * @param array $data
     */
    private function addSetting(array $data)
    {
        Setting::create($data);
    }

}

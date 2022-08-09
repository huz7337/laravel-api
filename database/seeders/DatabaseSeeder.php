<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(RolesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(SuperAdminSeeder::class);
        $this->call(PagesTableSeeder::class);
        $this->call(MenuTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
    }
}

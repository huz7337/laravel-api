<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $pages = Page::all();

        foreach ($pages as $key => $page) {
            $this->addItem([
                'page_id' => $page->id,
                'order' => $key,
            ]);
        }
    }

    /**
     * Add a super-admin user
     * @param array $data
     */
    private function addItem(array $data)
    {
        Menu::create($data);
    }

}

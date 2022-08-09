<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $pages = [
            [
                'title' => 'Home page',
                'content' => 'Home content',
                'slug' => 'home',
            ],
            [
                'title' => 'Page 1',
                'content' => 'Page content 1',
                'slug' => 'page-1',
            ],
            [
                'title' => 'Page 2',
                'content' => 'Page content 2',
                'slug' => 'page-2',
            ],
        ];


        foreach ($pages as $page) {
            $this->addPage($page);
        }
    }

    /**
     * Add a super-admin user
     * @param array $data
     */
    private function addPage(array $data)
    {
        Page::create($data);
    }

}

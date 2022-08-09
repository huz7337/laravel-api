<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Str;

class PagesService
{
    /**
     * Add a new page
     * @param array $data
     * @return Page
     */
    public function createPage(array $data): Page
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $page = Page::create($data);

        return $page;
    }

    /**
     * Update new page
     * @param array $data
     * @return Page
     */
    public function updatePage(Page $page, array $data): Page
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $page->update($data);

        return $page;
    }

}

<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Menu;

class CategoryService
{
    /**
     * Add a new category
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update category
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

}

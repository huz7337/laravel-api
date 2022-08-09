<?php

namespace App\Services;

use App\Models\Menu;

class MenuService
{
    /**
     * Add a new page
     * @param array $data
     * @return Menu
     */
    public function createMenuItem(array $data): Menu
    {
        $data['order'] = $data['order'] ?? 0;
        $menu = Menu::create($data);

        return $menu;
    }

    /**
     * Update new page
     * @param Menu $item
     * @param array $data
     * @return Menu
     */
    public function updateMenuItem(Menu $item, array $data): Menu
    {
        $item->update($data);

        return $item;
    }

}

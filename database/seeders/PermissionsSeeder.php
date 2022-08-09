<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->setPermissionsForSuperAdmins();
        $this->setPermissionsForAdmins();
    }

    /**
     * Set the permissions for super admins
     */
    private function setPermissionsForSuperAdmins()
    {
        $permissions = $this->_permissionsForAdmins();

        $superAdmin = Role::findByName(User::ROLE_SUPER_ADMIN);
        $superAdmin->revokePermissionTo(Permission::all());
        $superAdmin->givePermissionTo($permissions);
    }

    /**
     * Set the permissions for admins
     */
    private function setPermissionsForAdmins()
    {
        $permissions = $this->_permissionsForAdmins();
        $superAdmin = Role::findByName(User::ROLE_ADMIN);
        $superAdmin->revokePermissionTo(Permission::all());
        $superAdmin->givePermissionTo($permissions);
    }

    /**
     * Permissions for admins
     * @return array
     */
    private function _permissionsForAdmins(): array
    {
        $permissions[] = Permission::findOrCreate(User::PERMISSION_USER_LIST);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_USER_SHOW);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_USER_CREATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_USER_UPDATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_USER_DELETE);

        $permissions[] = Permission::findOrCreate(User::PERMISSION_ROLE_LIST);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_ROLE_SHOW);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_ROLE_CREATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_ROLE_UPDATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_ROLE_DELETE);

        $permissions[] = Permission::findOrCreate(User::PERMISSION_PAGE_LIST);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_PAGE_SHOW);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_PAGE_CREATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_PAGE_UPDATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_PAGE_DELETE);

        $permissions[] = Permission::findOrCreate(User::PERMISSION_POST_LIST);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_POST_SHOW);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_POST_CREATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_POST_UPDATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_POST_DELETE);

        $permissions[] = Permission::findOrCreate(User::PERMISSION_MENU_LIST);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_MENU_CREATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_MENU_UPDATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_MENU_DELETE);

        $permissions[] = Permission::findOrCreate(User::PERMISSION_CATEGORY_LIST);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_CATEGORY_CREATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_CATEGORY_UPDATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_CATEGORY_DELETE);

        $permissions[] = Permission::findOrCreate(User::PERMISSION_SETTING_LIST);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_SETTING_CREATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_SETTING_UPDATE);
        $permissions[] = Permission::findOrCreate(User::PERMISSION_SETTING_DELETE);

        return $permissions;
    }
}

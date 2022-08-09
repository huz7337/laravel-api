<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'Huz',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            $this->addUser($user);
            $this->attachAdminRole($user);
        }
    }

    /**
     * Add a super-admin user
     * @param array $data
     */
    private function addUser(array $data)
    {
        if (User::where('email', $data['email'])->doesntExist()) {
            User::create($data);
        }
    }

    /**
     * Attach super-admin role to users
     * @param array $data
     */
    private function attachAdminRole(array $data)
    {
        User::findByEmail($data['email'])->assignRole('super-admin');
    }
}

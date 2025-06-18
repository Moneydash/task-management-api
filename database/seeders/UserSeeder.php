<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // seeder for admin
        $admin_data = [
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        $user_data = [
            [
                'name' => 'user1',
                'email' => 'user1@user.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'user2',
                'email' => 'user2@user.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'user3',
                'email' => 'user3@user.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        $admin = User::create($admin_data);
        $user = User::insert($user_data);
        $role_admin = Role::where('name', 'Admin')->first();
        $role_user = Role::where('name', 'User')->first();

        $admin->roles()->attach($role_admin->id);

        // Get all users except admin and assign User role
        $regularUsers = User::where('email', '!=', 'admin@admin.com')->get();
        foreach ($regularUsers as $regularUser) {
            $regularUser->roles()->attach($role_user->id);
        }
    }
}

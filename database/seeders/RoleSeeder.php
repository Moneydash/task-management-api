<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_data = [
            [
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'User',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        Role::insert($role_data);
    }
}

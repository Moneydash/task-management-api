<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $task_data = [
            [
                "title" => "Login Bugfix",
                "description" => "Fix the bug on the login page",
                "status" => "completed",
                "priority" => "high",
                "order" => 1,
                "user_id" => 2
            ],
            [
                "title" => "Login Bugfix",
                "description" => "Fix the bug on the login page",
                "status" => "pending",
                "priority" => "high",
                "order" => 2,
                "user_id" => 3
            ],
            [
                "title" => "Login Bugfix",
                "description" => "Fix the bug on the login page",
                "status" => "pending",
                "priority" => "high",
                "order" => 3,
                "user_id" => 2
            ],
        ];

        Task::insert($task_data);
    }
}

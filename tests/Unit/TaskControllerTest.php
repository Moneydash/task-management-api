<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a regular user
        $this->user = User::factory()->create();
        $this->user->assignRole('User');

        // Create an admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    public function test_read_by_id_returns_tasks_for_specific_user()
    {
        // Create tasks for the user
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
                        ->getJson("/api/tasks/getTasksById/{$this->user->id}");

        $response->assertStatus(200)
                ->assertJsonStructure(['tasks'])
                ->assertJsonCount(3, 'tasks');
    }

    public function test_read_returns_all_tasks_for_admin()
    {
        // Create tasks for different users
        Task::factory()->count(2)->create(['user_id' => $this->user->id]);
        Task::factory()->count(2)->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)
                        ->getJson('/api/tasks/getTasks');

        $response->assertStatus(200)
                ->assertJsonStructure(['tasks'])
                ->assertJsonCount(4, 'tasks');
    }

    public function test_store_creates_new_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'priority' => 'high',
            'user_id' => $this->user->id
        ];

        $response = $this->actingAs($this->user)
                        ->postJson('/api/tasks/addTasks', $taskData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'task' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'priority',
                        'order',
                        'user_id',
                        'assignedUser'
                    ]
                ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id
        ]);
    }

    public function test_update_modifies_existing_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $updateData = [
            'title' => 'Updated Task',
            'status' => 'completed',
            'user_id' => $this->user->id
        ];

        $response = $this->actingAs($this->user)
                        ->putJson("/api/tasks/updateTask/{$task->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Task data has been updated successfully!']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'status' => 'completed'
        ]);
    }

    public function test_reorder_updates_task_order()
    {
        // Create tasks for the user
        $task1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'order' => 1
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'order' => 2
        ]);

        $reorderData = [
            ['id' => $task2->id, 'order' => 1],
            ['id' => $task1->id, 'order' => 2]
        ];

        $response = $this->actingAs($this->user)
                        ->postJson('/api/tasks/reorder', $reorderData);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Tasks reordered successfully!']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task2->id,
            'order' => 1
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task1->id,
            'order' => 2
        ]);
    }

    public function test_cache_is_cleared_after_task_modification()
    {
        // Create a task
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Get tasks to populate cache
        $this->actingAs($this->user)
             ->getJson("/api/tasks/getTasksById/{$this->user->id}");

        // Verify cache exists
        $this->assertTrue(Cache::has("user_{$this->user->id}_tasks"));

        // Update the task
        $this->actingAs($this->user)
             ->putJson("/api/tasks/updateTask/{$task->id}", [
                 'title' => 'Updated Task',
                 'user_id' => $this->user->id
             ]);

        // Verify cache is cleared
        $this->assertFalse(Cache::has("user_{$this->user->id}_tasks"));
    }
}

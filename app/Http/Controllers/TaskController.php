<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    public function readById($user_id) {
        $cacheKey = "user_{$user_id}_tasks";
    
        $tasks = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($user_id) {
            return Task::where('user_id', $user_id)->orderBy('order')->get();
        });
        return response()->json(['tasks' => $tasks], 200);
    }

    # for admins
    public function read(Request $request) {
        $cacheKey = "all_tasks";
    
        $tasks = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return Task::with('assignedUser')->get();
        });

        return response()->json(['tasks' => $tasks], 200);
    }

    public function store(TaskRequest $request) {
        $user_task_count = Task::where('user_id', $request->user_id)->count();
        DB::beginTransaction();
        try {
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'priority' => $request->priority,
                'order' => $user_task_count + 1,
                'user_id' => $request->user_id
            ]);

            // Clear both caches
            Cache::forget("user_{$request->user_id}_tasks");
            Cache::forget("all_tasks");

            DB::commit();
            return response()->json([
                'message' => 'Task has been created successfully!',
                'task' => $task->load('assignedUser')
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Failed creating task", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'message' => 'Failed to create task. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(TaskRequest $request, $task_id) {
        DB::beginTransaction();
        try {
            $task = Task::findOrFail($task_id);
            
            foreach($request->all() as $key => $value) {
                if ($value != $task->$key) {
                    $task->$key = $value;
                }
            }

            // Clear both caches
            Cache::forget("user_{$request->user_id}_tasks");
            Cache::forget("all_tasks");

            $task->save();
            DB::commit();
            return response()->json(['message' => 'Task data has been updated successfully!'], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            Log::error("Failed updating task", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'message' => 'Failed to update task. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reorder(Request $request) {
        DB::beginTransaction();
        try {
            $tasks = $request->all();
            $firstTask = Task::find($tasks[0]['id']);
            $user_id = $firstTask->user_id;
            
            foreach ($tasks as $task) {
                Task::where('id', $task['id'])
                    ->update(['order' => $task['order']]);
            }

            Cache::forget("user_{$user_id}_tasks");
            Cache::forget("all_tasks");

            DB::commit();
            return response()->json([
                'message' => 'Tasks reordered successfully!',
                'tasks' => Task::orderBy('order')->get()
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Failed reordering tasks", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'message' => 'Failed to reorder tasks. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function remove($task_id) {
        DB::beginTransaction();
        try {
            $task = Task::findOrFail($task_id);

            $task->delete();
            DB::commit();

            return response()->json(['message' => 'Task deleted sucessfully!'], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            Log::error("Failed deleting task", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'message' => 'Failed to delete task. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

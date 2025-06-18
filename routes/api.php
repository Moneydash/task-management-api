<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('register', [UserController::class, 'store'])->name('user.register');
Route::post('login', [UserController::class, 'login'])->name('user.login');

Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('tasks')->middleware('role:Admin,User')->group(function() {
        Route::get("getTasksByUserId/{user_id}", [TaskController::class, 'readById'])->name('tasks.readById');
        Route::post("addTasks", [TaskController::class, 'store'])->name('tasks.store');
        Route::put("updateTask/{task_id}", [TaskController::class, 'update'])->name('tasks.update');
        Route::post("reorder", [TaskController::class, 'reorder'])->name('tasks.reorder');
    });

    Route::prefix("tasks")->middleware('role:Admin')->group(function() {
        Route::delete('removeTask/{task_id}', [TaskController::class, 'remove'])->name('tasks.remove');
        Route::get("getTasks", [TaskController::class, 'read'])->name('tasks.read');
    });

    Route::prefix("user")->middleware('role:Admin')->group(function() {
        Route::get("lists", [UserController::class, 'readNonAdmin'])->name('user.nonAdminLists');
    });
});
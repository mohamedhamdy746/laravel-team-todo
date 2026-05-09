<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'hi hamdy'
    ]);
});

// Tasks routes
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

Route::get('/tasks/{task}', [TaskController::class, 'show'])->whereNumber('task')->name('tasks.show');
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->whereNumber('task')->name('tasks.edit');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->whereNumber('task')->name('tasks.update');
Route::put('/tasks/{task}/restore', [TaskController::class, 'restore'])->whereNumber('task')->name('tasks.restore');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->whereNumber('task')->name('tasks.destroy');
Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->whereNumber('task')->name('tasks.comments.store');

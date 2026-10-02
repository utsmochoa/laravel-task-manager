<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Redirige /tasks a /dashboard para mantener un único punto de entrada a las tareas
    Route::redirect('/tasks', '/dashboard');

    // Mantiene las rutas CRUD (create, store, show, edit, update, destroy)
    Route::resource('tasks', TaskController::class)->except(['index']);

    // Vista principal del Dashboard / Lista de tareas
    Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');

    // Rutas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
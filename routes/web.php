<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlmacentareaController;


// Acceso principal que carga la interfaz de tareas
Route::get("/", [AlmacentareaController::class, 'index'])->name('main');

// Rutas de recurso estándar (Create, Read, Update, Delete)
Route::resource("tareas", AlmacentareaController::class);

// Ruta personalizada para cambiar el estado dinámicamente desde la tabla
Route::patch('/tareas/{id}/cambiar-estado', [AlmacentareaController::class, 'cambiarEstado'])->name('tareas.cambiarEstado');





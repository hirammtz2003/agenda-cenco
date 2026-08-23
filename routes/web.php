<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MiPerfilController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\ReservaController;

// ============ RUTAS PÚBLICAS ============
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Recuperación de contraseña (pública)
Route::post('/recuperacion/validar', [App\Http\Controllers\Auth\RecuperacionController::class, 'validar'])
    ->name('recuperacion.validar');

// ============ RUTAS PROTEGIDAS (Requieren autenticación) ============
Route::middleware(['auth'])->group(function () {
    
    // Página de inicio (welcome)
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
    
    // Perfil de usuario (todos pueden ver su perfil)
    Route::get('/perfil', [MiPerfilController::class, 'show'])->name('mi-perfil');
    Route::put('/perfil', [MiPerfilController::class, 'update'])->name('profile.update');
    
    // ============ MÓDULOS DE RESERVA (Todos los usuarios autenticados) ============
    Route::prefix('reservas')->name('reservas.')->group(function () {
        Route::get('/', [ReservaController::class, 'index'])->name('index');
        Route::get('/crear', [ReservaController::class, 'create'])->name('create');
        Route::post('/store', [ReservaController::class, 'store'])->name('store');
        Route::get('/{id}/editar', [ReservaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ReservaController::class, 'update'])->name('update');
        Route::delete('/{id}', [ReservaController::class, 'destroy'])->name('destroy');
        Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])->name('mis-reservas');
        Route::get('/calendario', [ReservaController::class, 'calendario'])->name('calendario');
    });
    
    // ============ MÓDULOS DE ADMINISTRACIÓN (SOLO ADMINISTRADORES) ============
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        
        // Administración de Usuarios
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [UsuarioController::class, 'index'])->name('index');
            Route::get('/registro', [UsuarioController::class, 'create'])->name('registro');
            Route::post('/', [UsuarioController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [UsuarioController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UsuarioController::class, 'update'])->name('update');
            Route::delete('/{id}', [UsuarioController::class, 'destroy'])->name('destroy');
            Route::get('/consulta', [UsuarioController::class, 'consulta'])->name('consulta');
            Route::get('/seguridad', [UsuarioController::class, 'seguridad'])->name('seguridad');
            Route::post('/guardar-seguridad', [UsuarioController::class, 'guardarSeguridad'])->name('guardar-seguridad');
        });
        
        // Administración de Grupos
        Route::prefix('grupos')->name('grupos.')->group(function () {
            Route::get('/', [GrupoController::class, 'index'])->name('index');
            Route::get('/listar', [GrupoController::class, 'listar'])->name('listar');
            Route::post('/guardar', [GrupoController::class, 'guardar'])->name('guardar');
            Route::delete('/{id}', [GrupoController::class, 'eliminar'])->name('eliminar');
        });
    });
});
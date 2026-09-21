<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MiPerfilController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\MateriaLaboratorioController;
use App\Http\Controllers\SolicitudController;

// ============ RUTAS PÚBLICAS ============
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Recuperación de contraseña
Route::post('/recuperacion/validar', [App\Http\Controllers\Auth\RecuperacionController::class, 'validar'])
    ->name('recuperacion.validar');

// ============ RUTAS PROTEGIDAS ============
Route::middleware(['auth', 'verificar.password.temporal'])->group(function () {
    
    // Página de inicio
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
    
    // Perfil de usuario
    Route::get('/perfil', [MiPerfilController::class, 'show'])->name('mi-perfil');
    Route::put('/perfil', [MiPerfilController::class, 'update'])->name('profile.update');
    
    // ============ SOLICITUD DE RESERVA ============
    Route::prefix('solicitud')->name('solicitud.')->group(function () {
        Route::get('/', [SolicitudController::class, 'index'])->name('index');
        Route::post('/store', [SolicitudController::class, 'store'])->name('store');
        Route::get('/buscar/materias', [SolicitudController::class, 'buscarMaterias'])->name('buscar.materias');
        Route::get('/buscar/grupos', [SolicitudController::class, 'buscarGrupos'])->name('buscar.grupos');
    });
    
    // ============ MÓDULOS DE ADMINISTRACIÓN (SOLO ADMIN) ============
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        
        Route::get('/', function () {
            return redirect()->route('admin.usuarios.index');
        });
        
        // Administración de Usuarios
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [UsuarioController::class, 'index'])->name('index');
            Route::get('/registro', [UsuarioController::class, 'create'])->name('registro');
            Route::post('/', [UsuarioController::class, 'store'])->name('store');
            Route::get('/consulta', [UsuarioController::class, 'consulta'])->name('consulta');
            Route::get('/seguridad', [UsuarioController::class, 'seguridad'])->name('seguridad');
            
            // AJAX endpoints
            Route::post('/update-bulk', [UsuarioController::class, 'updateBulk'])->name('update-bulk');
            Route::delete('/{id}', [UsuarioController::class, 'destroy'])->name('destroy');
            Route::post('/buscar-seguridad', [UsuarioController::class, 'buscarParaSeguridad'])->name('buscar-seguridad');
            Route::get('/{id}/obtener-seguridad', [UsuarioController::class, 'obtenerUsuarioSeguridad'])->name('obtener-seguridad');
            Route::post('/guardar-seguridad', [UsuarioController::class, 'guardarSeguridad'])->name('guardar-seguridad');
        });
        
        // Administración de Grupos
        Route::prefix('grupos')->name('grupos.')->group(function () {
            Route::get('/', [GrupoController::class, 'index'])->name('index');
            Route::get('/listar', [GrupoController::class, 'listar'])->name('listar');
            Route::post('/guardar', [GrupoController::class, 'guardar'])->name('guardar');
            Route::get('/{id}', [GrupoController::class, 'obtener'])->name('obtener');
            Route::delete('/{id}', [GrupoController::class, 'eliminar'])->name('eliminar');
        });

        // Configuración de Materias y Laboratorios
        Route::prefix('materias-lab')->name('materias.')->group(function () {
            Route::get('/', [MateriaLaboratorioController::class, 'index'])->name('index');
            
            // Materias
            Route::get('/materias/listar', [MateriaLaboratorioController::class, 'listarMaterias'])->name('listar');
            Route::post('/materias/guardar', [MateriaLaboratorioController::class, 'guardarMateria'])->name('guardar');
            Route::get('/materias/{id}', [MateriaLaboratorioController::class, 'obtenerMateria'])->name('obtener');
            Route::delete('/materias/{id}', [MateriaLaboratorioController::class, 'eliminarMateria'])->name('eliminar');
            
            // Laboratorios
            Route::get('/laboratorios/listar', [MateriaLaboratorioController::class, 'listarLaboratorios'])->name('lab.listar');
            Route::post('/laboratorios/guardar', [MateriaLaboratorioController::class, 'guardarLaboratorio'])->name('lab.guardar');
            Route::get('/laboratorios/{id}', [MateriaLaboratorioController::class, 'obtenerLaboratorio'])->name('lab.obtener');
            Route::delete('/laboratorios/{id}', [MateriaLaboratorioController::class, 'eliminarLaboratorio'])->name('lab.eliminar');
            
            // Listado completo de laboratorios para el select de materias
            Route::get('/laboratorios-para-select', [MateriaLaboratorioController::class, 'listarLaboratorios'])->name('lab.select');
        });
    });

    
});
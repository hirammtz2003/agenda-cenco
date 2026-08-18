<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterTempController;
use App\Http\Controllers\MiPerfilController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\AlumnoController;

// Ruta de bienvenida (index)
Route::get('/', function () {
    return view('welcome');
})->name('welcome')->middleware('auth'); 

// Ruta de recuperación de contraseña (pública)
Route::post('/recuperacion/validar', [App\Http\Controllers\Auth\RecuperacionController::class, 'validar'])
    ->name('recuperacion.validar');

// Rutas de autenticación 
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware(['auth', 'verificar.password.temporal'])->group(function () {
    // Todas las rutas protegidas excepto mi-perfil y logout
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    // Perfil de usuario
    Route::get('/perfil', [MiPerfilController::class, 'show'])->name('mi-perfil');
    Route::put('/perfil', [MiPerfilController::class, 'update'])->name('profile.update');
    
    // ===== ADMINISTRACIÓN DE USUARIOS =====
    // Todas las rutas de admin requieren autenticación
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Rutas de CONSULTA (accesibles para G o C)
        Route::middleware(['puede.consultar'])->group(function () {
            Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
            Route::get('/usuarios/consulta', [UsuarioController::class, 'consulta'])->name('usuarios.consulta');
        });
        
        // Rutas de EDICIÓN (solo accesibles para G)
        Route::middleware(['puede.editar'])->group(function () {
            // Registro de usuarios
            Route::get('/usuarios/registro', [UsuarioController::class, 'create'])->name('usuarios.registro');
            Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
            
            // Edición y eliminación (acciones que modifican datos)
            Route::get('/usuarios/{id}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
            Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
            Route::post('/usuarios/update-bulk', [UsuarioController::class, 'updateBulk'])->name('usuarios.update-bulk');
            Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

            // Rutas para Seguridad y Privilegios
            Route::get('/usuarios/seguridad', [UsuarioController::class, 'seguridad'])->name('usuarios.seguridad');
            Route::post('/usuarios/buscar-seguridad', [UsuarioController::class, 'buscarParaSeguridad'])->name('usuarios.buscar-seguridad');
            Route::get('/usuarios/{id}/obtener-seguridad', [UsuarioController::class, 'obtenerUsuarioSeguridad'])->name('usuarios.obtener-seguridad');
            Route::post('/usuarios/guardar-seguridad', [UsuarioController::class, 'guardarSeguridad'])->name('usuarios.guardar-seguridad');            
        });

        // Rutas para grupos
        Route::prefix('grupos')->name('grupos.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\GrupoController::class, 'index'])->name('index');
            Route::get('/listar', [App\Http\Controllers\Admin\GrupoController::class, 'listar'])->name('listar');
            Route::post('/buscar-usuarios', [App\Http\Controllers\Admin\GrupoController::class, 'buscarUsuarios'])->name('buscar-usuarios');
            Route::post('/guardar', [App\Http\Controllers\Admin\GrupoController::class, 'guardar'])->name('guardar');
            Route::get('/{id}', [App\Http\Controllers\Admin\GrupoController::class, 'obtener'])->name('obtener');
            Route::delete('/{id}', [App\Http\Controllers\Admin\GrupoController::class, 'eliminar'])->name('eliminar');
            Route::post('/migrar', [App\Http\Controllers\Admin\GrupoController::class, 'migrarGrupos'])->name('migrar');
        });
    });

    // Rutas para becas (accesibles según permisos en posición 1 y 2)
    Route::prefix('becas')->name('becas.')->middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\BecaController::class, 'index'])->name('index');
        Route::get('/listar', [App\Http\Controllers\BecaController::class, 'listar'])->name('listar');
        Route::post('/guardar', [App\Http\Controllers\BecaController::class, 'guardar'])->name('guardar');
        Route::get('/{id}', [App\Http\Controllers\BecaController::class, 'obtener'])->name('obtener');
        Route::delete('/{id}', [App\Http\Controllers\BecaController::class, 'eliminar'])->name('eliminar');
    });

    // Rutas para gestión de alumnos
    Route::prefix('alumnos')->name('alumnos.')->middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\AlumnoController::class, 'index'])->name('index');
        Route::get('/registro', [App\Http\Controllers\AlumnoController::class, 'create'])->name('registro');

        Route::post('/alumnos/validar-numero-control', [AlumnoController::class, 'validarNumeroControl'])->name('validar-numero-control');
        
        // Rutas para lugar de nacimiento (API)
        Route::post('/buscar-lugar', [App\Http\Controllers\AlumnoController::class, 'buscarLugarNacimiento'])->name('buscar-lugar');
        Route::get('/lugar/{id}', [App\Http\Controllers\AlumnoController::class, 'obtenerLugarNacimiento'])->name('obtener-lugar');
        Route::post('/guardar-lugar', [App\Http\Controllers\AlumnoController::class, 'guardarLugarNacimiento'])->name('guardar-lugar');

        // Rutas para domicilio (API)
        Route::post('/buscar-domicilio', [App\Http\Controllers\AlumnoController::class, 'buscarDomicilio'])->name('buscar-domicilio');
        Route::get('/domicilio/{id}', [App\Http\Controllers\AlumnoController::class, 'obtenerDomicilio'])->name('obtener-domicilio');
        Route::post('/guardar-domicilio', [App\Http\Controllers\AlumnoController::class, 'guardarDomicilio'])->name('guardar-domicilio');
    
        // Rutas para secundaria (API)
        Route::post('/buscar-secundaria', [App\Http\Controllers\AlumnoController::class, 'buscarSecundaria'])->name('buscar-secundaria');
        Route::get('/secundaria/{id}', [App\Http\Controllers\AlumnoController::class, 'obtenerSecundaria'])->name('obtener-secundaria');
        Route::post('/guardar-secundaria', [App\Http\Controllers\AlumnoController::class, 'guardarSecundaria'])->name('guardar-secundaria');

        // Rutas para alumnos (API)
        Route::post('/buscar-apellido', [App\Http\Controllers\AlumnoController::class, 'buscarApellido'])->name('buscar-apellido');
        Route::post('/buscar-grupos', [App\Http\Controllers\AlumnoController::class, 'buscarGrupos'])->name('buscar-grupos');
        Route::post('/guardar-alumno', [App\Http\Controllers\AlumnoController::class, 'guardarAlumno'])->name('guardar-alumno');

        // Rutas para becas (API)
        Route::post('/buscar-becas', [App\Http\Controllers\AlumnoController::class, 'buscarBecas'])->name('buscar-becas');
        Route::post('/agregar-beca-temporal', [App\Http\Controllers\AlumnoController::class, 'agregarBecaTemporal'])->name('agregar-beca-temporal');
        Route::post('/eliminar-beca-temporal', [App\Http\Controllers\AlumnoController::class, 'eliminarBecaTemporal'])->name('eliminar-beca-temporal');
        Route::post('/limpiar-becas-temporales', [App\Http\Controllers\AlumnoController::class, 'limpiarBecasTemporales'])->name('limpiar-becas-temporales');
        Route::post('/guardar-beca-alumno', [AlumnoController::class, 'guardarBecaAlumno'])->name('guardar-beca-alumno');

        // Ruta para trabajo del alumno
        Route::post('/guardar-trabajo', [App\Http\Controllers\AlumnoController::class, 'guardarTrabajo'])->name('guardar-trabajo');

        // Rutas para familiares (API)
        Route::post('/buscar-parentesco', [App\Http\Controllers\AlumnoController::class, 'buscarParentesco'])->name('buscar-parentesco');
        Route::post('/agregar-familiar-temporal', [App\Http\Controllers\AlumnoController::class, 'agregarFamiliarTemporal'])->name('agregar-familiar-temporal');
        Route::post('/eliminar-familiar-temporal', [App\Http\Controllers\AlumnoController::class, 'eliminarFamiliarTemporal'])->name('eliminar-familiar-temporal');
        Route::get('/familiar-temporal/{tempId}', [App\Http\Controllers\AlumnoController::class, 'obtenerFamiliarTemporal'])->name('obtener-familiar-temporal');
        Route::post('/limpiar-familiares-temporales', [App\Http\Controllers\AlumnoController::class, 'limpiarFamiliaresTemporales'])->name('limpiar-familiares-temporales');

        // Rutas para datos familiares (API)
        Route::post('/buscar-estado-civil', [App\Http\Controllers\AlumnoController::class, 'buscarEstadoCivil'])->name('buscar-estado-civil');
        Route::post('/guardar-datos-familiares', [App\Http\Controllers\AlumnoController::class, 'guardarDatosFamiliares'])->name('guardar-datos-familiares');

        // Rutas para información socioeconómica (API)
        Route::post('/buscar-transportes', [App\Http\Controllers\AlumnoController::class, 'buscarTransportes'])->name('buscar-transportes');
        Route::post('/buscar-estado-civil-alumno', [App\Http\Controllers\AlumnoController::class, 'buscarEstadoCivilAlumno'])->name('buscar-estado-civil-alumno');
        Route::post('/guardar-info-socioeco', [App\Http\Controllers\AlumnoController::class, 'guardarInfoSocioeco'])->name('guardar-info-socioeco');

        // Rutas para datos académicos (API)
        Route::post('/guardar-datos-academicos', [App\Http\Controllers\AlumnoController::class, 'guardarDatosAcademicos'])->name('guardar-datos-academicos');
        Route::post('/guardar-problema-aprendizaje', [App\Http\Controllers\AlumnoController::class, 'guardarProblemaAprendizaje'])->name('guardar-problema-aprendizaje');

        // Rutas para datos de salud (API)
        Route::post('/guardar-datos-salud', [App\Http\Controllers\AlumnoController::class, 'guardarDatosSalud'])->name('guardar-datos-salud');

        // Rutas para actividades recreativas (API)
        Route::post('/buscar-deportes', [App\Http\Controllers\AlumnoController::class, 'buscarDeportes'])->name('buscar-deportes');
        Route::post('/guardar-actividades-recreativas', [App\Http\Controllers\AlumnoController::class, 'guardarActividadesRecreativas'])->name('guardar-actividades-recreativas');

        // Rutas para consulta y edición general
        Route::get('/consulta-general', [App\Http\Controllers\AlumnoController::class, 'consultaGeneral'])->name('consulta-general');
        Route::post('/buscar-grupos-tabla', [App\Http\Controllers\AlumnoController::class, 'buscarGruposTabla'])->name('buscar-grupos-tabla');
        Route::post('/actualizar-alumno-tabla', [App\Http\Controllers\AlumnoController::class, 'actualizarAlumnoTabla'])->name('actualizar-alumno-tabla');
        Route::post('/cambiar-estatus-alumno', [App\Http\Controllers\AlumnoController::class, 'cambiarEstatusAlumno'])->name('cambiar-estatus-alumno');

        // Rutas para consulta y edición individual
        Route::get('/consulta-individual', [App\Http\Controllers\AlumnoController::class, 'consultaIndividual'])->name('consulta-individual');
        Route::post('/buscar-alumnos-auto', [App\Http\Controllers\AlumnoController::class, 'buscarAlumnosAuto'])->name('buscar-alumnos-auto');
        Route::post('/toggle-sections-mode', [App\Http\Controllers\AlumnoController::class, 'toggleSectionsMode'])->name('toggle-sections-mode');
        Route::get('/get-sections-mode', [App\Http\Controllers\AlumnoController::class, 'getSectionsMode'])->name('get-sections-mode');

        // Ruta para actualizar alumno completo (crear más adelante)
        Route::post('/actualizar-alumno-completo', [App\Http\Controllers\AlumnoController::class, 'actualizarAlumnoCompleto'])->name('actualizar-alumno-completo');
        Route::get('/obtener-alumno-completo/{id}', [App\Http\Controllers\AlumnoController::class, 'obtenerAlumnoCompleto'])->name('obtener-alumno-completo');
        Route::post('/eliminar-beca-alumno', [App\Http\Controllers\AlumnoController::class, 'eliminarBecaAlumno'])->name('eliminar-beca-alumno');
    });

    // Datos y Estadísticas
    Route::prefix('datos-estadisticas')->name('datos-estadisticas.')->middleware(['auth', 'verificar.password.temporal'])->group(function () {
        
        Route::get('/', [App\Http\Controllers\DatosEstadisticasController::class, 'index'])->name('index');
        Route::get('/datos-prioritarios', [App\Http\Controllers\DatosEstadisticasController::class, 'datosPrioritarios'])->name('datos-prioritarios');
        Route::get('/estadisticas', [App\Http\Controllers\DatosEstadisticasController::class, 'estadisticas'])->name('estadisticas');
        // Grupo de rutas de Datos y Estadísticas
        Route::post('/estadisticas/grupos', [App\Http\Controllers\EstadisticasController::class, 'generarInformeGrupos'])->name('estadisticas.grupos');
        Route::post('/estadisticas/secundarias', [App\Http\Controllers\EstadisticasController::class, 'generarInformeSecundarias'])->name('estadisticas.secundarias');
        Route::post('/estadisticas/becas', [App\Http\Controllers\EstadisticasController::class, 'generarInformeBecas'])->name('estadisticas.becas');
        Route::post('/estadisticas/domicilios', [App\Http\Controllers\EstadisticasController::class, 'generarInformeDomicilios'])->name('estadisticas.domicilios');
        Route::post('/estadisticas/socioeconomica', [App\Http\Controllers\EstadisticasController::class, 'generarInformeSocioeconomico'])->name('estadisticas.socioeconomica');
        Route::post('/estadisticas/salud', [App\Http\Controllers\EstadisticasController::class, 'generarInformeSalud'])->name('estadisticas.salud');
        
        // API
        Route::post('/buscar-alumno', [App\Http\Controllers\DatosEstadisticasController::class, 'buscarAlumno'])->name('buscar-alumno');
        Route::post('/buscar-localidades', [App\Http\Controllers\DatosEstadisticasController::class, 'buscarLocalidades'])->name('buscar-localidades');
        Route::post('/aplicar-filtros', [App\Http\Controllers\DatosEstadisticasController::class, 'aplicarFiltros'])->name('aplicar-filtros');
        Route::post('/generar-listado', [App\Http\Controllers\DatosEstadisticasController::class, 'generarListadoPDF'])->name('generar-listado');
        Route::post('/generar-pdf', [App\Http\Controllers\DatosEstadisticasController::class, 'generarPDF'])->name('generar-pdf');
    });
});
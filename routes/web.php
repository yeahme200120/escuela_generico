<?php

use App\Http\Controllers\Alumnos\AlumnoController;
use App\Http\Controllers\Alumnos\InscripcionController;
use App\Http\Controllers\Admisiones\ProspectoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auditoria\AccessLogController;
use App\Http\Controllers\Auditoria\AuditLogController;
use App\Http\Controllers\Auditoria\QueryLogController;
use App\Http\Controllers\Auditoria\SesionesController;
use App\Http\Controllers\Configuracion\AparienciaController;
use App\Http\Controllers\Documentos\DocumentoController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\Finanzas\CargoController;
use App\Http\Controllers\Finanzas\CajaController;
use App\Http\Controllers\Finanzas\PagoController;
use App\Http\Controllers\Inventario\InventarioController;
use App\Http\Controllers\RH\EmpleadoController;
use Illuminate\Support\Facades\Route;

// ── Raíz ────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

// ── Guest ────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

// ── Panel autenticado ────────────────────────────────────────────────────
Route::middleware(['auth', 'check.active'])->group(function () {

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // ── Estudiantes ──────────────────────────────────────────────────
    Route::resource('estudiantes', EstudianteController::class);

    // ── Alumnos ─────────────────────────────────────────────────────
    Route::resource('alumnos', AlumnoController::class);
    Route::post('/alumnos/inscripcion', [InscripcionController::class, 'store'])
         ->name('alumnos.inscripcion.store');

    // ── Finanzas ─────────────────────────────────────────────────────
    Route::prefix('finanzas')->name('finanzas.')->group(function () {
        // Cargos
        Route::get('/cargos',  [CargoController::class, 'index'])->name('cargos.index');
        Route::post('/cargos', [CargoController::class, 'store'])->name('cargos.store');

        // Pagos
        Route::get('/pagos',          [PagoController::class, 'index'])  ->name('pagos.index');
        Route::post('/pagos',         [PagoController::class, 'store'])  ->name('pagos.store');
        Route::delete('/pagos/{pago}',[PagoController::class, 'destroy'])->name('pagos.destroy');

        // Caja
        Route::get('/caja',                          [CajaController::class, 'index']) ->name('caja.index');
        Route::post('/caja/{caja}/abrir',            [CajaController::class, 'abrir']) ->name('caja.abrir');
        Route::post('/caja/turno/{turno}/cerrar',    [CajaController::class, 'cerrar'])->name('caja.cerrar');
    });

    // ── Auditoría ────────────────────────────────────────────────────
    // Organización / Catálogos (scaffolded)
    Route::resource('organizaciones', App\Http\Controllers\OrganizacionController::class);
    Route::resource('escuelas', App\Http\Controllers\EscuelaController::class);
    Route::resource('sedes', App\Http\Controllers\SedeController::class);
    Route::resource('edificios', App\Http\Controllers\EdificioController::class);
    Route::resource('aulas', App\Http\Controllers\AulaController::class);
    Route::resource('ciclos', App\Http\Controllers\CicloEscolarController::class);
    Route::resource('niveles', App\Http\Controllers\NivelController::class);
    Route::resource('grados', App\Http\Controllers\GradoController::class);
    Route::resource('grupos', App\Http\Controllers\GrupoController::class);
    Route::resource('materias', App\Http\Controllers\MateriaController::class);
    Route::resource('planes', App\Http\Controllers\PlanEstudioController::class);

    // Académico
    Route::resource('horarios', App\Http\Controllers\HorarioController::class);
    Route::resource('asistencias', App\Http\Controllers\AsistenciaController::class);
    Route::resource('calificaciones', App\Http\Controllers\CalificacionController::class);
    Route::resource('periodos-evaluacion', App\Http\Controllers\PeriodoEvaluacionController::class);
    Route::resource('regularizaciones', App\Http\Controllers\RegularizacionController::class);

    // Alumnos / Trayectoria
    Route::resource('bajas', App\Http\Controllers\BajaController::class);
    Route::resource('tutores', App\Http\Controllers\TutorController::class);
    Route::resource('trayectorias', App\Http\Controllers\TrayectoriaController::class);
    Route::resource('docentes', App\Http\Controllers\DocenteController::class);

    // Finanzas adicionales
    Route::resource('conceptos', App\Http\Controllers\ConceptoPagoController::class);
    Route::resource('parcialidades', App\Http\Controllers\ParcialidadController::class);

    // Usuarios / Seguridad
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('roles', App\Http\Controllers\RolController::class);
    Route::resource('password-resets', App\Http\Controllers\PasswordResetController::class);
    Route::resource('two-factor', App\Http\Controllers\TwoFactorController::class);

    // RH / Inventario / Mantenimiento
    Route::resource('contratos', App\Http\Controllers\ContratoController::class);
    Route::resource('asistencia-personal', App\Http\Controllers\AsistenciaPersonalController::class);
    Route::resource('activos-fijos', App\Http\Controllers\ActivoFijoController::class);
    Route::resource('mantenimientos', App\Http\Controllers\MantenimientoController::class);

    // Comunicación / Calendario / Admisiones
    Route::resource('notificaciones', App\Http\Controllers\NotificacionController::class);
    Route::resource('calendario', App\Http\Controllers\CalendarioController::class);
    Route::resource('admisiones', App\Http\Controllers\AdmisionController::class);

    // Reportes
    Route::resource('reportes', App\Http\Controllers\ReporteController::class);

    // ── Auditoría ────────────────────────────────────────────────────
    Route::prefix('auditoria')->name('auditoria.')->group(function () {
        Route::get('/',         [AuditLogController::class,  'index'])->name('index');
        Route::get('/accesos',  [AccessLogController::class, 'index'])->name('accesos');
        Route::get('/sesiones', [SesionesController::class,  'index'])->name('sesiones');
        Route::delete('/sesiones/{uuid}', [SesionesController::class, 'destroy'])->name('sesiones.destroy');
        Route::get('/queries',  [QueryLogController::class,  'index'])->name('queries');
    });

    // ── Configuración ────────────────────────────────────────────────
    Route::prefix('configuracion')->name('configuracion.')->group(function () {
        Route::get('/apariencia',  [AparienciaController::class, 'index']) ->name('apariencia');
        Route::post('/apariencia', [AparienciaController::class, 'update'])->name('apariencia.update');
    });

    // ── RH ────────────────────────────────────────────────────────────
    Route::resource('rh/empleados', EmpleadoController::class)->names('rh.empleados');

    // ── Inventario ───────────────────────────────────────────────────
    Route::prefix('inventario')->name('inventario.')->group(function () {
        Route::get('/',              [InventarioController::class,'index'])      ->name('index');
        Route::post('/',             [InventarioController::class,'store'])      ->name('store');
        Route::post('/{id}/movimiento',[InventarioController::class,'movimiento'])->name('movimiento');
    });

    // ── Admisiones ───────────────────────────────────────────────────
    Route::prefix('admisiones')->name('admisiones.')->group(function () {
        Route::get('/prospectos',            [ProspectoController::class,'index'])      ->name('prospectos.index');
        Route::post('/prospectos',           [ProspectoController::class,'store'])      ->name('prospectos.store');
        Route::get('/prospectos/{id}',       [ProspectoController::class,'show'])       ->name('prospectos.show');
        Route::post('/prospectos/{id}/seguimiento',[ProspectoController::class,'seguimiento'])->name('prospectos.seguimiento');
    });

    // ── Documentos ───────────────────────────────────────────────────
    Route::prefix('documentos')->name('documentos.')->group(function () {
        Route::get('/',                         [DocumentoController::class,'index'])   ->name('index');
        Route::post('/',                        [DocumentoController::class,'store'])   ->name('store');
        Route::post('/{documento}/autorizar',   [DocumentoController::class,'autorizar'])->name('autorizar');
    });
});

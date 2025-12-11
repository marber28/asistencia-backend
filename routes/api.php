<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AulaController;
use App\Http\Controllers\Api\AnexoController;
use App\Http\Controllers\Api\AlumnoController;
use App\Http\Controllers\Api\MaestroController;
use App\Http\Controllers\Api\LeccionController;
use App\Http\Controllers\Api\AsistenciaAlumnoPDFController;
use App\Http\Controllers\Api\AsistenciaAlumnoController;
use App\Http\Controllers\Api\AsistenciaMaestroController;

use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\ReportController;

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    // aquí tus recursos protegidos...
    Route::get('usuarios/list', [UserController::class, 'list']);
    Route::apiResource('usuarios', UserController::class);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    Route::apiResource('aulas', AulaController::class);
    Route::apiResource('anexos', AnexoController::class);
    Route::post('alumnos/{alumnoId}/asignar-aula', [AlumnoController::class, 'asignarAula']);
    Route::apiResource('alumnos', AlumnoController::class);
    Route::post('alumnos/massive', [AlumnoController::class, 'storeMassive']);
    Route::get("alumnos/massive/template", [AlumnoController::class, "massiveTemplate"]);

    Route::apiResource('maestros', MaestroController::class);

    Route::post('lecciones/massive', [LeccionController::class, 'storeMassive']);
    Route::get("lecciones/massive/template", [LeccionController::class, "massiveTemplate"]);
    Route::apiResource('lecciones', LeccionController::class)->parameters(['lecciones' => 'leccion']);
    Route::get('lecciones/{leccion}/download', [LeccionController::class, 'downloadLeccion']);

    Route::get('/asistencia/pdf', [AsistenciaAlumnoPDFController::class, 'pdfMensual']);
    Route::post('asistencia-alumno/massive', [AsistenciaAlumnoController::class, 'storeMassive']);
    Route::get("asistencia-alumno/massive/template", [AsistenciaAlumnoController::class, "massiveTemplate"]);
    Route::apiResource('asistencia-alumno', AsistenciaAlumnoController::class);
    Route::get('aa/dia/{fecha}', [AsistenciaAlumnoController::class, 'porDia']);
    Route::get('aa/mes/{year}/{month}', [AsistenciaAlumnoController::class, 'porMes']);
    Route::get('aa/anio/{year}', [AsistenciaAlumnoController::class, 'porAnio']);

    Route::apiResource('asistencia-maestro', AsistenciaMaestroController::class);
    Route::get('am/dia/{fecha}', [AsistenciaMaestroController::class, 'porDia']);
    Route::get('am/mes/{year}/{month}', [AsistenciaMaestroController::class, 'porMes']);
    Route::get('am/anio/{year}', [AsistenciaMaestroController::class, 'porAnio']);
    
    Route::post('upload/leccion', [UploadController::class,'uploadLeccionPdf']);
    Route::get('reports/download', [ReportController::class,'downloadMonthly']);
});

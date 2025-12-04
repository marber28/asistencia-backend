<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AulaController;
use App\Http\Controllers\Api\AnexoController;
use App\Http\Controllers\Api\AlumnoController;
use App\Http\Controllers\Api\MaestroController;
use App\Http\Controllers\Api\LeccionController;
use App\Http\Controllers\Api\AsistenciaAlumnoController;
use App\Http\Controllers\Api\AsistenciaMaestroController;

use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\ReportController;

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    // aquí tus recursos protegidos...
    Route::apiResource('usuarios', UserController::class);
    Route::get('usuarios/list', [UserController::class, 'list']);

    Route::apiResource('aulas', AulaController::class);
    Route::apiResource('anexos', AnexoController::class);
    Route::apiResource('alumnos', AlumnoController::class);
    Route::apiResource('maestros', MaestroController::class);
    Route::apiResource('lecciones', LeccionController::class);
    Route::get('lecciones/{leccion}/download', [LeccionController::class, 'downloadLeccion']);

    Route::apiResource('asistencia-alumno', AsistenciaAlumnoController::class);
    Route::get('aa/dia/{fecha}', [AsistenciaAlumnoController::class, 'porDia']);
    Route::get('aa/mes/{year}/{month}', [AsistenciaAlumnoController::class, 'porMes']);
    Route::get('aa/anio/{year}', [AsistenciaAlumnoController::class, 'porAnio']);
    Route::post('asistencia-alumno/massive', [AsistenciaAlumnoController::class, 'storeMassive']);
    Route::get("asistencia-alumno/massive/template", [AsistenciaAlumnoController::class, "massiveTemplate"]);

    Route::apiResource('asistencia-maestro', AsistenciaMaestroController::class);
    Route::get('am/dia/{fecha}', [AsistenciaMaestroController::class, 'porDia']);
    Route::get('am/mes/{year}/{month}', [AsistenciaMaestroController::class, 'porMes']);
    Route::get('am/anio/{year}', [AsistenciaMaestroController::class, 'porAnio']);
    
    Route::post('upload/leccion', [UploadController::class,'uploadLeccionPdf']);
    Route::get('reports/download', [ReportController::class,'downloadMonthly']);
});

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AulaController;
use App\Http\Controllers\Api\MaestroController;
use App\Http\Controllers\Api\LeccionController;

use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\ReportController;

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    // aquí tus recursos protegidos...
    Route::apiResource('aulas', AulaController::class);
    Route::apiResource('maestros', MaestroController::class);
    Route::apiResource('lecciones', LeccionController::class);
    Route::get('lecciones/{leccion}/download', [LeccionController::class, 'downloadLeccion']);
    
    Route::post('upload/leccion', [UploadController::class,'uploadLeccionPdf']);
    Route::get('reports/download', [ReportController::class,'downloadMonthly']);
});

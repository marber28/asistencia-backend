<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AsistenciaAlumno;
use Illuminate\Http\Request;
use App\Models\Leccion;
use App\Models\Alumno;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_alumnos' => Alumno::count(),
            'asistencia_semanal' => AsistenciaAlumno::whereBetween('dia', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'leccion' => $this->currentLeccion(),
            'asistencia_mensual' => AsistenciaAlumno::whereMonth('dia', now()->month)->count(),
        ]);
    }

    private function currentLeccion() {
        $data = Leccion::whereBetween('fecha', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->get();

        return $data;
    }
}

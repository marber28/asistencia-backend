<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use PDF;
use App\Models\Alumno;
use App\Models\AsistenciaAlumno;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaAlumnoPDFController extends Controller
{
    public function pdfMensual(Request $request)
    {
        $request->validate([
            'anio' => 'required|integer',
            'mes'  => 'required|integer|min:1|max:12',
        ]);

        $anio = $request->anio;
        $mes = $request->mes;

        //$fecha = Carbon::create($anio, $mes, 1);

        $estados = [
            '' => '-',
            'presente' => 'X',
            'ausente' => 'No',
            'tarde' => 'T',
            'justificado' => 'J',
        ];

        $meses = [
            1 => "Enero",
            2 => "Febrero",
            3 => "Marzo",
            4 => "Abril",
            5 => "Mayo",
            6 => "Junio",
            7 => "Julio",
            8 => "Agosto",
            9 => "Setiembre",
            10 => "Octubre",
            11 => "Noviembre",
            12 => "Diciembre",
        ];

        /* -----------------------------------------------------
         * 1. OBTENER TODOS LOS DÍAS DEL MES CON ASISTENCIAS
         * -----------------------------------------------------*/
        $diasDelMes = AsistenciaAlumno::whereYear('dia', $anio)
            ->whereMonth('dia', $mes)
            ->selectRaw('DAY(dia) as dia')
            ->distinct()
            ->orderBy('dia')
            ->pluck('dia')
            ->toArray();

        /* -----------------------------------------------------
         * 2. OBTENER TODAS LAS ASISTENCIAS DEL MES
         * -----------------------------------------------------*/
        $asistencias = AsistenciaAlumno::with('alumno')
            ->whereYear('dia', $anio)
            ->whereMonth('dia', $mes)
            ->get();

        /* -----------------------------------------------------
         * 3. MAPEAR ASISTENCIAS → acceso instantáneo en Blade
         * -----------------------------------------------------*/
        $map = [];

        foreach ($asistencias as $a) {
            $diaNum = (int) Carbon::parse($a->dia)->day;
            $map[$a->alumno_id][$diaNum] = $a;
        }

        /* -----------------------------------------------------
         * 4. OBTENER TODOS LOS ALUMNOS
         * -----------------------------------------------------*/
        $alumnos = Alumno::orderBy('apellidos')->orderBy('nombres')->get();

        /* -----------------------------------------------------
         * 5. PASAR DATOS A LA VISTA
         * -----------------------------------------------------*/
        $data = [
            'mesNombre' => $meses[$mes],
            'diasDelMes' => $diasDelMes,
            'alumnos' => $alumnos,
            'map' => $map,
            'estados' => $estados,
        ];

        $pdf = PDF::loadView('pdf.asistencia_mensual', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download("asistencia_mensual_{$anio}_{$mes}.pdf");
    }
}

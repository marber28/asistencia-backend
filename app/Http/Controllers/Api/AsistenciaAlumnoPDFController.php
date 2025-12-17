<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use PDF;
use App\Models\Alumno;
use App\Models\Anexo;
use App\Models\AsistenciaAlumno;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaAlumnoPDFController extends Controller
{
    public function pdfMensual(Request $request)
    {
        /* ---------------- VALIDACIÓN ---------------- */
        $request->validate([
            'anio'     => 'required|integer',
            'mes'      => 'required|integer|min:1|max:12',
            'anexo_id' => 'required|exists:anexos,id',
        ]);

        $anio     = (int) $request->anio;
        $mes      = (int) $request->mes;
        $anexo_id = (int) $request->anexo_id;

        //Obtener anexo
        $anexo = Anexo::find($anexo_id);
        $anexoNombre = $anexo ? "_".$anexo->nombre : '';

        /* ---------------- ESTADOS ---------------- */
        $estados = [
            ''            => '-',
            'presente'    => 'X',
            'ausente'     => 'No',
            'tarde'       => 'T',
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

        /* ---------------- DÍAS (SÁBADOS) ---------------- */
        $diasDelMes = $this->getDiasMes($anio, $mes, 'd'); // [2,9,16,23,30]

        /* ---------------- ASISTENCIAS (LIVIANO) ---------------- */
        $asistencias = AsistenciaAlumno::select('alumno_id', 'dia', 'estado')
            ->whereYear('dia', $anio)
            ->whereMonth('dia', $mes)
            ->whereHas('alumno', fn($q) => $q->where('anexo_id', $anexo_id))
            ->get();

        if ($asistencias->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros de asistencia.'
            ], 400);
        }

        /* ---------------- MAPA + TOTALES ---------------- */
        $map = [];
        $totales = [];

        foreach ($asistencias as $a) {
            $dia = $a->dia->format('d');

            $map[$a->alumno_id][$dia] = $a->estado;

            if ($a->estado === 'presente') {
                $totales[$a->alumno_id] = ($totales[$a->alumno_id] ?? 0) + 1;
            }
        }

        /* ---------------- ALUMNOS (SOLO LOS NECESARIOS) ---------------- */
        $alumnos = Alumno::select('id', 'apellidos', 'nombres')
            ->where('anexo_id', $anexo_id)
            ->whereIn('id', array_keys($totales))
            ->get()
            ->map(fn($a) => [
                'id'      => $a->id,
                'nombre'  => "{$a->apellidos} {$a->nombres}",
                'total'   => $totales[$a->id] ?? 0,
            ])
            ->sortByDesc('total')   // 🔥 ORDEN POR ASISTENCIA
            ->values()
            ->toArray();

        /* ---------------- DATA PDF ---------------- */
        $data = [
            'mesNombre' => $meses[$mes],
            'diasDelMes' => $diasDelMes,
            'alumnos' => $alumnos,
            'map' => $map,
            'estados' => $estados,
        ];

        /* ---------------- PDF ---------------- */
        $pdf = PDF::loadView('pdf.asistencia_mensual', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download("asistencia_{$anio}_{$mes}{$anexoNombre}.pdf");
    }

    /**
     * Retorna los sábados del mes → 5 semanas máximo
     * Ej: [3, 10, 17, 24, 31]
     */

    private function getDiasMes($anio, $mes, $format = 'Y-m-d')
    {
        $sabados = [];

        // Fecha inicial y final del mes
        $inicio = new \DateTime("$anio-$mes-01");
        $fin = (clone $inicio)->modify('last day of this month');

        // Iterar día por día
        while ($inicio <= $fin) {
            // 6 = sábado (0 domingo, 6 sábado)
            if ($inicio->format('w') == 6) {
                $sabados[] = $inicio->format($format);
            }
            $inicio->modify('+1 day');
        }

        return $sabados;
    }
}

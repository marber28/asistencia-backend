<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsistenciaAlumnoRequest;
use App\Models\AsistenciaAlumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AsistenciaAlumnoController extends Controller
{
    protected $meses = [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
    ];

    public function index(Request $request)
    {
        $q = AsistenciaAlumno::with(['alumno', 'aula', 'leccion']);

        if ($request->filled('search')) {
            $search = $request->search;

            $q->where(function ($q) use ($search) {
                $q->where('dia', 'like', "%$search%") // campo propio del modelo
                    ->orWhere('estado', 'like', "%$search%") // campo propio del modelo
                    ->orWhereHas('alumno', function ($qa) use ($search) {
                        $qa->where('nombres', 'like', "%$search%")
                            ->orWhere('apellidos', 'like', "%$search%");
                    })
                    ->orWhereHas('aula', function ($qa) use ($search) {
                        $qa->where('nombre', 'like', "%$search%");
                    })
                    ->orWhereHas('leccion', function ($ql) use ($search) {
                        $ql->where('titulo', 'like', "%$search%");
                    });
            });
        }

        return $q->orderBy('dia', 'desc')->paginate(50);
    }


    public function store(StoreAsistenciaAlumnoRequest $request)
    {
        $data = $request->validated();

        $asistencia = AsistenciaAlumno::create($data);
        return response()->json($asistencia, 201);
    }

    public function storeMassive(Request $request)
    {
        $request->validate([
            "file" => "required|mimes:xlsx,csv,txt|max:4096",
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $rows = [];

        // Detectar extensión
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === "csv" || $extension === "txt") {
            // 🔹 Lectura CSV correcta
            if (($handle = fopen($path, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        } else {
            // 🔹 Lectura XLSX correcta (evita caracteres extraños)
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        }

        // Remover encabezado
        array_shift($rows);

        $errores = [];
        $insertados = 0;

        foreach ($rows as $i => $row) {
            $alumno_id = $row[0] ?? null;
            $aula_id = $row[1] ?? null;
            $dia = $row[2] ?? null;
            $estado = $row[3] ?? 'presente';
            $leccion_id = $row[4] ?? null;
            $observaciones = $row[5] ?? '';

            $validator = \Validator::make([
                "alumno_id" => $alumno_id,
                "aula_id" => $aula_id,
                "dia" => $dia,
                "estado" => $estado,
                "leccion_id" => $leccion_id,
            ], [
                "alumno_id" => "required|exists:alumnos,id",
                "aula_id" => "required|exists:aulas,id",
                "dia" => "required|date",
                "estado" => "required|in:presente,ausente,tarde,justificado",
                "leccion_id" => "nullable|exists:lecciones,id",
            ]);

            if ($validator->fails()) {
                $errores[] = "Fila " . ($i + 2) . ": " . implode(", ", $validator->errors()->all());
                continue;
            }

            AsistenciaAlumno::updateOrCreate(
                // Search criteria
                [
                    "alumno_id" => $alumno_id
                ],
                // Values to update/create
                [
                    "aula_id" => $aula_id,
                    "dia" => $dia,
                    "estado" => $estado,
                    "leccion_id" => $leccion_id,
                    "observaciones" => $observaciones,
                ]
            );

            $insertados++;
        }

        return response()->json([
            "insertados" => $insertados,
            "errores" => $errores,
        ], count($errores) ? 422 : 200);
    }

    public function massiveTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ENCABEZADOS
        $columns = [
            "alumno_id",
            "aula_id",
            "dia",
            "estado",
            "leccion_id",
            "observaciones",
        ];

        $sheet->fromArray([$columns], NULL, 'A1');

        // Estilo de encabezado
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
        }

        // Fila de ejemplo opcional
        $sheet->fromArray([
            [1, 3, "2025-01-15", "presente", 2, "Ejemplo"]
        ], NULL, 'A2');

        // Generar archivo
        $fileName = "plantilla_asistencia.xlsx";
        $writer = new Xlsx($spreadsheet);

        // Devolver como descarga
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        ]);
    }

    // subir imagen asociada al día/aula
    public function uploadLista(Request $request)
    {
        $request->validate(['aula_id' => 'required|exists:aulas,id', 'dia' => 'required|date', 'image' => 'required|image|max:5120']);
        $path = $request->file('image')->store('listas/' . $request->aula_id, 'public');
        // guardar referencia: opción 1 = asociar a todas asistencias del dia/aula
        AsistenciaAlumno::where('aula_id', $request->aula_id)->where('dia', $request->dia)->update(['lista_imagen' => $path]);
        return response()->json(['path' => $path], 201);
    }


    // generar PDF mensual (simple: dispara job)
    public function generateMonthlyPdf(Request $request)
    {
        $request->validate(['mes' => 'required|date_format:Y-m|regex:/^\d{4}-\d{2}$/', 'aula_id' => 'required|exists:aulas,id']);
        // enviar a job
        \App\Jobs\GenerateMonthlyPdf::dispatch($request->mes, $request->aula_id, $request->user());
        return response()->json(['message' => 'Generación en cola, cuando esté listo se guardará en storage/app/public/reports']);
    }

    // Asistencia por día (YYYY-MM-DD)
    public function porDia($fecha)
    {
        $data = DB::table('asistencia_alumnos')
            ->whereDate('dia', $fecha)
            ->select(
                DB::raw('DATE(dia) as fecha'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) AS presentes"),
                DB::raw("SUM(CASE WHEN estado = 'falta' THEN 1 ELSE 0 END) AS faltas"),
                DB::raw("SUM(CASE WHEN estado = 'tarde' THEN 1 ELSE 0 END) AS tardanzas")
            )
            ->groupBy('fecha')
            ->get();

        return response()->json($data);
    }

    // Asistencia por mes (YYYY-MM)
    public function porMes($year, $month)
    {
        $dias = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $data = AsistenciaAlumno::whereYear('dia', $year)
            ->whereMonth('dia', $month)
            ->select(
                DB::raw('DATE(dia) as fecha'),
                DB::raw('MONTH(dia) as mes'),
                DB::raw("COUNT(*) as total"),
                DB::raw("SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) AS presentes"),
                DB::raw("SUM(CASE WHEN estado = 'falta' THEN 1 ELSE 0 END) AS faltas"),
                DB::raw("SUM(CASE WHEN estado = 'tarde' THEN 1 ELSE 0 END) AS tardanzas")
            )
            ->groupBy('fecha', 'mes')
            ->orderBy('fecha')
            ->get();

        //obtener query sql para debug
        /* $sql = AsistenciaAlumno::
                whereYear('dia', $year)
                ->whereMonth('dia', $month)
                ->select(
                    DB::raw('DATE(dia) as fecha'),
                    DB::raw('MONTH(dia) as mes'),
                    DB::raw("COUNT(*) as total"),
                    DB::raw("SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) AS presentes"),
                    DB::raw("SUM(CASE WHEN estado = 'falta' THEN 1 ELSE 0 END) AS faltas"),
                    DB::raw("SUM(CASE WHEN estado = 'tarde' THEN 1 ELSE 0 END) AS tardanzas")
                )
                ->groupBy('fecha', 'mes')
                ->orderBy('fecha')
                ->toSql();
            dd($sql); */

        // Inicializar todos los días en 0
        $res = [];
        for ($i = 1; $i < $dias; $i++) {
            $res[$i] = [
                "fecha" => $i,
                "total" => 0,
                "presentes" => 0,
                "faltas" => 0,
                "tardanzas" => 0,
            ];
        }

        if ($data->count() == 0) {
            return response()->json($res);
        }
        // Acumular valores por día
        foreach ($data as $registro) {
            $dia = (int) date('d', strtotime($registro->fecha));

            $res[$dia]["total"] += $registro->total;
            $res[$dia]["presentes"] += (int) $registro->presentes;
            $res[$dia]["faltas"] += (int) $registro->faltas;
            $res[$dia]["tardanzas"] += (int) $registro->tardanzas;
        }

        // Convertir a array indexado si deseas
        $resultado = array_values($res);

        return response()->json($resultado);
    }

    // Asistencia por año (YYYY)
    public function porAnio($year)
    {
        $data = AsistenciaAlumno::whereYear('dia', $year)
            ->select(
                DB::raw('DATE(dia) as fecha'),
                DB::raw('MONTH(dia) as mes'),
                DB::raw("COUNT(*) as total"),
                DB::raw("SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) AS presentes"),
                DB::raw("SUM(CASE WHEN estado = 'falta' THEN 1 ELSE 0 END) AS faltas"),
                DB::raw("SUM(CASE WHEN estado = 'tarde' THEN 1 ELSE 0 END) AS tardanzas")
            )
            ->groupBy('fecha', 'mes')
            ->orderBy('fecha')
            ->get();

        // Inicializamos el array de resultados con todos los meses
        $res = [];
        foreach ($this->meses as $index => $nombreMes) {
            $res[$index + 1] = [
                "fecha" => $nombreMes,
                "total" => 0,
                "presentes" => 0,
                "faltas" => 0,
                "tardanzas" => 0,
            ];
        }
        // Sumamos los valores por mes
        foreach ($data as $registro) {
            $mes = $registro["mes"];
            $res[$mes]["presentes"] += (int)$registro["presentes"];
            $res[$mes]["tardanzas"] += (int)$registro["tardanzas"];
            $res[$mes]["faltas"] += (int)$registro["faltas"];
            $res[$mes]["total"] += $registro["total"];
        }

        // Convertimos a un array indexado por cero si lo deseas
        $res = array_values($res);

        return response()->json($res);
    }
}

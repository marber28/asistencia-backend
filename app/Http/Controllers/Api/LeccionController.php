<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Leccion;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreLeccionRequest;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LeccionController extends Controller
{
    public function index(Request $request)
    {
        $q = Leccion::query();
        if ($request->filled('from')) $q->where('fecha', '>=', $request->from);
        if ($request->filled('to')) $q->where('fecha', '<=', $request->to);
        return $q->orderBy('fecha', 'desc')->paginate(20);
    }

    public function store(StoreLeccionRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('archivo_pdf')) {
            $data['archivo_pdf'] = $request->file('archivo_pdf')->store('lecciones', 'public');
        }

        $leccion = Leccion::create($data);
        return response()->json($leccion, 201);
    }

    public function show(Leccion $leccion)
    {
        return $leccion;
    }

    public function update(Request $request, Leccion $leccion)
    {
        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255|unique:lecciones,titulo',$leccion->id,
            'fecha' => 'nullable|date',
            'versiculo' => 'nullable|string|max:255',
            'archivo_pdf' => 'nullable|mimes:pdf|max:10240'
        ]);

        if ($request->hasFile('archivo_pdf')) {
            // eliminar archivo anterior si existe
            if ($leccion->archivo_pdf && Storage::disk('public')->exists($leccion->archivo_pdf)) {
                Storage::disk('public')->delete($leccion->archivo_pdf);
            }
            $data['archivo_pdf'] = $request->file('archivo_pdf')->store('lecciones', 'public');
        }

        $leccion->update($data);
        return response()->json($leccion);
    }

    public function destroy(Leccion $leccion)
    {
        if ($leccion->archivo_pdf && Storage::disk('public')->exists($leccion->archivo_pdf)) {
            Storage::disk('public')->delete($leccion->archivo_pdf);
        }
        $leccion->delete();
        return response()->noContent();
    }

    public function download(Leccion $leccion)
    {
        if (! $leccion->archivo_pdf || ! Storage::disk('public')->exists($leccion->archivo_pdf)) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }
        return Storage::disk('public')->download($leccion->archivo_pdf);
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
            $titulo = $row[0] ?? null;
            $fecha = $row[1] ?? null;
            $versiculo = $row[2] ?? null;
            $archivo_pdf = $row[3] ?? null;

            $validator = \Validator::make([
                "titulo" => $titulo,
                "fecha" => $fecha,
                "versiculo" => $versiculo,
                "archivo_pdf" => $archivo_pdf,
            ], [
                'titulo' => 'required|string|max:255',
                'fecha' => 'required|date|max:255',
                'versiculo' => 'nullable|string',
                'archivo_pdf' => 'required'
            ]);

            if ($validator->fails()) {
                $errores[] = "Fila " . ($i + 2) . ": " . implode(", ", $validator->errors()->all());
                continue;
            }

            Leccion::updateOrCreate(
                // Search criteria
                [
                    'titulo' => $titulo,
                    "fecha" => $fecha
                ],
                // Values to update/create
                [
                    "versiculo" => $versiculo,
                    "archivo_pdf" => $archivo_pdf
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
            "titulo",
            "fecha",
            "versiculo",
            "archivo_pdf",
        ];

        $sheet->fromArray([$columns], NULL, 'A1');

        // Estilo de encabezado
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
        }

        // Fila de ejemplo opcional
        $sheet->fromArray([
            ['El amor de Dios', "2025-01-15", 'Juan 3:16', '/ruta']
        ], NULL, 'A2');

        // Generar archivo
        $fileName = "plantilla_leccion.xlsx";
        $writer = new Xlsx($spreadsheet);

        // Devolver como descarga
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        ]);
    }
}

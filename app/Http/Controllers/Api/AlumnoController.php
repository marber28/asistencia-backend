<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAlumnoRequest;
use App\Models\Alumno;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $q = Alumno::with('anexo');
        if ($request->filled('search')) {
            $q->where('nombres', 'like', '%' . $request->search . '%')
                ->orWhere('apellidos', 'like', '%' . $request->search . '%');
        }
        return $q->paginate(20);
    }

    public function store(StoreAlumnoRequest $request)
    {
        $data = $request->validated();

        $alumno = Alumno::create($data);
        return response()->json($alumno, 201);
    }

    public function show(Alumno $alumno)
    {
        return $alumno->load('aulas');
    }

    public function update(Request $request, Alumno $alumno)
    {
        $data = $request->validate([
            'nombres' => 'sometimes|required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'anexo_id' => 'required|exists:anexos,id',
            'genero' => 'sometimes|in:M,F',
            'fecha_nacimiento' => 'nullable|date:format:Y-m-d',
        ]);
        $alumno->update($data);
        return response()->json($alumno);
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->delete();
        return response()->noContent();
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
            $nombres = $row[0] ?? null;
            $apellidos = $row[1] ?? null;
            $fecha_nacimiento = $row[2] ?? null;
            $genero = $row[3] ?? null;
            $anexo_id = $row[4] ?? null;
            $foto = $row[4] ?? null;

            $validator = \Validator::make([
                "nombres" => $nombres,
                "apellidos" => $apellidos,
                "fecha_nacimiento" => $fecha_nacimiento,
                "genero" => $genero,
                "anexo_id" => $anexo_id,
                "foto" => $foto,
            ], [
                'nombres' => 'required|string|max:255',
                'apellidos' => 'nullable|string|max:255',
                'fecha_nacimiento' => 'nullable|date:format:Y-m-d',
                'genero' => 'sometimes|in:M,F',
                'anexo_id' => 'sometimes|exists:anexos,id',
                'foto' => 'nullable'
            ]);

            if ($validator->fails()) {
                $errores[] = "Fila " . ($i + 2) . ": " . implode(", ", $validator->errors()->all());
                continue;
            }

            Alumno::updateOrCreate(
                // Search criteria
                [
                    'nombres' => $nombres,
                    "apellidos" => $apellidos
                ],
                // Values to update/create
                [
                    "fecha_nacimiento" => $fecha_nacimiento,
                    "genero" => $genero,
                    "anexo_id" => $anexo_id,
                    "foto" => $foto
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
            "nombres",
            "apellidos",
            "fecha_nacimiento",
            "genero",
            "anexo_id",
            "foto",
        ];

        $sheet->fromArray([$columns], NULL, 'A1');

        // Estilo de encabezado
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
        }

        // Fila de ejemplo opcional
        $sheet->fromArray([
            ['Carlos', 'Rodriguez', "2025-01-15", 'M', 1, '/ruta']
        ], NULL, 'A2');

        // Generar archivo
        $fileName = "plantilla_alumno.xlsx";
        $writer = new Xlsx($spreadsheet);

        // Devolver como descarga
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        ]);
    }
}

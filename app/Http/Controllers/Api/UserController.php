<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuarioRequest;
use App\Models\User;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
    //index para obtener todos los usuarios
    public function index(Request $request)
    {
        $q = User::query()->with('roles');
        if ($request->filled('search')) {
            $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('lastname', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        $q->where('visible', 1);
        /* if ($request->filled('enabled')) {
            $q->where('enabled', filter_var($request->enabled, FILTER_VALIDATE_BOOLEAN));
        } */
        return $q->paginate(20);
    }

    public function show(User $user)
    {
        return [];
    }

    public function list()
    {
        // Obtener todos los usuarios
        $users = User::where('enabled', true)
            ->where('in_anexo', true)
            ->where('visible', true)
            ->get();
        return response()->json($users);
    }

    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();

        $data['password'] = bcrypt($request->input('password'));
        $data['visible'] = 1;
        $user = User::create($data);
        return response()->json($user, 201);
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'email'    => 'sometimes|nullable|email|unique:users,email,' . $usuario->id,
            'in_anexo' => 'sometimes|boolean',
            'password' => 'sometimes|nullable|min:6',
            'enabled' => 'sometimes|boolean',
        ]);

        // Procesar password solo si se envía
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $usuario->update($data);
        return response()->json($usuario);
    }

    public function destroy(User $user)
    {
        $user->delete();
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

            User::updateOrCreate(
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
        $fileName = "plantilla_usuarios.xlsx";
        $writer = new Xlsx($spreadsheet);

        // Devolver como descarga
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        ]);
    }
}

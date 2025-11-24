<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsistenciaRequest;
use App\Models\AsistenciaMaestro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsistenciaMaestroController extends Controller
{
    public function index(Request $request)
    {
        $q = AsistenciaMaestro::with('alumno', 'aula', 'leccion');
        if ($request->filled('dia')) $q->where('dia', $request->dia);
        if ($request->filled('aula')) $q->where('aula_id', $request->aula);
        return $q->orderBy('dia', 'desc')->paginate(50);
    }


    public function store(StoreAsistenciaRequest $request)
    {
        $data = $request->validated();
        $dia = $data['dia'];
        DB::transaction(function () use ($data, $dia) {
            foreach ($data['asistencias'] as $row) {
                AsistenciaMaestro::updateOrCreate(
                    ['alumno_id' => $row['alumno_id'], 'dia' => $dia],
                    ['aula_id' => $data['aula_id'], 'estado' => $row['estado'], 'leccion_id' => ($row['leccion_id'] ?? null), 'observaciones' => ($row['observaciones'] ?? null)]
                );
            }
        });
        return response()->json(['message' => 'Asistencias guardadas']);
    }


    // subir imagen asociada al día/aula
    public function uploadLista(Request $request)
    {
        $request->validate(['aula_id' => 'required|exists:aulas,id', 'dia' => 'required|date', 'image' => 'required|image|max:5120']);
        $path = $request->file('image')->store('listas/' . $request->aula_id, 'public');
        // guardar referencia: opción 1 = asociar a todas asistencias del dia/aula
        AsistenciaMaestro::where('aula_id', $request->aula_id)->where('dia', $request->dia)->update(['lista_imagen' => $path]);
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
}

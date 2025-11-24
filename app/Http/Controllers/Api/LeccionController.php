<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Leccion;
use Illuminate\Support\Facades\Storage;

class LeccionController extends Controller
{
    public function index(Request $request)
    {
        $q = Leccion::query();
        if ($request->filled('from')) $q->where('fecha', '>=', $request->from);
        if ($request->filled('to')) $q->where('fecha', '<=', $request->to);
        return $q->orderBy('fecha', 'desc')->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha' => 'nullable|date',
            'versiculo' => 'nullable|string|max:255',
            'archivo_pdf' => 'nullable|mimes:pdf|max:10240'
        ]);

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
            'titulo' => 'sometimes|required|string|max:255',
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
}

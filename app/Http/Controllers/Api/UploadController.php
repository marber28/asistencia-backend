<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadLeccionPdf(Request $r)
    {
        $r->validate(['archivo' => 'required|mimes:pdf|max:10240', 'titulo' => 'required']);
        $path = $r->file('archivo')->store('lecciones', 'public');
        $leccion = \App\Models\Leccion::create(['titulo' => $r->titulo, 'fecha' => $r->fecha ?? now(), 'versiculo' => $r->versiculo ?? null, 'archivo_pdf' => $path]);
        return response()->json($leccion, 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\DesarrolloLeccion as Desarrollo;
use App\Http\Controllers\Controller;
use App\Models\Anexo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DesarrolloLeccionController extends Controller
{
    public function storeOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'leccion_id' => 'required|exists:lecciones,id',
            'versiculo_memorizado' => 'required|string',
            'ensenanza' => 'required|string',
            'motivacion' => 'required|string',
            'estrategias' => 'required|string',
            'observaciones' => 'nullable|string',
            'user_id' => 'required|exists:users,id'
        ]);

        $anexo_id = Anexo::where('user_id', $validated['user_id'])->first()->id ?? null;

        if (!$anexo_id) {
            return response()->json([
                "succes" => false,
                "message" => "Usuario no permitido para generar desarrollo de lección",
                "data" => []
            ], 400);
        }

        $validated['anexo_id'] = $anexo_id;

        // Buscar si ya existe
        $desarrollo = Desarrollo::where('leccion_id', $validated['leccion_id'])
            ->where('user_id', $validated['user_id'])
            ->where('anexo_id', $anexo_id)
            ->first();

        if ($desarrollo) {
            $desarrollo->update($validated);
            $action = "updated";
        } else {
            $desarrollo = Desarrollo::create($validated);
            $action = "created";
        }

        return response()->json([
            "succes" => true,
            "message" => "Registro $action correctamente",
            "data" => $desarrollo
        ]);
    }

    public function generatePdf(Request $request)
    {
        $validated = $request->validate([
            'leccion' => 'required|exists:lecciones,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $user_id = $validated['user_id'];
        $leccion = $validated['leccion'];

        $anexo_id = Anexo::where('user_id', $user_id)->first()->id ?? null;

        if (!$anexo_id) {
            return response()->json([
                "succes" => false,
                "message" => "Usuario no permitido para generar PDF de desarrollo de lección",
                "data" => []
            ], 400);
        }

        $desarrollo = Desarrollo::where('user_id', $user_id)
            ->where('leccion_id', $leccion)
            ->where('anexo_id', $anexo_id)
            ->firstOrFail();

        $pdf = $this->pdfRender($desarrollo);
        $filename = "desarrolloleccion_{$desarrollo->id}{$user_id}{$anexo_id}.pdf";

        Storage::disk('public')->put("desarrollos/$filename", $pdf->output());
        $desarrollo->update(['pdf' => $filename]);

        return response()->json([
            "message" => "PDF generado",
            "url" => asset("storage/desarrollos/$filename")
        ]);
    }

    /** 📄 FUNCIÓN APARTE PARA GENERAR VIEW PDF */
    private function pdfRender($desarrollo)
    {
        return Pdf::loadView('pdf.desarrollo_leccion', ['data' => $desarrollo]);
    }

    /** GET para precargar datos en React */
    public function showByLeccion($leccion_id)
    {
        $user_id = auth()->id();
        return Desarrollo::where('leccion_id', $leccion_id)->where('user_id', $user_id)->first();
    }
}

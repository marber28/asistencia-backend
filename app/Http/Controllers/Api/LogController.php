<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Listar logs (para React)
     */
    public function index(Request $request)
    {
        $query = Log::query()->with('user');

        // 🔍 Filtros opcionales
        if ($request->filled('vista')) {
            $query->where('vista', 'like', '%' . $request->vista . '%');
        }

        if ($request->filled('detalle')) {
            $query->where('detalle', 'like', '%' . $request->detalle . '%');
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // 📄 Paginación
        $logs = $query
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json($logs);
    }

    /**
     * Registrar un log
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vista'   => 'required|string|max:255',
            'detalle' => 'nullable|string',
            'consumo' => 'nullable|numeric|min:0',
            'payload' => 'nullable|array',
        ]);

        $log = Log::create($data);

        return response()->json([
            'message' => 'Log registrado correctamente',
            'data' => $log,
        ], 201);
    }

    /**
     * Ver detalle de un log
     */
    public function show(Log $log)
    {
        return response()->json($log);
    }
}

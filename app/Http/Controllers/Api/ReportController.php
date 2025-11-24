<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function downloadMonthly(Request $r)
    {
        $r->validate(['file' => 'required|string']);
        $path = storage_path('app/public/reports/' . $r->file);
        if (!file_exists($path)) return response()->json(['message' => 'Archivo no encontrado'], 404);
        return response()->download($path);
    }
}

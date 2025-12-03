<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //index para obtener todos los usuarios
    public function index()
    {
        // Obtener todos los usuarios
        $users = User::where('enabled', true)->where('in_anexo', true)->get();
        return response()->json($users);
    }
}

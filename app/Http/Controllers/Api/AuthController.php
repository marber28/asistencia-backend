<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  public function login(Request $r)
  {
    $r->validate(['email' => 'required|email', 'password' => 'required']);
    $user = User::where('email', $r->email)->first();
    if (! $user || ! Hash::check($r->password, $user->password)) {
      throw ValidationException::withMessages(['email' => ['Credenciales inválidas.']]);
    }
    // crear token personal con abilities según rol
    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json(['token' => $token, 'user' => $user]);
  }

  public function me(Request $r)
  {
    return $r->user()->load('roles');
  }

  public function logout(Request $r)
  {
    $r->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'logged out']);
  }
}

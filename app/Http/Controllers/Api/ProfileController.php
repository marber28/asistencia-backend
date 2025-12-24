<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = \Auth::user();

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'email'  => 'sometimes|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
}

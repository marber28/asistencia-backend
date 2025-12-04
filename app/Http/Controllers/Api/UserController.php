<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuarioRequest;
use App\Models\User;

class UserController extends Controller
{
    //index para obtener todos los usuarios
    public function index(Request $request)
    {
        $q = User::query();
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

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            //'email'    => 'sometimes|nullable|email|unique:users,email,' . $user->id,
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
        \Log::debug($data);
        $user->update($data);
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }
}

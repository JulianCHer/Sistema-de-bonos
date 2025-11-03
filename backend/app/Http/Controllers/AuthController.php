<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1️⃣ Validamos que el usuario haya enviado los datos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2️⃣ Buscamos el usuario en la base de datos
        $user = DB::table('users')->where('email', $request->email)->first();

        // 3️⃣ Si no existe, devolvemos error
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 401);
        }

        // 4️⃣ Verificamos la contraseña
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Contraseña incorrecta'], 401);
        }

        $token = bin2hex(random_bytes(32));
        DB::table('users')->where('id', $user->id)->update(['remember_token' => $token]);

        // 5️⃣ Si todo está bien, retornamos los datos del usuario
        return response()->json([
            'message' => 'Login exitoso',
            'user' => [
                'id' => $user->id,
                'id_group' => $user->id_group,
                'name' => $user->name,
                'email' => $user->email
            ],
            'token' => $token
        ], 200);
    }
}

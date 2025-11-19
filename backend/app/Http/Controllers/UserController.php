<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            if ($perPage <= 0) {
                $perPage = 10;
            }

            $search = trim($request->input('search', ''));

            $usersQuery = DB::table('users')
                ->leftJoin('user_group', 'users.id_group', '=', 'user_group.Id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.surname',
                    'users.id_group',
                    'users.phone',
                    'users.address',
                    'users.img_url',
                    'users.document',
                    'users.type_document',
                    'users.email',
                    'user_group.Name as group_name'
                )
                ->where('users.State', 'Active');

            if ($search !== '') {
                $usersQuery->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere('users.surname', 'like', '%' . $search . '%')
                        ->orWhere('users.document', 'like', '%' . $search . '%');
                });
            }

            $users = $usersQuery
                ->orderBy('users.id', 'asc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function erased_users()
    {
        try {
            $users = DB::table('users')
                ->leftJoin('user_group', 'users.id_group', '=', 'user_group.Id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.surname',
                    'users.id_group',
                    'users.phone',
                    'users.address',
                    'users.img_url',
                    'users.document',
                    'users.type_document',
                    'users.email',
                    'user_group.Name as group_name'
                )
                ->where('users.State', 'Erased')
                ->orderBy('users.id', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los usuarios eliminados',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // 🔹 Validar los campos
            $validated = $request->validate([
                'nombre'          => 'required|string|max:255',
                'apellido'        => 'required|string|max:255',
                'email'           => 'required|email|unique:users,email',
                'password'        => 'required|min:6',
                'telefono'        => 'nullable|string|max:20',
                'tipo_documento'  => 'required|string|max:100',
                'documento'       => 'required|string|max:50|unique:users,document',
                'grupo'           => 'required|in:1,2',
                'direccion'       => 'nullable|string|max:255',
                'avatar'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // 🔹 Guardar la imagen si viene adjunta
            $img_url = null;
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('users', $fileName, 'public');
                $img_url = 'storage/users/' . $fileName;   // 📍 Ruta accesible desde navegador
            }

            // 🔹 Insertar en la base de datos
            DB::table('users')->insert([
                'id_group'      => $validated['grupo'],
                'name'          => $validated['nombre'],
                'surname'       => $validated['apellido'],
                'email'         => $validated['email'],
                'password'      => bcrypt($validated['password']),
                'phone'         => $validated['telefono'] ?? 0,
                'type_document' => $validated['tipo_documento'],
                'document'      => $validated['documento'],
                'address'       => $validated['direccion'] ?? '',
                'img_url'       => $img_url,
                'State'         => 'Active',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el usuario',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nombre'          => 'required|string|max:255',
                'apellido'        => 'required|string|max:255',
                'email'           => 'required|email|unique:users,email,' . $id,
                'password'        => 'nullable|min:6',
                'telefono'        => 'nullable|string|max:20',
                'tipo_documento'  => 'required|string|max:100',
                'documento'       => 'required',
                'grupo'           => 'required|integer',
                'direccion'       => 'nullable|string|max:255',
                'avatar'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $user = DB::table('users')->where('id', $id)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            $img_url = $user->img_url;

            if ($request->hasFile('avatar')) {
                if ($img_url && Storage::exists(str_replace('storage/', 'public/', $img_url))) {
                    Storage::delete(str_replace('storage/', 'public/', $img_url));
                }

                $file = $request->file('avatar');
                $fileName = uniqid('user_') . '.' . $file->getClientOriginalExtension();
                $file->storeAs('users', $fileName, 'public');
                $img_url = 'storage/users/' . $fileName;
            }

            DB::table('users')
                ->where('id', $id)
                ->update([
                    'id_group'      => $validated['grupo'],
                    'name'          => $validated['nombre'],
                    'surname'       => $validated['apellido'],
                    'phone'         => $validated['telefono'] ?? null,
                    'email'         => $validated['email'],
                    'type_document' => $validated['tipo_documento'],
                    'document'      => $validated['documento'],
                    'address'       => $validated['direccion'] ?? null,
                    'img_url'       => $img_url,
                    'updated_at'    => now(),
                    'password'      => $validated['password']
                        ? Hash::make($validated['password'])
                        : $user->password,
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = DB::table('users')->where('id', $id)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            DB::table('users')->where('id', $id)->update([
                'State'      => 'Erased',
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function restore_users($id)
    {
        try {
            $user = DB::table('users')->where('id', $id)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            DB::table('users')->where('id', $id)->update([
                'State'      => 'Active',
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario restaurado correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar usuario',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}

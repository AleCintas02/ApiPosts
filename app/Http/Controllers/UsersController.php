<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('https://jsonplaceholder.typicode.com/users');
            $data = $response->json();

            foreach ($data as $user) {
                $this->storeUsers($user);
            }

            return response()->json(['message' => 'Usuarios cargados']);
        } catch (\Exception $e) {
            Log::error('Error al cargar usuarios: ' . $e->getMessage());
            return response()->json(['message' => 'Error al cargar usuarios'], 500);
        }
    }

    public function storeUsers($data)
    {
        try {
            User::create([
                'id' => $data['id'],
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
            ]);
        } catch (QueryException $e) {
            Log::error('Error al almacenar usuario en la base de datos: ' . $e->getMessage());
            // Puedes agregar más manejo de errores específicos según sea necesario.
        }
    }

    public function listar()
    {
        try {
            $users = User::all();
            return [
                'status' => 'ok',
                'users' => $users,
            ];
        } catch (\Exception $e) {
            Log::error('Error al listar usuarios: ' . $e->getMessage());
            return response()->json(['message' => 'Error al listar usuarios'], 500);
        }
    }

    public function detallar($id_user)
    {
        $user = User::with('posts')->find($id_user);

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Usuario no encontrado'], 404);
        }

        return response()->json([
            'status' => 'ok',
            'User' => $user
        ], 200);
    }

    public function crearUser(Request $request){
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|string',
        ]);

        $usuario = User::create([
            'name'=> $request->name,
            'username'=> $request->username,
            'email'=> $request->email,
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Usuario creado',
            'usuario' => $usuario
        ]);
    }
}

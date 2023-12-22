<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostsController extends Controller
{
    public function index()
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/posts');
        $data = $response->json();

        foreach ($data as $posts) {
            $this->storePosts($posts);
        }
        return response()->json(['message' => 'Posts cargados']);
    }

    public function storePosts($data)
    {
        Post::create([
            'id' => $data['id'],
            'user_id' => $data['userId'],
            'title' => $data['title'],
            'body' => $data['body'],
        ]);
    }

    public function listar()
    {
        try {
            $posts = Post::all();
            return [
                'status' => 'ok',
                'posts' => $posts,
            ];
        } catch (Exception $e) {
            Log::error('Error al listar posts: ' . $e->getMessage());
            return response()->json(['message' => 'Error al listar posts'], 500);
        }
    }

    public function detallar($id_post)
    {
        $post = Post::with('user')->find($id_post);
        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'post no encontrado',
            ], 404);
        }

        return response()->json([
            'status' => 'ok',
            'posts' => $post,
        ]);
    }

    public function crear(Request $request, $id_user)
    {
        try {
            // Buscar al usuario por ID
            $user = User::findOrFail($id_user);

            // Validar los datos de la nueva publicación
            $request->validate([
                'title' => 'required|string',
                'body' => 'required|string',
            ]);

            // Crear la nueva publicación
            $post = new Post([
                'title' => $request->input('title'),
                'body' => $request->input('body'),
            ]);

            // Asociar la publicación al usuario
            $user->posts()->save($post);

            return response()->json([
                'status' => 'ok',
                'message' => 'Publicación creada exitosamente',
                'post' => $post,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la publicación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

<?php

use App\Http\Controllers\PostsController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('users')->group(function () {
    Route::post('save', [UsersController::class, 'index']);
    Route::post('listar', [UsersController::class, 'listar']);
    Route::post('detallar/{id_user}', [UsersController::class, 'detallar']);
    Route::post('crear', [UsersController::class, 'crearUser']);
});
Route::prefix('posts')->group(function () {
    Route::post('save-posts', [PostsController::class, 'index']);
    Route::post('listar', [PostsController::class, 'listar']);
    Route::post('detallar/{id_post}', [PostsController::class, 'detallar']);
    Route::post('crear/{id_user}', [PostsController::class, 'crear']);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TareaController;
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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Rutas para el controlador de usuarios, asignando nombres personalizados
Route::middleware('auth:sanctum')->prefix('usuarios')->group(function (){
    Route::get('/listUsers', [UsuarioController::class, 'index']);
    Route::post('/addUser', [UsuarioController::class, 'store']);
    Route::get('/getUser/{id}', [UsuarioController::class, 'show']);
    Route::put('/updateUser/{id}', [UsuarioController::class, 'update']);
    Route::delete('/deleteUser/{id}', [UsuarioController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('tareas')->group(function () {
    Route::get('/', [TareaController::class, 'index']);
    Route::post('/', [TareaController::class, 'store']);
    Route::get('/{tarea}', [TareaController::class, 'show']);
    Route::put('/{tarea}', [TareaController::class, 'update']);
    Route::delete('/{tarea}', [TareaController::class, 'destroy']);
    Route::get('/usuario/{usuario}', [TareaController::class, 'porUsuario']);
    Route::get('/estado/{estado}', [TareaController::class, 'porEstado']);
    Route::get('/vencidas/list', [TareaController::class, 'vencidas']);
});

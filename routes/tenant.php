<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\TareaController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return response()->json([
            'message' => 'API del Tenant: ' . tenant('id'),
            'tenant_id' => tenant('id'),
            'database' => config('database.connections.tenant.database')
        ]);
    });
});

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::post('/api/register', [AuthController::class, 'register']);
    Route::post('/api/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/api/logout', [AuthController::class, 'logout']);
        
        Route::get('/api/user', function (Illuminate\Http\Request $request) {
            return $request->user();
        });
        
        Route::prefix('api/usuarios')->group(function () {
            Route::get('/listUsers', [UsuarioController::class, 'index']);
            Route::post('/addUser', [UsuarioController::class, 'store']);
            Route::get('/getUser/{id}', [UsuarioController::class, 'show']);
            Route::put('/updateUser/{id}', [UsuarioController::class, 'update']);
            Route::delete('/deleteUser/{id}', [UsuarioController::class, 'destroy']);
        });
        
        Route::prefix('api/tareas')->group(function () {
            Route::get('/', [TareaController::class, 'index']);
            Route::post('/', [TareaController::class, 'store']);
            Route::get('/{tarea}', [TareaController::class, 'show']);
            Route::put('/{tarea}', [TareaController::class, 'update']);
            Route::delete('/{tarea}', [TareaController::class, 'destroy']);
            Route::get('/usuario/{usuario}', [TareaController::class, 'porUsuario']);
            Route::get('/estado/{estado}', [TareaController::class, 'porEstado']);
            Route::get('/vencidas/list', [TareaController::class, 'vencidas']);
        });
    });
});
<?php

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

Route::get('/status', function () {
    return response()->json([
        'message' => 'API multi-tenant',
        'version' => '1.0.0',
        'tenants' => \App\Models\Tenant::with('domains')->get()->map(function($tenant) {
            return [
                'id' => $tenant->id,
                'domains' => $tenant->domains->pluck('domain')
            ];
        })
    ]);
});

<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('user', function(\Illuminate\Http\Request $request) {
        return $request->user();
    });
    Route::post('logout', [AuthController::class, 'logout']);
    // Rutas protegidas aquí
});

Route::middleware('auth:api')->get('/check-session', function (Request $request) {
    return response()->json(['status' => 'ok', 'user' => $request->user()]);
});

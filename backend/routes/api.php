<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SorteoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

Route::get('/users', [UserController::class, 'index']);
Route::post('/users/store', [UserController::class, 'store']);
Route::post('/users/update/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::post('register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/sorteos', [SorteoController::class, 'index']);
Route::post('/sorteos', [SorteoController::class, 'store']);

Route::get('/ping', function () {
    try {
        \DB::connection()->getPdo();
        return response()->json(['status' => 'ok']);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'db_error',
            'message' => 'Sin conexión a la base de datos.'
        ], 503);
    }
});

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

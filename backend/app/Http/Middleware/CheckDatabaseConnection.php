<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckDatabaseConnection
{
    public function handle($request, Closure $next)
    {
        try {
            // Probar conexión rápida (una sola vez)
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'db_error',
                'message' => 'La conexión con la base de datos se ha perdido.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        try {
            return $next($request);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'status' => 'db_error',
                'message' => 'Error de conexión con la base de datos.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}

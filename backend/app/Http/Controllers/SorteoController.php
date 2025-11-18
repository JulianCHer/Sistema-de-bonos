<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sorteo;

class SorteoController extends Controller
{
    // =====================
    // Listar sorteos
    // =====================
    public function index()
    {
        try {
            $sorteos = Sorteo::orderBy('Id', 'desc')->get();

            return response()->json([
                'success' => true,
                'sorteos' => $sorteos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los sorteos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'Nombre' => 'required|string|max:255',
                'Fecha' => 'required|date',
                'Total_Tickets' => 'required|integer|min:1',
            ]);

            $sorteo = Sorteo::create($validated);

            return response()->json(['success' => true, 'sorteo' => $sorteo]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el sorteo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TareaController extends Controller
{

    public function index(): JsonResponse
    {
        $tareas = Tarea::with('usuario:id,nombre')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tareas
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => ['nullable', Rule::in(['pendiente', 'en_progreso', 'completada'])],
            'fecha_vencimiento' => 'nullable|date|after_or_equal:today'
        ]);

        $tarea = Tarea::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarea creada exitosamente',
            'data' => $tarea->load('usuario:id,nombre')
        ], 201);
    }

    public function show(Tarea $tarea): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $tarea->load('usuario:id,nombre')
        ]);
    }

    public function update(Request $request, Tarea $tarea): JsonResponse
    {
        $validated = $request->validate([
            'usuario_id' => 'sometimes|exists:usuarios,id',
            'titulo' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => ['sometimes', Rule::in(['pendiente', 'en_progreso', 'completada'])],
            'fecha_vencimiento' => 'nullable|date|after_or_equal:today'
        ]);

        $tarea->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarea actualizada exitosamente',
            'data' => $tarea->load('usuario:id,nombre')
        ]);
    }

    public function destroy(Tarea $tarea): JsonResponse
    {
        $tarea->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarea eliminada exitosamente'
        ]);
    }

    public function porUsuario(Usuario $usuario): JsonResponse
    {
        $tareas = $usuario->tareas()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tareas
        ]);
    }

    public function porEstado(Request $request): JsonResponse
    {
        $estado = $request->query('estado');
        
        if (!in_array($estado, ['pendiente', 'en_progreso', 'completada'])) {
            return response()->json([
                'success' => false,
                'message' => 'Estado no válido'
            ], 400);
        }

        $tareas = Tarea::with('usuario:id,nombre')
            ->porEstado($estado)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tareas
        ]);
    }

    public function vencidas(): JsonResponse
    {
        $tareas = Tarea::with('usuario:id,nombre')
            ->vencidas()
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tareas
        ]);
    }
}
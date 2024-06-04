<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function task_user($user_id) {
        try {
            $task = Task::where('user_id',$user_id)->get();
            return response()->json($task);
        } catch (Exception $e) {
            return response()->json(['error'=>'Error al obtener las tareas por estado'],500);
        }
    }
    public function status_task($status_name)
    {
        try {
            $tasks = Task::where('status',$status_name)->get();
            return response()->json($tasks);
        } catch (Exception $e) {
            return response()->json(['error'=>'Error al obtener las tareas por estado'],500);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $tasks = Task::all();
            return response()->json($tasks, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener las tareas.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:6',
                'status' => 'required|in:pending,in_progress,completed',
                'due_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $task = Task::create($request->all());

            return response()->json($task, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al crear la tarea. ' . $e], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            return response()->json($task, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Tarea no encontrada.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:6',
                'status' => 'required|in:pending,in_progress,completed',
                'due_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $task = Task::findOrFail($id);
            $task->update($request->all());

            return response()->json($task, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al actualizar la tarea.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();

            return response()->json(['message' => 'Tarea eliminada correctamente.'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al eliminar la task.'], 500);
        }
    }
}

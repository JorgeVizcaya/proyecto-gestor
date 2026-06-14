<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Almacentarea; 

class AlmacentareaController extends Controller
{
    /**
     * Muestra la lista y maneja la lógica de las pestañas (Imagen 4)
     */
     public function index(Request $request)
    {
        // Arrays de apoyo para los campos select en la vista
        $prioridades = ['baja' => 'baja', 'media' => 'media', 'alta' => 'alta', 'urgente' => 'urgente'];
        
        // Obtenemos todas las tareas registradas
        $tareas = Almacentarea::all();

        // Inicializamos la variable de la tarea seleccionada para edición como nula
        $tarea = null;

        // Si la URL recibe un ID para editar (ej: ?edit=5), busca el registro en la base de datos
        if ($request->has('edit')) {
            $tarea = Almacentarea::findOrFail($request->edit);
        }

        // Control para saber qué pestaña de formulario mostrar (1 = Nueva Tarea, 2 = Modificar Tarea)
        $tab = $request->get('tab', 1);

        return view('layouts.main', compact('tareas', 'tarea', 'tab', 'prioridades'));
    }

    /**
     * Guarda una nueva tarea (Imagen 5)
     */
    public function store(Request $request)
    {
        // Validamos estrictamente que la información sea correcta antes de procesar
        $request->validate([
            'titulo' => 'required|max:255',
            'descripcion' => 'nullable',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'fecha_limite' => 'required|date'
        ]);

        // Creación del registro en base de datos
        Almacentarea::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'prioridad' => $request->prioridad,
            'fecha_limite' => $request->fecha_limite
        ]);

        return redirect()->route('tareas.index')->with('success', 'Tarea creada correctamente');
    }

    /**
     * Redirecciona al index activando la pestaña de modificar (Imagen 6)
     */
    public function edit(int $id)
    {
        // No devolvemos una vista aquí, sino que redirigimos al index 
        // pasando el ID por la URL para que la función index() lo detecte
        return redirect()->route('tareas.index', ['edit' => $id, 'tab' => 2]);
    }

    /**
     * Actualiza la tarea (Imagen 7)
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'titulo' => 'required|max:255',
            'descripcion' => 'nullable',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'fecha_limite' => 'required|date'
        ]);

        $tarea = Almacentarea::findOrFail($id);

        // Controlamos el estado según el checkbox de la vista de edición
        $nuevoEstado = $request->has('estado') ? 'completada' : 'pendiente';

        // Lógica automática para asignar o limpiar la fecha de completado
        $fechaCompletado = $tarea->completada_el;
        if ($nuevoEstado === 'completada') {
            if (!$fechaCompletado) {
                $fechaCompletado = now()->toDateString();
            }
        } else {
            $fechaCompletado = null;
        }

        $tarea->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'estado' => $nuevoEstado,
            'prioridad' => $request->prioridad,
            'fecha_limite' => $request->fecha_limite,
            'completada_el' => $fechaCompletado
        ]);

        return redirect()->route('tareas.index')->with('success', 'Tarea actualizada correctamente');
    }

    /**
     * Elimina la tarea (Imagen 8)
     */
      public function destroy(string $id)
    {
        $tarea = Almacentarea::findOrFail($id);
        $tarea->delete();

        return redirect()->route('tareas.index')->with('success', 'Tarea eliminada correctamente');
    }


    /**
     * Método extra para el botón rápido "Completar" de la tabla (Imagen 9)
     */
    public function completar(int $id)
    {
        $tarea = Almacentarea::findOrFail($id);
        $tarea->estado = 'completada';
        $tarea->save();

        return redirect()->route('tareas.index')->with('success', 'Tarea marcada como completada');
    }
    
    public function cambiarEstado(Request $request, int $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada'
        ]);

        $tarea = Almacentarea::findOrFail($id);
        $tarea->estado = $request->estado;

        // Lógica para automatizar la fecha de completado
        if ($request->estado === 'completada') {
            if (!$tarea->completada_el) {
                $tarea->completada_el = now()->toDateString();
            }
        } else {
            $tarea->completada_el = null;
        }

        $tarea->save();

        return redirect()->route('tareas.index')->with('success', 'Estado actualizado correctamente');
    }

}
<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Organizacion;
use App\Models\Sede;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Estudiante::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('nombres', 'like', "%$search%")
                ->orWhere('apellido_paterno', 'like', "%$search%")
                ->orWhere('matricula', 'like', "%$search%");
        }

        if ($request->has('estatus')) {
            $query->where('estatus', $request->get('estatus'));
        }

        $estudiantes = $query->paginate(15);
        return view('estudiantes.index', compact('estudiantes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $organizaciones = Organizacion::all();
        $sedes = Sede::all();
        return view('estudiantes.create', compact('organizaciones', 'sedes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F,otro',
            'email' => 'nullable|email|unique:estudiantes,email',
            'telefono' => 'nullable|string|max:30',
            'matricula' => 'nullable|string|unique:estudiantes,matricula',
            'curp' => 'nullable|string|unique:estudiantes,curp|size:18',
            'organizacion_id' => 'nullable|exists:organizaciones,id',
            'sede_actual_id' => 'nullable|exists:sedes,id',
            'estatus' => 'required|in:activo,baja_temporal,baja_definitiva,egresado',
        ]);

        $estudiante = Estudiante::create($validated);

        return redirect()->route('estudiantes.show', $estudiante)
            ->with('success', 'Estudiante creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Estudiante $estudiante)
    {
        return view('estudiantes.show', compact('estudiante'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Estudiante $estudiante)
    {
        $organizaciones = Organizacion::all();
        $sedes = Sede::all();
        return view('estudiantes.edit', compact('estudiante', 'organizaciones', 'sedes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Estudiante $estudiante)
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F,otro',
            'email' => 'nullable|email|unique:estudiantes,email,' . $estudiante->id,
            'telefono' => 'nullable|string|max:30',
            'matricula' => 'nullable|string|unique:estudiantes,matricula,' . $estudiante->id,
            'curp' => 'nullable|string|unique:estudiantes,curp,' . $estudiante->id . '|size:18',
            'organizacion_id' => 'nullable|exists:organizaciones,id',
            'sede_actual_id' => 'nullable|exists:sedes,id',
            'estatus' => 'required|in:activo,baja_temporal,baja_definitiva,egresado',
        ]);

        $estudiante->update($validated);

        return redirect()->route('estudiantes.show', $estudiante)
            ->with('success', 'Estudiante actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Estudiante $estudiante)
    {
        $estudiante->delete();
        return redirect()->route('estudiantes.index')
            ->with('success', 'Estudiante eliminado exitosamente.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Escuela;
use App\Http\Requests\MateriaStoreRequest;
use App\Http\Requests\MateriaUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MateriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Materia::class);

        $materias = Materia::with('escuela.organizacion')
            ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                // Filtrar por escuelas de la organización del usuario
                $escuelasIds = Escuela::where('organizacion_id', auth()->user()->organizacion_id)->pluck('id');
                $q->whereIn('escuela_id', $escuelasIds);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', $search)
                          ->orWhere('clave', 'LIKE', $search)
                          ->orWhere('descripcion', 'LIKE', $search);
                });
            })
            ->when($request->filled('escuela_id'), function ($q) use ($request) {
                $q->where('escuela_id', $request->escuela_id);
            })
            ->when($request->filled('tipo'), function ($q) use ($request) {
                $q->where('tipo', $request->tipo);
            })
            ->when($request->filled('activa'), function ($q) use ($request) {
                $q->where('activa', $request->boolean('activa'));
            })
            ->orderBy('nombre')
            ->paginate(25)
            ->appends($request->only(['search', 'escuela_id', 'tipo', 'activa']));

        $escuelas = Escuela::orderBy('nombre')->get();
        $tipos = ['obligatoria', 'optativa', 'taller', 'extracurricular'];

        return view('materias.index', compact('materias', 'escuelas', 'tipos'));
    }

    public function create()
    {
        $this->authorize('create', Materia::class);
        $escuelas = Escuela::orderBy('nombre')->get();
        $tipos = ['obligatoria', 'optativa', 'taller', 'extracurricular'];
        return view('materias.create', compact('escuelas', 'tipos'));
    }

    public function store(MateriaStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['activa'] = $request->has('activa');

        $materia = DB::transaction(function () use ($validated) {
            return Materia::create($validated);
        });

        return redirect()->route('materias.index')
            ->with('success', "Materia '{$materia->nombre}' creada.");
    }

    public function show(Materia $materia)
    {
        $this->authorize('view', $materia);
        $materia->load('escuela.organizacion');
        return view('materias.show', compact('materia'));
    }

    public function edit(Materia $materia)
    {
        $this->authorize('update', $materia);
        $escuelas = Escuela::orderBy('nombre')->get();
        $tipos = ['obligatoria', 'optativa', 'taller', 'extracurricular'];
        return view('materias.edit', compact('materia', 'escuelas', 'tipos'));
    }

    public function update(MateriaUpdateRequest $request, Materia $materia)
    {
        $validated = $request->validated();
        $validated['activa'] = $request->has('activa');

        DB::transaction(function () use ($materia, $validated) {
            $materia->update($validated);
        });

        return redirect()->route('materias.index')
            ->with('success', "Materia '{$materia->nombre}' actualizada.");
    }

    public function destroy(Materia $materia)
    {
        $this->authorize('delete', $materia);
        $materia->delete();
        return redirect()->route('materias.index')
            ->with('success', "Materia '{$materia->nombre}' eliminada.");
    }
}
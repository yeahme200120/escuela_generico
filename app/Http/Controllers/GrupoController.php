<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Sede;
use App\Models\CicloEscolar;
use App\Models\Grado;
use App\Models\Aula;
use App\Models\Docente;
use App\Http\Requests\GrupoStoreRequest;
use App\Http\Requests\GrupoUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Grupo::class);

        $grupos = Grupo::with(['sede', 'cicloEscolar', 'grado', 'aulaPrincipal', 'docenteTutor'])
            ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                // Filtrar por sedes a las que tiene acceso el usuario
                $sedesIds = auth()->user()->userSedes()->pluck('sede_id');
                $q->whereIn('sede_id', $sedesIds);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where('nombre', 'LIKE', $search);
            })
            ->when($request->filled('sede_id'), function ($q) use ($request) {
                $q->where('sede_id', $request->sede_id);
            })
            ->when($request->filled('ciclo_escolar_id'), function ($q) use ($request) {
                $q->where('ciclo_escolar_id', $request->ciclo_escolar_id);
            })
            ->when($request->filled('activo'), function ($q) use ($request) {
                $q->where('activo', $request->boolean('activo'));
            })
            ->orderBy('nombre')
            ->paginate(25)
            ->appends($request->only(['search', 'sede_id', 'ciclo_escolar_id', 'activo']));

        $sedes = Sede::orderBy('nombre')->get();
        $ciclos = CicloEscolar::where('activo', true)->orderBy('nombre')->get();

        return view('grupos.index', compact('grupos', 'sedes', 'ciclos'));
    }

    public function create()
    {
        $this->authorize('create', Grupo::class);
        $sedes = Sede::orderBy('nombre')->get();
        $ciclos = CicloEscolar::where('activo', true)->orderBy('nombre')->get();
        $grados = Grado::where('activo', true)->orderBy('nombre')->get();
        $aulas = Aula::orderBy('nombre')->get();
        $docentes = Docente::orderBy('nombre')->get();

        return view('grupos.create', compact('sedes', 'ciclos', 'grados', 'aulas', 'docentes'));
    }

    public function store(GrupoStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['activo'] = $request->has('activo');

        $grupo = DB::transaction(function () use ($validated) {
            return Grupo::create($validated);
        });

        return redirect()->route('grupos.index')
            ->with('success', "Grupo '{$grupo->nombre}' creado.");
    }

    public function show(Grupo $grupo)
    {
        $this->authorize('view', $grupo);
        $grupo->load(['sede', 'cicloEscolar', 'grado', 'aulaPrincipal', 'docenteTutor']);
        return view('grupos.show', compact('grupo'));
    }

    public function edit(Grupo $grupo)
    {
        $this->authorize('update', $grupo);
        $sedes = Sede::orderBy('nombre')->get();
        $ciclos = CicloEscolar::where('activo', true)->orderBy('nombre')->get();
        $grados = Grado::where('activo', true)->orderBy('nombre')->get();
        $aulas = Aula::orderBy('nombre')->get();
        $docentes = Docente::orderBy('nombre')->get();

        return view('grupos.edit', compact('grupo', 'sedes', 'ciclos', 'grados', 'aulas', 'docentes'));
    }

    public function update(GrupoUpdateRequest $request, Grupo $grupo)
    {
        $validated = $request->validated();
        $validated['activo'] = $request->has('activo');

        DB::transaction(function () use ($grupo, $validated) {
            $grupo->update($validated);
        });

        return redirect()->route('grupos.index')
            ->with('success', "Grupo '{$grupo->nombre}' actualizado.");
    }

    public function destroy(Grupo $grupo)
    {
        $this->authorize('delete', $grupo);
        $grupo->delete();
        return redirect()->route('grupos.index')
            ->with('success', "Grupo '{$grupo->nombre}' eliminado.");
    }
}
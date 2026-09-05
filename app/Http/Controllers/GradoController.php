<?php

namespace App\Http\Controllers;

use App\Models\Grado;
use App\Models\Organizacion;
use App\Models\NivelEducativo;
use App\Http\Requests\GradoStoreRequest;
use App\Http\Requests\GradoUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Grado::class);

        $grados = Grado::with(['organizacion', 'nivelEducativo'])
            ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                $q->where('organizacion_id', auth()->user()->organizacion_id);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', $search)
                          ->orWhere('clave', 'LIKE', $search);
                });
            })
            ->when($request->filled('activo'), function ($q) use ($request) {
                $q->where('activo', $request->boolean('activo'));
            })
            ->when($request->filled('nivel_educativo_id'), function ($q) use ($request) {
                $q->where('nivel_educativo_id', $request->nivel_educativo_id);
            })
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(25)
            ->appends($request->only(['search', 'activo', 'nivel_educativo_id']));

        $niveles = NivelEducativo::orderBy('nombre')->get();

        return view('grados.index', compact('grados', 'niveles'));
    }

    public function create()
    {
        $this->authorize('create', Grado::class);
        $organizaciones = Organizacion::orderBy('nombre')->get();
        $niveles = NivelEducativo::orderBy('nombre')->get();
        return view('grados.create', compact('organizaciones', 'niveles'));
    }

    public function store(GradoStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['activo'] = $request->has('activo');

        $grado = DB::transaction(function () use ($validated) {
            return Grado::create($validated);
        });

        return redirect()->route('grados.index')
            ->with('success', "Grado '{$grado->nombre}' creado.");
    }

    public function show(Grado $grado)
    {
        $this->authorize('view', $grado);
        $grado->load(['organizacion', 'nivelEducativo']);
        return view('grados.show', compact('grado'));
    }

    public function edit(Grado $grado)
    {
        $this->authorize('update', $grado);
        $organizaciones = Organizacion::orderBy('nombre')->get();
        $niveles = NivelEducativo::orderBy('nombre')->get();
        return view('grados.edit', compact('grado', 'organizaciones', 'niveles'));
    }

    public function update(GradoUpdateRequest $request, Grado $grado)
    {
        $validated = $request->validated();
        $validated['activo'] = $request->has('activo');

        DB::transaction(function () use ($grado, $validated) {
            $grado->update($validated);
        });

        return redirect()->route('grados.index')
            ->with('success', "Grado '{$grado->nombre}' actualizado.");
    }

    public function destroy(Grado $grado)
    {
        $this->authorize('delete', $grado);
        $grado->delete();
        return redirect()->route('grados.index')
            ->with('success', "Grado '{$grado->nombre}' eliminado.");
    }
}
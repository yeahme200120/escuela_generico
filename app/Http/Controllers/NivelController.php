<?php

namespace App\Http\Controllers;

use App\Models\NivelEducativo;
use App\Models\Organizacion;
use App\Http\Requests\NivelEducativoStoreRequest;
use App\Http\Requests\NivelEducativoUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NivelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', NivelEducativo::class);

        $niveles = NivelEducativo::with('organizacion')
            ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                $q->where('organizacion_id', auth()->user()->organizacion_id);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where('nombre', 'LIKE', $search)
                  ->orWhere('clave', 'LIKE', $search);
            })
            ->when($request->filled('activo'), function ($q) use ($request) {
                $q->where('activo', $request->boolean('activo'));
            })
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(25)
            ->appends($request->only(['search', 'activo']));

        return view('niveles_educativos.index', compact('niveles'));
    }

    public function create()
    {
        $this->authorize('create', NivelEducativo::class);
        $organizaciones = Organizacion::orderBy('nombre')->get();
        return view('niveles_educativos.create', compact('organizaciones'));
    }

    public function store(NivelEducativoStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['activo'] = $request->has('activo');

        $nivel = DB::transaction(function () use ($validated) {
            return NivelEducativo::create($validated);
        });

        return redirect()->route('niveles.index')
            ->with('success', "Nivel '{$nivel->nombre}' creado.");
    }

    public function show(NivelEducativo $nivel)
    {
        $this->authorize('view', $nivel);
        $nivel->load('organizacion');
        return view('niveles_educativos.show', compact('nivel'));
    }

    public function edit(NivelEducativo $nivel)
    {
        $this->authorize('update', $nivel);
        $organizaciones = Organizacion::orderBy('nombre')->get();
        return view('niveles_educativos.edit', compact('nivel', 'organizaciones'));
    }

    public function update(NivelEducativoUpdateRequest $request, NivelEducativo $nivel)
    {
        $validated = $request->validated();
        $validated['activo'] = $request->has('activo');

        DB::transaction(function () use ($nivel, $validated) {
            $nivel->update($validated);
        });

        return redirect()->route('niveles.index')
            ->with('success', "Nivel '{$nivel->nombre}' actualizado.");
    }

    public function destroy(NivelEducativo $nivel)
    {
        $this->authorize('delete', $nivel);
        $nivel->delete();
        return redirect()->route('niveles.index')
            ->with('success', "Nivel '{$nivel->nombre}' eliminado.");
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\CicloEscolar;
use App\Models\Organizacion;
use App\Http\Requests\CicloEscolarStoreRequest;
use App\Http\Requests\CicloEscolarUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CicloEscolarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', CicloEscolar::class);

        $ciclos = CicloEscolar::with('organizacion')
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
            ->when($request->filled('es_actual'), function ($q) use ($request) {
                $q->where('es_actual', $request->boolean('es_actual'));
            })
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(25)
            ->appends($request->only(['search', 'activo', 'es_actual']));

        return view('ciclos_escolares.index', compact('ciclos'));
    }

    public function create()
    {
        $this->authorize('create', CicloEscolar::class);
        $organizaciones = Organizacion::orderBy('nombre')->get();
        return view('ciclos_escolares.create', compact('organizaciones'));
    }

    public function store(CicloEscolarStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['activo'] = $request->has('activo');
        $validated['es_actual'] = $request->has('es_actual');

        // Si se marca como actual, desmarcar los demás de la misma organización
        if ($validated['es_actual']) {
            CicloEscolar::where('organizacion_id', $validated['organizacion_id'])
                ->update(['es_actual' => false]);
        }

        $ciclo = DB::transaction(function () use ($validated) {
            return CicloEscolar::create($validated);
        });

        return redirect()->route('ciclos-escolares.index')
            ->with('success', "Ciclo escolar '{$ciclo->nombre}' creado.");
    }

    public function show(CicloEscolar $cicloEscolar)
    {
        $this->authorize('view', $cicloEscolar);
        $cicloEscolar->load('organizacion');
        return view('ciclos_escolares.show', compact('cicloEscolar'));
    }

    public function edit(CicloEscolar $cicloEscolar)
    {
        $this->authorize('update', $cicloEscolar);
        $organizaciones = Organizacion::orderBy('nombre')->get();
        return view('ciclos_escolares.edit', compact('cicloEscolar', 'organizaciones'));
    }

    public function update(CicloEscolarUpdateRequest $request, CicloEscolar $cicloEscolar)
    {
        $validated = $request->validated();
        $validated['activo'] = $request->has('activo');
        $validated['es_actual'] = $request->has('es_actual');

        if ($validated['es_actual']) {
            CicloEscolar::where('organizacion_id', $validated['organizacion_id'])
                ->where('id', '!=', $cicloEscolar->id)
                ->update(['es_actual' => false]);
        }

        DB::transaction(function () use ($cicloEscolar, $validated) {
            $cicloEscolar->update($validated);
        });

        return redirect()->route('ciclos-escolares.index')
            ->with('success', "Ciclo escolar '{$cicloEscolar->nombre}' actualizado.");
    }

    public function destroy(CicloEscolar $cicloEscolar)
    {
        $this->authorize('delete', $cicloEscolar);
        $cicloEscolar->delete();
        return redirect()->route('ciclos-escolares.index')
            ->with('success', "Ciclo escolar '{$cicloEscolar->nombre}' eliminado.");
    }
}
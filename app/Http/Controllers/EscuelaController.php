<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use App\Models\Organizacion;
use App\Http\Requests\EscuelaStoreRequest;
use App\Http\Requests\EscuelaUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EscuelaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Escuela::class);

        $escuelas = Escuela::with('organizacion')
            ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                $q->where('organizacion_id', auth()->user()->organizacion_id);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', $search)
                          ->orWhere('clave', 'LIKE', $search)
                          ->orWhere('ciudad', 'LIKE', $search);
                });
            })
            ->when($request->filled('activa'), function ($q) use ($request) {
                $q->where('activa', $request->boolean('activa'));
            })
            ->orderBy('nombre')
            ->paginate(25)
            ->appends($request->only(['search', 'activa']));

        return view('escuelas.index', compact('escuelas'));
    }

    public function create()
    {
        $this->authorize('create', Escuela::class);
        $organizaciones = Organizacion::orderBy('nombre')->get();
        return view('escuelas.create', compact('organizaciones'));
    }

    public function store(EscuelaStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['activa'] = $request->has('activa');

        $escuela = DB::transaction(function () use ($validated) {
            return Escuela::create($validated);
        });

        return redirect()->route('escuelas.index')
            ->with('success', "Escuela '{$escuela->nombre}' creada.");
    }

    public function show(Escuela $escuela)
    {
        $this->authorize('view', $escuela);
        $escuela->load('organizacion');
        return view('escuelas.show', compact('escuela'));
    }

    public function edit(Escuela $escuela)
    {
        $this->authorize('update', $escuela);
        $organizaciones = Organizacion::orderBy('nombre')->get();
        return view('escuelas.edit', compact('escuela', 'organizaciones'));
    }

    public function update(EscuelaUpdateRequest $request, Escuela $escuela)
    {
        $validated = $request->validated();
        $validated['activa'] = $request->has('activa');

        DB::transaction(function () use ($escuela, $validated) {
            $escuela->update($validated);
        });

        return redirect()->route('escuelas.index')
            ->with('success', "Escuela '{$escuela->nombre}' actualizada.");
    }

    public function destroy(Escuela $escuela)
    {
        $this->authorize('delete', $escuela);
        $escuela->delete();
        return redirect()->route('escuelas.index')
            ->with('success', "Escuela '{$escuela->nombre}' eliminada.");
    }
}
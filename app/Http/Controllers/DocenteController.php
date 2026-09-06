<?php

namespace App\Http\Controllers;

use App\Http\Requests\Academico\DocenteRequest;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\DocenteGrupoMateria;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\User;
use App\Services\Academico\DocenteService;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocenteController extends Controller
{
    public function __construct(
        private readonly DocenteService $docenteService,
        private readonly AuditService   $audit,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('docentes.ver');
        $orgId = auth()->user()->organizacion_id;

        $docentes = Docente::with('user')
            ->whereHas('user', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->q, fn($q, $s) => $q->whereHas('user', fn($u) =>
                $u->where('nombres', 'like', "%$s%")->orWhere('apellido_paterno', 'like', "%$s%")->orWhere('email', 'like', "%$s%")
            ))
            ->when($request->estatus, fn($q, $e) => $q->where('estatus', $e))
            ->paginate(25)->withQueryString();

        return view('docentes.index', compact('docentes'));
    }

    public function create(): View
    {
        $this->authorize('docentes.crear');
        $orgId = auth()->user()->organizacion_id;
        $users = User::where('organizacion_id', $orgId)->activos()
            ->whereDoesntHave('docente')
            ->orderBy('apellido_paterno')->get();
        return view('docentes.create', compact('users'));
    }

    public function store(DocenteRequest $request): RedirectResponse
    {
        $docente = Docente::create($request->validated());
        $this->audit->log(modulo: 'docentes', accion: 'create', model: Docente::class, modelId: $docente->id,
            descripcion: "Docente creado: usuario #{$docente->user_id}");
        return redirect()->route('docentes.show', $docente)->with('success', 'Docente registrado.');
    }

    public function show(Docente $docente): View
    {
        $this->authorize('docentes.ver');
        $docente->load('user', 'asignaciones.grupo', 'asignaciones.materia', 'asignaciones.cicloEscolar');
        $stats = $this->docenteService->estadisticas($docente);
        return view('docentes.show', compact('docente', 'stats'));
    }

    public function edit(Docente $docente): View
    {
        $this->authorize('docentes.editar');
        return view('docentes.edit', compact('docente'));
    }

    public function update(DocenteRequest $request, Docente $docente): RedirectResponse
    {
        $before = $docente->toArray();
        $docente->update($request->validated());
        $this->audit->log(modulo: 'docentes', accion: 'update', model: Docente::class, modelId: $docente->id,
            before: $before, after: $docente->fresh()->toArray());
        return redirect()->route('docentes.show', $docente)->with('success', 'Docente actualizado.');
    }

    public function destroy(Docente $docente): RedirectResponse
    {
        $this->authorize('docentes.editar');
        $docente->delete();
        $this->audit->log(modulo: 'docentes', accion: 'delete', model: Docente::class, modelId: $docente->id);
        return redirect()->route('docentes.index')->with('success', 'Docente eliminado.');
    }

    // Asignación grupo-materia §35
    public function asignar(Request $request, Docente $docente): RedirectResponse
    {
        $this->authorize('docentes.editar');
        $request->validate([
            'grupo_id'         => 'required|exists:grupos,id',
            'materia_id'       => 'required|exists:materias,id',
            'ciclo_escolar_id' => 'required|exists:ciclos_escolares,id',
            'sede_id'          => 'required|exists:sedes,id',
            'horas_semana'     => 'required|integer|between:1,40',
        ]);

        DocenteGrupoMateria::firstOrCreate(
            ['docente_id'=>$docente->id,'grupo_id'=>$request->grupo_id,'materia_id'=>$request->materia_id,'ciclo_escolar_id'=>$request->ciclo_escolar_id],
            ['sede_id'=>$request->sede_id,'horas_semana'=>$request->horas_semana,'activo'=>true]
        );

        $this->audit->log(modulo: 'docentes', accion: 'assign', model: Docente::class, modelId: $docente->id,
            descripcion: "Asignado grupo#{$request->grupo_id}/materia#{$request->materia_id}");

        return back()->with('success', 'Asignación registrada.');
    }
}

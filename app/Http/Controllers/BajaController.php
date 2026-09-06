<?php

namespace App\Http\Controllers;

use App\Http\Requests\Academico\BajaRequest;
use App\Models\Alumno;
use App\Models\Baja;
use App\Models\CicloEscolar;
use App\Models\Grado;
use App\Models\Grupo;
use App\Services\Alumnos\BajaService;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BajaController extends Controller
{
    public function __construct(
        private readonly BajaService  $bajaService,
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('control_escolar.bajas');
        $orgId = auth()->user()->organizacion_id;

        $bajas = Baja::with(['alumno', 'usuarioSolicita'])
            ->whereHas('alumno', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->tipo,    fn($q, $t) => $q->where('tipo', $t))
            ->when($request->estatus, fn($q, $e) => $q->where('estatus', $e))
            ->when($request->q, fn($q, $s) => $q->whereHas('alumno', fn($a) =>
                $a->where('nombres', 'like', "%$s%")->orWhere('apellido_paterno', 'like', "%$s%")->orWhere('matricula', 'like', "%$s%")
            ))
            ->orderByDesc('created_at')
            ->paginate(25)->withQueryString();

        return view('bajas.index', compact('bajas'));
    }

    public function create(Request $request): View
    {
        $this->authorize('control_escolar.bajas');
        $alumno = $request->alumno_id ? Alumno::findOrFail($request->alumno_id) : null;
        return view('bajas.create', compact('alumno'));
    }

    public function store(BajaRequest $request): RedirectResponse
    {
        $alumno = Alumno::findOrFail($request->alumno_id);
        $datos  = $request->validated();

        if ($request->hasFile('documento')) {
            $datos['documento'] = $request->file('documento')->store('bajas', 'local');
        }

        $baja = $this->bajaService->registrarBaja($alumno, $datos, auth()->id());

        return redirect()->route('alumnos.show', $alumno)
            ->with('success', "Baja '{$baja->tipo}' registrada correctamente.");
    }

    public function show(Baja $baja): View
    {
        $this->authorize('control_escolar.ver');
        $baja->load('alumno', 'usuarioSolicita', 'usuarioAutoriza');
        return view('bajas.show', compact('baja'));
    }

    public function edit(Baja $baja): View
    {
        $this->authorize('control_escolar.bajas');
        return view('bajas.edit', compact('baja'));
    }

    public function update(Request $request, Baja $baja): RedirectResponse
    {
        $this->authorize('control_escolar.bajas');
        $request->validate([
            'estatus'      => 'required|in:solicitada,aprobada,activa,reingresado,cancelada',
            'observaciones'=> 'nullable|string|max:1000',
        ]);

        $before = $baja->toArray();
        $baja->update($request->only('estatus', 'observaciones'));
        $this->audit->log(modulo: 'control_escolar', accion: 'update', model: Baja::class, modelId: $baja->id,
            before: $before, after: $baja->fresh()->toArray());

        return redirect()->route('bajas.show', $baja)->with('success', 'Baja actualizada.');
    }

    public function reingreso(Request $request): View
    {
        $this->authorize('control_escolar.bajas');
        $alumno  = Alumno::findOrFail($request->alumno_id);
        $ciclos  = CicloEscolar::where('es_actual', true)->orWhere('estatus', 'activo')->get();
        $grados  = Grado::activos()->get();
        $grupos  = Grupo::activos()->get();
        return view('bajas.reingreso', compact('alumno', 'ciclos', 'grados', 'grupos'));
    }

    public function procesarReingreso(Request $request): RedirectResponse
    {
        $this->authorize('control_escolar.bajas');
        $request->validate([
            'alumno_id'        => 'required|exists:alumnos,id',
            'sede_id'          => 'required|exists:sedes,id',
            'grado_id'         => 'required|exists:grados,id',
            'grupo_id'         => 'nullable|exists:grupos,id',
            'ciclo_escolar_id' => 'required|exists:ciclos_escolares,id',
            'fecha_reingreso'  => 'required|date',
            'motivo'           => 'required|string|min:10',
        ]);

        $alumno = Alumno::findOrFail($request->alumno_id);
        $this->bajaService->procesarReingreso($alumno, $request->validated(), auth()->id());

        return redirect()->route('alumnos.show', $alumno)->with('success', 'Reingreso procesado correctamente.');
    }
}

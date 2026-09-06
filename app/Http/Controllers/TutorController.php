<?php

namespace App\Http\Controllers;

use App\Http\Requests\Academico\TutorRequest;
use App\Models\Alumno;
use App\Models\Tutor;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutorController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('alumnos.ver');
        $orgId   = auth()->user()->organizacion_id;
        $tutores = Tutor::with('alumnos')
            ->where('organizacion_id', $orgId)
            ->when($request->q, fn($q, $s) => $q->where('nombres', 'like', "%$s%")->orWhere('apellido_paterno', 'like', "%$s%")->orWhere('email', 'like', "%$s%"))
            ->paginate(25)->withQueryString();
        return view('tutores.index', compact('tutores'));
    }

    public function create(Request $request): View
    {
        $this->authorize('alumnos.crear');
        $alumno = $request->alumno_id ? Alumno::findOrFail($request->alumno_id) : null;
        return view('tutores.create', compact('alumno'));
    }

    public function store(TutorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['organizacion_id'] = auth()->user()->organizacion_id;
        $alumnoId = $data['alumno_id'] ?? null;
        unset($data['alumno_id'], $data['es_principal'], $data['autorizado_recoger']);

        $tutor = Tutor::create($data);

        if ($alumnoId) {
            $tutor->alumnos()->attach($alumnoId, [
                'es_principal'       => $request->boolean('es_principal'),
                'autorizado_recoger' => $request->boolean('autorizado_recoger', true),
            ]);
        }

        $this->audit->log(modulo: 'alumnos', accion: 'create', model: Tutor::class, modelId: $tutor->id,
            descripcion: "Tutor creado: {$tutor->nombres} {$tutor->apellido_paterno}");

        return redirect()->route($alumnoId ? 'alumnos.show' : 'tutores.index', $alumnoId ? $alumnoId : [])
            ->with('success', 'Tutor registrado.');
    }

    public function show(Tutor $tutor): View
    {
        $this->authorize('alumnos.ver');
        $tutor->load('alumnos');
        return view('tutores.show', compact('tutor'));
    }

    public function edit(Tutor $tutor): View
    {
        $this->authorize('alumnos.editar');
        return view('tutores.edit', compact('tutor'));
    }

    public function update(TutorRequest $request, Tutor $tutor): RedirectResponse
    {
        $data = $request->validated();
        unset($data['alumno_id'], $data['es_principal'], $data['autorizado_recoger']);
        $before = $tutor->toArray();
        $tutor->update($data);
        $this->audit->log(modulo: 'alumnos', accion: 'update', model: Tutor::class, modelId: $tutor->id,
            before: $before, after: $tutor->fresh()->toArray());
        return redirect()->route('tutores.show', $tutor)->with('success', 'Tutor actualizado.');
    }

    public function destroy(Tutor $tutor): RedirectResponse
    {
        $this->authorize('alumnos.eliminar');
        $tutor->delete();
        $this->audit->log(modulo: 'alumnos', accion: 'delete', model: Tutor::class, modelId: $tutor->id);
        return redirect()->route('tutores.index')->with('success', 'Tutor eliminado.');
    }
}

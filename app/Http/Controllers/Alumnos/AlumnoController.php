<?php

namespace App\Http\Controllers\Alumnos;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Sede;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlumnoController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('alumnos.ver');

        $orgId = auth()->user()->organizacion_id;

        $alumnos = Alumno::query()
            ->where('organizacion_id', $orgId)
            ->when($request->q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nombre', 'like', "%{$q}%")
                        ->orWhere('apellido_paterno', 'like', "%{$q}%")
                        ->orWhere('apellido_materno', 'like', "%{$q}%")
                        ->orWhere('matricula', 'like', "%{$q}%")
                        ->orWhere('curp', 'like', "%{$q}%");
                });
            })
            ->when($request->sede_id, fn($query, $sedeId) => $query->where('sede_actual_id', $sedeId))
            ->when($request->estatus, fn($query, $estatus) => $query->where('estatus', $estatus))
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->paginate(25)
            ->withQueryString();

        return view('alumnos.index', compact('alumnos'));
    }

    public function create(): View
    {
        $this->authorize('alumnos.crear');

        $orgId = auth()->user()->organizacion_id;

        $sedes = Sede::where('organizacion_id', $orgId)
            ->orderBy('nombre')
            ->get();

        return view('alumnos.create', compact('sedes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('alumnos.crear');

        $data = $request->validate([
            'nombre'            => ['required', 'string', 'max:100'],
            'apellido_paterno'  => ['required', 'string', 'max:100'],
            'apellido_materno'  => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento'  => ['nullable', 'date'],
            'email'             => ['nullable', 'email', 'max:150'],
            'curp'              => ['nullable', 'string', 'max:18'],
        ]);

        $data['organizacion_id'] = auth()->user()->organizacion_id;

        $alumno = Alumno::create($data);

        $this->audit->log(
            modulo:      'alumnos',
            accion:      'create',
            descripcion: "Alumno registrado: {$alumno->nombre} {$alumno->apellido_paterno} #{$alumno->id}",
            model:       Alumno::class,
            modelId:     $alumno->id,
        );

        return redirect()->route('alumnos.index')
            ->with('success', 'Alumno registrado.');
    }

    public function show(int $id): View
    {
        $this->authorize('alumnos.ver');

        $orgId = auth()->user()->organizacion_id;

        $alumno = Alumno::where('organizacion_id', $orgId)
            ->with(['trayectorias', 'tutores', 'bajas'])
            ->findOrFail($id);

        return view('alumnos.show', compact('alumno'));
    }

    public function edit(int $id): View
    {
        $this->authorize('alumnos.editar');

        $orgId = auth()->user()->organizacion_id;

        $alumno = Alumno::where('organizacion_id', $orgId)->findOrFail($id);

        return view('alumnos.edit', compact('alumno'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorize('alumnos.editar');

        $orgId  = auth()->user()->organizacion_id;
        $alumno = Alumno::where('organizacion_id', $orgId)->findOrFail($id);

        $data = $request->validate([
            'nombre'           => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'email'            => ['nullable', 'email', 'max:150'],
            'curp'             => ['nullable', 'string', 'max:18'],
        ]);

        $alumno->update($data);

        $this->audit->log(
            modulo:      'alumnos',
            accion:      'update',
            descripcion: "Alumno actualizado #{$alumno->id}",
            model:       Alumno::class,
            modelId:     $alumno->id,
        );

        return redirect()->route('alumnos.show', $alumno->id)
            ->with('success', 'Alumno actualizado.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorize('alumnos.eliminar');

        $orgId  = auth()->user()->organizacion_id;
        $alumno = Alumno::where('organizacion_id', $orgId)->findOrFail($id);

        $alumno->delete();

        $this->audit->log(
            modulo:      'alumnos',
            accion:      'delete',
            descripcion: "Alumno eliminado #{$alumno->id}",
            model:       Alumno::class,
            modelId:     $alumno->id,
        );

        return redirect()->route('alumnos.index')
            ->with('success', 'Alumno eliminado.');
    }
}

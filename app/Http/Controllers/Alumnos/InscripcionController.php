<?php

namespace App\Http\Controllers\Alumnos;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\AlumnoGrupoHistorial;
use App\Models\TrayectoriaAlumno;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('control_escolar.inscribir');

        $data = $request->validate([
            'alumno_id'       => ['required', 'integer', 'exists:alumnos,id'],
            'ciclo_escolar_id' => ['required', 'integer', 'exists:ciclos_escolares,id'],
            'grado_id'        => ['required', 'integer', 'exists:grados,id'],
            'grupo_id'        => ['required', 'integer', 'exists:grupos,id'],
            'sede_id'         => ['required', 'integer', 'exists:sedes,id'],
        ]);

        DB::transaction(function () use ($data) {
            $alumno = Alumno::findOrFail($data['alumno_id']);

            // Actualizar datos de inscripción en el alumno
            $alumno->update([
                'situacion_inscripcion' => 'inscrito',
                'sede_actual_id'        => $data['sede_id'],
                'estatus'               => 'activo',
            ]);

            // Crear trayectoria
            TrayectoriaAlumno::create([
                'alumno_id'         => $data['alumno_id'],
                'ciclo_escolar_id'  => $data['ciclo_escolar_id'],
                'grado_id'          => $data['grado_id'],
                'grupo_id'          => $data['grupo_id'],
                'sede_id'           => $data['sede_id'],
                'estatus'           => 'inscrito',
                'situacion_academica' => 'regular',
                'fecha_inicio'      => now()->toDateString(),
            ]);

            // Crear historial de grupo
            AlumnoGrupoHistorial::create([
                'alumno_id'        => $data['alumno_id'],
                'grupo_id'         => $data['grupo_id'],
                'ciclo_escolar_id' => $data['ciclo_escolar_id'],
                'fecha_inicio'     => now()->toDateString(),
            ]);
        });

        $this->audit->log(
            modulo:      'control_escolar',
            accion:      'inscribir',
            descripcion: "Alumno #{$data['alumno_id']} inscrito en grupo #{$data['grupo_id']} ciclo #{$data['ciclo_escolar_id']}",
            model:       Alumno::class,
            modelId:     $data['alumno_id'],
        );

        return redirect()->back()
            ->with('success', 'Alumno inscrito correctamente.');
    }
}

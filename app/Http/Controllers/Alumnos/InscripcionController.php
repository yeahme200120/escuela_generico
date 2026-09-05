<?php

namespace App\Http\Controllers\Alumnos;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\NivelEducativo;
use App\Models\Sede;
use App\Models\TrayectoriaAlumno;
use App\Models\AlumnoGrupoHistorial;
use App\Http\Requests\InscripcionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InscripcionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Paso 1: Buscar alumno para inscribir
     */
    public function index(Request $request)
    {
        $this->authorize('control_escolar.inscribir');

        $alumno = null;
        $search = $request->get('search');

        if ($search) {
            $alumno = Alumno::where(function ($q) use ($search) {
                $q->where('matricula', 'LIKE', "%{$search}%")
                    ->orWhere('nombres', 'LIKE', "%{$search}%")
                    ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                    ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                    ->orWhere('curp', 'LIKE', "%{$search}%");
            })
            ->where('estatus', 'activo')
            ->where('activo', true)
            ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                $q->where('organizacion_id', auth()->user()->organizacion_id);
            })
            ->first();
        }

        return view('alumnos.inscripcion.index', compact('alumno', 'search'));
    }

    /**
     * Paso 2: Mostrar formulario de inscripción
     */
    public function create(Request $request)
    {
        $this->authorize('control_escolar.inscribir');

        $alumnoId = $request->get('alumno_id');
        $alumno = Alumno::findOrFail($alumnoId);

        // Verificar que el alumno no esté ya inscrito en el ciclo actual
        $cicloActual = CicloEscolar::where('es_actual', true)
            ->where('activo', true)
            ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                $q->where('organizacion_id', auth()->user()->organizacion_id);
            })
            ->first();

        if ($cicloActual) {
            $inscrito = TrayectoriaAlumno::where('alumno_id', $alumno->id)
                ->where('ciclo_escolar_id', $cicloActual->id)
                ->where('estatus', 'activo')
                ->exists();

            if ($inscrito) {
                return redirect()->route('alumnos.inscripcion.index')
                    ->with('error', "El alumno {$alumno->nombre_completo} ya está inscrito en el ciclo actual.");
            }
        }

        // Datos para los selects
        $sedes = Sede::where('activa', true)
            ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                $q->where('organizacion_id', auth()->user()->organizacion_id);
            })
            ->orderBy('nombre')
            ->get();

        $ciclos = CicloEscolar::where('activo', true)
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        $niveles = NivelEducativo::where('activo', true)
            ->orderBy('orden')
            ->get();

        $grados = collect(); // Se llenarán vía AJAX o al cargar
        $grupos = collect(); // Se llenarán vía AJAX o al cargar

        return view('alumnos.inscripcion.create', compact(
            'alumno',
            'sedes',
            'ciclos',
            'niveles',
            'grados',
            'grupos',
            'cicloActual'
        ));
    }

    /**
     * Obtener grados por nivel (AJAX)
     */
    public function getGrados(Request $request)
    {
        $request->validate(['nivel_id' => 'required|exists:niveles_educativos,id']);

        $grados = Grado::where('nivel_educativo_id', $request->nivel_id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['id', 'nombre', 'clave']);

        return response()->json($grados);
    }

    /**
     * Obtener grupos por grado y sede (AJAX)
     */
    public function getGrupos(Request $request)
    {
        $request->validate([
            'grado_id' => 'required|exists:grados,id',
            'sede_id' => 'required|exists:sedes,id',
            'ciclo_id' => 'required|exists:ciclos_escolares,id',
        ]);

        $grupos = Grupo::where('grado_id', $request->grado_id)
            ->where('sede_id', $request->sede_id)
            ->where('ciclo_escolar_id', $request->ciclo_id)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'turno', 'capacidad', 'alumnos_count']);

        // Calcular disponibilidad
        foreach ($grupos as $grupo) {
            $inscritos = TrayectoriaAlumno::where('grupo_id', $grupo->id)
                ->where('estatus', 'activo')
                ->count();
            $grupo->disponible = ($grupo->capacidad - $inscritos) > 0;
            $grupo->cupos_disponibles = $grupo->capacidad - $inscritos;
        }

        return response()->json($grupos);
    }

    /**
     * Paso 3: Confirmar y procesar la inscripción
     */
    public function store(InscripcionRequest $request)
    {
        $validated = $request->validated();

        // Validar capacidad del grupo
        $grupo = Grupo::findOrFail($validated['grupo_id']);
        $inscritos = TrayectoriaAlumno::where('grupo_id', $grupo->id)
            ->where('estatus', 'activo')
            ->count();

        if ($inscritos >= $grupo->capacidad) {
            throw ValidationException::withMessages([
                'grupo_id' => 'El grupo seleccionado ya tiene cupo completo.'
            ]);
        }

        // Validar que el alumno no esté ya inscrito en este ciclo/grupo
        $existe = TrayectoriaAlumno::where('alumno_id', $validated['alumno_id'])
            ->where('ciclo_escolar_id', $validated['ciclo_escolar_id'])
            ->where('estatus', 'activo')
            ->exists();

        if ($existe) {
            throw ValidationException::withMessages([
                'alumno_id' => 'El alumno ya está inscrito en este ciclo escolar.'
            ]);
        }

        $alumno = Alumno::findOrFail($validated['alumno_id']);

        DB::transaction(function () use ($validated, $alumno, $grupo) {
            // 1. Actualizar sede actual del alumno
            if ($alumno->sede_actual_id !== $validated['sede_id']) {
                $alumno->update(['sede_actual_id' => $validated['sede_id']]);
            }

            // 2. Registrar trayectoria
            $trayectoria = TrayectoriaAlumno::create([
                'alumno_id' => $validated['alumno_id'],
                'ciclo_escolar_id' => $validated['ciclo_escolar_id'],
                'sede_id' => $validated['sede_id'],
                'grado_id' => $validated['grado_id'],
                'grupo_id' => $validated['grupo_id'],
                'estatus' => 'activo',
                'situacion_academica' => 'regular',
                'fecha_inicio' => $validated['fecha_inscripcion'] ?? now(),
                'observaciones' => $validated['observaciones'] ?? null,
                'usuario_id' => auth()->id(),
            ]);

            // 3. Registrar historial de grupos
            AlumnoGrupoHistorial::create([
                'alumno_id' => $validated['alumno_id'],
                'grupo_id' => $validated['grupo_id'],
                'ciclo_escolar_id' => $validated['ciclo_escolar_id'],
                'fecha_inicio' => $validated['fecha_inscripcion'] ?? now(),
                'motivo' => 'Inscripción',
                'usuario_id' => auth()->id(),
            ]);

            // 4. Actualizar situación académica del alumno si venía en otro estado
            if ($alumno->situacion_academica !== 'regular') {
                $alumno->update(['situacion_academica' => 'regular']);
            }

            // 5. (Opcional) Generar cargos automáticos
            if ($request->has('generar_cargos') && $request->generar_cargos) {
                // Disparar job o servicio para generar cargos por inscripción
                // app(CargoService::class)->generarCargosInscripcion($trayectoria);
            }
        });

        return redirect()->route('alumnos.show', $alumno)
            ->with('success', "Alumno {$alumno->nombre_completo} inscrito correctamente en el grupo {$grupo->nombre}.");
    }

    /**
     * Verificar si un alumno está inscrito en el ciclo actual (AJAX)
     */
    public function verificarInscrito(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'ciclo_id' => 'required|exists:ciclos_escolares,id',
        ]);

        $inscrito = TrayectoriaAlumno::where('alumno_id', $request->alumno_id)
            ->where('ciclo_escolar_id', $request->ciclo_id)
            ->where('estatus', 'activo')
            ->exists();

        return response()->json(['inscrito' => $inscrito]);
    }
}
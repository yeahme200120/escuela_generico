<?php

namespace App\Http\Controllers\Alumnos;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Organizacion;
use App\Models\Sede;
use App\Http\Requests\AlumnoStoreRequest;
use App\Http\Requests\AlumnoUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumnoController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('viewAny', Alumno::class);

        $alumnos = Alumno::with(['organizacion', 'sedeActual'])
            ->when(!auth()->user()->esSuperadmin(), function ($q) {
                $q->where('organizacion_id', auth()->user()->organizacion_id);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($query) use ($search) {
                    $query->where('nombres', 'LIKE', $search)
                        ->orWhere('apellido_paterno', 'LIKE', $search)
                        ->orWhere('apellido_materno', 'LIKE', $search)
                        ->orWhere('matricula', 'LIKE', $search)
                        ->orWhere('curp', 'LIKE', $search);
                });
            })
            ->when($request->filled('estatus'), function ($q) use ($request) {
                $q->where('estatus', $request->estatus);
            })
            ->when($request->filled('situacion_academica'), function ($q) use ($request) {
                $q->where('situacion_academica', $request->situacion_academica);
            })
            ->when($request->filled('sede_actual_id'), function ($q) use ($request) {
                $q->where('sede_actual_id', $request->sede_actual_id);
            })
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombres')
            ->paginate(25)
            ->appends($request->only(['search', 'estatus', 'situacion_academica', 'sede_actual_id']));

        // Para filtros
        $sedes = Sede::where('activa', true)
            ->when(!auth()->user()->esSuperadmin(), function ($q) {
                $q->where('organizacion_id', auth()->user()->organizacion_id);
            })
            ->orderBy('nombre')
            ->get();

        return view('alumnos.index', compact('alumnos', 'sedes'));
    }

    public function create()
    {
        $this->authorize('create', Alumno::class);

        $organizaciones = Organizacion::orderBy('nombre')->get();
        $sedes = Sede::where('activa', true)
            ->when(!auth()->user()->esSuperadmin(), function ($q) {
                $q->where('organizacion_id', auth()->user()->organizacion_id);
            })
            ->orderBy('nombre')
            ->get();

        return view('alumnos.create', compact('organizaciones', 'sedes'));
    }

    public function store(AlumnoStoreRequest $request)
    {
        $validated = $request->validated();

        // Valores por defecto
        $validated['estatus'] = $validated['estatus'] ?? 'activo';
        $validated['situacion_academica'] = $validated['situacion_academica'] ?? 'regular';
        $validated['activo'] = $request->has('activo');
        $validated['fecha_ingreso'] = $validated['fecha_ingreso'] ?? now()->toDateString();

        // Generar matrícula automática si no se proporcionó
        if (empty($validated['matricula'])) {
            $validated['matricula'] = $this->generarMatricula($validated['organizacion_id']);
        }

        $alumno = DB::transaction(function () use ($validated) {
            return Alumno::create($validated);
        });

        return redirect()->route('alumnos.index')
            ->with('success', "Alumno {$alumno->nombre_completo} creado correctamente.");
    }

    public function show(Alumno $alumno)
    {
        $this->authorize('view', $alumno);

        $alumno->load(['organizacion', 'sedeActual', 'trayectorias' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }, 'tutores', 'gruposHistorial']);

        return view('alumnos.show', compact('alumno'));
    }

    public function edit(Alumno $alumno)
    {
        $this->authorize('update', $alumno);

        $organizaciones = Organizacion::orderBy('nombre')->get();
        $sedes = Sede::where('activa', true)
            ->when(!auth()->user()->esSuperadmin(), function ($q) {
                $q->where('organizacion_id', auth()->user()->organizacion_id);
            })
            ->orderBy('nombre')
            ->get();

        return view('alumnos.edit', compact('alumno', 'organizaciones', 'sedes'));
    }

    public function update(AlumnoUpdateRequest $request, Alumno $alumno)
    {
        $validated = $request->validated();

        $validated['estatus'] = $validated['estatus'] ?? 'activo';
        $validated['situacion_academica'] = $validated['situacion_academica'] ?? 'regular';
        $validated['activo'] = $request->has('activo');

        DB::transaction(function () use ($alumno, $validated) {
            $alumno->update($validated);
        });

        return redirect()->route('alumnos.index')
            ->with('success', "Alumno {$alumno->nombre_completo} actualizado correctamente.");
    }

    public function destroy(Alumno $alumno)
    {
        $this->authorize('delete', $alumno);

        // Verificar si tiene relaciones activas (pagos, calificaciones, etc.)
        // Si tiene, no permitir eliminar
        if ($alumno->pagos()->exists() || $alumno->calificaciones()->exists()) {
            return back()->with('error', 'No se puede eliminar el alumno porque tiene registros asociados (pagos, calificaciones, etc.).');
        }

        $nombre = $alumno->nombre_completo;
        DB::transaction(function () use ($alumno) {
            $alumno->delete();
        });

        return redirect()->route('alumnos.index')
            ->with('success', "Alumno {$nombre} eliminado correctamente.");
    }

    /**
     * Genera una matrícula automática: AÑO + NÚMERO CORRELATIVO
     */
    private function generarMatricula($organizacionId)
    {
        $year = now()->format('Y');
        $ultimo = Alumno::where('organizacion_id', $organizacionId)
            ->where('matricula', 'LIKE', $year . '%')
            ->orderBy('matricula', 'desc')
            ->first();

        if ($ultimo) {
            $numero = (int) substr($ultimo->matricula, 4) + 1;
        } else {
            $numero = 1;
        }

        return $year . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
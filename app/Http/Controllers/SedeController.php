<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use App\Models\Escuela;
use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SedeController extends Controller
{
    /**
     * Constructor: aplica middleware de autenticación a todo el controlador.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra el listado paginado y filtrable de sedes.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Sede::class);

        $sedes = Sede::with(['escuela.organizacion'])

            // Filtro por organización (si no es superadmin)
            ->when(!auth()->user()->isSuperAdmin(), function ($query) {
                $query->where('organizacion_id', auth()->user()->organizacion_id);
            })

            // Búsqueda textual
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'LIKE', $search)
                      ->orWhere('clave', 'LIKE', $search)
                      ->orWhere('ciudad', 'LIKE', $search)
                      ->orWhere('direccion', 'LIKE', $search);
                });
            })

            // Filtro por estado activa/inactiva
            ->when($request->filled('activa'), function ($query) use ($request) {
                $query->where('activa', $request->boolean('activa'));
            })

            // Filtro por escuela
            ->when($request->filled('escuela_id'), function ($query) use ($request) {
                $query->where('escuela_id', $request->escuela_id);
            })

            // Ordenamiento dinámico (seguro)
            ->when($request->filled('sort'), function ($query) use ($request) {
                $allowed = ['id', 'nombre', 'clave', 'ciudad', 'activa', 'created_at'];
                if (in_array($request->sort, $allowed)) {
                    $direction = $request->get('direction', 'asc');
                    $query->orderBy($request->sort, $direction === 'desc' ? 'desc' : 'asc');
                }
            }, function ($query) {
                $query->orderBy('nombre', 'asc');
            })

            ->paginate(25)
            ->appends($request->only(['search', 'activa', 'escuela_id', 'sort', 'direction']));

        // Para los filtros de la vista, cargamos las escuelas (opcional)
        $escuelas = Escuela::orderBy('nombre')->get();

        return view('sedes.index', compact('sedes', 'escuelas'));
    }

    /**
     * Muestra el formulario para crear una nueva sede.
     */
    public function create()
    {
        $this->authorize('create', Sede::class);

        // Cargar datos para los selects
        $organizaciones = Organizacion::orderBy('nombre')->get();
        $escuelas = Escuela::orderBy('nombre')->get();

        return view('sedes.create', compact('organizaciones', 'escuelas'));
    }

    /**
     * Almacena una nueva sede en la base de datos.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Sede::class);

        // 1. Validación exhaustiva de todos los campos
        $validated = $request->validate([
            'organizacion_id'          => 'required|exists:organizaciones,id',
            'escuela_id'               => 'required|exists:escuelas,id',
            'nombre'                   => 'required|string|max:200|unique:sedes,nombre',
            'clave'                    => 'nullable|string|max:50|unique:sedes,clave',
            'email'                    => 'nullable|email|max:255',
            'telefono'                 => 'nullable|string|max:30',
            'direccion'                => 'nullable|string',
            'ciudad'                   => 'nullable|string|max:100',
            'estado'                   => 'nullable|string|max:100',
            'pais'                     => 'nullable|string|max:100',
            'codigo_postal'            => 'nullable|string|max:10',
            'latitud'                  => 'nullable|numeric|between:-90,90',
            'longitud'                 => 'nullable|numeric|between:-180,180',
            'radio_geocerca_metros'    => 'nullable|integer|min:0',
            'geocerca_activa'          => 'sometimes|boolean',
            'calificacion_minima'      => 'nullable|numeric|min:0|max:10',
            'calificacion_maxima'      => 'nullable|numeric|min:0|max:10',
            'tolerancia_retardo_minutos' => 'nullable|integer|min:0',
            'zona_horaria'             => 'nullable|string|max:60',
            'moneda'                   => 'nullable|string|max:10',
            'activa'                   => 'sometimes|boolean',
            'configuracion'            => 'nullable|json', // Se guarda como string JSON
        ]);

        // 2. Normalizar campos booleanos (checkboxes)
        $validated['activa'] = $request->has('activa');
        $validated['geocerca_activa'] = $request->has('geocerca_activa');

        // 3. Valores por defecto para campos nulos que son requeridos lógicamente
        $validated['radio_geocerca_metros'] = $validated['radio_geocerca_metros'] ?? 500;
        $validated['calificacion_minima'] = $validated['calificacion_minima'] ?? 6.00;
        $validated['calificacion_maxima'] = $validated['calificacion_maxima'] ?? 10.00;
        $validated['tolerancia_retardo_minutos'] = $validated['tolerancia_retardo_minutos'] ?? 10;
        $validated['zona_horaria'] = $validated['zona_horaria'] ?? 'America/Mexico_City';
        $validated['moneda'] = $validated['moneda'] ?? 'MXN';
        $validated['pais'] = $validated['pais'] ?? 'México';

        // 4. Transacción para asegurar integridad
        $sede = DB::transaction(function () use ($validated) {
            return Sede::create($validated);
        });

        // 5. Redireccionar con mensaje de éxito
        return redirect()->route('sedes.index')
            ->with('success', "Sede '{$sede->nombre}' creada exitosamente.");
    }

    /**
     * Muestra los detalles de una sede específica.
     */
    public function show(Sede $sede)
    {
        $this->authorize('view', $sede);

        // Cargar relaciones para mostrar información completa
        $sede->load(['escuela.organizacion']);

        return view('sedes.show', compact('sede'));
    }

    /**
     * Muestra el formulario para editar una sede existente.
     */
    public function edit(Sede $sede)
    {
        $this->authorize('update', $sede);

        // Cargar datos para los selects
        $organizaciones = Organizacion::orderBy('nombre')->get();
        $escuelas = Escuela::orderBy('nombre')->get();

        return view('sedes.edit', compact('sede', 'organizaciones', 'escuelas'));
    }

    /**
     * Actualiza una sede existente en la base de datos.
     */
    public function update(Request $request, Sede $sede)
    {
        $this->authorize('update', $sede);

        // 1. Validación con reglas de unicidad ignorando el registro actual
        $validated = $request->validate([
            'organizacion_id'          => 'required|exists:organizaciones,id',
            'escuela_id'               => 'required|exists:escuelas,id',
            'nombre'                   => ['required', 'string', 'max:200', Rule::unique('sedes')->ignore($sede->id)],
            'clave'                    => ['nullable', 'string', 'max:50', Rule::unique('sedes')->ignore($sede->id)],
            'email'                    => 'nullable|email|max:255',
            'telefono'                 => 'nullable|string|max:30',
            'direccion'                => 'nullable|string',
            'ciudad'                   => 'nullable|string|max:100',
            'estado'                   => 'nullable|string|max:100',
            'pais'                     => 'nullable|string|max:100',
            'codigo_postal'            => 'nullable|string|max:10',
            'latitud'                  => 'nullable|numeric|between:-90,90',
            'longitud'                 => 'nullable|numeric|between:-180,180',
            'radio_geocerca_metros'    => 'nullable|integer|min:0',
            'geocerca_activa'          => 'sometimes|boolean',
            'calificacion_minima'      => 'nullable|numeric|min:0|max:10',
            'calificacion_maxima'      => 'nullable|numeric|min:0|max:10',
            'tolerancia_retardo_minutos' => 'nullable|integer|min:0',
            'zona_horaria'             => 'nullable|string|max:60',
            'moneda'                   => 'nullable|string|max:10',
            'activa'                   => 'sometimes|boolean',
            'configuracion'            => 'nullable|json',
        ]);

        // 2. Normalizar campos booleanos
        $validated['activa'] = $request->has('activa');
        $validated['geocerca_activa'] = $request->has('geocerca_activa');

        // 3. Valores por defecto (si vienen vacíos)
        $validated['radio_geocerca_metros'] = $validated['radio_geocerca_metros'] ?? 500;
        $validated['calificacion_minima'] = $validated['calificacion_minima'] ?? 6.00;
        $validated['calificacion_maxima'] = $validated['calificacion_maxima'] ?? 10.00;
        $validated['tolerancia_retardo_minutos'] = $validated['tolerancia_retardo_minutos'] ?? 10;
        $validated['zona_horaria'] = $validated['zona_horaria'] ?? 'America/Mexico_City';
        $validated['moneda'] = $validated['moneda'] ?? 'MXN';
        $validated['pais'] = $validated['pais'] ?? 'México';

        // 4. Transacción para asegurar integridad
        DB::transaction(function () use ($sede, $validated) {
            $sede->update($validated);
        });

        // 5. Redireccionar con mensaje de éxito
        return redirect()->route('sedes.index')
            ->with('success', "Sede '{$sede->nombre}' actualizada correctamente.");
    }

    /**
     * Elimina (soft delete) una sede.
     */
    public function destroy(Sede $sede)
    {
        $this->authorize('delete', $sede);

        // Verificar si la sede tiene dependencias activas antes de eliminar (opcional)
        // Ejemplo: if ($sede->alumnos()->exists()) { ... }

        DB::transaction(function () use ($sede) {
            $sede->delete(); // Soft delete
        });

        return redirect()->route('sedes.index')
            ->with('success', "Sede '{$sede->nombre}' desactivada correctamente.");
    }
}
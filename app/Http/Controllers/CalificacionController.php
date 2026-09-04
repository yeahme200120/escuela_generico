<?php namespace App\Http\Controllers;
use App\Models\Calificacion;
use App\Services\RiesgoAcademicoService;

class CalificacionController extends Controller {
    
    private RiesgoAcademicoService $riesgoService;
    
    public function __construct(RiesgoAcademicoService $riesgoService) {
        $this->riesgoService = $riesgoService;
    }
    
    public function index() {
        $calificaciones = Calificacion::with(['alumno', 'materia'])
            ->paginate(15);
        return view('calificaciones.index', ['calificaciones' => $calificaciones]);
    }
    
    public function store(\Illuminate\Http\Request $request) {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'materia_id' => 'required|exists:materias,id',
            'calificacion' => 'required|numeric|min:0|max:100'
        ]);
        
        $calificacion = Calificacion::create($request->all());
        
        return redirect()->route('calificaciones.show', $calificacion)
            ->with('success', 'Calificación registrada correctamente.');
    }
    
    public function show(Calificacion $calificacion) {
        // Calcular riesgo si calificación es baja
        if ($calificacion->calificacion < 70) {
            $riesgo = $this->riesgoService->evaluarRiesgo($calificacion->alumno_id, null);
        }
        
        return view('calificaciones.show', [
            'calificacion' => $calificacion,
            'riesgo' => $riesgo ?? null
        ]);
    }
}

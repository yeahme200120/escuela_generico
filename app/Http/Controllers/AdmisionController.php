<?php namespace App\Http\Controllers;
use App\Models\Admision;
use App\Services\RiesgoAcademicoService;
use App\Services\PythonJobService;

class AdmisionController extends Controller {
    
    private RiesgoAcademicoService $riesgoService;
    private PythonJobService $pythonService;
    
    public function __construct(RiesgoAcademicoService $riesgoService, PythonJobService $pythonService) {
        $this->riesgoService = $riesgoService;
        $this->pythonService = $pythonService;
    }
    
    public function index() {
        $admisiones = Admision::with('alumno')->paginate(15);
        return view('admisiones.index', ['admisiones' => $admisiones]);
    }
    
    public function store(\Illuminate\Http\Request $request) {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'estado' => 'required|in:pendiente,aprobada,rechazada'
        ]);
        
        $admision = Admision::create($request->all());
        
        // Si fue aprobada, calcular riesgo inicial
        if ($request->input('estado') === 'aprobada') {
            $this->pythonService->despacharAsync('calcular_riesgo', [
                'alumno_id' => $request->input('alumno_id'),
                'ciclo_id' => null
            ], auth()->id());
        }
        
        return redirect()->route('admisiones.show', $admision)
            ->with('success', 'Admisión procesada correctamente.');
    }
}

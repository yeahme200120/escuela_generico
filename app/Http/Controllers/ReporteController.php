<?php namespace App\Http\Controllers;
use App\Models\Reporte;
use App\Services\GeneradorReportesService;
use App\Services\PythonJobService;

class ReporteController extends Controller {
    
    private GeneradorReportesService $generador;
    private PythonJobService $pythonService;
    
    public function __construct(GeneradorReportesService $generador, PythonJobService $pythonService) {
        $this->generador = $generador;
        $this->pythonService = $pythonService;
    }
    
    public function index() {
        $reportes = Reporte::paginate(15);
        return view('reportes.index', ['reportes' => $reportes]);
    }
    
    public function create() {
        return view('reportes.create');
    }
    
    public function store(\Illuminate\Http\Request $request) {
        $request->validate([
            'tipo' => 'required|in:calificaciones,asistencia,estudiantes,docentes,indicadores',
            'formato' => 'required|in:excel,pdf'
        ]);
        
        $tipo = $request->input('tipo');
        $filtros = [
            'ciclo_id' => $request->input('ciclo_id'),
            'grupo_id' => $request->input('grupo_id'),
            'sede_id' => $request->input('sede_id'),
            'fecha_desde' => $request->input('fecha_desde'),
            'fecha_hasta' => $request->input('fecha_hasta')
        ];
        
        // Despachar trabajo async a Python
        $this->pythonService->despacharAsync('generar_reportes', [
            'tipo' => $tipo,
            'filtros' => $filtros,
            'formato' => $request->input('formato')
        ], auth()->id());
        
        return redirect()->route('reportes.index')
            ->with('success', 'Reporte en generación. Se notificará cuando esté listo.');
    }
    
    public function show(Reporte $reporte) {
        return view('reportes.show', ['reporte' => $reporte]);
    }
    
    public function descargar(Reporte $reporte) {
        return response()->download($reporte->ruta_archivo);
    }
}

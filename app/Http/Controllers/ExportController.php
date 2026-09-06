<?php namespace App\Http\Controllers;
use App\Services\ExportService;
use App\Services\Python\PythonJobService;
use Illuminate\Http\Request;

class ExportController extends Controller {
    
    private ExportService $exportService;
    private PythonJobService $pythonService;
    
    public function __construct(ExportService $exportService, PythonJobService $pythonService) {
        $this->exportService = $exportService;
        $this->pythonService = $pythonService;
    }
    
    public function exportarExcel(Request $request) {
        $request->validate([
            'modelo' => 'required|string',
            'columnas' => 'nullable|array'
        ]);
        
        $resultado = $this->exportService->exportarExcel(
            $request->input('modelo'),
            $request->input('columnas', []),
            $request->all()
        );
        
        if ($resultado) {
            $this->exportService->auditarExportacion(
                auth()->id(),
                $request->input('modelo'),
                $request->all(),
                'excel',
                $resultado['registros']
            );
            
            return response()->download($resultado['ruta']);
        }
        
        return redirect()->back()->with('error', 'Error generando exportaci�n.');
    }
    
    public function exportarPDF(Request $request) {
        $request->validate([
            'modelo' => 'required|string'
        ]);
        
        // Despachar a Python para generar PDF
        $this->pythonService->despacharAsync('generar_reportes', [
            'tipo' => $request->input('modelo'),
            'formato' => 'pdf',
            'filtros' => $request->all()
        ], auth()->id());
        
        return redirect()->back()
            ->with('success', 'Exportaci�n PDF en progreso. Se descargar� autom�ticamente.');
    }
}

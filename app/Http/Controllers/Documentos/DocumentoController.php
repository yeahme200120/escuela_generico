<?php
namespace App\Http\Controllers\Documentos;
use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\TipoDocumento;
use App\Services\Documentos\DocumentoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentoController extends Controller
{
    public function __construct(private readonly DocumentoService $docService) {}

    public function index(Request $request): View
    {
        $this->authorize('documentos.ver');
        $orgId = auth()->user()->organizacion_id;
        $docs = Documento::with(['alumno','tipoDocumento','sede'])
            ->whereHas('sede',fn($q)=>$q->where('organizacion_id',$orgId))
            ->when($request->alumno_id, fn($q,$id)=>$q->where('alumno_id',$id))
            ->when($request->estado, fn($q,$e)=>$q->where('estado',$e))
            ->when($request->tipo_id, fn($q,$id)=>$q->where('tipo_documento_id',$id))
            ->orderByDesc('created_at')->paginate(25)->withQueryString();
        $tipos = TipoDocumento::where('organizacion_id',$orgId)->activos()->get();
        return view('documentos.index', compact('docs','tipos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('documentos.generar');
        $data = $request->validate(['alumno_id'=>'required|exists:alumnos,id','tipo_documento_id'=>'required|exists:tipos_documento,id','sede_id'=>'required|exists:sedes,id']);
        $doc = $this->docService->crear($data['alumno_id'],$data['tipo_documento_id'],$data['sede_id'],auth()->id());
        return redirect()->route('documentos.index')->with('success',"Documento generado con folio {$doc->folio}.");
    }

    public function autorizar(Request $request, Documento $documento): RedirectResponse
    {
        $this->authorize('documentos.autorizar');
        $this->docService->autorizar($documento, auth()->id());
        return back()->with('success','Documento autorizado.');
    }
}

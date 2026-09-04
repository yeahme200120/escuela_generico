<?php
namespace App\Http\Controllers\Admisiones;
use App\Http\Controllers\Controller;
use App\Models\Prospecto;
use App\Models\SeguimientoProspecto;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProspectoController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $orgId = auth()->user()->organizacion_id;
        $prospectos = Prospecto::where('organizacion_id',$orgId)
            ->when($request->estatus, fn($q,$e)=>$q->where('estatus',$e))
            ->when($request->q, fn($q,$s)=>$q->where('nombres','like',"%$s%")->orWhere('apellido_paterno','like',"%$s%")->orWhere('email','like',"%$s%"))
            ->orderByDesc('created_at')->paginate(25)->withQueryString();
        return view('admisiones.prospectos.index', compact('prospectos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['nombres'=>'required|string|max:100','apellido_paterno'=>'required|string|max:100','apellido_materno'=>'nullable|string|max:100','email'=>'nullable|email','telefono'=>'nullable|string|max:30','sede_interes_id'=>'nullable|exists:sedes,id','nivel_interes'=>'nullable|string|max:100']);
        $data['organizacion_id'] = auth()->user()->organizacion_id;
        $p = Prospecto::create($data);
        $this->audit->log(modulo:'admisiones',accion:'create',model:Prospecto::class,modelId:$p->id);
        return redirect()->route('admisiones.prospectos.index')->with('success','Prospecto registrado.');
    }

    public function seguimiento(Request $request, int $id): RedirectResponse
    {
        $p = Prospecto::where('organizacion_id',auth()->user()->organizacion_id)->findOrFail($id);
        $request->validate(['tipo'=>'required|in:llamada,email,visita,cita,nota','descripcion'=>'required|string|max:1000','estatus'=>'nullable|in:nuevo,contactado,citado,evaluado,admitido,rechazado,cancelado']);
        SeguimientoProspecto::create(['prospecto_id'=>$p->id,'usuario_id'=>auth()->id(),'tipo'=>$request->tipo,'descripcion'=>$request->descripcion]);
        if($request->estatus) $p->update(['estatus'=>$request->estatus]);
        return back()->with('success','Seguimiento registrado.');
    }

    public function show(int $id): View
    {
        $p = Prospecto::with('seguimientos.usuario','sedeInteres')->where('organizacion_id',auth()->user()->organizacion_id)->findOrFail($id);
        return view('admisiones.prospectos.show', ['prospecto'=>$p]);
    }
}

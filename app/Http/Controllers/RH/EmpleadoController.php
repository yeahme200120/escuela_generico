<?php
namespace App\Http\Controllers\RH;
use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmpleadoController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('rh.ver');
        $orgId = auth()->user()->organizacion_id;
        $empleados = Empleado::with('user')->where('organizacion_id',$orgId)
            ->when($request->q, fn($q,$s)=>$q->whereHas('user',fn($u)=>$u->where('nombres','like',"%$s%")->orWhere('apellido_paterno','like',"%$s%")))
            ->when($request->estatus, fn($q,$e)=>$q->where('estatus',$e))
            ->paginate(25)->withQueryString();
        return view('rh.empleados.index', compact('empleados'));
    }

    public function create(): View
    {
        $this->authorize('rh.gestionar');
        return view('rh.empleados.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('rh.gestionar');
        $data = $request->validate(['user_id'=>'required|exists:users,id','puesto'=>'nullable|string|max:150','departamento'=>'nullable|string|max:100','fecha_ingreso'=>'nullable|date','tipo_contrato'=>'required|in:base,contrato,honorarios,tiempo_parcial','salario'=>'nullable|numeric|min:0']);
        $data['organizacion_id'] = auth()->user()->organizacion_id;
        $emp = Empleado::create($data);
        $this->audit->log(modulo:'rh',accion:'create',model:Empleado::class,modelId:$emp->id);
        return redirect()->route('rh.empleados.index')->with('success','Empleado registrado.');
    }

    public function show(int $id): View
    {
        $this->authorize('rh.ver');
        $empleado = Empleado::with(['user','contratos','asistencias'])->where('organizacion_id',auth()->user()->organizacion_id)->findOrFail($id);
        return view('rh.empleados.show', compact('empleado'));
    }

    public function edit(int $id): View
    {
        $this->authorize('rh.gestionar');
        $empleado = Empleado::where('organizacion_id',auth()->user()->organizacion_id)->findOrFail($id);
        return view('rh.empleados.edit', compact('empleado'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorize('rh.gestionar');
        $empleado = Empleado::where('organizacion_id',auth()->user()->organizacion_id)->findOrFail($id);
        $data = $request->validate(['puesto'=>'nullable|string|max:150','departamento'=>'nullable|string|max:100','tipo_contrato'=>'required|in:base,contrato,honorarios,tiempo_parcial','salario'=>'nullable|numeric|min:0','estatus'=>'required|in:activo,baja,suspendido']);
        $empleado->update($data);
        return redirect()->route('rh.empleados.show',$id)->with('success','Empleado actualizado.');
    }
}

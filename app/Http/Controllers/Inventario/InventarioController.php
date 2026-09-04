<?php
namespace App\Http\Controllers\Inventario;
use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventarioController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('inventario.ver');
        $orgId = auth()->user()->organizacion_id;
        $items = Inventario::with('sede')
            ->whereHas('sede',fn($q)=>$q->where('organizacion_id',$orgId))
            ->when($request->sede_id, fn($q,$s)=>$q->where('sede_id',$s))
            ->when($request->q, fn($q,$s)=>$q->where('nombre','like',"%$s%"))
            ->paginate(25)->withQueryString();
        return view('inventario.index', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('inventario.gestionar');
        $data = $request->validate(['sede_id'=>'required|exists:sedes,id','nombre'=>'required|string|max:200','unidad_medida'=>'nullable|string|max:30','stock_minimo'=>'integer|min:0','precio_unitario'=>'nullable|numeric|min:0']);
        Inventario::create($data);
        return redirect()->route('inventario.index')->with('success','Artículo registrado.');
    }

    public function movimiento(Request $request, int $id): RedirectResponse
    {
        $this->authorize('inventario.gestionar');
        $item = Inventario::findOrFail($id);
        $data = $request->validate(['tipo'=>'required|in:entrada,salida,ajuste','cantidad'=>'required|integer|min:1','motivo'=>'nullable|string|max:300']);
        $anterior = $item->stock_actual;
        $nuevo = match($data['tipo']) {
            'entrada' => $anterior + $data['cantidad'],
            'salida'  => max(0, $anterior - $data['cantidad']),
            default   => $data['cantidad'],
        };
        \Illuminate\Support\Facades\DB::transaction(function() use($item,$data,$anterior,$nuevo){
            $item->update(['stock_actual'=>$nuevo]);
            MovimientoInventario::create(['inventario_id'=>$item->id,'sede_id'=>$item->sede_id,'tipo'=>$data['tipo'],'cantidad'=>$data['cantidad'],'stock_anterior'=>$anterior,'stock_posterior'=>$nuevo,'motivo'=>$data['motivo']??null,'usuario_id'=>auth()->id()]);
        });
        return back()->with('success','Movimiento registrado.');
    }
}

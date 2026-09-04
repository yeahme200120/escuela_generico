<?php

namespace App\Http\Controllers\Auditoria;

use App\Http\Controllers\Controller;
use App\Models\QueryLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueryLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', \App\Models\AuditLog::class);

        $orgId = auth()->user()->organizacion_id;

        $query = QueryLog::with('user')
            ->where('organizacion_id', $orgId)
            ->orderByDesc('created_at');

        if ($tipo = $request->input('tipo')) {
            $query->where('tipo', $tipo);
        }
        if ($request->boolean('solo_lentas')) {
            $query->where('es_lenta', true);
        }
        if ($tabla = $request->input('tabla')) {
            $query->where('tabla_principal', 'like', "%{$tabla}%");
        }
        if ($reqId = $request->input('request_id')) {
            $query->where('request_id', 'like', "%{$reqId}%");
        }
        if ($desde = $request->input('desde')) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta = $request->input('hasta')) {
            $query->whereDate('created_at', '<=', $hasta);
        }

        $logs         = $query->paginate(50)->withQueryString();
        $total_lentas = QueryLog::where('organizacion_id', $orgId)->where('es_lenta', true)->count();

        return view('auditoria.queries', compact('logs', 'total_lentas'));
    }
}

<?php

namespace App\Http\Controllers\Auditoria;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', AuditLog::class);

        $orgId = auth()->user()->organizacion_id;

        $query = AuditLog::with(['user', 'sede'])
            ->where('organizacion_id', $orgId)
            ->orderByDesc('created_at');

        // ── Filtros ──────────────────────────────────────────────
        if ($modulo = $request->input('modulo')) {
            $query->where('modulo', $modulo);
        }
        if ($accion = $request->input('accion')) {
            $query->where('accion', $accion);
        }
        if ($resultado = $request->input('resultado')) {
            $query->where('resultado', $resultado);
        }
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }
        if ($desde = $request->input('desde')) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta = $request->input('hasta')) {
            $query->whereDate('created_at', '<=', $hasta);
        }
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhere('user_nombre', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('model_descripcion', 'like', "%{$search}%");
            });
        }

        $logs    = $query->paginate(25)->withQueryString();
        $modulos = AuditLog::where('organizacion_id', $orgId)
                            ->distinct()->orderBy('modulo')->pluck('modulo');

        return view('auditoria.index', compact('logs', 'modulos'));
    }
}

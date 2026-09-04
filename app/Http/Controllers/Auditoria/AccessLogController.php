<?php

namespace App\Http\Controllers\Auditoria;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', \App\Models\AuditLog::class);

        $orgId = auth()->user()->organizacion_id;

        $query = AccessLog::with('user')
            ->where('organizacion_id', $orgId)
            ->orderByDesc('created_at');

        if ($evento = $request->input('evento')) {
            $query->where('evento', $evento);
        }
        if ($resultado = $request->input('resultado')) {
            $query->where('resultado', $resultado);
        }
        if ($request->boolean('solo_anomalias')) {
            $query->where(function ($q) {
                $q->where('viaje_imposible', true)
                  ->orWhere('es_nuevo_dispositivo', true)
                  ->orWhere('fuera_de_geocerca', true)
                  ->orWhere('fuera_de_horario', true);
            });
        }
        if ($ip = $request->input('ip')) {
            $query->where('ip_address', 'like', "%{$ip}%");
        }
        if ($desde = $request->input('desde')) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta = $request->input('hasta')) {
            $query->whereDate('created_at', '<=', $hasta);
        }
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('email_intento', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs  = $query->paginate(50)->withQueryString();
        $total_anomalias = AccessLog::where('organizacion_id', $orgId)
            ->where(function ($q) {
                $q->where('viaje_imposible', true)
                  ->orWhere('fuera_de_geocerca', true)
                  ->orWhere('es_nuevo_dispositivo', true);
            })->count();

        return view('auditoria.accesos', compact('logs', 'total_anomalias'));
    }
}

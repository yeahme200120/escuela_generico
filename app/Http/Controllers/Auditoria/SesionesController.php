<?php

namespace App\Http\Controllers\Auditoria;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SesionesController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', \App\Models\AuditLog::class);

        $orgId = auth()->user()->organizacion_id;

        $query = UserSession::with(['user', 'sede'])
            ->where('organizacion_id', $orgId)
            ->orderByDesc('first_seen_at');

        if ($request->boolean('solo_activas')) {
            $query->where('active', true);
        }
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }
        if ($device = $request->input('device_type')) {
            $query->where('device_type', $device);
        }
        if ($search = $request->input('q')) {
            $query->whereHas('user', fn($q) =>
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('nombres', 'like', "%{$search}%")
            )->orWhere('ip_address', 'like', "%{$search}%");
        }

        $sesiones      = $query->paginate(30)->withQueryString();
        $total_activas = UserSession::where('organizacion_id', $orgId)->where('active', true)->count();
        $mi_session    = session('user_session_uuid');

        return view('auditoria.sesiones', compact('sesiones', 'total_activas', 'mi_session'));
    }

    public function destroy(Request $request, string $uuid, AuthService $authService): RedirectResponse
    {
        $this->authorize('viewAny', \App\Models\AuditLog::class);

        $orgId = auth()->user()->organizacion_id;

        $sesion = UserSession::where('uuid', $uuid)
            ->where('organizacion_id', $orgId)
            ->firstOrFail();

        // No puede revocar su propia sesión desde aquí
        if ($uuid === session('user_session_uuid')) {
            return back()->with('error', 'Usa "Cerrar sesión" para cerrar tu sesión actual.');
        }

        $authService->revocarSesion($uuid, 'admin_revoke');

        return back()->with('success', "Sesión {$uuid} revocada correctamente.");
    }
}

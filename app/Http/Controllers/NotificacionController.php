<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Services\Comunicacion\NotificacionService;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    public function __construct(
        private readonly NotificacionService $notifService,
        private readonly AuditService        $audit,
    ) {}

    public function index(Request $request): View
    {
        $orgId = auth()->user()->organizacion_id;
        $notificaciones = Notificacion::where('organizacion_id', $orgId)
            ->when($request->canal,   fn($q, $c) => $q->where('canal', $c))
            ->when($request->estado,  fn($q, $e) => $q->where('estado', $e))
            ->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('notificaciones.index', compact('notificaciones'));
    }

    public function create(): View
    {
        return view('notificaciones.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'titulo'     => 'required|string|max:200',
            'cuerpo'     => 'required|string',
            'canal'      => 'required|in:interna,email,sms,push',
            'tipo'       => 'required|in:info,exito,advertencia,peligro',
            'segmento'   => 'required|in:todos,docentes,alumnos,tutores,sede',
            'sede_id'    => 'nullable|exists:sedes,id',
        ]);

        $data['organizacion_id'] = auth()->user()->organizacion_id;
        $data['remitente_id']    = auth()->id();
        $data['estado']          = 'borrador';

        $notif = Notificacion::create($data);

        $this->audit->log(modulo: 'comunicacion', accion: 'create', model: Notificacion::class, modelId: $notif->id,
            descripcion: "Notificación creada: {$notif->titulo}");

        return redirect()->route('notificaciones.show', $notif)->with('success', 'Notificación creada.');
    }

    public function show(Notificacion $notificacion): View
    {
        $notificacion->load('remitente');
        return view('notificaciones.show', compact('notificacion'));
    }

    public function edit(Notificacion $notificacion): View
    {
        if ($notificacion->estado !== 'borrador') abort(403, 'Solo se pueden editar borradores.');
        return view('notificaciones.edit', compact('notificacion'));
    }

    public function update(Request $request, Notificacion $notificacion): RedirectResponse
    {
        if ($notificacion->estado !== 'borrador') abort(403);
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'cuerpo' => 'required|string',
        ]);
        $notificacion->update($data);
        return redirect()->route('notificaciones.show', $notificacion)->with('success', 'Notificación actualizada.');
    }

    public function destroy(Notificacion $notificacion): RedirectResponse
    {
        if ($notificacion->estado !== 'borrador') abort(403, 'Solo se pueden eliminar borradores.');
        $notificacion->delete();
        return redirect()->route('notificaciones.index')->with('success', 'Notificación eliminada.');
    }

    /** Envía la notificación a los destinatarios §85 */
    public function enviar(Notificacion $notificacion): RedirectResponse
    {
        if ($notificacion->estado !== 'borrador') {
            return back()->with('error', 'Esta notificación ya fue enviada.');
        }

        $this->notifService->enviar($notificacion);

        $this->audit->log(modulo: 'comunicacion', accion: 'send', model: Notificacion::class,
            modelId: $notificacion->id, descripcion: "Notificación enviada: {$notificacion->titulo}");

        return redirect()->route('notificaciones.index')->with('success', 'Notificación enviada.');
    }
}

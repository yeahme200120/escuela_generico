<?php namespace App\Services\Comunicacion;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificacionService {
    public function enviarMulticanal($titulo, $mensaje, $destinatarios = [], $canales = ['email', 'interna']) {
        $notif = Notificacion::create([
            'titulo' => $titulo,
            'cuerpo' => $mensaje,
            'tipo' => 'info',
            'estado' => 'borrador'
        ]);
        
        if (in_array('interna', $canales)) {
            foreach ($destinatarios as $userId) {
                // El pivot usa 'leida' y 'leida_at'
                $notif->usuarios()->attach($userId, ['leida' => false, 'leida_at' => null]);
            }
        }
        
        if (in_array('email', $canales)) {
            foreach ($destinatarios as $userId) {
                $user = User::find($userId);
                if ($user && $user->email) {
                    try {
                        Mail::to($user->email)->queue(new \App\Mail\NotificacionMail($titulo, $mensaje));
                    } catch (\Exception $e) {
                        \Log::error('Error enviando email: ' . $e->getMessage());
                    }
                }
            }
        }
        
        return $notif;
    }
    
    public function marcarLeida($notifId, $userId) {
        return \DB::table('notificacion_usuario')
            ->where('notificacion_id', $notifId)
            ->where('user_id', $userId)
            ->update(['leida' => true, 'fecha_lectura' => now()]);
    }
    
    public function obtenerNoLeidas($userId) {
        return User::find($userId)->notificaciones()
            ->wherePivot('leida', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}

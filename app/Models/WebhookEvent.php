<?php
namespace App\Models;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookEvent extends Model {
    use HasUuid;
    protected $table = 'webhook_events';
    protected $fillable = ['organizacion_id','evento','url','metodo','payload','headers_respuesta','codigo_respuesta','respuesta_body','estado','intentos','proximo_intento','idempotency_key','firma','request_id'];
    protected $casts = ['payload'=>'array','headers_respuesta'=>'array','proximo_intento'=>'datetime','created_at'=>'datetime','updated_at'=>'datetime'];
    public function organizacion(): BelongsTo { return $this->belongsTo(Organizacion::class); }
    public function scopePendientes($q) { return $q->whereIn('estado',['pendiente','reintentando']); }
    public function scopeFallidos($q) { return $q->where('estado','fallido'); }
    public function puedReintentar(): bool { return $this->intentos < 5 && $this->estado !== 'enviado'; }
}

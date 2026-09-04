<?php
namespace App\Models;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Notificacion extends Model {
    use HasUuid;
    protected $fillable = ['organizacion_id','remitente_id','titulo','cuerpo','canal','tipo','destinatarios_config','total_enviados','total_fallidos','estado','enviado_at'];
    protected $casts = ['destinatarios_config'=>'array','enviado_at'=>'datetime'];
    public function remitente(): BelongsTo { return $this->belongsTo(User::class,'remitente_id'); }
    public function usuarios(): BelongsToMany { return $this->belongsToMany(User::class,'notificacion_usuario')->withPivot(['leida','leida_at'])->withTimestamps(); }
    public function scopeNoLeidas($q, int $userId) { return $q->whereHas('usuarios', fn($u)=>$u->where('user_id',$userId)->where('leida',false)); }
}

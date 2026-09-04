<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PythonJob extends Model {
    protected $table = 'python_jobs';
    protected $fillable = ['job_id','organizacion_id','usuario_id','tipo','payload','resultado','estado','archivo_resultado','error','progreso','iniciado_at','completado_at'];
    protected $casts = ['payload'=>'array','resultado'=>'array','iniciado_at'=>'datetime','completado_at'=>'datetime','created_at'=>'datetime','updated_at'=>'datetime'];
    public function organizacion(): BelongsTo { return $this->belongsTo(Organizacion::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class,'usuario_id'); }
    public function scopePendientes($q) { return $q->whereIn('estado',['pendiente','procesando']); }
    public function estaCompletado(): bool { return $this->estado === 'completado'; }
    public function estaFallido(): bool { return $this->estado === 'fallido'; }
}

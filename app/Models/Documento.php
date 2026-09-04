<?php
namespace App\Models;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Documento extends Model {
    use HasUuid, Auditable, SoftDeletes;
    protected $fillable = ['alumno_id','tipo_documento_id','sede_id','folio','version','archivo','hash_archivo','estado','generado_por','autorizado_por','generado_at','autorizado_at'];
    protected $casts = ['generado_at'=>'datetime','autorizado_at'=>'datetime'];
    public function alumno(): BelongsTo { return $this->belongsTo(Alumno::class); }
    public function tipoDocumento(): BelongsTo { return $this->belongsTo(TipoDocumento::class); }
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function generadoPor(): BelongsTo { return $this->belongsTo(User::class,'generado_por'); }
    public function autorizadoPor(): BelongsTo { return $this->belongsTo(User::class,'autorizado_por'); }
    public function scopePorEstado($q,$e) { return $q->where('estado',$e); }
    public function getAuditDescription(): string { return "Documento: {$this->tipoDocumento?->nombre} folio:{$this->folio} alumno#{$this->alumno_id}"; }
}

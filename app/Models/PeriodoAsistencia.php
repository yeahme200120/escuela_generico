<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodoAsistencia extends Model {
    protected $table = 'periodos_asistencia';
    protected $fillable = ['sede_id','ciclo_escolar_id','nombre','fecha_inicio','fecha_fin','activo'];
    protected $casts = ['fecha_inicio'=>'date','fecha_fin'=>'date','activo'=>'boolean'];
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function cicloEscolar(): BelongsTo { return $this->belongsTo(CicloEscolar::class); }
    public function scopeActivos($q) { return $q->where('activo',true); }
}

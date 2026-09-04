<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsistenciaPersonal extends Model {
    public $timestamps = false;
    protected $table = 'asistencia_personal';
    protected $fillable = ['empleado_id','sede_id','fecha','estado','hora_entrada','hora_salida','latitud','longitud','precision_metros','fuente_ubicacion','registrado_por'];
    protected $casts = ['fecha'=>'date','latitud'=>'decimal:8','longitud'=>'decimal:8','precision_metros'=>'decimal:2','created_at'=>'datetime'];
    public function empleado(): BelongsTo { return $this->belongsTo(Empleado::class); }
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function registradoPor(): BelongsTo { return $this->belongsTo(User::class,'registrado_por'); }
}

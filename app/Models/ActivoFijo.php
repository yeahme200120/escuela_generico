<?php
namespace App\Models;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ActivoFijo extends Model {
    use Auditable, SoftDeletes;
    protected $table = 'activos_fijos';
    protected $fillable = ['sede_id','edificio_id','aula_id','codigo','nombre','categoria','numero_serie','valor','fecha_adquisicion','estado','responsable_id','activo'];
    protected $casts = ['valor'=>'decimal:2','fecha_adquisicion'=>'date','activo'=>'boolean'];
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function edificio(): BelongsTo { return $this->belongsTo(Edificio::class); }
    public function aula(): BelongsTo { return $this->belongsTo(Aula::class); }
    public function responsable(): BelongsTo { return $this->belongsTo(User::class,'responsable_id'); }
    public function scopeActivos($q) { return $q->where('activo',true); }
}

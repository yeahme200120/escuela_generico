<?php
namespace App\Models;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventario extends Model {
    use Auditable, SoftDeletes;
    protected $fillable = ['sede_id','categoria_id','codigo','nombre','descripcion','unidad_medida','stock_actual','stock_minimo','precio_unitario','activo'];
    protected $casts = ['precio_unitario'=>'decimal:2','activo'=>'boolean'];
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function categoria(): BelongsTo { return $this->belongsTo(CategoriaInventario::class,'categoria_id'); }
    public function movimientos(): HasMany { return $this->hasMany(MovimientoInventario::class,'inventario_id'); }
    public function scopeActivos($q) { return $q->where('activo',true); }
    public function necesitaReposicion(): bool { return $this->stock_actual <= $this->stock_minimo; }
}

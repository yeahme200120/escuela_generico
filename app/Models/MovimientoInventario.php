<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MovimientoInventario extends Model {
    public $timestamps = false;
    protected $table = 'movimientos_inventario';
    protected $fillable = ['inventario_id','sede_id','tipo','cantidad','stock_anterior','stock_posterior','referencia','motivo','usuario_id'];
    protected $casts = ['created_at'=>'datetime'];
    public function inventario(): BelongsTo { return $this->belongsTo(Inventario::class); }
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class,'usuario_id'); }
}

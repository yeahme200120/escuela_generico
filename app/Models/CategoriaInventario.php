<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class CategoriaInventario extends Model {
    protected $table = 'categorias_inventario';
    protected $fillable = ['organizacion_id','nombre','tipo','activa'];
    protected $casts = ['activa'=>'boolean'];
    public function items(): HasMany { return $this->hasMany(Inventario::class,'categoria_id'); }
}

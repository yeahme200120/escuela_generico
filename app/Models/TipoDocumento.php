<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TipoDocumento extends Model {
    protected $table = 'tipos_documento';
    protected $fillable = ['organizacion_id','nombre','clave','categoria','requiere_autorizacion','activo'];
    protected $casts = ['requiere_autorizacion'=>'boolean','activo'=>'boolean'];
    public function documentos(): HasMany { return $this->hasMany(Documento::class); }
    public function scopeActivos($q) { return $q->where('activo',true); }
}

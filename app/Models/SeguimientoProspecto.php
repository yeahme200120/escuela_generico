<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SeguimientoProspecto extends Model {
    public $timestamps = false;
    protected $table = 'seguimientos_prospecto';
    protected $fillable = ['prospecto_id','usuario_id','tipo','descripcion'];
    protected $casts = ['created_at'=>'datetime'];
    public function prospecto(): BelongsTo { return $this->belongsTo(Prospecto::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class,'usuario_id'); }
}

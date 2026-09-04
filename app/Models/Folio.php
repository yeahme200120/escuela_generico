<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Folio extends Model {
    protected $fillable = ['sede_id','tipo_documento_id','anio','ultimo_numero'];
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function tipoDocumento(): BelongsTo { return $this->belongsTo(TipoDocumento::class); }
}

<?php
namespace App\Models;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CalendarioEscolar extends Model {
    use Auditable;
    protected $table = 'calendario_escolar';
    protected $fillable = ['sede_id','ciclo_escolar_id','titulo','descripcion','tipo','fecha_inicio','fecha_fin','todo_el_dia','color'];
    protected $casts = ['fecha_inicio'=>'date','fecha_fin'=>'date','todo_el_dia'=>'boolean'];
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function cicloEscolar(): BelongsTo { return $this->belongsTo(CicloEscolar::class); }
    public function scopeDelPeriodo($q, $inicio, $fin) { return $q->where('fecha_inicio','>=',$inicio)->where('fecha_inicio','<=',$fin); }
}

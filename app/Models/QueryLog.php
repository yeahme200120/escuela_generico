<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueryLog extends Model
{
    protected $table = 'query_logs';

    public $timestamps = false;

    protected $fillable = [
        'request_id', 'audit_log_uuid', 'session_uuid',
        'user_id', 'organizacion_id', 'sede_id',
        'connection', 'sql', 'sql_raw', 'bindings', 'tiempo_ms',
        'filas_afectadas', 'tipo', 'tabla_principal', 'origen',
        'en_transaccion', 'es_lenta',
        'ip_address', 'latitud', 'longitud', 'precision_metros',
        'created_at',
    ];

    protected $casts = [
        'created_at'      => 'datetime',
        'bindings'        => 'array',
        'tiempo_ms'       => 'decimal:3',
        'en_transaccion'  => 'boolean',
        'es_lenta'        => 'boolean',
        'latitud'         => 'decimal:8',
        'longitud'        => 'decimal:8',
        'precision_metros'=> 'decimal:2',
    ];

    public function user(): BelongsTo         { return $this->belongsTo(User::class);         }
    public function organizacion(): BelongsTo { return $this->belongsTo(Organizacion::class); }
    public function sede(): BelongsTo         { return $this->belongsTo(Sede::class);          }

    public function scopeLentas($q)            { return $q->where('es_lenta', true);      }
    public function scopeDeRequest($q, $reqId) { return $q->where('request_id', $reqId);  }
    public function scopeDeSession($q, $uuid)  { return $q->where('session_uuid', $uuid); }
    public function scopeDeTipo($q, $tipo)     { return $q->where('tipo', $tipo);         }
}

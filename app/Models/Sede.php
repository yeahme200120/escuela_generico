<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sede extends Model
{
    use HasUuid, Auditable, SoftDeletes;

    protected $table = 'sedes';

    protected $fillable = [
        'uuid', 'organizacion_id', 'escuela_id', 'nombre', 'clave',
        'email', 'telefono', 'direccion', 'ciudad', 'estado', 'pais',
        'codigo_postal', 'latitud', 'longitud', 'radio_geocerca_metros',
        'geocerca_activa', 'calificacion_minima', 'calificacion_maxima',
        'tolerancia_retardo_minutos', 'zona_horaria', 'moneda',
        'activa', 'configuracion',
    ];

    protected $casts = [
        'activa'                   => 'boolean',
        'geocerca_activa'          => 'boolean',
        'latitud'                  => 'decimal:8',
        'longitud'                 => 'decimal:8',
        'calificacion_minima'      => 'decimal:2',
        'calificacion_maxima'      => 'decimal:2',
        'configuracion'            => 'array',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------
    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class, 'escuela_id');
    }

    public function edificios(): HasMany
    {
        return $this->hasMany(Edificio::class, 'sede_id');
    }

    public function aulas(): HasMany
    {
        return $this->hasMany(Aula::class, 'sede_id');
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'sede_id');
    }

    public function userSedes(): HasMany
    {
        return $this->hasMany(UserSede::class, 'sede_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    public function getAuditDescription(): string
    {
        return "Sede: {$this->nombre}";
    }

    /**
     * Verifica si unas coordenadas están dentro de la geocerca de la sede.
     */
    public function estaDentroDeGeocerca(float $lat, float $lon): bool
    {
        if (!$this->geocerca_activa || !$this->latitud || !$this->longitud) {
            return true; // si la geocerca no está activa, siempre pasa
        }
        $distancia = $this->calcularDistanciaMetros($lat, $lon, $this->latitud, $this->longitud);
        return $distancia <= $this->radio_geocerca_metros;
    }

    /**
     * Fórmula de Haversine para distancia entre dos puntos en metros.
     */
    public function calcularDistanciaMetros(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $r     = 6371000; // radio de la Tierra en metros
        $dLat  = deg2rad($lat2 - $lat1);
        $dLon  = deg2rad($lon2 - $lon1);
        $a     = sin($dLat / 2) ** 2
               + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $r * 2 * asin(sqrt($a));
    }
}

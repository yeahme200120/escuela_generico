<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sistema de Auditoría y Trazabilidad
    |--------------------------------------------------------------------------
    */

    // Activar/desactivar todo el sistema de auditoría
    'enabled' => env('AUDIT_ENABLED', true),

    // Almacenar las queries SQL ejecutadas en cada request
    'store_queries' => env('AUDIT_STORE_QUERIES', true),

    // Umbral de query lenta (ms) — queries que superen esto se marcan como es_lenta=true
    'slow_query_ms' => env('AUDIT_SLOW_QUERY_MS', 500),

    // Nombre del header de Request ID
    'request_id_header' => env('REQUEST_ID_HEADER', 'X-Request-ID'),

    // Retención de audit_logs en días (0 = sin límite)
    'retention_days' => env('AUDIT_RETENTION_DAYS', 0),

    // Retención de query_logs en días (se limpian más frecuentemente)
    'query_log_retention_days' => env('AUDIT_QUERY_LOG_RETENTION_DAYS', 30),

    // Retención de access_logs en días
    'access_log_retention_days' => env('AUDIT_ACCESS_LOG_RETENTION_DAYS', 365),

    // Módulos excluidos de auditoría de modelos automática
    'exclude_models' => [
        \App\Models\UserSession::class,
        \App\Models\AccessLog::class,
        \App\Models\AuditLog::class,
        \App\Models\QueryLog::class,
    ],

    // Máximo de intentos de login fallidos antes de bloqueo temporal
    'max_login_attempts'    => env('AUDIT_MAX_LOGIN_ATTEMPTS', 5),

    // Duración del bloqueo temporal en minutos
    'lockout_minutes'       => env('AUDIT_LOCKOUT_MINUTES', 15),

    // Velocidad máxima en km/h para considerar un viaje posible
    'max_travel_speed_kmh'  => env('AUDIT_MAX_TRAVEL_SPEED_KMH', 900),

    // Si la geo es requerida para operaciones críticas (actualmente informativo)
    'geo_required'          => env('AUDIT_GEO_REQUIRED', false),

    /*
    |--------------------------------------------------------------------------
    | Geolocalización
    |--------------------------------------------------------------------------
    */
    'geo' => [
        // Headers que el frontend envía con la ubicación
        'headers' => [
            'latitude'  => 'X-Geo-Latitude',
            'longitude' => 'X-Geo-Longitude',
            'accuracy'  => 'X-Geo-Accuracy',
            'altitude'  => 'X-Geo-Altitude',
            'speed'     => 'X-Geo-Speed',
            'source'    => 'X-Geo-Source',
        ],
        // Header con fingerprint del dispositivo
        'device_id_header'   => 'X-Device-ID',
        'device_info_header' => 'X-Device-Info',
        'session_id_header'  => 'X-Session-ID',
    ],

];

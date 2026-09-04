<?php

namespace App\Support;

/**
 * GeoContext — Objeto de valor inmutable que representa la ubicación geográfica
 * capturada en el request actual.
 *
 * Se puebla desde:
 *  1. Header X-Geo-Latitude / X-Geo-Longitude / X-Geo-Accuracy enviados por el frontend
 *     (Alpine.js/GeoCapture antes del submit de login u operación crítica).
 *  2. El middleware GeoTrace que lo inyecta en el contenedor por request.
 *
 * Nunca bloquea la operación aunque la geo no esté disponible.
 */
final class GeoContext
{
    public function __construct(
        public readonly ?float  $latitud          = null,
        public readonly ?float  $longitud         = null,
        public readonly ?float  $precisionMetros  = null,
        public readonly ?float  $altitud          = null,
        public readonly ?float  $velocidad        = null,
        public readonly ?string $fuente           = null,   // gps, network, ip, denied, unavailable
        public readonly ?string $ipAddress        = null,
        public readonly ?string $deviceId         = null,
        public readonly ?string $deviceType       = null,
        public readonly ?string $sistemaOperativo = null,
        public readonly ?string $navegador        = null,
        public readonly ?string $userAgent        = null,
        public readonly ?int    $pantallaAncho    = null,
        public readonly ?int    $pantallaAlto     = null,
        public readonly ?string $zonaHoraria      = null,
        public readonly ?string $idioma           = null,
        public readonly ?float  $pixelRatio       = null,
        public readonly bool    $esTouch          = false,
    ) {}

    public function tieneUbicacion(): bool
    {
        return $this->latitud !== null && $this->longitud !== null;
    }

    public function toArray(): array
    {
        return [
            'latitud'          => $this->latitud,
            'longitud'         => $this->longitud,
            'precision_metros' => $this->precisionMetros,
            'altitud'          => $this->altitud,
            'velocidad'        => $this->velocidad,
            'fuente_ubicacion' => $this->fuente,
            'ip_address'       => $this->ipAddress,
            'device_id'        => $this->deviceId,
            'device_type'      => $this->deviceType,
            'sistema_operativo'=> $this->sistemaOperativo,
            'navegador'        => $this->navegador,
            'user_agent'       => $this->userAgent,
        ];
    }

    /**
     * Construye un GeoContext a partir del objeto Request de Laravel.
     * Lee headers X-Geo-* inyectados por el frontend + User-Agent.
     */
    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        // Parsear User-Agent básico
        $ua   = $request->userAgent() ?? '';
        [$so, $browser] = self::parseUserAgent($ua);

        // Device-info del header X-Device-Info (JSON enviado por DeviceInfo.get() en JS)
        $deviceInfo = [];
        if ($raw = $request->header('X-Device-Info')) {
            $deviceInfo = json_decode($raw, true) ?? [];
        }

        return new self(
            latitud:          self::floatHeader($request, 'X-Geo-Latitude'),
            longitud:         self::floatHeader($request, 'X-Geo-Longitude'),
            precisionMetros:  self::floatHeader($request, 'X-Geo-Accuracy'),
            altitud:          self::floatHeader($request, 'X-Geo-Altitude'),
            velocidad:        self::floatHeader($request, 'X-Geo-Speed'),
            fuente:           $request->header('X-Geo-Source') ?: 'unknown',
            ipAddress:        $request->ip(),
            deviceId:         $request->header('X-Device-ID'),
            deviceType:       self::inferDeviceType($ua, $deviceInfo),
            sistemaOperativo: $so,
            navegador:        $browser,
            userAgent:        $ua,
            pantallaAncho:    isset($deviceInfo['screen_width'])  ? (int)$deviceInfo['screen_width']  : null,
            pantallaAlto:     isset($deviceInfo['screen_height']) ? (int)$deviceInfo['screen_height'] : null,
            zonaHoraria:      $deviceInfo['timezone']    ?? null,
            idioma:           $deviceInfo['language']    ?? $request->header('Accept-Language'),
            pixelRatio:       isset($deviceInfo['pixel_ratio']) ? (float)$deviceInfo['pixel_ratio'] : null,
            esTouch:          (bool)($deviceInfo['touch'] ?? false),
        );
    }

    // ----------------------------------------------------------------
    // Helpers privados
    // ----------------------------------------------------------------

    private static function floatHeader(\Illuminate\Http\Request $request, string $header): ?float
    {
        $val = $request->header($header);
        return ($val !== null && $val !== '') ? (float)$val : null;
    }

    private static function parseUserAgent(string $ua): array
    {
        // Sistema operativo
        $so = match(true) {
            str_contains($ua, 'Windows NT') => 'Windows',
            str_contains($ua, 'Mac OS X')   => 'macOS',
            str_contains($ua, 'Android')    => 'Android',
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Linux')      => 'Linux',
            default                         => 'Unknown',
        };

        // Navegador
        $browser = match(true) {
            str_contains($ua, 'Edg/')     => 'Edge',
            str_contains($ua, 'OPR/')     => 'Opera',
            str_contains($ua, 'Chrome/')  => 'Chrome',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Safari/') && !str_contains($ua, 'Chrome') => 'Safari',
            default                       => 'Unknown',
        };

        return [$so, $browser];
    }

    private static function inferDeviceType(string $ua, array $deviceInfo): string
    {
        if (!empty($deviceInfo['touch']) && str_contains($ua, 'Mobile')) return 'mobile';
        if (str_contains($ua, 'iPad') || str_contains($ua, 'Tablet'))   return 'tablet';
        if (str_contains($ua, 'Mobile') || str_contains($ua, 'Android'))return 'mobile';
        return 'desktop';
    }
}

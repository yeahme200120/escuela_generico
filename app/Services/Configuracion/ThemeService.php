<?php

namespace App\Services\Configuracion;

use App\Models\SystemSetting;
use App\Services\Auditoria\AuditService;
use Illuminate\Support\Facades\Cache;

/**
 * ThemeService
 *
 * Lee y persiste la configuración visual de la organización.
 * Los colores se guardan en system_settings (grupo = 'theme').
 * Se cachean por organización para no golpear la BD en cada request.
 */
class ThemeService
{
    // Claves del tema que se pueden editar
    public const KEYS = [
        'theme.primary', 'theme.secondary', 'theme.success',
        'theme.warning',  'theme.danger',   'theme.info',
        'theme.background','theme.surface', 'theme.text',
        'theme.logo',     'theme.favicon',
    ];

    // Colores (validamos que sean hex al guardar)
    public const COLOR_KEYS = [
        'theme.primary','theme.secondary','theme.success',
        'theme.warning','theme.danger','theme.info',
        'theme.background','theme.surface','theme.text',
    ];

    public function __construct(
        private readonly AuditService $audit,
    ) {}

    /**
     * Retorna el tema efectivo de una organización.
     * Merge de defaults de config/theme.php + overrides en system_settings.
     */
    public function getForOrganizacion(?int $orgId): array
    {
        $defaults = $this->defaults();

        if (!$orgId) return $defaults;

        $stored = Cache::remember(
            "theme_org_{$orgId}",
            now()->addMinutes(30),
            fn () => SystemSetting::where('organizacion_id', $orgId)
                        ->where('grupo', 'theme')
                        ->pluck('value', 'key')
                        ->toArray()
        );

        return array_merge($defaults, $stored);
    }

    /**
     * Persiste los valores del tema de una organización.
     * Solo guarda claves reconocidas y valida colores hex.
     */
    public function saveForOrganizacion(int $orgId, array $data, int $userId): void
    {
        $saved = [];

        foreach (self::KEYS as $key) {
            $field = str_replace('theme.', '', $key); // primary, secondary, etc.

            if (!array_key_exists($field, $data)) continue;

            $value = trim($data[$field]);

            // Validar hex para colores
            if (in_array($key, self::COLOR_KEYS)) {
                if (!preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) continue;
            }

            SystemSetting::updateOrCreate(
                ['organizacion_id' => $orgId, 'key' => $key],
                ['value' => $value, 'type' => 'color', 'grupo' => 'theme', 'updated_by' => $userId]
            );

            $saved[$key] = $value;
        }

        // Invalidar caché
        Cache::forget("theme_org_{$orgId}");

        // Auditoría
        $this->audit->log(
            modulo:      'configuracion',
            accion:      'update',
            descripcion: 'Actualización de tema visual',
            after:       $saved,
            resultado:   'success',
        );
    }

    /**
     * Defaults desde config/theme.php.
     */
    public function defaults(): array
    {
        return [
            'theme.primary'    => config('theme.primary'),
            'theme.secondary'  => config('theme.secondary'),
            'theme.success'    => config('theme.success'),
            'theme.warning'    => config('theme.warning'),
            'theme.danger'     => config('theme.danger'),
            'theme.info'       => config('theme.info'),
            'theme.background' => config('theme.background'),
            'theme.surface'    => config('theme.surface'),
            'theme.text'       => config('theme.text'),
            'theme.logo'       => config('theme.logo'),
            'theme.favicon'    => config('theme.favicon'),
        ];
    }
}

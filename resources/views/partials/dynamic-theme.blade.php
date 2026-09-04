{{--
    Tema dinámico — inyecta variables CSS --se-* desde system_settings de la organización.
    Si no hay configuración guardada, usa los defaults del diseño.
    Solo se consulta la BD cuando hay un usuario autenticado con organización.
--}}
@php
    use App\Models\SystemSetting;

    $orgId = auth()->user()?->organizacion_id;

    $defaults = [
        'theme.primary'    => '#2563eb',
        'theme.secondary'  => '#64748b',
        'theme.success'    => '#16a34a',
        'theme.warning'    => '#d97706',
        'theme.danger'     => '#dc2626',
        'theme.info'       => '#0891b2',
        'theme.background' => '#f1f5f9',
        'theme.surface'    => '#ffffff',
        'theme.text'       => '#0f172a',
    ];

    $settings = $orgId
        ? SystemSetting::where('organizacion_id', $orgId)
                        ->where('grupo', 'theme')
                        ->pluck('value', 'key')
                        ->toArray()
        : [];

    $t = array_merge($defaults, $settings);
@endphp
<style>
    :root {
        --se-primary:    {{ $t['theme.primary']    }};
        --se-secondary:  {{ $t['theme.secondary']  }};
        --se-success:    {{ $t['theme.success']    }};
        --se-warning:    {{ $t['theme.warning']    }};
        --se-danger:     {{ $t['theme.danger']     }};
        --se-info:       {{ $t['theme.info']       }};
        --se-bg:         {{ $t['theme.background'] }};
        --se-surface:    {{ $t['theme.surface']    }};
        --se-text:       {{ $t['theme.text']       }};
        /* Bootstrap overrides dinámicos */
        --bs-primary:    {{ $t['theme.primary']    }};
        --bs-body-bg:    {{ $t['theme.background'] }};
        --bs-body-color: {{ $t['theme.text']       }};
    }
    .btn-primary, .bg-primary { background-color: {{ $t['theme.primary'] }} !important; border-color: {{ $t['theme.primary'] }} !important; }
    .text-primary { color: {{ $t['theme.primary'] }} !important; }
    .border-primary { border-color: {{ $t['theme.primary'] }} !important; }
</style>

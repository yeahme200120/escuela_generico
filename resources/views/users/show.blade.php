<x-layouts.app page-title="{{ $user->nombre_completo }}">
<x-ui.page-header title="{{ $user->nombre_completo }}"
    :items="[['label'=>'Usuarios','url'=>route('users.index')],['label'=>$user->nombre_completo]]">
    <x-slot name="actions">
        @can('usuarios.editar')
        <a href="{{ route('users.edit',$user) }}" class="btn btn-sm btn-outline-primary">Editar</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<div class="row g-3">
    <div class="col-md-4">
        <x-ui.card title="Datos">
            <div class="text-center mb-3">
                <x-ui.avatar :name="$user->nombre_completo" size="xl" />
                <div class="fw-semibold mt-2">{{ $user->nombre_completo }}</div>
                <div class="text-muted" style="font-size:.875rem">{{ $user->email }}</div>
            </div>
            <dl class="row mb-0" style="font-size:.875rem">
                <dt class="col-5 text-muted">Username</dt><dd class="col-7">{{ $user->username ?? '—' }}</dd>
                <dt class="col-5 text-muted">Teléfono</dt><dd class="col-7">{{ $user->telefono ?? '—' }}</dd>
                <dt class="col-5 text-muted">Estado</dt>
                <dd class="col-7"><x-ui.badge :type="$user->activo?'success':'secondary'">{{ $user->activo?'Activo':'Inactivo' }}</x-ui.badge></dd>
                <dt class="col-5 text-muted">Último acceso</dt><dd class="col-7" style="font-size:.8rem">{{ $user->ultimo_acceso_at?->format('d/m/Y H:i') ?? 'Nunca' }}</dd>
                <dt class="col-5 text-muted">2FA</dt><dd class="col-7">{{ $user->two_factor_enabled?'✅ Activo':'❌' }}</dd>
            </dl>
        </x-ui.card>
        <x-ui.card title="Roles" class="mt-3">
            @foreach($user->roles as $r)
            <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:.875rem">
                <span>{{ $r->nombre }}</span>
                <x-ui.badge type="secondary" small>nv.{{ $r->nivel }}</x-ui.badge>
            </div>
            @endforeach
        </x-ui.card>
        <x-ui.card title="Sedes" class="mt-3">
            @foreach($user->sedes as $s)
            <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:.875rem">
                <span>{{ $s->nombre }}</span>
                @if($s->pivot->es_principal)
                <x-ui.badge type="primary" small>Principal</x-ui.badge>
                @endif
            </div>
            @endforeach
        </x-ui.card>
    </div>
    <div class="col-md-8">
        <x-ui.card title="Sesiones activas" :flush="true">
            <div class="table-responsive">
                <table class="table table-se table-sm mb-0">
                    <thead><tr><th>Dispositivo</th><th>IP</th><th>Ubicación</th><th>Último acceso</th><th></th></tr></thead>
                    <tbody>
                        @forelse($user->userSessions->where('active',true) as $s)
                        <tr>
                            <td style="font-size:.8rem">
                                <div>{{ $s->sistema_operativo }} / {{ $s->navegador }}</div>
                                <small class="text-muted">{{ $s->device_type }}</small>
                            </td>
                            <td style="font-size:.8rem">{{ $s->ip_address }}</td>
                            <td style="font-size:.78rem">
                                @if($s->latitud)
                                <span class="text-success">{{ number_format($s->latitud,4) }},{{ number_format($s->longitud,4) }}</span>
                                @else —
                                @endif
                            </td>
                            <td style="font-size:.78rem">{{ $s->last_seen_at?->diffForHumans() }}</td>
                            <td>
                                @if($s->uuid !== session('user_session_uuid'))
                                <form method="POST" action="{{ route('auditoria.sesiones.destroy',$s->uuid) }}" data-confirm="¿Revocar esta sesión?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Revocar</button>
                                </form>
                                @else
                                <span class="badge text-bg-primary" style="font-size:.65rem">Actual</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3" style="font-size:.875rem">Sin sesiones activas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
</x-layouts.app>

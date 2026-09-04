<x-layouts.app page-title="Auditoría — Sesiones">

<x-ui.page-header title="Sesiones activas"
    subtitle="Dispositivos conectados actualmente. Puedes revocar sesiones sospechosas.">
    <x-slot name="actions">
        <span class="badge text-bg-success">{{ $total_activas }} activas ahora</span>
    </x-slot>
</x-ui.page-header>

{{-- Filtros --}}
<x-ui.filter-bar :action="route('auditoria.sesiones')">
    <x-slot name="fields">
        <div class="col-12 col-sm-4 col-md-3 col-lg-3">
            <label class="form-label form-label-sm mb-1">Búsqueda</label>
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="usuario, IP…" value="{{ request('q') }}">
        </div>
        <div class="col-6 col-md-2 col-lg-2">
            <label class="form-label form-label-sm mb-1">Dispositivo</label>
            <select name="device_type" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="desktop" {{ request('device_type')==='desktop' ?'selected':'' }}>Desktop</option>
                <option value="mobile"  {{ request('device_type')==='mobile'  ?'selected':'' }}>Móvil</option>
                <option value="tablet"  {{ request('device_type')==='tablet'  ?'selected':'' }}>Tablet</option>
            </select>
        </div>
        <div class="col-auto d-flex align-items-end">
            <div class="form-check mb-0">
                <input type="checkbox" class="form-check-input" name="solo_activas" value="1"
                       id="chk_activas" {{ request('solo_activas') ? 'checked':'' }}>
                <label class="form-check-label" for="chk_activas" style="font-size:.8rem">Solo activas</label>
            </div>
        </div>
    </x-slot>
</x-ui.filter-bar>

<x-ui.card :flush="true">
    @if($sesiones->isEmpty())
        <x-ui.empty-state message="No hay sesiones con los filtros aplicados." />
    @else
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Usuario</th>
                    <th>Dispositivo</th>
                    <th>IP</th>
                    <th>Ubicación (login)</th>
                    <th>Inicio</th>
                    <th>Última actividad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sesiones as $sesion)
                @php $esMia = $sesion->uuid === $mi_session; @endphp
                <tr>
                    <td>
                        @if($sesion->active)
                            <span class="badge text-bg-success">Activa</span>
                        @else
                            <span class="badge text-bg-secondary">Revocada</span>
                        @endif
                        @if($esMia)
                            <span class="badge text-bg-primary ms-1" style="font-size:.65rem">Esta sesión</span>
                        @endif
                    </td>
                    <td style="font-size:.8rem">
                        <span class="d-block fw-medium">{{ $sesion->user?->nombre_completo ?? '—' }}</span>
                        <span class="text-muted">{{ $sesion->user?->email }}</span>
                    </td>
                    <td style="font-size:.78rem">
                        <span class="d-block">{{ $sesion->sistema_operativo ?? '?' }} / {{ $sesion->navegador ?? '?' }}</span>
                        @if($sesion->device_type)
                            <span class="badge text-bg-secondary" style="font-size:.65rem">{{ $sesion->device_type }}</span>
                        @endif
                        @if($sesion->zona_horaria)
                            <span class="text-muted d-block" style="font-size:.7rem">{{ $sesion->zona_horaria }}</span>
                        @endif
                    </td>
                    <td style="font-size:.8rem" class="text-nowrap">
                        {{ $sesion->ip_address ?? '—' }}
                    </td>
                    <td style="font-size:.78rem">
                        @if($sesion->latitud)
                            <span class="geo-indicator available">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>
                                {{ number_format($sesion->latitud,4) }}, {{ number_format($sesion->longitud,4) }}
                            </span>
                            @if($sesion->precision_metros)
                                <span class="text-muted d-block" style="font-size:.7rem">±{{ round($sesion->precision_metros) }}m</span>
                            @endif
                        @else
                            <span class="geo-indicator unavailable">Sin geo</span>
                        @endif
                    </td>
                    <td style="font-size:.8rem" class="text-nowrap">
                        {{ $sesion->first_seen_at?->format('d/m/Y H:i') ?? '—' }}
                    </td>
                    <td style="font-size:.8rem" class="text-nowrap">
                        {{ $sesion->last_seen_at?->diffForHumans() ?? '—' }}
                    </td>
                    <td>
                        @if($sesion->active && !$esMia)
                        <form method="POST"
                              action="{{ route('auditoria.sesiones.destroy', $sesion->uuid) }}"
                              data-confirm="¿Revocar esta sesión? El usuario perderá el acceso inmediatamente.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Revocar
                            </button>
                        </form>
                        @elseif($esMia)
                            <span class="text-muted" style="font-size:.75rem">Sesión actual</span>
                        @else
                            <span class="text-muted" style="font-size:.75rem">
                                {{ $sesion->revoked_reason ?? 'Revocada' }}
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">
        {{ $sesiones->links() }}
    </div>
    @endif
</x-ui.card>

</x-layouts.app>

<x-layouts.app page-title="Auditoría — Accesos">

<x-ui.page-header title="Log de accesos"
    subtitle="Todos los intentos de login: exitosos, fallidos y bloqueados.">
    <x-slot name="actions">
        @if($total_anomalias > 0)
            <span class="badge text-bg-danger">
                ⚠ {{ $total_anomalias }} anomalías detectadas
            </span>
        @endif
        <span class="badge text-bg-secondary">{{ number_format($logs->total()) }} registros</span>
    </x-slot>
</x-ui.page-header>

{{-- Filtros --}}
<x-ui.filter-bar :action="route('auditoria.accesos')">
    <x-slot name="fields">
        <div class="col-12 col-sm-4 col-md-3 col-lg-2">
            <label class="form-label form-label-sm mb-1">Búsqueda</label>
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="email, IP…" value="{{ request('q') }}">
        </div>
        <div class="col-6 col-md-2 col-lg-1">
            <label class="form-label form-label-sm mb-1">Evento</label>
            <select name="evento" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach(['login','logout','login_failed','session_revoked'] as $ev)
                    <option value="{{ $ev }}" {{ request('evento') === $ev ? 'selected':'' }}>{{ $ev }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2 col-lg-1">
            <label class="form-label form-label-sm mb-1">Resultado</label>
            <select name="resultado" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="success" {{ request('resultado')==='success'?'selected':'' }}>Exitoso</option>
                <option value="failed"  {{ request('resultado')==='failed' ?'selected':'' }}>Fallido</option>
                <option value="blocked" {{ request('resultado')==='blocked'?'selected':'' }}>Bloqueado</option>
            </select>
        </div>
        <div class="col-6 col-md-2 col-lg-2">
            <label class="form-label form-label-sm mb-1">Desde</label>
            <input type="date" name="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
        </div>
        <div class="col-6 col-md-2 col-lg-2">
            <label class="form-label form-label-sm mb-1">Hasta</label>
            <input type="date" name="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
        </div>
        <div class="col-auto d-flex align-items-end">
            <div class="form-check mb-0">
                <input type="checkbox" class="form-check-input" name="solo_anomalias" value="1"
                       id="chk_anomalias" {{ request('solo_anomalias') ? 'checked':'' }}>
                <label class="form-check-label" for="chk_anomalias" style="font-size:.8rem">
                    Solo anomalías
                </label>
            </div>
        </div>
    </x-slot>
</x-ui.filter-bar>

{{-- Tabla --}}
<x-ui.card :flush="true">
    @if($logs->isEmpty())
        <x-ui.empty-state message="No hay registros de acceso con los filtros aplicados." />
    @else
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr>
                    <th>Fecha / hora</th>
                    <th>Evento</th>
                    <th>Email/Usuario</th>
                    <th>IP</th>
                    <th>Dispositivo</th>
                    <th>Ubicación</th>
                    <th>Anomalías</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                @php $hayAnomalia = $log->tieneAnomalias(); @endphp
                <tr class="{{ $hayAnomalia ? 'table-warning' : '' }}">
                    <td class="text-nowrap" style="font-size:.8rem">
                        <span class="d-block">{{ $log->created_at->format('d/m/Y') }}</span>
                        <span class="text-muted">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>
                    <td>
                        <span class="badge-modulo">{{ $log->evento }}</span>
                    </td>
                    <td style="font-size:.8rem">
                        {{ $log->email_intento ?? $log->user?->email ?? '—' }}
                    </td>
                    <td style="font-size:.8rem" class="text-nowrap">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                    <td style="font-size:.78rem">
                        @if($log->sistema_operativo)
                            <span class="d-block">{{ $log->sistema_operativo }}</span>
                        @endif
                        @if($log->navegador)
                            <span class="text-muted">{{ $log->navegador }}</span>
                        @endif
                        @if($log->device_type)
                            <span class="badge text-bg-secondary mt-1" style="font-size:.65rem">{{ $log->device_type }}</span>
                        @endif
                    </td>
                    <td style="font-size:.78rem">
                        @if($log->latitud)
                            <span class="geo-indicator available">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>
                                {{ number_format($log->latitud,4) }},{{ number_format($log->longitud,4) }}
                            </span>
                            @if($log->precision_metros)
                                <span class="d-block text-muted" style="font-size:.7rem">±{{ round($log->precision_metros) }}m · {{ $log->fuente_ubicacion }}</span>
                            @endif
                        @else
                            <span class="geo-indicator unavailable">Sin geo</span>
                        @endif
                    </td>
                    <td style="font-size:.75rem">
                        @if($log->viaje_imposible)
                            <span class="anomalia-badge bg-danger text-white">✈ Viaje imposible</span>
                        @endif
                        @if($log->fuera_de_geocerca)
                            <span class="anomalia-badge bg-warning text-dark">📍 Fuera geocerca</span>
                        @endif
                        @if($log->es_nuevo_dispositivo)
                            <span class="anomalia-badge bg-info text-white">🔑 Nuevo dispositivo</span>
                        @endif
                        @if($log->fuera_de_horario)
                            <span class="anomalia-badge bg-secondary text-white">🕐 Fuera de horario</span>
                        @endif
                        @if(!$hayAnomalia)
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                        $badge = match($log->resultado) {
                            'success' => ['success','Exitoso'],
                            'failed'  => ['danger', 'Fallido'],
                            'blocked' => ['warning','Bloqueado'],
                            default   => ['secondary',$log->resultado],
                        };
                        @endphp
                        <x-ui.badge :type="$badge[0]">{{ $badge[1] }}</x-ui.badge>
                        @if($log->motivo_rechazo)
                            <span class="d-block text-muted mt-1" style="font-size:.7rem">{{ $log->motivo_rechazo }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">
        {{ $logs->links() }}
    </div>
    @endif
</x-ui.card>

</x-layouts.app>

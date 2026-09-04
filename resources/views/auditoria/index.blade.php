<x-layouts.app page-title="Auditoría — Registros">

<x-ui.page-header title="Registros de auditoría"
    subtitle="Historial completo e inmutable de todas las acciones del sistema.">
    <x-slot name="actions">
        <span class="badge text-bg-secondary">{{ number_format($logs->total()) }} registros</span>
    </x-slot>
</x-ui.page-header>

{{-- Filtros --}}
<x-ui.filter-bar :action="route('auditoria.index')">
    <x-slot name="fields">
        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
            <label class="form-label form-label-sm mb-1">Búsqueda</label>
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="usuario, IP, descripción…" value="{{ request('q') }}">
        </div>
        <div class="col-6 col-md-2 col-lg-2">
            <label class="form-label form-label-sm mb-1">Módulo</label>
            <select name="modulo" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach($modulos as $m)
                    <option value="{{ $m }}" {{ request('modulo') === $m ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_',' ',$m)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2 col-lg-1">
            <label class="form-label form-label-sm mb-1">Resultado</label>
            <select name="resultado" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="success"      {{ request('resultado') === 'success'      ? 'selected':'' }}>Exitoso</option>
                <option value="failed"       {{ request('resultado') === 'failed'       ? 'selected':'' }}>Fallido</option>
                <option value="unauthorized" {{ request('resultado') === 'unauthorized' ? 'selected':'' }}>No autorizado</option>
                <option value="error"        {{ request('resultado') === 'error'        ? 'selected':'' }}>Error</option>
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
    </x-slot>
</x-ui.filter-bar>

{{-- Tabla --}}
<x-ui.card :flush="true">
    @if($logs->isEmpty())
        <x-ui.empty-state message="No hay registros de auditoría con los filtros aplicados." />
    @else
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr>
                    <th>Fecha / hora</th>
                    <th>Usuario</th>
                    <th>Módulo / Acción</th>
                    <th>Descripción</th>
                    <th>IP</th>
                    <th>Ubicación</th>
                    <th>Resultado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    {{-- Fecha --}}
                    <td class="text-nowrap" style="font-size:.8rem">
                        <span class="d-block">{{ $log->created_at->format('d/m/Y') }}</span>
                        <span class="text-muted">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>

                    {{-- Usuario --}}
                    <td style="font-size:.8rem">
                        <span class="d-block fw-medium">{{ $log->user_nombre ?? '—' }}</span>
                        <span class="text-muted">{{ $log->user_email ?? '' }}</span>
                        @if($log->user_rol)
                            <span class="badge text-bg-secondary mt-1" style="font-size:.65rem">{{ $log->user_rol }}</span>
                        @endif
                    </td>

                    {{-- Módulo/Acción --}}
                    <td class="text-nowrap" style="font-size:.8rem">
                        <span class="badge-modulo">{{ $log->modulo }}</span>
                        <span class="d-block text-muted mt-1">{{ $log->accion }}</span>
                    </td>

                    {{-- Descripción --}}
                    <td style="font-size:.8rem;max-width:240px">
                        <span class="d-block text-truncate" title="{{ $log->descripcion }}">
                            {{ $log->descripcion ?? '—' }}
                        </span>
                        @if($log->model_descripcion)
                            <span class="text-muted d-block text-truncate" title="{{ $log->model_descripcion }}">
                                {{ $log->model_descripcion }}
                            </span>
                        @endif
                    </td>

                    {{-- IP --}}
                    <td style="font-size:.8rem" class="text-nowrap">
                        {{ $log->ip_address ?? '—' }}
                    </td>

                    {{-- Ubicación geo --}}
                    <td style="font-size:.78rem">
                        @if($log->latitud)
                            <span class="geo-indicator available">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                </svg>
                                {{ number_format($log->latitud,4) }},{{ number_format($log->longitud,4) }}
                            </span>
                            @if($log->precision_metros)
                                <span class="d-block text-muted" style="font-size:.7rem">±{{ round($log->precision_metros) }}m</span>
                            @endif
                        @else
                            <span class="geo-indicator unavailable">Sin geo</span>
                        @endif
                    </td>

                    {{-- Resultado --}}
                    <td>
                        @php
                        $badge = match($log->resultado?->value ?? $log->resultado) {
                            'success'      => ['success', 'Exitoso'],
                            'failed'       => ['danger',  'Fallido'],
                            'unauthorized' => ['warning', 'No autorizado'],
                            'error'        => ['danger',  'Error'],
                            default        => ['secondary', $log->resultado],
                        };
                        @endphp
                        <x-ui.badge :type="$badge[0]">{{ $badge[1] }}</x-ui.badge>
                    </td>

                    {{-- Detalle --}}
                    <td>
                        @if($log->changes || $log->before_data || $log->after_data)
                        <button type="button" class="btn btn-link btn-sm p-0 text-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-audit-{{ $log->id }}"
                                title="Ver cambios">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4.5a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9z"/>
                                <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            </svg>
                        </button>

                        {{-- Modal cambios --}}
                        <div class="modal fade" id="modal-audit-{{ $log->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title">
                                            Cambios — {{ $log->descripcion }}
                                        </h6>
                                        <small class="text-muted ms-2">{{ $log->created_at->format('d/m/Y H:i:s') }}</small>
                                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        {{-- Datos del evento --}}
                                        <div class="row g-2 mb-3" style="font-size:.8rem">
                                            <div class="col-4"><span class="text-muted">Request ID:</span><br><code>{{ $log->request_id }}</code></div>
                                            <div class="col-4"><span class="text-muted">Permiso:</span><br>{{ $log->permission_usado ?? '—' }}</div>
                                            <div class="col-4"><span class="text-muted">Duración:</span><br>{{ $log->duracion_ms }}ms</div>
                                        </div>
                                        @if($log->motivo)
                                            <div class="alert alert-warning py-2 mb-3" style="font-size:.8rem">
                                                <strong>Motivo:</strong> {{ $log->motivo }}
                                            </div>
                                        @endif
                                        @if($log->changes)
                                        <h6 class="fw-semibold mb-2" style="font-size:.8rem">Cambios detectados</h6>
                                        <table class="table table-sm table-bordered mb-3" style="font-size:.78rem">
                                            <thead class="table-light"><tr><th>Campo</th><th>Antes</th><th>Después</th></tr></thead>
                                            <tbody>
                                                @foreach($log->changes as $campo => $vals)
                                                <tr>
                                                    <td class="fw-medium">{{ $campo }}</td>
                                                    <td class="text-danger">{{ is_array($vals['before']) ? json_encode($vals['before']) : ($vals['before'] ?? '—') }}</td>
                                                    <td class="text-success">{{ is_array($vals['after'])  ? json_encode($vals['after'])  : ($vals['after']  ?? '—') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif
                                        <div class="row g-2">
                                            @if($log->before_data)
                                            <div class="col-6">
                                                <p class="mb-1 fw-medium" style="font-size:.78rem">Datos anteriores</p>
                                                <pre class="p-2 rounded" style="background:#0f172a;color:#e2e8f0;font-size:.72rem;max-height:200px;overflow-y:auto">{{ json_encode($log->before_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                            @endif
                                            @if($log->after_data)
                                            <div class="col-6">
                                                <p class="mb-1 fw-medium" style="font-size:.78rem">Datos posteriores</p>
                                                <pre class="p-2 rounded" style="background:#0f172a;color:#e2e8f0;font-size:.72rem;max-height:200px;overflow-y:auto">{{ json_encode($log->after_data,  JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

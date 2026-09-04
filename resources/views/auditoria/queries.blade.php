<x-layouts.app page-title="Auditoría — Queries SQL">

<x-ui.page-header title="Queries SQL"
    subtitle="Registro de todas las consultas ejecutadas, vinculadas a cada acción de usuario.">
    <x-slot name="actions">
        @if($total_lentas > 0)
            <span class="badge text-bg-warning text-dark">⚡ {{ $total_lentas }} queries lentas</span>
        @endif
        <span class="badge text-bg-secondary">{{ number_format($logs->total()) }} registros</span>
    </x-slot>
</x-ui.page-header>

{{-- Filtros --}}
<x-ui.filter-bar :action="route('auditoria.queries')">
    <x-slot name="fields">
        <div class="col-12 col-sm-4 col-md-2">
            <label class="form-label form-label-sm mb-1">Tipo</label>
            <select name="tipo" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach(['SELECT','INSERT','UPDATE','DELETE','BEGIN','COMMIT','ROLLBACK'] as $t)
                    <option value="{{ $t }}" {{ request('tipo') === $t ? 'selected':'' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1">Tabla</label>
            <input type="text" name="tabla" class="form-control form-control-sm"
                   placeholder="users, alumnos…" value="{{ request('tabla') }}">
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label form-label-sm mb-1">Request ID</label>
            <input type="text" name="request_id" class="form-control form-control-sm font-monospace"
                   placeholder="REQ-…" value="{{ request('request_id') }}">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1">Desde</label>
            <input type="date" name="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1">Hasta</label>
            <input type="date" name="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
        </div>
        <div class="col-auto d-flex align-items-end">
            <div class="form-check mb-0">
                <input type="checkbox" class="form-check-input" name="solo_lentas" value="1"
                       id="chk_lentas" {{ request('solo_lentas') ? 'checked':'' }}>
                <label class="form-check-label" for="chk_lentas" style="font-size:.8rem">Solo lentas</label>
            </div>
        </div>
    </x-slot>
</x-ui.filter-bar>

<x-ui.card :flush="true">
    @if($logs->isEmpty())
        <x-ui.empty-state message="No hay queries registradas con los filtros aplicados." />
    @else
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr>
                    <th>Fecha / hora</th>
                    <th>Tipo</th>
                    <th>Tabla</th>
                    <th>SQL</th>
                    <th>Tiempo (ms)</th>
                    <th>Origen</th>
                    <th>Request ID</th>
                    <th>Geo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr class="{{ $log->es_lenta ? 'table-warning' : '' }}">
                    <td class="text-nowrap" style="font-size:.78rem">
                        <span class="d-block">{{ $log->created_at->format('d/m/Y') }}</span>
                        <span class="text-muted">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>
                    <td>
                        @php
                        $tipoBadge = match($log->tipo) {
                            'SELECT'   => 'info',
                            'INSERT'   => 'success',
                            'UPDATE'   => 'warning',
                            'DELETE'   => 'danger',
                            'BEGIN','COMMIT','ROLLBACK' => 'secondary',
                            default    => 'secondary',
                        };
                        @endphp
                        <x-ui.badge :type="$tipoBadge" :small="true">{{ $log->tipo ?? '?' }}</x-ui.badge>
                        @if($log->es_lenta)
                            <span class="d-block mt-1" style="font-size:.65rem;color:#d97706">⚡ LENTA</span>
                        @endif
                    </td>
                    <td style="font-size:.78rem" class="text-nowrap">
                        {{ $log->tabla_principal ?? '—' }}
                    </td>
                    <td>
                        {{-- Click para ver SQL completo en modal --}}
                        <code class="sql-preview"
                              data-full-sql="{{ $log->sql }}"
                              title="Click para ver SQL completo">
                            {{ Str::limit($log->sql, 80) }}
                        </code>
                    </td>
                    <td class="text-nowrap" style="font-size:.8rem">
                        <span class="{{ $log->es_lenta ? 'text-danger fw-bold' : '' }}">
                            {{ number_format($log->tiempo_ms, 1) }}
                        </span>
                    </td>
                    <td style="font-size:.72rem;max-width:180px">
                        <span class="text-truncate d-block font-monospace" title="{{ $log->origen }}">
                            {{ $log->origen ? Str::afterLast($log->origen, '\\') : '—' }}
                        </span>
                    </td>
                    <td style="font-size:.72rem" class="text-nowrap">
                        <code>{{ Str::limit($log->request_id ?? '—', 20) }}</code>
                    </td>
                    <td style="font-size:.75rem">
                        @if($log->latitud)
                            <span class="geo-indicator available" style="font-size:.7rem">
                                {{ number_format($log->latitud,3) }},{{ number_format($log->longitud,3) }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
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

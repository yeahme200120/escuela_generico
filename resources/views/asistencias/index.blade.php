<x-layouts.app page-title="Asistencias">
<x-ui.page-header title="Pase de lista" subtitle="Registro de asistencias por grupo y fecha." />

<x-ui.filter-bar :action="route('asistencias.index')">
    <x-slot name="fields">
        <div class="col-md-3">
            <label class="form-label form-label-sm mb-1">Grupo</label>
            <select name="grupo_id" class="form-select form-select-sm">
                <option value="">Seleccionar grupo...</option>
                @foreach($grupos as $g)
                <option value="{{ $g->id }}" {{ request('grupo_id')==$g->id?'selected':'' }}>
                    {{ $g->nombre }} — {{ $g->grado?->nombre }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label form-label-sm mb-1">Fecha</label>
            <input type="date" name="fecha" class="form-control form-control-sm"
                   value="{{ request('fecha', now()->format('Y-m-d')) }}">
        </div>
    </x-slot>
</x-ui.filter-bar>

@if(request('grupo_id'))
    @if($alumnos->count() > 0)

    {{-- Resumen del día --}}
    @php
    $totalAlumnos  = $alumnos->count();
    $presentes = collect($asistenciasExistentes)->where('estado','presente')->count();
    $faltas    = collect($asistenciasExistentes)->where('estado','falta')->count();
    $retardos  = collect($asistenciasExistentes)->where('estado','retardo')->count();
    $esHoy     = request('fecha', now()->format('Y-m-d')) === now()->format('Y-m-d');
    @endphp
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3"><x-ui.stat-card label="Total alumnos" :value="$totalAlumnos" color="secondary" /></div>
        <div class="col-6 col-md-3"><x-ui.stat-card label="Presentes" :value="$presentes" color="success" /></div>
        <div class="col-6 col-md-3"><x-ui.stat-card label="Faltas" :value="$faltas" color="danger" /></div>
        <div class="col-6 col-md-3"><x-ui.stat-card label="Retardos" :value="$retardos" color="warning" /></div>
    </div>

    <x-ui.card>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0 fw-semibold">
                Lista — {{ \Carbon\Carbon::parse(request('fecha', now()->format('Y-m-d')))->format('d/m/Y') }}
                @if($esHoy)<span class="badge text-bg-success ms-2" style="font-size:.7rem">HOY</span>@endif
            </h6>
            @if(count($asistenciasExistentes) > 0)
            <span class="badge text-bg-info" style="font-size:.75rem">Pase ya registrado</span>
            @endif
        </div>

        <form method="POST" action="{{ route('asistencias.store') }}">
            @csrf
            <input type="hidden" name="grupo_id" value="{{ request('grupo_id') }}">
            <input type="hidden" name="fecha"    value="{{ request('fecha', now()->format('Y-m-d')) }}">
            <input type="hidden" name="ciclo_id" value="{{ $cicloActual?->id }}">

            {{-- Botones de selección masiva --}}
            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-outline-success btn-sm" onclick="setAll('presente')">✓ Todos presentes</button>
                <button type="button" class="btn btn-outline-danger btn-sm"  onclick="setAll('falta')">✗ Todos falta</button>
            </div>

            <div class="table-responsive">
                <table class="table table-se mb-0">
                    <thead>
                        <tr>
                            <th style="width:30px">#</th>
                            <th>Alumno</th>
                            <th style="width:160px">Estado</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alumnos as $i => $alumno)
                        @php
                        $prev  = $asistenciasExistentes[$alumno->id] ?? null;
                        $est   = is_array($prev) ? ($prev['estado'] ?? 'presente') : ($prev?->estado ?? 'presente');
                        $obs   = is_array($prev) ? ($prev['observacion'] ?? '') : ($prev?->observacion ?? '');
                        $rowClass = match($est) { 'falta'=>'table-danger','retardo'=>'table-warning', default=>'' };
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="text-muted" style="font-size:.8rem">{{ $i+1 }}</td>
                            <td style="font-size:.875rem">
                                <input type="hidden" name="lista[{{ $i }}][alumno_id]" value="{{ $alumno->id }}">
                                <div class="fw-medium">{{ $alumno->apellido_paterno }} {{ $alumno->nombres }}</div>
                                <small class="text-muted">{{ $alumno->matricula }}</small>
                            </td>
                            <td>
                                <select name="lista[{{ $i }}][estado]"
                                        class="form-select form-select-sm estado-select"
                                        onchange="updateRow(this)">
                                    @foreach(['presente'=>'✓ Presente','falta'=>'✗ Falta','retardo'=>'⚠ Retardo','justificada'=>'📋 Justificada'] as $v=>$l)
                                    <option value="{{ $v }}" {{ $est === $v ? 'selected':'' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="lista[{{ $i }}][observacion]"
                                       class="form-control form-control-sm"
                                       value="{{ $obs }}" placeholder="Opcional">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                    Guardar pase
                </button>
                <span class="text-muted" style="font-size:.8rem">{{ $totalAlumnos }} alumnos · {{ request('fecha', now()->format('Y-m-d')) }}</span>
            </div>
        </form>
    </x-ui.card>

    @else
    <x-ui.card>
        <x-ui.empty-state message="No hay alumnos activos asignados a este grupo." />
    </x-ui.card>
    @endif
@else
<x-ui.card>
    <x-ui.empty-state message="Selecciona un grupo y fecha para registrar el pase de lista." />
</x-ui.card>
@endif

@push('scripts')
<script>
function setAll(estado) {
    document.querySelectorAll('.estado-select').forEach(sel => {
        sel.value = estado;
        updateRow(sel);
    });
}
function updateRow(sel) {
    const row = sel.closest('tr');
    row.classList.remove('table-danger','table-warning');
    if (sel.value === 'falta')    row.classList.add('table-danger');
    if (sel.value === 'retardo')  row.classList.add('table-warning');
}
</script>
@endpush
</x-layouts.app>

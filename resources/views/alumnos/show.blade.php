<x-layouts.app page-title="Alumno: {{ $alumno->nombre_completo }}">
<x-ui.page-header title="{{ $alumno->nombre_completo }}"
    :items="[['label'=>'Alumnos','url'=>route('alumnos.index')],['label'=>$alumno->nombre_completo]]">
    <x-slot name="actions">
        @can('alumnos.editar')
        <a href="{{ route('alumnos.edit',$alumno) }}" class="btn btn-sm btn-outline-primary">Editar</a>
        @endcan
        @can('control_escolar.bajas')
        <a href="{{ route('bajas.create',['alumno_id'=>$alumno->id]) }}" class="btn btn-sm btn-outline-danger ms-1">Registrar baja</a>
        @endcan
        @can('control_escolar.inscribir')
        <a href="{{ route('alumnos.inscripcion.create',['alumno_id'=>$alumno->id]) }}" class="btn btn-sm btn-outline-success ms-1">Inscribir</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<div class="row g-3">
    {{-- Columna izquierda: datos + tutores --}}
    <div class="col-md-4">
        <x-ui.card title="Datos personales">
            <dl class="row mb-0" style="font-size:.875rem">
                <dt class="col-5 text-muted">Matrícula</dt>
                <dd class="col-7 fw-medium">{{ $alumno->matricula ?? '—' }}</dd>
                <dt class="col-5 text-muted">CURP</dt>
                <dd class="col-7">{{ $alumno->curp ?? '—' }}</dd>
                <dt class="col-5 text-muted">Nacimiento</dt>
                <dd class="col-7">{{ $alumno->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</dd>
                <dt class="col-5 text-muted">Sexo</dt>
                <dd class="col-7">{{ $alumno->sexo ?? '—' }}</dd>
                <dt class="col-5 text-muted">Email</dt>
                <dd class="col-7">{{ $alumno->email ?? '—' }}</dd>
                <dt class="col-5 text-muted">Teléfono</dt>
                <dd class="col-7">{{ $alumno->telefono ?? '—' }}</dd>
                <dt class="col-5 text-muted">Sede actual</dt>
                <dd class="col-7">{{ $alumno->sedeActual?->nombre ?? '—' }}</dd>
                <dt class="col-5 text-muted">Estatus</dt>
                <dd class="col-7">
                    <x-ui.badge :type="match($alumno->estatus){
                        'activo'=>'success','egresado'=>'info',
                        default=>'danger'
                    }">{{ ucfirst(str_replace('_',' ',$alumno->estatus)) }}</x-ui.badge>
                </dd>
                <dt class="col-5 text-muted">Riesgo</dt>
                <dd class="col-7">
                    <x-ui.badge :type="match($alumno->estatus_riesgo){
                        'riesgo_alto'=>'danger','riesgo_medio'=>'warning',
                        'observacion'=>'info',default=>'success'
                    }">{{ ucfirst(str_replace('_',' ',$alumno->estatus_riesgo)) }}</x-ui.badge>
                </dd>
            </dl>
        </x-ui.card>

        <x-ui.card title="Tutores" class="mt-3">
            @forelse($alumno->tutores as $t)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:.875rem">
                <div>
                    <div class="fw-medium">{{ $t->nombres }} {{ $t->apellido_paterno }}</div>
                    <small class="text-muted">{{ $t->parentesco }} · {{ $t->telefono ?? '—' }}</small>
                </div>
                @if($t->pivot->es_principal)
                    <x-ui.badge type="primary" small>Principal</x-ui.badge>
                @endif
            </div>
            @empty
            <x-ui.empty-state message="Sin tutores registrados." />
            @endforelse
            @can('alumnos.editar')
            <div class="mt-2">
                <a href="{{ route('tutores.create',['alumno_id'=>$alumno->id]) }}" class="btn btn-link btn-sm p-0">+ Agregar tutor</a>
            </div>
            @endcan
        </x-ui.card>
    </div>

    {{-- Columna derecha: tabs --}}
    <div class="col-md-8">
        <x-ui.card :flush="true">
            <ul class="nav nav-tabs px-3 pt-2" id="tabsAlumno" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-tray" type="button">Trayectoria</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bajas" type="button">Bajas</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-cargos" type="button">Adeudos</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-docs" type="button">Documentos</button></li>
            </ul>
            <div class="tab-content p-3">
                {{-- Trayectoria --}}
                <div class="tab-pane fade show active" id="tab-tray">
                    @forelse($alumno->trayectorias->sortByDesc('fecha_inicio') as $t)
                    <div class="d-flex justify-content-between align-items-start py-2 border-bottom" style="font-size:.875rem">
                        <div>
                            <div class="fw-medium">{{ $t->cicloEscolar?->nombre ?? '—' }}</div>
                            <small class="text-muted">{{ $t->grado?->nombre }} {{ $t->grupo?->nombre }}</small>
                            @if($t->motivo)
                            <small class="d-block text-muted">{{ $t->motivo }}</small>
                            @endif
                        </div>
                        <div class="text-end">
                            <x-ui.badge type="secondary" small>{{ $t->estatus }}</x-ui.badge>
                            <div class="text-muted mt-1" style="font-size:.75rem">{{ $t->fecha_inicio?->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    @empty
                    <x-ui.empty-state message="Sin trayectoria registrada." />
                    @endforelse
                </div>

                {{-- Bajas --}}
                <div class="tab-pane fade" id="tab-bajas">
                    @forelse($alumno->bajas as $b)
                    <div class="py-2 border-bottom" style="font-size:.875rem">
                        <div class="d-flex justify-content-between">
                            <x-ui.badge :type="match($b->tipo){'temporal'=>'warning','desercion'=>'danger',default=>'secondary'}" small>{{ ucfirst($b->tipo) }}</x-ui.badge>
                            <small class="text-muted">{{ $b->fecha_solicitud?->format('d/m/Y') }}</small>
                        </div>
                        <p class="mb-0 mt-1 text-muted" style="font-size:.8rem">{{ $b->motivo }}</p>
                    </div>
                    @empty
                    <x-ui.empty-state message="Sin bajas registradas." />
                    @endforelse
                </div>

                {{-- Adeudos --}}
                <div class="tab-pane fade" id="tab-cargos">
                    @php $cargosActivos = $alumno->cargos->whereIn('estado',['pendiente','parcial','vencido']); @endphp
                    @forelse($cargosActivos as $c)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:.875rem">
                        <div>
                            <div class="fw-medium">{{ $c->concepto?->nombre }}</div>
                            <small class="text-muted">Vence: {{ $c->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-semibold">${{ number_format($c->total,2) }}</div>
                            <x-ui.badge :type="$c->estado==='vencido'?'danger':'warning'" small>{{ ucfirst($c->estado) }}</x-ui.badge>
                        </div>
                    </div>
                    @empty
                    <x-ui.empty-state message="Sin adeudos pendientes." />
                    @endforelse
                </div>

                {{-- Documentos --}}
                <div class="tab-pane fade" id="tab-docs">
                    @forelse($alumno->documentos as $d)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:.875rem">
                        <div>
                            <div class="fw-medium">{{ $d->tipoDocumento?->nombre }}</div>
                            <small class="text-muted">Folio: {{ $d->folio }} · v{{ $d->version }}</small>
                        </div>
                        <x-ui.badge :type="match($d->estado){'autorizado'=>'success','generado'=>'info','cancelado'=>'danger',default=>'secondary'}" small>{{ ucfirst($d->estado) }}</x-ui.badge>
                    </div>
                    @empty
                    <x-ui.empty-state message="Sin documentos generados." />
                    @endforelse
                    @can('documentos.generar')
                    <div class="mt-2">
                        <a href="{{ route('documentos.store') }}" class="btn btn-link btn-sm p-0">+ Generar documento</a>
                    </div>
                    @endcan
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
</x-layouts.app>

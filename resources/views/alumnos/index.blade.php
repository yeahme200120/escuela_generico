<x-layouts.app page-title="Alumnos">
<x-ui.page-header title="Alumnos" subtitle="Gestión de alumnos inscritos.">
    <x-slot name="actions">
        @can('alumnos.crear')
        <a href="{{ route('alumnos.create') }}" class="btn btn-primary btn-sm">+ Nuevo alumno</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.filter-bar :action="route('alumnos.index')">
    <x-slot name="fields">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="Nombre, matrícula o CURP..." value="{{ request('q') }}">
        </div>
        <div class="col-md-2">
            <select name="estatus" class="form-select form-select-sm">
                <option value="">Estatus</option>
                @foreach(['activo','baja_temporal','baja_definitiva','egresado'] as $e)
                <option value="{{ $e }}" {{ request('estatus')===$e?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$e)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="estatus_riesgo" class="form-select form-select-sm">
                <option value="">Riesgo</option>
                @foreach(['normal','observacion','riesgo_medio','riesgo_alto'] as $r)
                <option value="{{ $r }}" {{ request('estatus_riesgo')===$r?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr><th>Alumno</th><th>Matrícula</th><th>Sede</th><th>Situación</th><th>Riesgo</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($alumnos as $a)
                <tr>
                    <td>
                        <div class="fw-medium" style="font-size:.875rem">{{ $a->nombre_completo }}</div>
                        <small class="text-muted">{{ $a->curp }}</small>
                    </td>
                    <td style="font-size:.875rem">{{ $a->matricula ?? '—' }}</td>
                    <td style="font-size:.8rem">{{ $a->sedeActual?->nombre ?? '—' }}</td>
                    <td>
                        <x-ui.badge :type="match($a->estatus){
                            'activo'=>'success','egresado'=>'info',
                            'baja_temporal'=>'warning',default=>'danger'
                        }" small>{{ ucfirst(str_replace('_',' ',$a->estatus)) }}</x-ui.badge>
                    </td>
                    <td>
                        <x-ui.badge :type="match($a->estatus_riesgo){
                            'riesgo_alto'=>'danger','riesgo_medio'=>'warning',
                            'observacion'=>'info',default=>'success'
                        }" small>{{ ucfirst(str_replace('_',' ',$a->estatus_riesgo)) }}</x-ui.badge>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('alumnos.show',$a) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @can('alumnos.editar')
                        <a href="{{ route('alumnos.edit',$a) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin alumnos registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top d-flex justify-content-between">
        <small class="text-muted">{{ $alumnos->total() }} alumnos</small>
        {{ $alumnos->links() }}
    </div>
</x-ui.card>
</x-layouts.app>

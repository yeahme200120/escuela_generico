<x-layouts.app page-title="Documentos escolares">
<x-ui.page-header title="Documentos escolares" subtitle="Constancias, certificados, cartas y expedientes.">
    <x-slot name="actions">
        @can('documentos.generar')
        <a href="{{ route('documentos.store') }}" class="btn btn-primary btn-sm"
           data-bs-toggle="modal" data-bs-target="#modal-generar-doc">+ Generar documento</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.filter-bar :action="route('documentos.index')">
    <x-slot name="fields">
        <div class="col-md-3">
            <select name="tipo_id" class="form-select form-select-sm">
                <option value="">Tipo</option>
                @foreach($tipos as $t)
                <option value="{{ $t->id }}" {{ request('tipo_id')==$t->id?'selected':'' }}>{{ $t->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="estado" class="form-select form-select-sm">
                <option value="">Estado</option>
                @foreach(['pendiente','generado','autorizado','entregado','cancelado'] as $e)
                <option value="{{ $e }}" {{ request('estado')===$e?'selected':'' }}>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Alumno</th><th>Tipo</th><th>Folio</th><th>Estado</th><th>Generado</th><th></th></tr></thead>
            <tbody>
                @forelse($docs as $d)
                <tr>
                    <td style="font-size:.875rem">
                        <div class="fw-medium">{{ $d->alumno?->nombre_completo }}</div>
                        <small class="text-muted">{{ $d->alumno?->matricula }}</small>
                    </td>
                    <td style="font-size:.875rem"><x-ui.badge type="info" small>{{ $d->tipoDocumento?->nombre }}</x-ui.badge></td>
                    <td style="font-size:.8rem"><code>{{ $d->folio }}</code> <small class="text-muted">v{{ $d->version }}</small></td>
                    <td>
                        <x-ui.badge :type="match($d->estado){'autorizado'=>'success','generado'=>'info','entregado'=>'primary','cancelado'=>'secondary',default=>'warning'}">
                            {{ ucfirst($d->estado) }}
                        </x-ui.badge>
                    </td>
                    <td style="font-size:.78rem">{{ $d->generado_at?->format('d/m/Y') }}</td>
                    <td class="text-end">
                        @if($d->estado === 'generado')
                        @can('documentos.autorizar')
                        <form method="POST" action="{{ route('documentos.autorizar',$d) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success">Autorizar</button>
                        </form>
                        @endcan
                        @endif
                        @if($d->archivo)
                        <a href="{{ route('documentos.index',['download'=>$d->id]) }}" class="btn btn-sm btn-outline-secondary ms-1">⬇</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin documentos generados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $docs->links() }}</div>
</x-ui.card>

{{-- Modal generar documento --}}
@can('documentos.generar')
<x-ui.modal id="modal-generar-doc" title="Generar documento">
    <form method="POST" action="{{ route('documentos.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-medium">Alumno <span class="text-danger">*</span></label>
            <select name="alumno_id" class="form-select" required>
                <option value="">Buscar alumno...</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-medium">Tipo de documento <span class="text-danger">*</span></label>
            <select name="tipo_documento_id" class="form-select" required>
                <option value="">Seleccionar...</option>
                @foreach($tipos as $t)
                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-medium">Sede <span class="text-danger">*</span></label>
            <select name="sede_id" class="form-select" required>
                @foreach(\App\Models\Sede::whereHas('organizacion',fn($q)=>$q->where('id',auth()->user()->organizacion_id))->activas()->get() as $s)
                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                @endforeach
            </select>
        </div>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary btn-sm">Generar</button>
        </x-slot>
    </form>
</x-ui.modal>
@endcan
</x-layouts.app>

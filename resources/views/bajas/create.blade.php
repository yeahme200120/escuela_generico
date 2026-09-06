<x-layouts.app page-title="Registrar baja">
<x-ui.page-header title="Registrar baja"
    :items="[['label'=>'Bajas','url'=>route('bajas.index')],['label'=>'Nueva']]" />

<form method="POST" action="{{ route('bajas.store') }}" enctype="multipart/form-data">
@csrf
<div class="row g-3">
    <div class="col-md-8">
        <x-ui.card title="Datos de la baja">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium">Alumno <span class="text-danger">*</span></label>
                    @if(isset($alumno))
                        <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
                        <div class="form-control bg-light">{{ $alumno->nombre_completo }} — {{ $alumno->matricula }}</div>
                    @else
                        <select name="alumno_id" class="form-select @error('alumno_id') is-invalid @enderror" required>
                            <option value="">Seleccionar alumno...</option>
                        </select>
                        @error('alumno_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Tipo de baja <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required
                            onchange="mostrarDesercion(this.value)">
                        <option value="">Seleccionar...</option>
                        @foreach(['temporal'=>'Baja temporal','definitiva'=>'Baja definitiva','desercion'=>'Deserción','traslado'=>'Traslado','egreso'=>'Egreso'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('tipo')===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Fecha solicitud <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_solicitud" class="form-control" value="{{ old('fecha_solicitud', now()->format('Y-m-d')) }}" required>
                </div>
                <div id="campo-desercion" style="display:none" class="col-md-6">
                    <label class="form-label fw-medium">Motivo de deserción</label>
                    <select name="motivo_desercion" class="form-select">
                        <option value="">Seleccionar...</option>
                        @foreach(['abandono','inasistencia_prolongada','problemas_economicos','problemas_familiares','cambio_ciudad','cambio_escuela','bajo_aprovechamiento','motivo_personal','otro'] as $m)
                        <option value="{{ $m }}" {{ old('motivo_desercion')===$m?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$m)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Motivo <span class="text-danger">*</span></label>
                    <textarea name="motivo" class="form-control @error('motivo') is-invalid @enderror"
                              rows="3" required minlength="10" placeholder="Describir el motivo de la baja...">{{ old('motivo') }}</textarea>
                    @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Documento de soporte</label>
                    <x-ui.file-upload name="documento" accept=".pdf,.jpg,.png" :maxMb="5" />
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
                </div>
            </div>
        </x-ui.card>
    </div>
    <div class="col-md-4">
        <x-ui.card title="Fechas">
            <div class="mb-3">
                <label class="form-label fw-medium">Fecha de inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Fecha fin estimada</label>
                <input type="date" name="fecha_fin_estimada" class="form-control" value="{{ old('fecha_fin_estimada') }}">
                <small class="text-muted">Solo para bajas temporales</small>
            </div>
        </x-ui.card>
    </div>
</div>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-danger">Registrar baja</button>
    @if(isset($alumno))
    <a href="{{ route('alumnos.show',$alumno) }}" class="btn btn-outline-secondary">Cancelar</a>
    @else
    <a href="{{ route('bajas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    @endif
</div>
</form>

@push('scripts')
<script>
function mostrarDesercion(tipo) {
    document.getElementById('campo-desercion').style.display = tipo === 'desercion' ? '' : 'none';
}
</script>
@endpush
</x-layouts.app>

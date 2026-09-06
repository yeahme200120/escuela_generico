<x-layouts.app page-title="Nuevo reporte">
<x-ui.page-header title="Generar reporte"
    :items="[['label'=>'Reportes','url'=>route('reportes.index')],['label'=>'Nuevo']]" />

<form method="POST" action="{{ route('reportes.store') }}">
@csrf
<div class="row g-3">
    <div class="col-md-8">
        <x-ui.card title="Configuración del reporte">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Tipo de reporte <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach(['calificaciones'=>'Calificaciones','asistencias'=>'Asistencias','alumnos'=>'Alumnos','docentes'=>'Docentes','indicadores'=>'Indicadores académicos'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('tipo')===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Ciclo escolar</label>
                    <select name="ciclo_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\CicloEscolar::where('organizacion_id',auth()->user()->organizacion_id)->orderByDesc('es_actual')->get() as $c)
                        <option value="{{ $c->id }}" {{ old('ciclo_id')==$c->id?'selected':'' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Sede</label>
                    <select name="sede_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach(\App\Models\Sede::whereHas('organizacion',fn($q)=>$q->where('id',auth()->user()->organizacion_id))->activas()->get() as $s)
                        <option value="{{ $s->id }}" {{ old('sede_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Fecha desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ old('fecha_desde') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ old('fecha_hasta') }}">
                </div>
            </div>
        </x-ui.card>
    </div>
    <div class="col-md-4">
        <x-ui.card title="Información">
            <p class="text-muted" style="font-size:.875rem">
                Los reportes pequeños (calificaciones/asistencias) se generan inmediatamente en CSV.
            </p>
            <p class="text-muted" style="font-size:.875rem">
                Los reportes grandes se encolan y procesan de forma asíncrona vía Python.
                Recibirás una notificación cuando estén listos.
            </p>
            <div class="alert alert-info py-2 mb-0" style="font-size:.8rem">
                📊 Los reportes quedan registrados en el historial con auditoría completa.
            </div>
        </x-ui.card>
    </div>
</div>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Generar reporte</button>
    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>

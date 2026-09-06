<x-layouts.app page-title="Prospecto: {{ $prospecto->nombre_completo }}">
<x-ui.page-header title="{{ $prospecto->nombre_completo }}"
    :items="[['label'=>'Prospectos','url'=>route('admisiones.prospectos.index')],['label'=>$prospecto->nombre_completo]]">
    <x-slot name="actions">
        <x-ui.badge :type="['nuevo'=>'secondary','contactado'=>'info','citado'=>'primary','evaluado'=>'warning','admitido'=>'success','rechazado'=>'danger'][$prospecto->estatus] ?? 'secondary'">
            {{ ucfirst($prospecto->estatus) }}
        </x-ui.badge>
    </x-slot>
</x-ui.page-header>

<div class="row g-3">
    <div class="col-md-4">
        <x-ui.card title="Datos del prospecto">
            <dl class="row mb-3" style="font-size:.875rem">
                <dt class="col-5 text-muted">Email</dt><dd class="col-7">{{ $prospecto->email ?? '—' }}</dd>
                <dt class="col-5 text-muted">Teléfono</dt><dd class="col-7">{{ $prospecto->telefono ?? '—' }}</dd>
                <dt class="col-5 text-muted">Nivel</dt><dd class="col-7">{{ $prospecto->nivel_interes ?? '—' }}</dd>
                <dt class="col-5 text-muted">Sede interés</dt><dd class="col-7">{{ $prospecto->sedeInteres?->nombre ?? '—' }}</dd>
                <dt class="col-5 text-muted">Ciclo</dt><dd class="col-7">{{ $prospecto->ciclo_interes ?? '—' }}</dd>
                <dt class="col-5 text-muted">Asignado a</dt><dd class="col-7">{{ $prospecto->asignadoA?->nombres ?? '—' }}</dd>
            </dl>
            @if($prospecto->observaciones)
            <p class="text-muted mb-0" style="font-size:.8rem">{{ $prospecto->observaciones }}</p>
            @endif
        </x-ui.card>

        {{-- Actualizar estatus --}}
        <x-ui.card title="Actualizar estatus" class="mt-3">
            <form method="POST" action="{{ route('admisiones.prospectos.seguimiento',$prospecto->id) }}">
                @csrf
                <input type="hidden" name="tipo" value="nota">
                <div class="mb-2">
                    <select name="estatus" class="form-select form-select-sm">
                        @foreach(['nuevo','contactado','citado','evaluado','admitido','rechazado','cancelado'] as $e)
                        <option value="{{ $e }}" {{ $prospecto->estatus===$e?'selected':'' }}>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <textarea name="descripcion" class="form-control form-control-sm" rows="2" placeholder="Nota del seguimiento..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Guardar seguimiento</button>
            </form>
        </x-ui.card>
    </div>

    <div class="col-md-8">
        <x-ui.card title="Seguimientos" :flush="true">
            @forelse($prospecto->seguimientos->sortByDesc('created_at') as $s)
            <div class="px-3 py-2 border-bottom">
                <div class="d-flex justify-content-between">
                    <x-ui.badge type="info" small>{{ ucfirst($s->tipo) }}</x-ui.badge>
                    <small class="text-muted">{{ $s->created_at?->format('d/m/Y H:i') }}</small>
                </div>
                <p class="mb-0 mt-1" style="font-size:.875rem">{{ $s->descripcion }}</p>
                <small class="text-muted">por {{ $s->usuario?->nombres }}</small>
            </div>
            @empty
            <x-ui.empty-state message="Sin seguimientos registrados." />
            @endforelse
        </x-ui.card>
    </div>
</div>
</x-layouts.app>

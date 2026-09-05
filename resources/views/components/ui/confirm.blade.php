@props([
    'id'       => 'confirm-'.Str::random(6),
    'title'    => '¿Confirmar acción?',
    'message'  => '¿Estás seguro de continuar? Esta acción no se puede deshacer.',
    'action'   => '#',
    'method'   => 'POST',
    'label'    => 'Confirmar',
    'type'     => 'danger',   // danger | warning | primary
    'motivo'   => false,      // true = pide campo motivo
])

{{-- Botón disparador (slot opcional, si se pasa) --}}
@isset($trigger)
    <span data-bs-toggle="modal" data-bs-target="#{{ $id }}">{{ $trigger }}</span>
@endisset

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body pt-4 text-center">
                <div class="mb-3">
                    @if($type === 'danger')
                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex p-3 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-danger" viewBox="0 0 16 16">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                    </div>
                    @endif
                    <h6 class="fw-semibold">{{ $title }}</h6>
                    <p class="text-muted mb-0" style="font-size:.875rem">{{ $message }}</p>
                </div>
                <form method="POST" action="{{ $action }}" id="{{ $id }}-form">
                    @csrf
                    @if($method !== 'POST') @method($method) @endif
                    @if($motivo)
                    <div class="mb-3 text-start">
                        <label class="form-label form-label-sm fw-medium">Motivo <span class="text-danger">*</span></label>
                        <textarea name="motivo" class="form-control form-control-sm" rows="2" required minlength="10" placeholder="Describe el motivo..."></textarea>
                    </div>
                    @endif
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-{{ $type }} btn-sm flex-fill">{{ $label }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

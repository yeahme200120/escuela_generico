<div class="d-flex justify-content-end">
    <button {{ $attributes->merge(['class' => 'btn btn-secondary me-2', 'data-bs-dismiss' => 'modal']) }}>Cancelar</button>
    <button {{ $attributes->merge(['class' => 'btn btn-danger']) }}>{{ $slot ?? 'Confirmar' }}</button>
</div>

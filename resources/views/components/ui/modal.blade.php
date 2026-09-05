@props([
    'id'       => 'modal-'.Str::random(6),
    'title'    => '',
    'size'     => '',        // sm | lg | xl | fullscreen
    'static'   => false,     // backdrop estático
    'scrollable' => false,
    'centered'   => false,
])
@php
$dialogClass = collect([
    'modal-dialog',
    $size ? "modal-$size" : '',
    $scrollable ? 'modal-dialog-scrollable' : '',
    $centered   ? 'modal-dialog-centered'   : '',
])->filter()->implode(' ');
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}-label" aria-hidden="true"
     {{ $static ? 'data-bs-backdrop=static data-bs-keyboard=false' : '' }}>
    <div class="{{ $dialogClass }}">
        <div class="modal-content">
            @if($title)
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="{{ $id }}-label">{{ $title }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            @endif
            <div class="modal-body">
                {{ $slot }}
            </div>
            @isset($footer)
            <div class="modal-footer">{{ $footer }}</div>
            @endisset
        </div>
    </div>
</div>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($pageTitle) ? $pageTitle . ' — ' : '' }}{{ config('app.name') }}</title>

    {{-- Tema dinámico desde system_settings --}}
    @include('partials.dynamic-theme')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Estilos adicionales de la página --}}
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
@include('partials.sidebar')

{{-- Contenido principal --}}
<div id="main-content">

    {{-- Topbar --}}
    @include('partials.topbar')

    {{-- Flash messages --}}
    @if(session()->hasAny(['success','error','warning','info']))
    <div class="px-4 pt-3">
        @foreach(['success'=>'success','error'=>'danger','warning'=>'warning','info'=>'info'] as $key => $bsType)
            @if(session($key))
            <div class="alert alert-{{ $bsType }} alert-dismissible auto-dismiss fade show d-flex align-items-center gap-2" role="alert">
                @if($bsType === 'success')
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                @elseif($bsType === 'danger')
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                @endif
                <span>{{ session($key) }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    {{-- Errores de validación globales (para formularios que redirijan aquí) --}}
    @if($errors->any())
    <div class="px-4 pt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    {{-- Contenido de la página --}}
    <main id="page-content" role="main">
        {{ $slot }}
    </main>

</div>{{-- /#main-content --}}

{{-- Scripts adicionales de la página --}}
@stack('scripts')

</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>

    {{-- Tema dinámico --}}
    @include('partials.dynamic-theme')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="login-wrapper">
    {{ $slot }}
</div>

{{-- Flash messages (pueden venir de redirects con ->with('success', ...)) --}}
@if(session()->hasAny(['success','error','warning','info']))
<div style="position:fixed;top:1rem;right:1rem;z-index:9999;min-width:280px;max-width:360px">
    @foreach(['success'=>'success','error'=>'danger','warning'=>'warning','info'=>'info'] as $key => $type)
        @if(session($key))
        <div class="alert alert-{{ $type }} alert-dismissible auto-dismiss fade show shadow-sm" role="alert">
            {{ session($key) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
    @endforeach
</div>
@endif

</body>
</html>

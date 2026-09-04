<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Sistema Escolar
|--------------------------------------------------------------------------
| Rutas API v1. Autenticadas con Sanctum.
| El browser NUNCA debe comunicarse directamente con Python (ver spec §3).
*/

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Información del usuario autenticado
    Route::get('/user', fn(Request $request) => $request->user());

    // Las rutas específicas se añadirán por módulo
});

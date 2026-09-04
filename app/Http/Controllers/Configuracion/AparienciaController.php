<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Services\Configuracion\ThemeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AparienciaController extends Controller
{
    public function __construct(
        private readonly ThemeService $theme,
    ) {}

    public function index(): View
    {
        $orgId  = auth()->user()->organizacion_id;
        $actual = $this->theme->getForOrganizacion($orgId);

        return view('configuracion.apariencia', [
            'tema'     => $actual,
            'defaults' => $this->theme->defaults(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('configuracion.apariencia.editar');

        $request->validate([
            'primary'    => ['required', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'secondary'  => ['required', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'success'    => ['required', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'warning'    => ['required', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'danger'     => ['required', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'info'       => ['required', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'background' => ['required', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'surface'    => ['required', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'text'       => ['required', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
        ]);

        $orgId = auth()->user()->organizacion_id;

        $this->theme->saveForOrganizacion(
            orgId:  $orgId,
            data:   $request->only(array_map(
                fn($k) => str_replace('theme.', '', $k),
                ThemeService::COLOR_KEYS
            )),
            userId: auth()->id(),
        );

        return redirect()->route('configuracion.apariencia')
                         ->with('success', 'Tema actualizado correctamente.');
    }
}

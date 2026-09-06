<?php

namespace App\Http\Controllers;

use App\Services\Seguridad\TwoFactorService;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(
        private readonly TwoFactorService $twoFactorService,
        private readonly AuditService     $audit,
    ) {}

    /** Muestra la página de configuración 2FA */
    public function index(): View
    {
        $user = auth()->user();
        return view('two-factor.index', ['user' => $user]);
    }

    /** Genera y muestra el QR para activar 2FA */
    public function create(): View
    {
        $user   = auth()->user();
        $secret = $this->twoFactorService->generarSecret();
        session(['2fa_secret_temp' => $secret]);
        $qrUrl = $this->twoFactorService->generarQrUrl($user, $secret);
        return view('two-factor.create', compact('secret', 'qrUrl'));
    }

    /** Confirma y activa 2FA tras verificar el código */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string|digits:6']);
        $user   = auth()->user();
        $secret = session('2fa_secret_temp');

        if (!$secret) return back()->with('error', 'Sesión expirada. Intenta de nuevo.');

        if (!$this->twoFactorService->verificarCodigo($secret, $request->code)) {
            return back()->with('error', 'Código inválido. Verifica tu aplicación de autenticación.');
        }

        $this->twoFactorService->activar($user, $secret);
        session()->forget('2fa_secret_temp');

        $this->audit->log(modulo: 'seguridad', accion: '2fa_activated', model: \App\Models\User::class,
            modelId: $user->id, descripcion: '2FA activado por el usuario');

        return redirect()->route('two-factor.index')->with('success', 'Autenticación de dos factores activada.');
    }

    /** Desactiva 2FA (requiere contraseña) */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|string']);
        $user = auth()->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Contraseña incorrecta.');
        }

        $this->twoFactorService->desactivar($user);
        $this->audit->log(modulo: 'seguridad', accion: '2fa_deactivated', model: \App\Models\User::class,
            modelId: $user->id, descripcion: '2FA desactivado');

        return redirect()->route('two-factor.index')->with('success', '2FA desactivado.');
    }

    /** Pantalla de verificación del código durante el login */
    public function challenge(): View
    {
        if (!session('2fa_user_id')) abort(403);
        return view('two-factor.challenge');
    }

    /** Verifica el código durante el login */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);
        $userId = session('2fa_user_id');
        if (!$userId) abort(403);

        $user = \App\Models\User::findOrFail($userId);

        if (!$this->twoFactorService->verificarCodigo($user->two_factor_secret, $request->code)) {
            $this->audit->log(modulo: 'seguridad', accion: '2fa_failed', model: \App\Models\User::class,
                modelId: $user->id, resultado: 'failed');
            return back()->with('error', 'Código incorrecto.');
        }

        \Illuminate\Support\Facades\Auth::login($user);
        session()->forget('2fa_user_id');

        $this->audit->log(modulo: 'seguridad', accion: '2fa_verified', model: \App\Models\User::class,
            modelId: $user->id, resultado: 'success');

        return redirect()->intended(route('dashboard'));
    }
}

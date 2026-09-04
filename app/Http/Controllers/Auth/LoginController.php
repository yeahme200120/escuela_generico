<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    // ── GET /login ────────────────────────────────────────────────
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // ── POST /login ───────────────────────────────────────────────
    public function store(LoginRequest $request, AuthService $authService): RedirectResponse
    {
        // Los datos de geo y dispositivo llegan como campos ocultos del form
        // (populados por GeoCapture/DeviceInfo en JS antes del submit)
        $geoData = [
            'latitude'  => $request->input('geo_latitude'),
            'longitude' => $request->input('geo_longitude'),
            'accuracy'  => $request->input('geo_accuracy'),
            'altitude'  => $request->input('geo_altitude'),
            'source'    => $request->input('geo_source', 'unknown'),
        ];

        // Inyectar device_id en el header virtual para que GeoTrace lo capture
        if ($deviceId = $request->input('device_id')) {
            $request->headers->set('X-Device-ID', $deviceId);
        }
        if ($deviceInfo = $request->input('device_info')) {
            $request->headers->set('X-Device-Info', $deviceInfo);
        }

        $result = $authService->login(
            email:    $request->input('email'),
            password: $request->input('password'),
            remember: $request->boolean('remember'),
            geoData:  $geoData,
        );

        if ($result->isSuccess()) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => $result->message]);
    }
}

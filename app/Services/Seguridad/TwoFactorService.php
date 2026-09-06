<?php namespace App\Services\Seguridad;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService {
    protected $google2fa;
    
    public function __construct() {
        $this->google2fa = new Google2FA();
    }
    
    public function generarSecretTotp() {
        return $this->google2fa->generateSecretKey();
    }
    
    public function obtenerCodigoQR($email, $secret) {
        return $this->google2fa->getQRCodeInline(
            config('app.name'),
            $email,
            $secret
        );
    }
    
    public function verificarCodigoTotp($secret, $codigo) {
        return $this->google2fa->verifyKeyAndTimestamp($secret, $codigo, 1);
    }
    
    public function generarCodigoSMS() {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    public function enviarCodigoSMS($telefono, $codigo) {
        try {
            \Log::info("SMS enviado a $telefono: c�digo $codigo");
            return true;
        } catch (\Exception $e) {
            \Log::error('Error enviando SMS: ' . $e->getMessage());
            return false;
        }
    }
    
    public function habilitarDosFactores($userId, $metodo = 'totp') {
        $user = User::find($userId);
        if (!$user) return false;
        
        $user->update([
            'dos_factores_habilitado' => true,
            'dos_factores_metodo' => $metodo
        ]);
        return true;
    }
    
    public function deshabilitarDosFactores($userId) {
        $user = User::find($userId);
        if (!$user) return false;
        
        $user->update([
            'dos_factores_habilitado' => false,
            'dos_factores_metodo' => null,
            'dos_factores_secret' => null
        ]);
        return true;
    }
}

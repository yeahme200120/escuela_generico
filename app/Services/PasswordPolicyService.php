<?php namespace App\Services;
use App\Models\User;

class PasswordPolicyService {
    private $minLength = 8;
    private $requireUppercase = true;
    private $requireNumbers = true;
    private $requireSpecial = true;
    private $expirationDays = 90;
    
    public function validarFuerza($password) {
        $errores = [];
        
        if (strlen($password) < $this->minLength) {
            $errores[] = "Mínimo $this->minLength caracteres";
        }
        
        if ($this->requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $errores[] = "Debe incluir mayúsculas";
        }
        
        if ($this->requireNumbers && !preg_match('/[0-9]/', $password)) {
            $errores[] = "Debe incluir números";
        }
        
        if ($this->requireSpecial && !preg_match('/[!@#$%^&*]/', $password)) {
            $errores[] = "Debe incluir caracteres especiales";
        }
        
        return [
            'valida' => count($errores) === 0,
            'errores' => $errores,
            'fuerza' => $this->calcularFuerza($password)
        ];
    }
    
    public function verificarHistorial($userId, $password) {
        $passwordHistory = \DB::table('password_history')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($passwordHistory as $hist) {
            if (\Hash::check($password, $hist->password_hash)) {
                return false;
            }
        }
        return true;
    }
    
    public function registrarCambio($userId, $passwordHash) {
        \DB::table('password_history')->insert([
            'user_id' => $userId,
            'password_hash' => $passwordHash,
            'created_at' => now()
        ]);
    }
    
    public function verificarExpiracion($userId) {
        $user = User::find($userId);
        if (!$user || !$user->ultimo_cambio_password) return true;
        
        $diasTranscurridos = now()->diffInDays($user->ultimo_cambio_password);
        return $diasTranscurridos < $this->expirationDays;
    }
    
    private function calcularFuerza($password) {
        $puntos = 0;
        if (strlen($password) >= 8) $puntos++;
        if (strlen($password) >= 12) $puntos++;
        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) $puntos++;
        if (preg_match('/[0-9]/', $password)) $puntos++;
        if (preg_match('/[!@#$%^&*]/', $password)) $puntos++;
        
        return ['nivel' => $puntos, 'texto' => ['Muy débil', 'Débil', 'Media', 'Fuerte', 'Muy fuerte'][$puntos] ?? 'Máxima'][1];
    }
}

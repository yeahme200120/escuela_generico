<?php namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ConfiguracionHerenciaService {
    
    private $niveles = ['organizacion', 'escuela', 'sede', 'ciclo_escolar'];
    
    public function obtenerConfiguracion(string $nivel, int $id): array {
        try {
            // Verificar nivel válido
            if (!in_array($nivel, $this->niveles)) {
                return ['error' => "Nivel '$nivel' no válido"];
            }
            
            // Verificar en cache
            $cacheKey = "config_{$nivel}_{$id}";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            
            // Obtener configuración del nivel actual
            $config = DB::table('configuraciones')
                ->where('nivel', $nivel)
                ->where('nivel_id', $id)
                ->first();
            
            $resultado = [];
            
            if ($config) {
                $resultado = json_decode($config->valores, true) ?? [];
            }
            
            // Heredar del nivel superior si falta algo
            $resultado = $this->heredarDelPadre($nivel, $id, $resultado);
            
            // Cachear por 1 hora
            Cache::put($cacheKey, $resultado, 3600);
            
            return $resultado;
            
        } catch (\Exception $e) {
            \Log::error("Error obteniendo configuración: " . $e->getMessage());
            return [];
        }
    }
    
    private function heredarDelPadre(string $nivelActual, int $idActual, array $config): array {
        $posicion = array_search($nivelActual, $this->niveles);
        
        // Si no hay padre o es el nivel superior, retornar
        if ($posicion === 0) {
            return $this->establecerDefectos($config);
        }
        
        // Obtener padre según el nivel
        $padreNivel = $this->niveles[$posicion - 1];
        $idPadre = $this->obtenerIdPadre($nivelActual, $idActual);
        
        if (!$idPadre) {
            return $this->establecerDefectos($config);
        }
        
        // Recursivamente heredar del padre
        $configPadre = $this->obtenerConfiguracion($padreNivel, $idPadre);
        
        // Mezclar: config actual sobrescribe padre
        return array_merge($configPadre, $config);
    }
    
    private function obtenerIdPadre(string $nivelActual, int $id): ?int {
        return match ($nivelActual) {
            'escuela' => DB::table('escuelas')->find($id)?->organizacion_id,
            'sede' => DB::table('sedes')->find($id)?->escuela_id,
            'ciclo_escolar' => DB::table('ciclos_escolares')->find($id)?->sede_id,
            default => null
        };
    }
    
    private function establecerDefectos(array $config): array {
        $defectos = [
            'minimo_probatorio' => 60.0,
            'minimo_aprobacion' => 70.0,
            'escala_calificacion' => 100,
            'dias_tolerancia_vencimiento' => 5,
            'horas_clase_por_semana' => 5,
            'porcentaje_asistencia_minima' => 80.0,
            'permitir_regularizacion' => true,
            'auto_generar_reportes' => false
        ];
        
        return array_merge($defectos, $config);
    }
    
    public function guardarConfiguracion(string $nivel, int $id, array $config): bool {
        try {
            if (!in_array($nivel, $this->niveles)) {
                return false;
            }
            
            DB::table('configuraciones')->updateOrInsert(
                ['nivel' => $nivel, 'nivel_id' => $id],
                ['valores' => json_encode($config), 'updated_at' => now()]
            );
            
            // Limpiar cache
            Cache::forget("config_{$nivel}_{$id}");
            
            \Log::info("Configuración guardada: $nivel:$id");
            return true;
            
        } catch (\Exception $e) {
            \Log::error("Error guardando configuración: " . $e->getMessage());
            return false;
        }
    }
    
    public function heredarConfiguracion(string $desdeNivel, int $desdeId, string $paraNivel = null): bool {
        try {
            $posicionDesde = array_search($desdeNivel, $this->niveles);
            
            if ($posicionDesde === false) {
                return false;
            }
            
            $configOrigen = $this->obtenerConfiguracion($desdeNivel, $desdeId);
            
            // Si se especifica nivel destino, heredar solo a ese nivel
            if ($paraNivel) {
                $posicionPara = array_search($paraNivel, $this->niveles);
                if ($posicionPara <= $posicionDesde) {
                    return false; // No se puede heredar hacia arriba
                }
                
                $hijos = $this->obtenerHijosDeNivel($desdeNivel, $desdeId);
                foreach ($hijos as $hijo) {
                    $this->guardarConfiguracion($paraNivel, $hijo->id, $configOrigen);
                }
            } else {
                // Heredar a todos los niveles inferiores
                for ($i = $posicionDesde + 1; $i < count($this->niveles); $i++) {
                    $nivelDestino = $this->niveles[$i];
                    $hijos = $this->obtenerHijosDeNivel($desdeNivel, $desdeId);
                    
                    foreach ($hijos as $hijo) {
                        $this->guardarConfiguracion($nivelDestino, $hijo->id, $configOrigen);
                    }
                }
            }
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error("Error heredando configuración: " . $e->getMessage());
            return false;
        }
    }
    
    private function obtenerHijosDeNivel(string $nivel, int $id): array {
        return match ($nivel) {
            'organizacion' => DB::table('escuelas')->where('organizacion_id', $id)->get()->toArray(),
            'escuela' => DB::table('sedes')->where('escuela_id', $id)->get()->toArray(),
            'sede' => DB::table('ciclos_escolares')->where('sede_id', $id)->get()->toArray(),
            default => []
        };
    }
    
    public function calcularMinimoProbatorio(int $sedeId): float {
        $config = $this->obtenerConfiguracion('sede', $sedeId);
        return $config['minimo_probatorio'] ?? 60.0;
    }
    
    public function verificarHerencia(string $parametro): array {
        try {
            $niveles = [];
            
            foreach ($this->niveles as $nivel) {
                $registros = DB::table('configuraciones')
                    ->where('nivel', $nivel)
                    ->where('valores', 'LIKE', "%$parametro%")
                    ->count();
                
                $niveles[$nivel] = $registros;
            }
            
            return $niveles;
            
        } catch (\Exception $e) {
            \Log::error("Error verificando herencia: " . $e->getMessage());
            return [];
        }
    }
}

<?php namespace App\Services;
use App\Models\Cargo;
use App\Models\Pago;
use App\Models\Parcialidad;
use Carbon\Carbon;

class CobranzaService {
    public function calcularAdeudos($alumnoId, $hasta = null) {
        $hasta = $hasta ?? now();
        $adeudos = Cargo::where('alumno_id', $alumnoId)
            ->where('estado', 'pendiente')
            ->where('created_at', '<=', $hasta)
            ->get();
        
        $total = $adeudos->sum('monto');
        $vencidos = $adeudos->filter(fn($c) => $c->fecha_vencimiento && $c->fecha_vencimiento < now())->count();
        
        return [
            'total_adeudado' => $total,
            'cantidad_cargos' => $adeudos->count(),
            'cargos_vencidos' => $vencidos,
            'items' => $adeudos
        ];
    }
    
    public function aplicarRecargo($cargoId, $porcentaje = 5) {
        $cargo = Cargo::find($cargoId);
        if (!$cargo) return false;
        
        $recargo = $cargo->monto * ($porcentaje / 100);
        $cargo->update([
            'monto' => $cargo->monto + $recargo,
            'observaciones' => "Recargo $porcentaje% aplicado el " . now()
        ]);
        return $cargo;
    }
    
    public function aplicarDescuento($cargoId, $descuento, $razon = null) {
        $cargo = Cargo::find($cargoId);
        if (!$cargo) return false;
        
        $nuevoMonto = max(0, $cargo->monto - $descuento);
        $cargo->update([
            'monto' => $nuevoMonto,
            'observaciones' => "Descuento: $descuento. Razón: $razon"
        ]);
        return $cargo;
    }
    
    public function generarParcialidades($cargoId, $numeroParcialidades) {
        $cargo = Cargo::find($cargoId);
        if (!$cargo) return false;
        
        $montoParcialidad = $cargo->monto / $numeroParcialidades;
        $parcialidades = [];
        
        for ($i = 1; $i <= $numeroParcialidades; $i++) {
            $fechaVencimiento = now()->addMonths($i);
            $parcialidades[] = Parcialidad::create([
                'cargo_id' => $cargoId,
                'numero_parcialidad' => $i,
                'total_parcialidades' => $numeroParcialidades,
                'monto' => $montoParcialidad,
                'fecha_vencimiento' => $fechaVencimiento,
                'estado' => 'pendiente'
            ]);
        }
        
        return $parcialidades;
    }
}

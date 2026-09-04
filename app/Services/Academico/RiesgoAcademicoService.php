<?php
namespace App\Services\Academico;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Asistencia;

/**
 * RiesgoAcademicoService — §29, §75
 * Motor de reglas para clasificar alumnos: normal / observacion / riesgo_medio / riesgo_alto
 */
class RiesgoAcademicoService
{
    // Umbrales configurables
    private float $minimoAprobatorio    = 6.0;
    private float $umbralBajoPromedio   = 7.0;
    private float $umbralBajaAsistencia = 0.80; // 80%
    private int   $minimoMateriasFaltas = 2;

    public function __construct(
        private readonly AsistenciaService $asistenciaService,
    ) {}

    /**
     * Calcula y persiste el estatus_riesgo del alumno para un ciclo.
     * Retorna el nivel: normal | observacion | riesgo_medio | riesgo_alto
     */
    public function calcular(Alumno $alumno, int $cicloId): string
    {
        $puntos = 0;

        // 1. Materias reprobadas
        $reprobadas = Calificacion::where('alumno_id', $alumno->id)
            ->where('ciclo_escolar_id', $cicloId)
            ->where('resultado', 'reprobado')
            ->count();
        if ($reprobadas >= $this->minimoMateriasFaltas) $puntos += 2;
        elseif ($reprobadas >= 1) $puntos += 1;

        // 2. Promedio general
        $promedio = Calificacion::where('alumno_id', $alumno->id)
            ->where('ciclo_escolar_id', $cicloId)
            ->whereNotNull('calificacion')
            ->avg('calificacion') ?? 10.0;
        if ($promedio < $this->minimoAprobatorio) $puntos += 3;
        elseif ($promedio < $this->umbralBajoPromedio) $puntos += 1;

        // 3. Asistencia
        $pctAsistencia = $this->asistenciaService->calcularPorcentaje($alumno->id, $cicloId);
        if ($pctAsistencia < 0.70) $puntos += 3;
        elseif ($pctAsistencia < $this->umbralBajaAsistencia) $puntos += 2;

        // 4. Regularizaciones pendientes
        $regPendientes = \App\Models\Regularizacion::where('alumno_id', $alumno->id)
            ->where('ciclo_escolar_id', $cicloId)
            ->where('resultado', 'pendiente')
            ->count();
        if ($regPendientes >= 2) $puntos += 2;
        elseif ($regPendientes >= 1) $puntos += 1;

        // Clasificar
        $nivel = match(true) {
            $puntos >= 6 => 'riesgo_alto',
            $puntos >= 4 => 'riesgo_medio',
            $puntos >= 2 => 'observacion',
            default      => 'normal',
        };

        // Persistir en el alumno
        $alumno->withoutAudit(fn($a) => $a->update(['estatus_riesgo' => $nivel]));

        return $nivel;
    }

    /**
     * Calcula riesgo para todos los alumnos activos de una sede/ciclo.
     * Retorna conteo por nivel.
     */
    public function calcularMasivo(int $sedeId, int $cicloId): array
    {
        $conteo = ['normal'=>0,'observacion'=>0,'riesgo_medio'=>0,'riesgo_alto'=>0];

        Alumno::where('sede_actual_id', $sedeId)
            ->where('estatus', 'activo')
            ->chunk(100, function ($alumnos) use ($cicloId, &$conteo) {
                foreach ($alumnos as $alumno) {
                    $nivel = $this->calcular($alumno, $cicloId);
                    $conteo[$nivel]++;
                }
            });

        return $conteo;
    }
}

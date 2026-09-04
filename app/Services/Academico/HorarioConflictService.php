<?php
namespace App\Services\Academico;

use App\Models\Horario;
use App\Models\Aula;

/**
 * HorarioConflictService — §37
 * Detecta colisiones antes de crear/editar un horario.
 */
class HorarioConflictService
{
    /**
     * Retorna array de conflictos. Vacío = sin conflictos.
     */
    public function verificar(array $datos, ?int $exceptoId = null): array
    {
        $conflictos = [];

        $base = Horario::where('ciclo_escolar_id', $datos['ciclo_escolar_id'])
            ->where('dia_semana', $datos['dia_semana'])
            ->where('hora_inicio', '<', $datos['hora_fin'])
            ->where('hora_fin', '>', $datos['hora_inicio'])
            ->whereNull('deleted_at')
            ->when($exceptoId, fn($q) => $q->where('id', '!=', $exceptoId));

        // Docente ocupado
        if ((clone $base)->where('docente_id', $datos['docente_id'])->exists()) {
            $conflictos[] = 'El docente ya tiene clase en ese bloque horario.';
        }

        // Aula ocupada
        if (!empty($datos['aula_id']) && (clone $base)->where('aula_id', $datos['aula_id'])->exists()) {
            $conflictos[] = 'El aula ya está ocupada en ese bloque horario.';
        }

        // Grupo ocupado
        if ((clone $base)->where('grupo_id', $datos['grupo_id'])->exists()) {
            $conflictos[] = 'El grupo ya tiene clase en ese bloque horario.';
        }

        // Materia duplicada en el grupo
        if ((clone $base)->where('grupo_id', $datos['grupo_id'])->where('materia_id', $datos['materia_id'])->exists()) {
            $conflictos[] = 'La materia ya está asignada al grupo en ese ciclo.';
        }

        // Capacidad del aula
        if (!empty($datos['aula_id'])) {
            $aula = Aula::find($datos['aula_id']);
            if ($aula) {
                $grupo = \App\Models\Grupo::find($datos['grupo_id']);
                if ($grupo && $grupo->capacidad > $aula->capacidad) {
                    $conflictos[] = "La capacidad del aula ({$aula->capacidad}) es menor que el grupo ({$grupo->capacidad}).";
                }
            }
        }

        return $conflictos;
    }

    public function sinConflictos(array $datos, ?int $exceptoId = null): bool
    {
        return empty($this->verificar($datos, $exceptoId));
    }
}

"""
Worker de riesgo académico §29, §75
Motor de reglas + futuro modelo predictivo ML
"""

import logging
log = logging.getLogger("sistema_escolar.riesgo")


async def calcular(data: dict) -> dict:
    """
    Entrada: sede_id, ciclo_id, alumnos (lista de dicts con calificaciones/asistencias)
    Salida:  clasificación por nivel de riesgo
    """
    sede_id  = data.get("sede_id")
    ciclo_id = data.get("ciclo_id")
    alumnos  = data.get("alumnos", [])

    log.info(f"Calculando riesgo sede={sede_id} alumnos={len(alumnos)}")

    resultados = {"normal": 0, "observacion": 0, "riesgo_medio": 0, "riesgo_alto": 0}
    clasificaciones = []

    for alumno in alumnos:
        puntos = 0

        # Calificaciones
        cal_promedio = alumno.get("promedio", 10.0)
        if cal_promedio < 6.0:  puntos += 3
        elif cal_promedio < 7.0: puntos += 1

        # Materias reprobadas
        rep = alumno.get("materias_reprobadas", 0)
        if rep >= 2:  puntos += 2
        elif rep >= 1: puntos += 1

        # Asistencia
        asist = alumno.get("pct_asistencia", 1.0)
        if asist < 0.70:  puntos += 3
        elif asist < 0.80: puntos += 2

        # Clasificar
        if puntos >= 6:   nivel = "riesgo_alto"
        elif puntos >= 4: nivel = "riesgo_medio"
        elif puntos >= 2: nivel = "observacion"
        else:             nivel = "normal"

        resultados[nivel] += 1
        clasificaciones.append({"alumno_id": alumno.get("id"), "nivel": nivel, "puntos": puntos})

    return {
        "sede_id":         sede_id,
        "ciclo_id":        ciclo_id,
        "resumen":         resultados,
        "clasificaciones": clasificaciones,
    }

"""
Tests básicos de los workers Python §99
Ejecutar: cd python && python -m pytest tests/ -v
"""

import asyncio
import pytest
import sys
import os

sys.path.insert(0, os.path.join(os.path.dirname(__file__), ".."))


class TestEstadisticas:
    def test_calcular_retorna_estructura_correcta(self):
        from app.workers.estadisticas import calcular
        data = {"sede_id": 1, "ciclo_id": 1}
        result = asyncio.run(calcular(data))
        assert "approval_rate" in result
        assert "failure_rate" in result
        assert "dropout_rate" in result

    def test_calcular_con_datos_vacios(self):
        from app.workers.estadisticas import calcular
        result = asyncio.run(calcular({}))
        assert result is not None


class TestRiesgo:
    def test_clasificacion_riesgo_alto(self):
        from app.workers.riesgo import calcular
        data = {
            "sede_id": 1, "ciclo_id": 1,
            "alumnos": [
                {"id": 1, "promedio": 4.5, "materias_reprobadas": 3, "pct_asistencia": 0.55},
            ]
        }
        result = asyncio.run(calcular(data))
        assert result["clasificaciones"][0]["nivel"] == "riesgo_alto"

    def test_clasificacion_normal(self):
        from app.workers.riesgo import calcular
        data = {
            "sede_id": 1, "ciclo_id": 1,
            "alumnos": [
                {"id": 2, "promedio": 9.0, "materias_reprobadas": 0, "pct_asistencia": 0.95},
            ]
        }
        result = asyncio.run(calcular(data))
        assert result["clasificaciones"][0]["nivel"] == "normal"

    def test_resumen_suma_correctamente(self):
        from app.workers.riesgo import calcular
        alumnos = [
            {"id": 1, "promedio": 4.0, "materias_reprobadas": 3, "pct_asistencia": 0.50},
            {"id": 2, "promedio": 9.5, "materias_reprobadas": 0, "pct_asistencia": 0.99},
        ]
        result = asyncio.run(calcular({"alumnos": alumnos}))
        total = sum(result["resumen"].values())
        assert total == len(alumnos)


class TestHorarios:
    def test_propuesta_con_grupos(self):
        from app.workers.horarios import optimizar
        data = {
            "grupos": [{"id": 1, "materias": [{"id": 1}, {"id": 2}]}],
            "docentes": [], "aulas": []
        }
        result = asyncio.run(optimizar(data))
        assert "propuesta" in result
        assert len(result["propuesta"]) == 2

    def test_propuesta_sin_grupos(self):
        from app.workers.horarios import optimizar
        result = asyncio.run(optimizar({"grupos": []}))
        assert result["propuesta"] == []

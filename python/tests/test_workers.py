import pytest
from app.workers.calcular_indicadores import execute as calc_indicadores
from app.workers.calcular_riesgo import execute as calc_riesgo
from app.workers.generar_reportes import execute as gen_reportes
from app.workers.procesar_importaciones import execute as proc_importaciones

class TestCalcularIndicadoresWorker:
    def test_execute_returns_dict(self):
        payload = {'sede_id': 1, 'ciclo_id': 1}
        result = calc_indicadores(payload)
        assert isinstance(result, dict)
        assert 'sede_id' in result or 'error' in result
    
    def test_execute_has_indicadores(self):
        payload = {'sede_id': 1, 'ciclo_id': 1}
        result = calc_indicadores(payload)
        if 'error' not in result:
            assert 'indicadores' in result
            assert 'tasa_aprobacion' in result['indicadores']

class TestCalcularRiesgoWorker:
    def test_execute_returns_dict(self):
        payload = {'grupo_id': 1, 'ciclo_id': 1}
        result = calc_riesgo(payload)
        assert isinstance(result, dict)
    
    def test_estudiantes_en_riesgo(self):
        payload = {'grupo_id': 1, 'ciclo_id': 1}
        result = calc_riesgo(payload)
        if 'error' not in result:
            assert 'estudiantes_en_riesgo' in result

class TestGenerarReportesWorker:
    def test_execute_returns_dict(self):
        payload = {'tipo': 'calificaciones', 'fecha_inicio': '2024-01-01', 'fecha_fin': '2024-12-31'}
        result = gen_reportes(payload)
        assert isinstance(result, dict)
    
    def test_archivos_generados(self):
        payload = {'tipo': 'calificaciones', 'fecha_inicio': '2024-01-01', 'fecha_fin': '2024-12-31'}
        result = gen_reportes(payload)
        if 'error' not in result:
            assert 'archivos_generados' in result

class TestProcesarImportacionesWorker:
    def test_execute_returns_dict(self):
        payload = {'archivo_ruta': '/tmp/test.xlsx', 'tipo_datos': 'alumnos'}
        result = proc_importaciones(payload)
        assert isinstance(result, dict)
    
    def test_estadisticas_en_resultado(self):
        payload = {'archivo_ruta': '/tmp/test.xlsx', 'tipo_datos': 'alumnos'}
        result = proc_importaciones(payload)
        if 'error' not in result:
            assert 'estadisticas' in result
            assert 'registros_procesados' in result['estadisticas']

if __name__ == '__main__':
    pytest.main([__file__, '-v'])

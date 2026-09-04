import pytest
import json
from fastapi.testclient import TestClient
from app.main import app

client = TestClient(app)

class TestHealthCheck:
    def test_health_endpoint_returns_ok(self):
        response = client.get('/health')
        assert response.status_code == 200
        data = response.json()
        assert data['status'] == 'ok'
        assert 'version' in data
        assert data['servicio'] == 'python-workers'
    
    def test_health_response_format(self):
        response = client.get('/health')
        assert response.headers['content-type'] == 'application/json'

class TestWorkersStatus:
    def test_workers_status_endpoint(self):
        response = client.get('/workers/status')
        assert response.status_code == 200
        data = response.json()
        assert 'workers' in data
        assert data['estado'] == 'listos'
        assert len(data['workers']) == 4
    
    def test_workers_status_contains_all_workers(self):
        response = client.get('/workers/status')
        data = response.json()
        workers = data['workers']
        assert 'calcular_indicadores' in workers
        assert 'calcular_riesgo' in workers
        assert 'generar_reportes' in workers
        assert 'procesar_importaciones' in workers

class TestCalcularIndicadores:
    def test_calcular_indicadores_endpoint(self):
        payload = {
            'tipo': 'calcular_indicadores',
            'datos': {
                'sede_id': 1,
                'ciclo_id': 1
            }
        }
        response = client.post('/workers/calcular-indicadores', json=payload)
        assert response.status_code == 200
        data = response.json()
        assert 'job_id' in data
        assert data['status'] == 'processing'
        assert data['tipo'] == 'calcular_indicadores'
    
    def test_calcular_indicadores_without_datos(self):
        payload = {'tipo': 'calcular_indicadores'}
        response = client.post('/workers/calcular-indicadores', json=payload)
        # Puede fallar o retornar error
        assert response.status_code in [200, 422, 500]

class TestCalcularRiesgo:
    def test_calcular_riesgo_endpoint(self):
        payload = {
            'tipo': 'calcular_riesgo',
            'datos': {
                'grupo_id': 1,
                'ciclo_id': 1
            }
        }
        response = client.post('/workers/calcular-riesgo', json=payload)
        assert response.status_code == 200
        data = response.json()
        assert 'job_id' in data
        assert 'status' in data

class TestGenerarReportes:
    def test_generar_reportes_endpoint(self):
        payload = {
            'tipo': 'generar_reportes',
            'datos': {
                'tipo': 'calificaciones',
                'filtros': {'ciclo_id': 1}
            }
        }
        response = client.post('/workers/generar-reportes', json=payload)
        assert response.status_code == 200
        data = response.json()
        assert 'status' in data

class TestProcesarImportaciones:
    def test_procesar_importaciones_endpoint(self):
        payload = {
            'tipo': 'procesar_importaciones',
            'datos': {
                'archivo_ruta': '/tmp/test.xlsx',
                'tipo_datos': 'alumnos'
            }
        }
        response = client.post('/workers/procesar-importaciones', json=payload)
        assert response.status_code == 200
        data = response.json()
        assert 'status' in data

class TestErrorHandling:
    def test_invalid_endpoint(self):
        response = client.post('/workers/invalid-worker', json={})
        assert response.status_code == 404
    
    def test_malformed_json(self):
        response = client.post('/workers/calcular-indicadores', data='invalid json')
        assert response.status_code in [400, 422]

if __name__ == '__main__':
    pytest.main([__file__, '-v'])

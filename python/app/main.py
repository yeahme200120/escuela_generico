from fastapi import FastAPI, HTTPException, BackgroundTasks
from pydantic import BaseModel
from typing import List, Optional
import asyncio
import logging

app = FastAPI(
    title='Sistema Escolar - Python Workers',
    description='Microservicio de procesamiento asincronico',
    version='1.0.0'
)

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class TrabajoProcesamiento(BaseModel):
    tipo: str
    datos: dict
    usuario_id: Optional[int] = None

@app.get('/health')
def health():
    return {'status': 'ok', 'version': '1.0.0', 'servicio': 'python-workers'}

@app.get('/workers/status')
def status_workers():
    return {
        'workers': [
            'calcular_indicadores',
            'calcular_riesgo',
            'generar_reportes',
            'procesar_importaciones'
        ],
        'estado': 'listos',
        'version': '1.0.0'
    }

@app.post('/workers/calcular-indicadores')
def calcular_indicadores(payload: TrabajoProcesamiento, background_tasks: BackgroundTasks):
    logger.info(f"Trabajo recibido: {payload.tipo}")
    try:
        from app.workers.calcular_indicadores import execute
        background_tasks.add_task(execute, payload.datos)
        return {
            'job_id': 'pending',
            'status': 'processing',
            'tipo': payload.tipo,
            'mensaje': 'Procesando indicadores academicos'
        }
    except Exception as e:
        logger.error(f"Error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post('/workers/calcular-riesgo')
def calcular_riesgo(payload: TrabajoProcesamiento, background_tasks: BackgroundTasks):
    logger.info(f"Trabajo recibido: {payload.tipo}")
    try:
        from app.workers.calcular_riesgo import execute
        background_tasks.add_task(execute, payload.datos)
        return {
            'job_id': 'pending',
            'status': 'processing',
            'tipo': payload.tipo,
            'mensaje': 'Calculando riesgo academico'
        }
    except Exception as e:
        logger.error(f"Error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post('/workers/generar-reportes')
def generar_reportes(payload: TrabajoProcesamiento, background_tasks: BackgroundTasks):
    logger.info(f"Trabajo recibido: {payload.tipo}")
    try:
        from app.workers.generar_reportes import execute
        background_tasks.add_task(execute, payload.datos)
        return {
            'job_id': 'pending',
            'status': 'processing',
            'tipo': payload.tipo,
            'mensaje': 'Generando reportes masivos'
        }
    except Exception as e:
        logger.error(f"Error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post('/workers/procesar-importaciones')
def procesar_importaciones(payload: TrabajoProcesamiento, background_tasks: BackgroundTasks):
    logger.info(f"Trabajo recibido: {payload.tipo}")
    try:
        from app.workers.procesar_importaciones import execute
        background_tasks.add_task(execute, payload.datos)
        return {
            'job_id': 'pending',
            'status': 'processing',
            'tipo': payload.tipo,
            'mensaje': 'Procesando importacion de datos'
        }
    except Exception as e:
        logger.error(f"Error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == '__main__':
    import uvicorn
    uvicorn.run(app, host='0.0.0.0', port=8001, log_level='info')

from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import uvicorn

app = FastAPI(title='Sistema Escolar - Python Workers', version='1.0')

@app.get('/health')
def health():
    return {'status': 'ok', 'version': '1.0'}

@app.post('/workers/calcular-indicadores')
def calcular_indicadores(payload: dict):
    return {'job_id': 'pending', 'status': 'processing'}

@app.post('/workers/calcular-riesgo')
def calcular_riesgo(payload: dict):
    return {'job_id': 'pending', 'status': 'processing'}

if __name__ == '__main__':
    uvicorn.run(app, host='0.0.0.0', port=8001)

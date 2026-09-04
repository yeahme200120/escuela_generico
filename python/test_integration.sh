#!/bin/bash
# Test Integration Script for Sistema Escolar Python Workers

echo "==================================================================="
echo "TESTING SISTEMA ESCOLAR PYTHON WORKERS"
echo "==================================================================="
echo ""

API_URL="http://localhost:8001"
SECRET="dev-secret-key"

echo "[1/5] Health Check..."
curl -s -X GET "/health" \
  -H "X-Python-Secret: \" | jq .
echo ""

echo "[2/5] Workers Status..."
curl -s -X GET "/workers/status" \
  -H "X-Python-Secret: \" | jq .
echo ""

echo "[3/5] Testing calcular_indicadores..."
curl -s -X POST "/workers/calcular-indicadores" \
  -H "X-Python-Secret: \" \
  -H "Content-Type: application/json" \
  -d '{"tipo":"calcular_indicadores","datos":{"sede_id":1,"ciclo_id":1}}' | jq .
echo ""

echo "[4/5] Testing calcular_riesgo..."
curl -s -X POST "/workers/calcular-riesgo" \
  -H "X-Python-Secret: \" \
  -H "Content-Type: application/json" \
  -d '{"tipo":"calcular_riesgo","datos":{"grupo_id":1,"ciclo_id":1}}' | jq .
echo ""

echo "[5/5] Testing generar_reportes..."
curl -s -X POST "/workers/generar-reportes" \
  -H "X-Python-Secret: \" \
  -H "Content-Type: application/json" \
  -d '{"tipo":"generar_reportes","datos":{"tipo":"calificaciones","fecha_inicio":"2024-01-01","fecha_fin":"2024-12-31"}}' | jq .
echo ""

echo "==================================================================="
echo "TESTS COMPLETADOS"
echo "==================================================================="

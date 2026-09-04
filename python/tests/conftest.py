import pytest
import os
from pathlib import Path

# Agregar app al path
sys.path.insert(0, str(Path(__file__).parent.parent))

@pytest.fixture
def test_data():
    return {
        'sede_id': 1,
        'ciclo_id': 1,
        'grupo_id': 1,
        'alumno_id': 1
    }

@pytest.fixture
def mock_db():
    """Mock database for testing"""
    return {}

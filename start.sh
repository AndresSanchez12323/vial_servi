#!/bin/bash
# Script de inicio para el Rellenador Automático de Formularios

echo "=========================================="
echo "  Rellenador Automático de Formularios"
echo "          VialServi v1.0"
echo "=========================================="
echo ""

# Verificar si Python está instalado
if ! command -v python3 &> /dev/null
then
    echo "❌ Python 3 no está instalado"
    echo "Por favor, instala Python 3 desde https://www.python.org/downloads/"
    exit 1
fi

echo "✓ Python 3 encontrado: $(python3 --version)"
echo ""

# Verificar si las dependencias están instaladas
echo "Verificando dependencias..."
if python3 -c "import selenium" 2>/dev/null; then
    echo "✓ Selenium instalado"
else
    echo "❌ Selenium no encontrado"
    echo "Instalando dependencias..."
    pip install -r requirements.txt
fi

if python3 -c "import faker" 2>/dev/null; then
    echo "✓ Faker instalado"
else
    echo "❌ Faker no encontrado"
    echo "Instalando dependencias..."
    pip install -r requirements.txt
fi

echo ""
echo "Iniciando el programa..."
echo ""

# Ejecutar el script principal
python3 auto_form_filler.py

echo ""
echo "Programa finalizado"

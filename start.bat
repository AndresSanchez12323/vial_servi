@echo off
REM Script de inicio para el Rellenador Automático de Formularios (Windows)

echo ==========================================
echo   Rellenador Automático de Formularios
echo           VialServi v1.0
echo ==========================================
echo.

REM Verificar si Python está instalado
python --version >nul 2>&1
if %errorlevel% neq 0 (
    echo X Python no está instalado
    echo Por favor, instala Python desde https://www.python.org/downloads/
    pause
    exit /b 1
)

echo [OK] Python encontrado
echo.

REM Verificar dependencias
echo Verificando dependencias...
python -c "import selenium" >nul 2>&1
if %errorlevel% neq 0 (
    echo [!] Selenium no encontrado
    echo Instalando dependencias...
    pip install -r requirements.txt
) else (
    echo [OK] Selenium instalado
)

python -c "import faker" >nul 2>&1
if %errorlevel% neq 0 (
    echo [!] Faker no encontrado
    echo Instalando dependencias...
    pip install -r requirements.txt
) else (
    echo [OK] Faker instalado
)

echo.
echo Iniciando el programa...
echo.

REM Ejecutar el script principal
python auto_form_filler.py

echo.
echo Programa finalizado
pause

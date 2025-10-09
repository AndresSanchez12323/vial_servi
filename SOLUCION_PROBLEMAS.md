# Guía de Solución de Problemas

Esta guía te ayudará a resolver los problemas más comunes al usar el rellenador automático de formularios.

## 🔍 Diagnóstico Rápido

### Paso 1: Verificar Python

```bash
python --version
# o
python3 --version
```

**Resultado esperado**: `Python 3.7.x` o superior

**Si falla**: 
- Windows: Descarga desde https://www.python.org/downloads/
- Mac: `brew install python3`
- Linux: `sudo apt install python3`

### Paso 2: Verificar Dependencias

```bash
python -c "import selenium; print('✓ Selenium OK')"
python -c "import faker; print('✓ Faker OK')"
python -c "import dotenv; print('✓ Dotenv OK')"
```

**Si falla alguna**:
```bash
pip install -r requirements.txt
```

### Paso 3: Verificar Chrome

Abre Google Chrome y ve a: `chrome://version/`

**Si Chrome no está instalado**:
- Windows/Mac: https://www.google.com/chrome/
- Linux: `sudo apt install google-chrome-stable`

### Paso 4: Verificar Servidor Web

Abre tu navegador y ve a: `http://localhost`

**Si no carga**:
- Inicia XAMPP/WAMP/MAMP
- O inicia un servidor PHP: `php -S localhost:8000`

## ❌ Errores Comunes

### Error 1: "python: command not found"

**Síntoma**:
```bash
$ python auto_form_filler.py
python: command not found
```

**Soluciones**:
1. Intenta con `python3`:
   ```bash
   python3 auto_form_filler.py
   ```

2. Verifica la instalación:
   ```bash
   which python3
   ```

3. Agrega Python al PATH (Windows):
   - Busca "Variables de entorno" en el menú de inicio
   - Edita la variable "Path"
   - Agrega la ruta de instalación de Python

### Error 2: "No module named 'selenium'"

**Síntoma**:
```bash
ModuleNotFoundError: No module named 'selenium'
```

**Soluciones**:
1. Instala las dependencias:
   ```bash
   pip install selenium faker python-dotenv
   ```

2. Si usas pip3:
   ```bash
   pip3 install selenium faker python-dotenv
   ```

3. Verifica la instalación:
   ```bash
   pip list | grep selenium
   ```

### Error 3: "ChromeDriver executable needs to be in PATH"

**Síntoma**:
```bash
selenium.common.exceptions.WebDriverException: Message: 'chromedriver' executable needs to be in PATH
```

**Soluciones**:

**Opción A** (Recomendada - Automática):
```bash
pip install webdriver-manager
```

Luego modifica `auto_form_filler.py` para usar:
```python
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.chrome.service import Service

service = Service(ChromeDriverManager().install())
self.driver = webdriver.Chrome(service=service, options=options)
```

**Opción B** (Manual):
1. Descarga ChromeDriver: https://chromedriver.chromium.org/downloads
2. Elige la versión que coincida con tu Chrome
3. Extrae el archivo
4. Agrega al PATH o colócalo en la carpeta del proyecto

### Error 4: "Connection refused" o "Cannot connect to URL"

**Síntoma**:
```bash
urllib3.exceptions.MaxRetryError: HTTPConnectionPool(host='localhost', port=80): Max retries exceeded
```

**Soluciones**:
1. Verifica que el servidor esté corriendo:
   ```bash
   curl http://localhost
   ```

2. Si usas un puerto diferente (ejemplo: 8080):
   ```bash
   python auto_form_filler.py
   # Cuando pregunte, ingresa: http://localhost:8080
   ```

3. Verifica el estado del servidor:
   - XAMPP: Abre el panel de control y verifica que Apache esté en verde
   - WAMP: Verifica que el icono de la bandeja sea verde
   - PHP: `php -S localhost:8000` en una terminal separada

### Error 5: "No such element: Unable to locate element"

**Síntoma**:
```bash
selenium.common.exceptions.NoSuchElementException: Message: no such element: Unable to locate element: {"method":"css selector","selector":"#nombre"}
```

**Posibles causas y soluciones**:

1. **La página no cargó completamente**:
   - Aumenta el tiempo de espera en el código
   - Verifica tu conexión a internet

2. **El formulario cambió**:
   - Verifica que los IDs de los campos sean correctos
   - Usa las herramientas de desarrollo del navegador (F12) para inspeccionar

3. **URL incorrecta**:
   - Verifica que estés accediendo a la URL correcta
   - Ejemplo: `http://localhost/registro_cliente.php`

### Error 6: "Session not created: Chrome version must be between..."

**Síntoma**:
```bash
selenium.common.exceptions.SessionNotCreatedException: Message: session not created: Chrome version must be between 114 and 118
```

**Solución**:
1. Actualiza Chrome a la última versión
2. O actualiza Selenium:
   ```bash
   pip install --upgrade selenium
   ```

3. O usa webdriver-manager (instalación automática):
   ```bash
   pip install webdriver-manager
   ```

### Error 7: "Permission denied" (Linux/Mac)

**Síntoma**:
```bash
bash: ./start.sh: Permission denied
```

**Solución**:
```bash
chmod +x start.sh
./start.sh
```

### Error 8: "JSONDecodeError: Expecting value"

**Síntoma**:
```bash
json.decoder.JSONDecodeError: Expecting value: line 1 column 1 (char 0)
```

**Soluciones**:
1. Verifica que `form_data.json` existe
2. Verifica que el JSON sea válido:
   ```bash
   python -c "import json; json.load(open('form_data.json'))"
   ```

3. Si el archivo está corrupto, copia el contenido de ejemplo:
   ```json
   {
     "registro_cliente": {
       "nombre": "Juan",
       "apellido": "Pérez",
       "cedula": "1234567890",
       "telefono": "3001234567",
       "email": "juan.perez@example.com",
       "password": "MiContraseña123",
       "confirm_password": "MiContraseña123"
     },
     "contactenos": {
       "nombre": "María González",
       "email": "maria.gonzalez@example.com",
       "mensaje": "Hola, me gustaría obtener más información."
     }
   }
   ```

### Error 9: El navegador se cierra inmediatamente

**Síntoma**: El navegador Chrome se abre y se cierra al instante.

**Soluciones**:
1. Revisa los errores en la consola
2. Desactiva el modo headless editando `.env`:
   ```
   HEADLESS=false
   ```

3. Agrega pausas para debugging:
   ```python
   time.sleep(10)  # Después de abrir el navegador
   ```

### Error 10: "Invalid password" o "Contraseña inválida"

**Síntoma**: El formulario rechaza la contraseña.

**Solución**:
Asegúrate de que las contraseñas cumplan los requisitos:
- Mínimo 8 caracteres (en `registro_cliente.php`)
- Mínimo 6 caracteres (en `registro.php`)

Edita `form_data.json`:
```json
"password": "MiContraseña123",
"confirm_password": "MiContraseña123"
```

## 🔧 Debugging Avanzado

### Modo Verbose

Agrega prints en el código para ver qué está pasando:

```python
print(f"DEBUG: Navegando a {url}")
print(f"DEBUG: Buscando elemento con ID: {element_id}")
print(f"DEBUG: Valor a ingresar: {value}")
```

### Captura de Pantalla

Agrega esto después de un error:

```python
try:
    # ... tu código ...
except Exception as e:
    self.driver.save_screenshot('error.png')
    print(f"Error capturado en error.png: {e}")
```

### Ver el HTML de la página

```python
print(self.driver.page_source)
```

### Ver los logs del navegador

```python
for entry in self.driver.get_log('browser'):
    print(entry)
```

## 🆘 Comandos Útiles para Debugging

### Ver logs de Selenium
```bash
python auto_form_filler.py 2>&1 | tee debug.log
```

### Verificar conectividad
```bash
ping localhost
curl -I http://localhost
```

### Ver procesos de Chrome
```bash
# Windows
tasklist | findstr chrome

# Linux/Mac
ps aux | grep chrome
```

### Limpiar caché de pip
```bash
pip cache purge
pip install --no-cache-dir -r requirements.txt
```

## 📞 ¿Aún tienes problemas?

1. **Revisa la documentación completa**: `README_AUTO_FORM_FILLER.md`
2. **Verifica los ejemplos**: `ejemplo_uso.py`
3. **Lee la guía rápida**: `GUIA_RAPIDA.md`

### Información útil para reportar un problema:

```bash
# Versión de Python
python --version

# Versión de Chrome
google-chrome --version  # Linux
# o abre Chrome y ve a chrome://version/

# Versiones de librerías
pip list | grep -E "(selenium|faker)"

# Sistema operativo
uname -a  # Linux/Mac
systeminfo  # Windows
```

## ✅ Checklist de Verificación Completa

Antes de reportar un problema, verifica:

- [ ] Python 3.7+ instalado y funcionando
- [ ] Google Chrome instalado y actualizado
- [ ] Dependencias instaladas (`pip list`)
- [ ] Servidor web corriendo (`curl http://localhost`)
- [ ] Puedes acceder manualmente a los formularios en el navegador
- [ ] Los archivos del proyecto están intactos
- [ ] No hay errores de sintaxis (`python -m py_compile auto_form_filler.py`)
- [ ] `form_data.json` es válido
- [ ] No hay conflictos de puerto
- [ ] Firewall no está bloqueando el acceso

## 💡 Tips de Prevención

1. **Mantén todo actualizado**:
   ```bash
   pip install --upgrade selenium faker python-dotenv
   ```

2. **Usa entornos virtuales**:
   ```bash
   python -m venv venv
   source venv/bin/activate  # Linux/Mac
   venv\Scripts\activate  # Windows
   ```

3. **Haz backups de `form_data.json`** antes de editarlo

4. **Prueba primero en modo sin enviar** (responde 'n' cuando pregunte)

5. **Cierra otros navegadores Chrome** antes de ejecutar

---

**Si ninguna solución funcionó**, por favor contacta con el equipo de desarrollo proporcionando:
- Mensaje de error completo
- Sistema operativo y versión
- Versiones de Python, Chrome y Selenium
- Pasos que seguiste antes del error

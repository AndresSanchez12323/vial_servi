# Guía Rápida de Inicio - Rellenador Automático de Formularios

Esta es una guía rápida para comenzar a usar el rellenador automático de formularios en menos de 5 minutos.

## 🚀 Inicio Rápido

### 1. Instalar Python (si no lo tienes)

#### Windows:
1. Descarga Python desde [python.org](https://www.python.org/downloads/)
2. Durante la instalación, marca "Add Python to PATH"
3. Verifica: Abre CMD y escribe `python --version`

#### macOS:
```bash
brew install python3
```

#### Linux (Ubuntu/Debian):
```bash
sudo apt update
sudo apt install python3 python3-pip
```

### 2. Instalar dependencias

Abre una terminal/CMD en la carpeta del proyecto y ejecuta:

```bash
pip install -r requirements.txt
```

O instala manualmente:
```bash
pip install selenium faker python-dotenv
```

### 3. Instalar Google Chrome

Si no tienes Chrome instalado:
- Windows/Mac: Descarga desde [google.com/chrome](https://www.google.com/chrome/)
- Linux: `sudo apt install google-chrome-stable`

### 4. ¡Ejecutar el programa!

```bash
python auto_form_filler.py
```

## 📖 Uso Básico

### Opción 1: Uso Interactivo (Recomendado para principiantes)

```bash
python auto_form_filler.py
```

Sigue las instrucciones en pantalla:
1. Ingresa la URL (ejemplo: `http://localhost`)
2. Selecciona qué formulario quieres rellenar
3. El programa rellena automáticamente y te pregunta si deseas enviar

### Opción 2: Ejemplos Programáticos

```bash
python ejemplo_uso.py
```

Selecciona uno de los ejemplos pre-configurados.

### Opción 3: Personalizar datos

Edita el archivo `form_data.json` con tus datos:

```json
{
  "registro_cliente": {
    "nombre": "TU NOMBRE",
    "apellido": "TU APELLIDO",
    "cedula": "1234567890",
    "telefono": "3001234567",
    "email": "tu@email.com",
    "password": "tuPassword123",
    "confirm_password": "tuPassword123"
  }
}
```

Luego ejecuta:
```bash
python auto_form_filler.py
# Selecciona opción con 'f' (ejemplo: 1f para usar datos del archivo)
```

## ⚡ Comandos Rápidos

```bash
# Instalar todo
pip install -r requirements.txt

# Ejecutar en modo interactivo
python auto_form_filler.py

# Ver ejemplos
python ejemplo_uso.py

# Verificar instalación
python -c "import selenium; print('✓ Selenium instalado')"
python -c "import faker; print('✓ Faker instalado')"
```

## 🛠️ Solución Rápida de Problemas

### "python: command not found"
**Solución**: Intenta con `python3` en lugar de `python`

### "No module named 'selenium'"
**Solución**: Instala las dependencias
```bash
pip install selenium faker python-dotenv
```

### "ChromeDriver not found"
**Solución**: El navegador Chrome debe estar instalado. Si el error persiste:
```bash
pip install webdriver-manager
```

### "Connection refused" o "Cannot connect"
**Solución**: 
1. Verifica que tu servidor web esté corriendo
2. Asegúrate de que la URL sea correcta (incluye el puerto si no es 80)
3. Ejemplo: `http://localhost:8080` en lugar de `http://localhost`

## 💡 Consejos

1. **Primera vez**: Usa datos aleatorios (opción sin 'f') para probar
2. **Modo prueba**: Cuando el script pregunte "¿Desea enviar?", responde 'n' para solo ver cómo se rellena
3. **Personalización**: Edita `form_data.json` para usar tus propios datos de prueba
4. **Múltiples pruebas**: Usa datos aleatorios para generar diferentes usuarios de prueba

## 📺 Video Tutorial (Paso a Paso)

### Paso 1: Instalación
```bash
# Terminal 1: Instalar dependencias
cd vial_servi
pip install -r requirements.txt
```

### Paso 2: Iniciar servidor web
```bash
# Terminal 2: Inicia tu servidor (ejemplo: XAMPP, WAMP, o servidor PHP)
php -S localhost:8000
```

### Paso 3: Ejecutar el rellenador
```bash
# Terminal 1: Ejecutar el script
python auto_form_filler.py

# Cuando pregunte la URL, ingresa: http://localhost:8000
# Selecciona opción 1 (Registro de cliente)
# Espera a que se rellene el formulario
# Decide si enviar o no
```

## 🎓 Ejemplos de Flujos Completos

### Ejemplo 1: Probar el registro de un cliente
```bash
python auto_form_filler.py
# URL: http://localhost
# Opción: 1 (datos aleatorios)
# ¿Enviar?: n (solo prueba)
```

### Ejemplo 2: Enviar un mensaje de contacto
```bash
python auto_form_filler.py
# URL: http://localhost
# Opción: 2f (datos del archivo)
# ¿Enviar?: s (enviar)
```

### Ejemplo 3: Rellenar múltiples formularios
```bash
python auto_form_filler.py
# URL: http://localhost
# Opción: 3 (todos los formularios con datos aleatorios)
# ¿Enviar?: s (cada uno)
```

## 📝 Notas Importantes

- ⚠️ Este programa es solo para **desarrollo y pruebas**
- ⚠️ No uses contraseñas reales en `form_data.json`
- ⚠️ Asegúrate de que el servidor web esté corriendo antes de ejecutar
- ✅ Los datos aleatorios son generados con la librería Faker
- ✅ Puedes detener el programa en cualquier momento con `Ctrl+C`

## 🆘 ¿Necesitas más ayuda?

Lee el archivo completo: `README_AUTO_FORM_FILLER.md`

---

**¡Listo! Ahora ya puedes automatizar el llenado de formularios en VialServi** 🎉

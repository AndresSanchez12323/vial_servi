# Rellenador Automático de Formularios - VialServi

Este programa en Python automatiza el llenado de formularios en el sistema VialServi. Utiliza Selenium WebDriver para interactuar con los formularios web de manera automática.

## 🚀 Características

- ✅ Rellena automáticamente el formulario de registro de clientes
- ✅ Rellena automáticamente el formulario de contacto
- ✅ Genera datos aleatorios realistas usando Faker
- ✅ Permite usar datos personalizados desde un archivo JSON
- ✅ Interfaz interactiva de línea de comandos
- ✅ Opción de previsualizar antes de enviar

## 📋 Requisitos Previos

### 1. Python
Necesitas tener Python 3.7 o superior instalado. Puedes verificar tu versión con:

```bash
python --version
# o
python3 --version
```

Si no tienes Python instalado, descárgalo desde [python.org](https://www.python.org/downloads/)

### 2. Google Chrome
El script utiliza Chrome como navegador. Asegúrate de tener Google Chrome instalado en tu sistema.

### 3. ChromeDriver
ChromeDriver es necesario para que Selenium controle Chrome. Tienes dos opciones:

#### Opción A: Instalación automática (Recomendado)
```bash
pip install webdriver-manager
```

#### Opción B: Instalación manual
1. Descarga ChromeDriver desde [chromedriver.chromium.org](https://chromedriver.chromium.org/downloads)
2. Asegúrate de que la versión coincida con tu versión de Chrome
3. Agrega ChromeDriver a tu PATH del sistema

## 🔧 Instalación

### 1. Clonar o descargar el repositorio
```bash
git clone https://github.com/AndresSanchez12323/vial_servi.git
cd vial_servi
```

### 2. Instalar dependencias de Python
```bash
pip install -r requirements.txt
```

O instalar manualmente:
```bash
pip install selenium==4.15.2 faker==20.1.0 python-dotenv==1.0.0
```

## 📝 Configuración

### Archivo de Datos Personalizados (Opcional)

El script incluye un archivo `form_data.json` donde puedes personalizar los datos que se rellenarán en los formularios:

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
    "mensaje": "Hola, me gustaría obtener más información sobre sus servicios. Gracias."
  }
}
```

Puedes editar estos valores según tus necesidades.

## 🎮 Uso

### Paso 1: Iniciar el servidor web local

Primero, asegúrate de que tu aplicación VialServi esté corriendo en un servidor web. Por ejemplo:

```bash
# Si usas XAMPP, WAMP o similar, inicia el servidor
# La aplicación debería estar accesible en http://localhost o http://localhost:80
```

### Paso 2: Ejecutar el script

```bash
python auto_form_filler.py
# o
python3 auto_form_filler.py
```

### Paso 3: Seguir las instrucciones

1. **Ingresa la URL base**: El script te pedirá la URL donde está alojada tu aplicación
   - Por defecto: `http://localhost`
   - Ejemplo: `http://localhost:8080` o `http://192.168.1.100`

2. **Selecciona una opción del menú**:
   ```
   1. Rellenar formulario de Registro de Cliente
   2. Rellenar formulario de Contacto
   3. Rellenar todos los formularios
   4. Salir
   ```

3. **Modo de datos**:
   - **Sin 'f'** (ejemplo: `1`, `2`, `3`): Genera datos aleatorios
   - **Con 'f'** (ejemplo: `1f`, `2f`, `3f`): Usa datos del archivo `form_data.json`

4. **Confirmar envío**: Después de rellenar el formulario, el script te preguntará si deseas enviarlo
   - `s` o `S`: Envía el formulario
   - `n` o `N`: No envía (solo prueba)

## 💡 Ejemplos de Uso

### Ejemplo 1: Rellenar el registro con datos aleatorios
```bash
$ python auto_form_filler.py
Ingrese la URL base: http://localhost
Ingrese su opción: 1

# El script generará datos aleatorios y rellenará el formulario
# Te preguntará si deseas enviarlo
```

### Ejemplo 2: Rellenar el contacto con datos del archivo
```bash
$ python auto_form_filler.py
Ingrese la URL base: http://localhost
Ingrese su opción: 2f

# El script usará los datos de form_data.json
# Te preguntará si deseas enviarlo
```

### Ejemplo 3: Rellenar todos los formularios
```bash
$ python auto_form_filler.py
Ingrese la URL base: http://localhost
Ingrese su opción: 3

# El script rellenará ambos formularios con datos aleatorios
```

## 🛠️ Solución de Problemas

### Error: "ChromeDriver no encontrado"
**Solución**: Instala ChromeDriver siguiendo las instrucciones de la sección "Requisitos Previos"

### Error: "No se puede conectar a la URL"
**Solución**: 
- Verifica que el servidor web esté corriendo
- Asegúrate de que la URL sea correcta (incluye el puerto si es necesario)
- Ejemplo: `http://localhost:8080` en lugar de `http://localhost`

### Error: "No se pudo encontrar un elemento del formulario"
**Solución**:
- Verifica que la página esté completamente cargada
- Asegúrate de que los IDs de los elementos del formulario sean correctos
- Revisa que la estructura HTML de los formularios no haya cambiado

### El navegador se cierra inmediatamente
**Solución**:
- Verifica que no haya errores en la consola
- Intenta ejecutar en modo no-headless para ver qué sucede
- Revisa los permisos de ejecución del script

## 📚 Estructura del Código

```
auto_form_filler.py          # Script principal
├── FormFiller               # Clase principal
│   ├── __init__()          # Inicialización del navegador
│   ├── load_form_data()    # Carga datos desde JSON
│   ├── fill_registro_cliente()  # Rellena registro de cliente
│   ├── fill_contactenos()   # Rellena formulario de contacto
│   └── close()             # Cierra el navegador
├── print_menu()            # Muestra el menú
└── main()                  # Función principal
```

## 🔒 Notas de Seguridad

⚠️ **Importante**: Este script es solo para propósitos de prueba y desarrollo.

- No uses contraseñas reales en el archivo `form_data.json`
- No compartas el archivo `form_data.json` si contiene información sensible
- Usa datos de prueba en entornos de desarrollo, no en producción

## 🤝 Contribuciones

Si encuentras algún problema o tienes sugerencias de mejora:

1. Reporta el problema en GitHub Issues
2. Propón mejoras mediante Pull Requests
3. Contacta al equipo de desarrollo

## 📄 Licencia

Este proyecto es parte del sistema VialServi y está destinado únicamente para uso interno y de desarrollo.

## ✨ Características Adicionales Planeadas

- [ ] Soporte para más formularios (crear servicio, registro de empleados, etc.)
- [ ] Modo batch para rellenar múltiples formularios simultáneamente
- [ ] Exportación de resultados a archivo de log
- [ ] Validación de datos antes de enviar
- [ ] Modo headless (sin interfaz gráfica) para servidores

## 📞 Soporte

Para obtener ayuda o reportar problemas:
- GitHub Issues: [https://github.com/AndresSanchez12323/vial_servi/issues](https://github.com/AndresSanchez12323/vial_servi/issues)
- Email: [Contactar al equipo de desarrollo]

---

**Desarrollado con ❤️ para VialServi**

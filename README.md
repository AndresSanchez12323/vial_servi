[![Deploy on Railway](https://railway.app/button.svg)](https://railway.app/template/new?template=https%3A%2F%2Fgithub.com%2FAndresSanchez12323%2Fvial_servi)

> Haz clic en el boton para desplegar en Railway en 1 clic.

# 🚗 VialServi - Sistema de Gestión de Servicios Viales

## 📋 Descripción del Proyecto

**VialServi** es una aplicación web desarrollada en PHP para la gestión integral de servicios de asistencia vial. El sistema permite administrar servicios automotrices como cambio de llantas, reparación de motor, cambio de aceite, alineación y balanceo, revisión de frenos, y servicio de conductor elegido.

La aplicación está diseñada para ser utilizada tanto por **empleados** (administradores, técnicos, central) como por **clientes** que desean solicitar y dar seguimiento a los servicios realizados en sus vehículos.

---

## ⚙️ Requisitos del Sistema

### Software Requerido

- **PHP** 8.0 o superior
- **MySQL/MariaDB** 10.4 o superior
- **Servidor Web** Apache (recomendado XAMPP, WAMP, MAMP o LAMP)
- **Navegador Web** moderno (Chrome, Firefox, Edge, Safari)

### Extensiones PHP Requeridas

- `mysqli` - Para conexión con MySQL
- `session` - Para manejo de sesiones
- `password` - Para cifrado de contraseñas

---

## 🛠️ Instalación

### Paso 1: Clonar el repositorio

```bash
git clone https://github.com/AndresSanchez12323/vial_servi.git
```

### Paso 2: Configurar el servidor web

1. Copia la carpeta del proyecto a la carpeta raíz de tu servidor web:
   - **XAMPP (Windows):** `C:\xampp\htdocs\vial_servi`
   - **LAMP (Linux):** `/var/www/html/vial_servi`
   - **MAMP (Mac):** `/Applications/MAMP/htdocs/vial_servi`

### Paso 3: Crear la base de datos

1. Accede a **phpMyAdmin** (generalmente en `http://localhost/phpmyadmin`)
2. Crea una nueva base de datos llamada `vial_servi`
3. Importa el archivo `vial_servi.sql` ubicado en la raíz del proyecto

### Paso 4: Configurar la conexión a la base de datos

Edita el archivo `config.php` con tus credenciales de base de datos:

```php
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña_segura"; // Usa una contraseña segura
$dbname = "vial_servi";
```

> ⚠️ **Seguridad:** Nunca uses contraseñas vacías o débiles en producción. Asegúrate de configurar credenciales seguras para tu base de datos.

### Paso 5: Acceder a la aplicación

Abre tu navegador y visita:
- **Panel de Empleados:** `http://localhost/vial_servi/index.php`
- **Panel de Clientes:** `http://localhost/vial_servi/login_cliente.php`

---

## 📁 Estructura del Proyecto

```
vial_servi/
├── config.php                          # Configuración de conexión a BD
├── index.php                           # Login de empleados
├── login_cliente.php                   # Login de clientes
├── registro.php                        # Registro de empleados
├── registro_cliente.php                # Registro de clientes
├── session.php                         # Manejo de sesiones
├── validPermissions.php                # Validación de permisos
├── header.php                          # Cabecera común
│
├── # PANELES PRINCIPALES
├── administrador.php                   # Panel del SuperAdmin
├── dashboard.php                       # Dashboard de empleados
├── cliente_dashboard.php               # Dashboard de clientes
├── cliente_vehiculo.php                # Gestión de vehículos del cliente
│
├── # GESTIÓN DE SERVICIOS
├── crear_servicio.php                  # Crear nuevos tipos de servicio
├── crear_servicio_realizado.php        # Registrar servicio realizado (empleados)
├── crear_servicio_realizado_cliente.php # Solicitar servicio (clientes)
├── gestionar_servicios.php             # Administrar servicios
├── editar_servicio_realizado.php       # Editar servicio realizado
├── editar_novedades_servicio.php       # Editar novedades de servicios
├── edit_service.php                    # Editar servicio
├── delete_service.php                  # Eliminar servicio
│
├── # CONSULTAS Y REPORTES
├── consulta_general.php                # Consulta general (administradores)
├── consulta_general_tecnico.php        # Consulta para técnicos
├── consulta_identificacion.php         # Consulta por identificación
├── descargar_dashboard_excel.php       # Descargar reporte en Excel
├── descargar_reporte_cliente.php       # Descargar reporte del cliente
│
├── # GESTIÓN DE ROLES Y USUARIOS
├── roles.php                           # Visualizar roles
├── add_role.php                        # Agregar nuevo rol
├── editar_rol.php                      # Editar rol existente
├── eliminar_rol.php                    # Eliminar rol
├── manage_roles.php                    # Gestionar permisos de roles
├── delete.php                          # Eliminar usuario
│
├── # RECUPERACIÓN DE CONTRASEÑA
├── recuperar_contraseña.php            # Recuperación para empleados
├── recuperar_contraseña_cliente.php    # Recuperación para clientes
├── actualizar_contraseña.php           # Actualizar contraseña empleados
├── actualizar_contraseña_cliente.php   # Actualizar contraseña clientes
├── 1Recuperar_contraseña.php           # Proceso de recuperación
│
├── # PÁGINAS PÚBLICAS
├── quienes_somos.html                  # Información de la empresa
├── contactenos.php                     # Formulario de contacto
│
├── # CIERRE DE SESIÓN
├── logout.php                          # Logout de empleados
├── logout_cliente.php                  # Logout de clientes
│
├── # RECURSOS
├── css/
│   └── estilos.css                     # Estilos CSS
├── js/
│   └── session-check.js                # Verificación de sesión
├── Imagenes/                           # Imágenes del sistema
│   ├── Logo.jpg
│   ├── Login.jpg
│   ├── Dashboard.jpg
│   └── ...
├── uploads/                            # Archivos subidos (fotos de servicios)
│
└── vial_servi.sql                      # Script de base de datos
```

---

## 🗄️ Estructura de la Base de Datos

### Tablas Principales

| Tabla | Descripción |
|-------|-------------|
| `empleados` | Información de los empleados del sistema |
| `clientes` | Datos de los clientes |
| `vehiculos` | Vehículos registrados |
| `servicios` | Catálogo de servicios disponibles |
| `servicios_realizados` | Historial de servicios realizados |
| `roles` | Roles del sistema |
| `permisos` | Permisos disponibles |
| `rol_permisos` | Relación roles-permisos |
| `marcas` | Marcas de vehículos |
| `modelos` | Modelos de vehículos |
| `colores` | Colores de vehículos |
| `municipios` | Municipios de Antioquia |
| `mensajes` | Mensajes del formulario de contacto |
| `alertas_recordatorios` | Alertas y recordatorios de servicios |

---

## 👥 Roles y Permisos

### Roles del Sistema

| ID | Rol | Descripción |
|----|-----|-------------|
| 0 | **SuperAdmin** | Control total del sistema, gestión de usuarios y roles |
| 1 | **Administrador** | Acceso total al sistema operativo |
| 2 | **Técnico** | Gestión de servicios con restricciones |
| 3 | **Central** | Monitoreo y control intermedio |
| 5 | **Nuevo** | Rol asignado a usuarios recién registrados (sin permisos) |

### Permisos Disponibles

| Permiso | Descripción |
|---------|-------------|
| `crear_servicio` | Puede crear nuevos servicios |
| `leer_servicio` | Puede ver los servicios |
| `actualizar_servicio` | Puede modificar servicios |
| `eliminar_servicio` | Puede eliminar servicios |
| `ver_reporte_administrador` | Ver reportes del administrador |
| `ver_reporte_tecnico` | Ver reportes del técnico |

---

## 🚀 Funcionalidades Principales

### Para Empleados

- ✅ **Autenticación segura** con bloqueo por intentos fallidos (3 intentos, bloqueo 24h)
- ✅ **Dashboard interactivo** con gráficos de Highcharts
- ✅ **Registro de servicios** realizados con fotos
- ✅ **Consulta general** de todos los servicios
- ✅ **Exportación a Excel** de reportes mensuales
- ✅ **Filtrado por mes** en reportes y estadísticas
- ✅ **Gestión de novedades** en servicios

### Para Administradores

- ✅ **Gestión de usuarios** (crear, editar, eliminar)
- ✅ **Gestión de roles** y permisos
- ✅ **Estadísticas generales** (servicios por tipo, municipio, empleado)
- ✅ **Consulta por identificación** de clientes

### Para Clientes

- ✅ **Portal de cliente** con vista de sus vehículos
- ✅ **Historial de servicios** con filtros
- ✅ **Solicitud de nuevos servicios**
- ✅ **Descarga de reportes** personalizados
- ✅ **Paginación** en listados
- ✅ **Estados de servicio** (Programado/Completado)

---

## 📊 Servicios Disponibles

1. **Cambio de llantas** - Cambio completo de las llantas del vehículo
2. **Reparación de motor** - Servicio de reparación de motor averiado
3. **Cambio de aceite** - Cambio de aceite y revisión de filtros
4. **Alineación y balanceo** - Alineación y balanceo de ruedas
5. **Revisión de frenos** - Revisión y ajuste del sistema de frenos
6. **Conductor elegido** - Personal capacitado para trasladarte a cualquier lugar

---

## 🔐 Seguridad

- ✅ Contraseñas cifradas con `password_hash()` y `PASSWORD_DEFAULT`
- ✅ Consultas preparadas (Prepared Statements) para prevenir SQL Injection
- ✅ Configuración de cookies de sesión seguras (`httponly`, `samesite`)
- ✅ Sistema de bloqueo por intentos fallidos de login
- ✅ Validación de permisos por rol en cada página
- ✅ Sanitización de datos de entrada con `htmlspecialchars()`

---

## 📍 Ubicación

**VialServi** está ubicado en:
- 📍 Rionegro, Antioquia, Colombia
- 🗺️ Coordenadas: 6.176433911165689, -75.43906652215328

---

## 🛡️ Tecnologías Utilizadas

- **Backend:** PHP 8.0+
- **Base de datos:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **Librerías:**
  - [SweetAlert2](https://sweetalert2.github.io/) - Alertas elegantes
  - [Highcharts](https://www.highcharts.com/) - Gráficos interactivos
  - [Bootstrap 4](https://getbootstrap.com/) - Framework CSS
  - [Font Awesome](https://fontawesome.com/) - Iconos
  - [Leaflet](https://leafletjs.com/) - Mapas interactivos

---

## 👨‍💻 Acceso al Sistema

El sistema cuenta con diferentes roles de usuario. Los usuarios de prueba están definidos en el archivo `vial_servi.sql`.

> ⚠️ **Importante:** 
> - Las contraseñas están cifradas en la base de datos usando `password_hash()`.
> - Se recomienda cambiar las contraseñas por defecto antes de usar en producción.
> - Contacta al administrador del sistema para obtener las credenciales de acceso.

---

## 📱 Capturas de Pantalla

El sistema cuenta con interfaces responsivas para:
- Login de empleados y clientes
- Dashboard con estadísticas
- Gestión de servicios
- Panel de administración
- Portal de clientes

---

## 🤝 Contribución

1. Haz un Fork del proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Haz commit de tus cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto es de uso privado para VialServi.

---

## 📞 Contacto

Para más información sobre VialServi, puedes contactarnos a través del formulario en la página de **Contáctenos** o visitar nuestra sección de **Quiénes Somos**.

---

**Desarrollado con ❤️ para VialServi**

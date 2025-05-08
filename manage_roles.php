<?php
session_start();
require_once 'config.php'; // Archivo de conexión a la BD

// Variable para mensajes de retroalimentación
$mensaje = '';
$tipoMensaje = '';

// Función para obtener todos los roles
function obtenerRoles($conn) {
    $sqlRoles = "SELECT id, nombre FROM roles";
    return $conn->query($sqlRoles);
}

// Función para obtener todos los permisos
function obtenerPermisos($conn) {
    $sqlPermisos = "SELECT id, nombre FROM permisos";
    return $conn->query($sqlPermisos);
}

// Función para obtener los permisos asignados a un rol
function obtenerPermisosDeRol($conn, $rol_id) {
    $sql = "SELECT p.id, p.nombre FROM permisos p 
            INNER JOIN rol_permisos rp ON p.id = rp.permiso_id 
            WHERE rp.rol_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rol_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Obtener datos iniciales
$resultRoles = obtenerRoles($conn);
$resultPermisos = obtenerPermisos($conn);

// Ver permisos de un rol específico (READ)
if (isset($_GET['ver_rol']) && !empty($_GET['ver_rol'])) {
    $rol_id = $_GET['ver_rol'];
    $sqlRolNombre = "SELECT nombre FROM roles WHERE id = ?";
    $stmt = $conn->prepare($sqlRolNombre);
    $stmt->bind_param("i", $rol_id);
    $stmt->execute();
    $resultRolNombre = $stmt->get_result();
    $rolNombre = $resultRolNombre->fetch_assoc()['nombre'];
    
    $permisosDelRol = obtenerPermisosDeRol($conn, $rol_id);
}

// Asignar permiso a rol (CREATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'asignar') {
    $rol_id = $_POST['rol_id'];
    $permiso_id = $_POST['permiso_id'];

    // Verificar si ya existe la relación
    $checkSql = "SELECT * FROM rol_permisos WHERE rol_id = ? AND permiso_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $rol_id, $permiso_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Insertar nuevo permiso para el rol
        $insertSql = "INSERT INTO rol_permisos (rol_id, permiso_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ii", $rol_id, $permiso_id);
        
        if ($stmt->execute()) {
            $mensaje = "Permiso asignado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "Error al asignar permiso: " . $conn->error;
            $tipoMensaje = "error";
        }
    } else {
        $mensaje = "Este permiso ya está asignado a este rol.";
        $tipoMensaje = "warning";
    }
}

// Crear nuevo permiso
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'crear_permiso') {
    $nombre_permiso = trim($_POST['nombre_permiso']);
    $descripcion_permiso = trim($_POST['descripcion_permiso']);
    
    // Verificar si el permiso ya existe
    $checkSql = "SELECT * FROM permisos WHERE nombre = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $nombre_permiso);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Insertar nuevo permiso
        $insertSql = "INSERT INTO permisos (nombre, descripcion) VALUES (?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ss", $nombre_permiso, $descripcion_permiso);
        
        if ($stmt->execute()) {
            $mensaje = "Permiso creado correctamente.";
            $tipoMensaje = "success";
            // Actualizar la lista de permisos
            $resultPermisos = obtenerPermisos($conn);
        } else {
            $mensaje = "Error al crear permiso: " . $conn->error;
            $tipoMensaje = "error";
        }
    } else {
        $mensaje = "Este nombre de permiso ya existe.";
        $tipoMensaje = "warning";
    }
}

// Eliminar permiso de un rol (DELETE)
if (isset($_GET['eliminar_permiso']) && isset($_GET['rol_id'])) {
    $permiso_id = $_GET['eliminar_permiso'];
    $rol_id = $_GET['rol_id'];
    
    $deleteSql = "DELETE FROM rol_permisos WHERE rol_id = ? AND permiso_id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("ii", $rol_id, $permiso_id);
    
    if ($stmt->execute()) {
        $mensaje = "Permiso eliminado correctamente.";
        $tipoMensaje = "success";
    } else {
        $mensaje = "Error al eliminar permiso: " . $conn->error;
        $tipoMensaje = "error";
    }
    
    // Redirigir para evitar reenvío del formulario
    header("Location: manage_roles.php?ver_rol=" . $rol_id);
    exit();
}

// Actualizar permisos de un rol (UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'actualizar_permisos') {
    $rol_id = $_POST['rol_id'];
    $nuevos_permisos = isset($_POST['permisos']) ? $_POST['permisos'] : [];
    
    // Primero, obtener todos los permisos actuales
    $permisosActuales = [];
    $resultPermisosActuales = obtenerPermisosDeRol($conn, $rol_id);
    while ($permiso = $resultPermisosActuales->fetch_assoc()) {
        $permisosActuales[] = $permiso['id'];
    }
    
    // Comenzar transacción
    $conn->begin_transaction();
    try {
        // Eliminar permisos que ya no están seleccionados
        foreach ($permisosActuales as $permisoId) {
            if (!in_array($permisoId, $nuevos_permisos)) {
                $deleteSql = "DELETE FROM rol_permisos WHERE rol_id = ? AND permiso_id = ?";
                $stmt = $conn->prepare($deleteSql);
                $stmt->bind_param("ii", $rol_id, $permisoId);
                $stmt->execute();
            }
        }
        
        // Agregar nuevos permisos
        foreach ($nuevos_permisos as $permisoId) {
            if (!in_array($permisoId, $permisosActuales)) {
                $insertSql = "INSERT INTO rol_permisos (rol_id, permiso_id) VALUES (?, ?)";
                $stmt = $conn->prepare($insertSql);
                $stmt->bind_param("ii", $rol_id, $permisoId);
                $stmt->execute();
            }
        }
        
        $conn->commit();
        $mensaje = "Permisos actualizados correctamente.";
        $tipoMensaje = "success";
    } catch (Exception $e) {
        $conn->rollback();
        $mensaje = "Error al actualizar permisos: " . $e->getMessage();
        $tipoMensaje = "error";
    }
    
    // Redirigir para evitar reenvío del formulario
    header("Location: manage_roles.php?ver_rol=" . $rol_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Roles y Permisos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('Imagenes/LoginCliente.jpg');
            text-align: center;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: inline-block;
            margin-top: 20px;
            min-width: 500px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        select, button, input[type="checkbox"], input[type="text"] {
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            border-radius: 5px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 15px;
        }
        button:hover {
            background: #0056b3;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-button {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .action-button:hover {
            background: #bd2130;
        }
        .checkbox-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
        }
        .checkbox-item {
            margin: 5px 0;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            background: #e9ecef;
            cursor: pointer;
            border: 1px solid #dee2e6;
            border-radius: 5px 5px 0 0;
            margin: 0 5px;
        }
        .tab.active {
            background: #fff;
            border-bottom: none;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-family: Arial, sans-serif;
            font-size: 16px;
            resize: vertical;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Gestión de Roles y Permisos</h1>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipoMensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab" onclick="openTab(event, 'crear_permiso')">Crear Permiso</div>
            <div class="tab active" onclick="openTab(event, 'asignar')">Asignar Permiso</div>
            <div class="tab" onclick="openTab(event, 'ver')">Ver Permisos de Rol</div>
            <div class="tab" onclick="openTab(event, 'editar')">Editar Permisos</div>
        </div>
        
        <!-- Pestaña: Crear Permiso (NEW) -->
        <div id="crear_permiso" class="tab-content">
            <h2>Crear Nuevo Permiso</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="crear_permiso">
                <div class="form-group">
                    <label for="nombre_permiso">Nombre del Permiso:</label>
                    <input type="text" name="nombre_permiso" id="nombre_permiso" required placeholder="Ej: gestionar_usuarios">
                </div>

                <div class="form-group">
                    <label for="descripcion_permiso">Descripción:</label>
                    <textarea name="descripcion_permiso" id="descripcion_permiso" rows="4" placeholder="Descripción detallada del permiso"></textarea>
                </div>

                <button type="submit">Crear Permiso</button>
            </form>
        </div>
        
        <!-- Pestaña: Asignar Permiso (CREATE) -->
        <div id="asignar" class="tab-content active">
            <h2>Asignar Permiso a un Rol</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="asignar">
                <div class="form-group">
                    <label for="rol_id">Seleccionar Rol:</label>
                    <select name="rol_id" required>
                        <option value="">-- Seleccionar --</option>
                        <?php 
                        $resultRoles->data_seek(0);
                        while ($rol = $resultRoles->fetch_assoc()) { 
                        ?>
                            <option value="<?php echo $rol['id']; ?>"><?php echo $rol['nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="permiso_id">Seleccionar Permiso:</label>
                    <select name="permiso_id" required>
                        <option value="">-- Seleccionar --</option>
                        <?php 
                        $resultPermisos->data_seek(0);
                        while ($permiso = $resultPermisos->fetch_assoc()) { 
                        ?>
                            <option value="<?php echo $permiso['id']; ?>"><?php echo $permiso['nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <button type="submit">Asignar Permiso</button>
            </form>
        </div>
        
        <!-- Pestaña: Ver Permisos de Rol (READ) -->
        <div id="ver" class="tab-content">
            <h2>Ver Permisos de un Rol</h2>
            <form method="GET">
                <div class="form-group">
                    <label for="ver_rol">Seleccionar Rol:</label>
                    <select name="ver_rol" onchange="this.form.submit()" required>
                        <option value="">-- Seleccionar --</option>
                        <?php 
                        $resultRoles->data_seek(0);
                        while ($rol = $resultRoles->fetch_assoc()) { 
                            $selected = (isset($_GET['ver_rol']) && $_GET['ver_rol'] == $rol['id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $rol['id']; ?>" <?php echo $selected; ?>><?php echo $rol['nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </form>
            
            <?php if (isset($_GET['ver_rol']) && !empty($_GET['ver_rol'])): ?>
                <h3>Permisos del rol: <?php echo $rolNombre; ?></h3>
                
                <?php if ($permisosDelRol->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Permiso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($permiso = $permisosDelRol->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $permiso['id']; ?></td>
                                    <td><?php echo $permiso['nombre']; ?></td>
                                    <td>
                                        <a href="?eliminar_permiso=<?php echo $permiso['id']; ?>&rol_id=<?php echo $_GET['ver_rol']; ?>" 
                                           class="action-button" 
                                           onclick="return confirm('¿Estás seguro de eliminar este permiso del rol?')">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Este rol no tiene permisos asignados.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Pestaña: Editar Permisos (UPDATE) -->
        <div id="editar" class="tab-content">
            <h2>Editar Permisos de un Rol</h2>
            <form method="GET" id="seleccionarRolForm">
                <div class="form-group">
                    <label for="ver_rol">Seleccionar Rol:</label>
                    <select name="ver_rol" onchange="this.form.submit()" required>
                        <option value="">-- Seleccionar --</option>
                        <?php 
                        $resultRoles->data_seek(0);
                        while ($rol = $resultRoles->fetch_assoc()) { 
                            $selected = (isset($_GET['ver_rol']) && $_GET['ver_rol'] == $rol['id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $rol['id']; ?>" <?php echo $selected; ?>><?php echo $rol['nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </form>
            
            <?php if (isset($_GET['ver_rol']) && !empty($_GET['ver_rol'])): ?>
                <h3>Editar permisos para: <?php echo $rolNombre; ?></h3>
                
                <form method="POST">
                    <input type="hidden" name="accion" value="actualizar_permisos">
                    <input type="hidden" name="rol_id" value="<?php echo $_GET['ver_rol']; ?>">
                    
                    <div class="form-group">
                        <label>Seleccionar Permisos:</label>
                        <div class="checkbox-group">
                            <?php 
                            $resultPermisos->data_seek(0);
                            
                            // Obtener IDs de permisos actuales
                            $permisosActualesIds = [];
                            if (isset($permisosDelRol)) {
                                $permisosDelRol->data_seek(0);
                                while ($p = $permisosDelRol->fetch_assoc()) {
                                    $permisosActualesIds[] = $p['id'];
                                }
                            }
                            
                            while ($permiso = $resultPermisos->fetch_assoc()): 
                                $checked = in_array($permiso['id'], $permisosActualesIds) ? 'checked' : '';
                            ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="permisos[]" value="<?php echo $permiso['id']; ?>" id="permiso_<?php echo $permiso['id']; ?>" <?php echo $checked; ?>>
                                    <label for="permiso_<?php echo $permiso['id']; ?>"><?php echo $permiso['nombre']; ?></label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    
                    <button type="submit">Actualizar Permisos</button>
                </form>
            <?php endif; ?>
        </div>

        <div style="margin-top: 20px;">
            <button onclick="window.location.href='roles.php'">Volver a Gestión de Roles</button>
        </div>
    </div>

    <script>
        // Función para gestionar las pestañas
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            
            // Ocultar todas las pestañas
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            
            // Desactivar todos los botones de pestañas
            tablinks = document.getElementsByClassName("tab");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            
            // Mostrar la pestaña actual y activar el botón correspondiente
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
        
        // Mostrar pestaña actual basado en la URL
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_GET['ver_rol'])): ?>
                openTab({currentTarget: document.querySelector('.tab:nth-child(3)')}, 'ver');
            <?php endif; ?>
        });
    </script>

</body>
</html>
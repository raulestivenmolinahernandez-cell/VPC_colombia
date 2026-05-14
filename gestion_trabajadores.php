<?php
session_start();
include("conexion.php");

// PROTEGER SOLO PARA ADMIN Y SUPERVISOR
if (!isset($_SESSION['usuario']) || 
   ($_SESSION['rol'] !== 'Admin' && $_SESSION['rol'] !== 'Supervisor')) {
    header("Location: bienvenido.php");
    exit();
}

// FILTRO DE BÚSQUEDA
$busqueda = "";
if (isset($_GET['buscar'])) {
    $busqueda = $_GET['buscar'];
}

// AGREGAR TRABAJADOR
if (isset($_POST['registrar'])) {

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = $_POST['cedula'];
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    // 🔍 VALIDAR CÉDULA
    $checkCedula = $conn->prepare("SELECT * FROM Empleado WHERE Documento_Identidad=?");
    $checkCedula->bind_param("s", $cedula);
    $checkCedula->execute();
    $resCedula = $checkCedula->get_result();

    if ($resCedula->num_rows > 0) {
        $mensaje = "⚠️ Documento ya registrado";
    } else {

        // 🔍 VALIDAR CORREO
        $checkCorreo = $conn->prepare("SELECT * FROM Usuario WHERE Correo_Electronico=?");
        $checkCorreo->bind_param("s", $correo);
        $checkCorreo->execute();
        $resCorreo = $checkCorreo->get_result();

        if ($resCorreo->num_rows > 0) {
            $mensaje = "⚠️ Correo ya registrado";
        } else {

            // 🔍 VALIDAR USUARIO
            $checkUser = $conn->prepare("SELECT * FROM Usuario WHERE Nombre_Usuario=?");
            $checkUser->bind_param("s", $usuario);
            $checkUser->execute();
            $resUser = $checkUser->get_result();

            if ($resUser->num_rows > 0) {
                $mensaje = "⚠️ Usuario ya existe";
            } else {

                // INSERTAR USUARIO
                $sql_usuario = $conn->prepare("INSERT INTO Usuario (Nombre_Usuario, Contrasena, Rol, Correo_Electronico)
                                              VALUES (?, ?, ?, ?)");
                $sql_usuario->bind_param("ssss", $usuario, $contrasena, $rol, $correo);

                if ($sql_usuario->execute()) {

                    $nuevoID = $conn->insert_id;

                    // INSERTAR EMPLEADO
                    $sql_empleado = $conn->prepare("INSERT INTO Empleado (ID_Empleado, Nombre, Apellido, Documento_Identidad, Cargo, Horario_Asignado, Estado)
                                                   VALUES (?, ?, ?, ?, ?, 'Sin asignar', 'Activo')");
                    $sql_empleado->bind_param("issss", $nuevoID, $nombre, $apellido, $cedula, $rol);

                    if ($sql_empleado->execute()) {
                        $mensaje = "Empleado registrado correctamente.";
                    } else {
                        $mensaje = "Error en tabla empleado.";
                    }

                } else {
                    $mensaje = "Error al registrar usuario.";
                }
            }
        }
    }
}

// ELIMINAR TRABAJADOR
if (isset($_GET['eliminar'])) {

    $id = $_GET['eliminar'];

    $conn->query("DELETE FROM Empleado WHERE ID_Empleado = '$id'");
    $conn->query("DELETE FROM Usuario WHERE ID_Usuario = '$id'");

    header("Location: gestion_trabajadores.php");
    exit();
}

// CONSULTA DE EMPLEADOS
$sql_lista = "
SELECT 
    U.ID_Usuario,
    E.Nombre,
    E.Apellido,
    E.Documento_Identidad,
    U.Nombre_Usuario,
    U.Correo_Electronico,
    U.Rol,
    E.Estado
FROM Usuario U
JOIN Empleado E ON U.ID_Usuario = E.ID_Empleado
WHERE 
    E.Nombre LIKE '%$busqueda%' OR
    E.Apellido LIKE '%$busqueda%' OR
    E.Documento_Identidad LIKE '%$busqueda%'
ORDER BY E.Nombre ASC
";

$resultado = $conn->query($sql_lista);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Trabajadores</title>

<style>

body {
    margin: 0;
    display: flex;
    font-family: Arial;
    background: #f2f4f7;
}

/* ========== FRANJA IZQUIERDA ========== */
.sidebar {
    width: 220px;
    background: #002855;
    color: white;
    height: 100vh;
    padding: 25px;
}

.sidebar img {
    width: 150px;
    display: block;
    margin: 0 auto 30px auto;
}

.sidebar a {
    display: block;
    background: orange;
    padding: 10px;
    margin-top: 15px;
    text-align: center;
    text-decoration: none;
    color: black;
    font-weight: bold;
    border-radius: 5px;
}

/* ========== CONTENIDO DERECHA ========== */
.contenido {
    flex: 1;
    padding: 30px;
}

h1 {
    text-align: center;
    color: #002855;
}

.container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    width: 95%;
    margin: auto;
}

form input, form select {
    padding: 10px;
    width: 180px;
    margin: 5px;
    border-radius: 5px;
    border: 1px solid #aaa;
}

.btn {
    background: #0066cc;
    color: white;
    border: none;
    padding: 10px 22px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

.btn:hover {
    background: #004b99;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
}

table thead {
    background: #002855;
    color: white;
}

table th, table td {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: center;
}

.eliminar {
    color: red;
    font-weight: bold;
    text-decoration: none;
}

.mensaje {
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    font-weight: bold;
}

.error {
    background: #ffe0e0;
    color: #900;
}

.exito {
    background: #e0ffe0;
    color: #060;
}

</style>
</head>

<body>

<div class="sidebar">
    <img src="assets/logo1.png" alt="logo">
    <a href="bienvenido.php">Volver a Mis Aplicaciones</a>
    <a href="logout.php">Cerrar Sesión</a>
</div>

<div class="contenido">

<h1>Gestión de Trabajadores</h1>

<div class="container">

<h2>Registrar Nuevo Empleado</h2>

<form method="POST">

    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="apellido" placeholder="Apellido" required>
    <input type="text" name="cedula" placeholder="Cédula" required>

    <br>

    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="email" name="correo" placeholder="Correo" required>

    <select name="rol" required>
        <option value="">Seleccione Rol</option>
        <option value="Admin">Admin</option>
        <option value="Supervisor">Supervisor</option>
        <option value="Empleado">Empleado</option>
    </select>

    <input type="password" name="contrasena" placeholder="Contraseña" required>

    <button class="btn" name="registrar">Registrar</button>

</form>

<?php 
if (isset($mensaje)) {
    $clase = (strpos($mensaje, 'correctamente') !== false) ? 'exito' : 'error';
    echo "<div class='mensaje $clase'>$mensaje</div>";
}
?>

<h2>Listado de Empleados</h2>

<form method="GET">
    <input type="text" name="buscar" placeholder="Buscar por nombre, apellido o cédula" value="<?php echo $busqueda; ?>">
    <button class="btn">Buscar</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Cédula</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>
                        <td>{$fila['ID_Usuario']}</td>
                        <td>{$fila['Documento_Identidad']}</td>
                        <td>{$fila['Nombre']}</td>
                        <td>{$fila['Apellido']}</td>
                        <td>{$fila['Nombre_Usuario']}</td>
                        <td>{$fila['Correo_Electronico']}</td>
                        <td>{$fila['Rol']}</td>
                        <td>{$fila['Estado']}</td>
                        <td><a class='eliminar' href='?eliminar={$fila['ID_Usuario']}'>Eliminar</a></td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No hay empleados registrados.</td></tr>";
        }
        ?>
    </tbody>
</table>

</div>
</div>

</body>
</html>
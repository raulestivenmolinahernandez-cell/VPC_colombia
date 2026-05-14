<?php
session_start();
include("conexion.php");

// PROTEGER PÁGINA
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// ======================================================
// OBTENER DATOS DEL USUARIO LOGUEADO + SU EMPLEADO REAL
// ======================================================

$usuario = $_SESSION['usuario'];

$sql = "SELECT 
            U.ID_Usuario,
            U.Nombre_Usuario,
            E.ID_Empleado,
            E.Nombre,
            E.Apellido,
            E.Documento_Identidad
        FROM Usuario U
        LEFT JOIN Empleado E ON E.ID_Empleado = U.ID_Usuario
        WHERE U.Nombre_Usuario = '$usuario'
        LIMIT 1";

$res = $conn->query($sql);

if (!$res || $res->num_rows == 0) {
    die("Error: Usuario no encontrado o sin empleado asociado.");
}

$datos = $res->fetch_assoc();

$id_usuario   = $datos['ID_Usuario'];
$id_empleado  = $datos['ID_Empleado']; // ID usado en asistencia
$nombre       = $datos['Nombre'] . " " . $datos['Apellido'];
$cedula       = $datos['Documento_Identidad'];

// ======================================================
// REGISTRAR ENTRADA
// ======================================================
if (isset($_POST['entrada'])) {

    date_default_timezone_set('America/Bogota');
    $fecha = date("Y-m-d");
    $hora  = date("H:i:s");

    $sql = "INSERT INTO Registro_Asistencia 
            (Fecha, Hora_Entrada, Tipo_Registro, Observaciones, ID_Empleado)
            VALUES ('$fecha', '$hora', 'Manual', '', '$id_empleado')";

    if ($conn->query($sql)) {
        $mensaje = "Entrada registrada correctamente.";
    } else {
        $mensaje = "Error al registrar entrada: " . $conn->error;
    }
}

// ======================================================
// REGISTRAR SALIDA
// ======================================================
if (isset($_POST['salida'])) {

    date_default_timezone_set('America/Bogota');
    $hora_salida = date("H:i:s");
    $observaciones = $_POST['observaciones'];

    // Buscar el último registro sin salida para este empleado
    $sql = "SELECT ID_Registro 
            FROM Registro_Asistencia
            WHERE ID_Empleado = '$id_empleado' AND Hora_Salida IS NULL
            ORDER BY ID_Registro DESC
            LIMIT 1";

    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $id_registro = $row['ID_Registro'];

        $sql2 = "UPDATE Registro_Asistencia
                 SET Hora_Salida = '$hora_salida', Observaciones = '$observaciones'
                 WHERE ID_Registro = '$id_registro'";

        if ($conn->query($sql2)) {
            $mensaje = "Salida registrada correctamente.";
        } else {
            $mensaje = "Error al registrar salida: " . $conn->error;
        }
    } else {
        $mensaje = "No hay entrada registrada para este usuario.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro de Horas</title>

<style>
    body {
        font-family: Arial, sans-serif;
        display: flex;
        height: 100vh;
        margin: 0;
    }

    .sidebar {
        background: #002855;
        width: 200px;
        padding: 20px;
        color: white;
    }

    .sidebar button {
        width: 100%;
        background: orange;
        border: none;
        padding: 10px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
        margin-top: 20px;
    }

    .contenido {
        flex: 1;
        padding: 40px;
        text-align: center;
    }

    h1 {
        font-size: 28px;
        margin-bottom: 20px;
        font-weight: bold;
    }

    table {
        width: 70%;
        margin: auto;
        border-collapse: collapse;
    }

    td {
        padding: 12px;
        border: 1px solid black;
        text-align: center;
        font-size: 18px;
    }

    .btn-reg {
        padding: 12px 30px;
        margin: 15px;
        background: #0066cc;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
    }

    .btn-reg:hover {
        background: #004b99;
    }

    textarea {
        margin-top: 10px;
        width: 40%;
        font-size: 16px;
    }

    .mensaje {
        font-size: 20px;
        margin-top: 20px;
        color: green;
        font-weight: bold;
    }

    .volver {
        display: block;
        margin-bottom: 20px;
        text-decoration: none;
        color: #0066cc;
        font-size: 18px;
        font-weight: bold;
    }
</style>
</head>

<body>

<div class="sidebar">
    <img src="assets/logo1.png" alt="VPC Logo" width="150">
    <form action="bienvenido.php" method="POST">
        <button>Volver</button>
    </form>
</div>

<div class="contenido">
    
    <h1>REGISTRO DE HORAS</h1>

    <table>
        <tr>
            <td><b>Trabajador</b></td>
            <td><?php echo $nombre; ?></td>
        </tr>
        <tr>
            <td><b>Identificación</b></td>
            <td><?php echo $cedula; ?></td>
        </tr>
    </table>

    <br><br>

    <!-- ENTRADA -->
    <form method="POST">
        <button class="btn-reg" name="entrada">Registrar Entrada</button>
    </form>

    <!-- SALIDA -->
    <form method="POST">
        <textarea name="observaciones" rows="3" placeholder="Observaciones opcionales..."></textarea><br>
        <button class="btn-reg" name="salida">Registrar Salida</button>
    </form>

    <?php if (isset($mensaje)) echo "<p class='mensaje'>$mensaje</p>"; ?>

</div>

</body>
</html>

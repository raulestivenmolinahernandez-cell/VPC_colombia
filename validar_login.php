<?php
session_start();
include("conexion.php");

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

$sql = "SELECT * FROM Usuario WHERE Nombre_Usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();
    $clave_hash = $row['Contrasena'];

    if (password_verify($clave, $clave_hash)) {

        $_SESSION['usuario'] = $row['Nombre_Usuario']; // ✅
        $_SESSION['rol'] = $row['Rol']; // ✅

        header("Location: bienvenido.php");
        exit();

    } else {
        echo "❌ Contraseña incorrecta. <a href='login.php'>Volver</a>";
    }
} else {
    echo "❌ Usuario no encontrado. <a href='login.php'>Volver</a>";
}

$stmt->close();
$conn->close();
?>


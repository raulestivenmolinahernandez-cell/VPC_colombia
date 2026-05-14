<?php
include("conexion.php");
session_start();

// Validar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // VALIDAR QUE EXISTAN LAS VARIABLES
    if (!isset($_POST['nombre'], $_POST['correo'], $_POST['rol'], $_POST['contrasena'])) {
        die("Error: Faltan datos en el formulario.");
    }

    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];  // ID_Rol
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    // CONSULTA CORREGIDA
    $sql = "INSERT INTO Usuario (Nombre_Usuario, Contrasena, Correo_Electronico, ID_Rol)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error al preparar consulta SQL: " . $conn->error);
    }

    $stmt->bind_param("sssi", $nombre, $contrasena, $correo, $rol);

    if ($stmt->execute()) {
        echo "<script>alert('Usuario registrado correctamente'); window.location='gestion_trabajadores.php';</script>";
    } else {
        echo "Error al registrar usuario: " . $stmt->error;
    }

    $stmt->close();
}
?>



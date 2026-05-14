<?php
$host = "localhost";
$user = "root";
$pass = "";
$bd   = "gestion_nomina";

$conn = new mysqli($host, $user, $pass, $bd);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>

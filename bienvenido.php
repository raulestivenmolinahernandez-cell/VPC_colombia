<?php
session_start();

// Verificar si la sesión existe
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener el rol del usuario
$rol = $_SESSION['rol'];

// Función para mostrar módulos según el rol
function showModule($allowed_roles, $current_rol) {
    return in_array($current_rol, $allowed_roles);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>VPC - Mis Aplicaciones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background-color: #f0f0f0;
        }

        .sidebar {
            width: 250px;
            background-color: #0d284a;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            width: 200px;
            height: 100px;
            margin-bottom: 10px;
        }

        .logout-button {
            width: 100%;
            padding: 10px;
            background: #ff5722;
            border: none;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background 0.3s;
        }
        .logout-button:hover { background: #e64a19; }

        .main-content {
            flex-grow: 1;
            padding: 20px 40px;
        }

        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { color: #333; }

        .modules-container {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .module-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 250px;
            text-align: center;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .module-card:hover { transform: translateY(-5px); }

        .module-card h3 { margin-top: 0; }
        .module-card a {
            display: inline-block;
            padding: 10px 20px;
            background: #ffaa00;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }
        .module-card a:hover { background: #e69500; }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="assets/logo1.png" alt="VPC Logo">
        </div>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </div>

    <div class="main-content">
        <div class="header"><h1>MIS APLICACIONES</h1></div>

        <div class="modules-container">

            <?php if (showModule(['Admin', 'Supervisor'], $rol)): ?>
            <div class="module-card">
                <h3>Gestión de trabajadores</h3>
                <div class="icon"><i class="fas fa-handshake"></i></div>
                <p>Administrador, Supervisor</p>
                <a href="gestion_trabajadores.php">Entrar</a>
            </div>
            <?php endif; ?>

            <?php if (showModule(['Admin', 'Supervisor', 'Empleado'], $rol)): ?>
            <div class="module-card">
                <h3>Historial de Entradas y Salidas</h3>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                <p>Todos los Roles</p>
                <a href="historial_asistencia.php">Entrar</a>
            </div>
            <?php endif; ?>

            <?php if (showModule(['Admin'], $rol)): ?>
            <div class="module-card">
                <h3>Nómina</h3>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                <p>Solo Administrador</p>
                <a href="gestion_nomina.php">Entrar</a>
            </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>

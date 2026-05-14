<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - VPC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .reg-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
            text-align: center;
            width: 400px;
        }
        .reg-box img {
            width: 120px;
            margin-bottom: 10px;
        }
        .reg-box h2 {
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }
        .reg-box input[type="text"],
        .reg-box input[type="password"],
        .reg-box input[type="email"],
        .reg-box select {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #aaa;
            border-radius: 4px;
        }
        .reg-box button {
            width: 95%;
            padding: 10px;
            background: #28a745;
            border: none;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
        }
        .reg-box button:hover {
            background: #218838;
        }
        .extra {
            margin-top: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="reg-box">
        <img src="assets/logo.png" alt="VPC Logo">
        <h2>Registro de Usuario</h2>

        <form action="registrar_usuario.php" method="POST">
            <input type="text" name="usuario" placeholder="Nombre de usuario" required><br>

            <input type="password" name="clave" placeholder="Contraseña" required><br>

            <input type="email" name="correo" placeholder="Correo electrónico" required><br>

            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="Admin">Administrador</option>
                <option value="Empleado">Empleado</option>
                <option value="Supervisor">Supervisor</option>
            </select>
            <button type="submit">Registrar</button>
        </form>

        <div class="extra">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
        </div>
    </div>
</body>
</html>






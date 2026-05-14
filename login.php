<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - VPC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
            text-align: center;
            width: 350px;
        }
        .login-box img {
            width: 120px;
            margin-bottom: 10px;
        }
        .login-box h2 {
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #aaa;
            border-radius: 4px;
        }
        .login-box label {
            display: block;
            text-align: left;
            margin-left: 20px;
            font-size: 14px;
        }
        .login-box input[type="checkbox"] {
            margin-right: 5px;
        }
        .login-box button {
            width: 95%;
            padding: 10px;
            background: #00a6ff;
            border: none;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
        }
        .login-box button:hover {
            background: #0088cc;
        }
        .extra {
            margin-top: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="assets/logo.png" alt="VPC Logo">
        <h2>Bienvenidos 👋</h2>

        <form action="validar_login.php" method="POST">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" required>

            <label for="clave">Contraseña</label>
            <input type="password" name="clave" required>

            <button type="submit">Sign in</button>
        </form>

    </div>
</body>
</html>

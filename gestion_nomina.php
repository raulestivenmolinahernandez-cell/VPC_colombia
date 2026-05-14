<?php
session_start();
include("conexion.php");

// Protección: Solo Admin puede entrar a Nómina
if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] ?? '') !== 'Admin') {
    header("Location: bienvenido.php");
    exit();
}

// Obtener registros de asistencia y datos de empleado
$sql = "SELECT 
            E.ID_Empleado, E.Nombre, E.Apellido, E.Documento_Identidad,
            RA.Fecha, RA.Hora_Entrada, RA.Hora_Salida, RA.Observaciones
        FROM Empleado E
        JOIN Registro_Asistencia RA ON RA.ID_Empleado = E.ID_Empleado
        ORDER BY RA.Fecha DESC, RA.Hora_Entrada DESC
        LIMIT 500";

$res = $conn->query($sql);

function calcularHoras($entrada, $salida) {
    if (empty($entrada) || empty($salida)) return "Pendiente";
    try {
        $e = new DateTime($entrada);
        $s = new DateTime($salida);
        $diff = $e->diff($s);
        return $diff->format('%H:%I');
    } catch (Exception $ex) {
        return "Err";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Gestión Nómina | VPC</title>
<style>
    body { font-family: Arial, sans-serif; margin:0; background:#f5f7fa; display:flex; height:100vh; }
    .sidebar { background:#002855; width:220px; padding:20px; color:white; display:flex; flex-direction:column; align-items:center; }
    .sidebar img { width:150px; margin-bottom:10px; }
    .sidebar .btn { width:100%; margin:6px 0; padding:10px; background:#0d4b7a; border:none; color:white; border-radius:6px; cursor:pointer; }
    .content { flex:1; padding:28px; overflow:auto; }
    .volver { color:#0066cc; text-decoration:none; font-weight:bold; display:inline-block; margin-bottom:12px; }
    .card { background:white; padding:18px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.06); }
    h2 { color:#002855; margin-top:0; }
    table { width:100%; border-collapse:collapse; margin-top:14px; }
    th { background:#002855; color:white; padding:10px; }
    td { padding:10px; border:1px solid #e6e6e6; text-align:center; }
    .small { font-size:13px; color:#666; }
</style>
</head>
<body>

<div class="sidebar">
    <img src="assets/logo1.png" alt="logo">
    <button class="btn" onclick="location.href='bienvenido.php'">⬅ Volver</button>
    <div style="flex:1"></div>
    <form action="logout.php" method="POST">
        <button class="btn" type="submit" style="background:#ff6b6b;">Cerrar sesión</button>
    </form>
</div>

<div class="content">
    <a class="volver" href="bienvenido.php">⬅ Volver a Mis Aplicaciones</a>

    <div class="card">
        <h2>PANEL DE NÓMINA</h2>
        <p class="small">Últimos registros de asistencia (entrada / salida)</p>

        <table>
            <thead>
                <tr>
                    <th>ID Empleado</th>
                    <th>Cédula</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Fecha</th>
                    <th>Hora Entrada</th>
                    <th>Hora Salida</th>
                    <th>Horas Realizadas</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($res && $res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $hrs = calcularHoras($row['Hora_Entrada'], $row['Hora_Salida']);
                        echo "<tr>";
                        echo "<td>".htmlspecialchars($row['ID_Empleado'])."</td>";
                        echo "<td>".htmlspecialchars($row['Documento_Identidad'])."</td>";
                        echo "<td>".htmlspecialchars($row['Nombre'])."</td>";
                        echo "<td>".htmlspecialchars($row['Apellido'])."</td>";
                        echo "<td>".$row['Fecha']."</td>";
                        echo "<td>".$row['Hora_Entrada']."</td>";
                        echo "<td>".($row['Hora_Salida'] ?? 'Pendiente')."</td>";
                        echo "<td>".$hrs."</td>";
                        echo "<td>".htmlspecialchars($row['Observaciones'])."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No hay registros de asistencia.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>

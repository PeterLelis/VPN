<?php
include('../config.php');

// Seguridad: Solo trabajadores
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'trabajador') {
    header("Location: ../index.php"); exit();
}

$user_id = $_SESSION['usuario_id'];

// Obtener el historial completo (o filtrado por mes)
$sql = "SELECT *, 
        DATE_FORMAT(entrada, '%d/%m/%Y') as fecha,
        DATE_FORMAT(entrada, '%H:%i') as hora_entrada,
        DATE_FORMAT(salida, '%H:%i') as hora_salida,
        horas_totales
        FROM registros_horarios 
        WHERE usuario_id = '$user_id' 
        ORDER BY entrada DESC";

$res = mysqli_query($conexion, $sql);

// Calcular total de horas del mes actual para el resumen
$sql_total = "SELECT SUM(horas_totales) as total_mes FROM registros_horarios 
              WHERE usuario_id = '$user_id' AND MONTH(entrada) = MONTH(CURRENT_DATE())";
$res_total = mysqli_query($conexion, $sql_total);
$total_data = mysqli_fetch_assoc($res_total);
$horas_acumuladas = $total_data['total_mes'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Jornada - CorpNet</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <nav class="navbar">
        <h1>CORP<span>NET</span> / Historial</h1>
        <a href="dashboard.php" class="btn btn-outline">Volver al Panel</a>
    </nav>

    <main class="container">
        <div class="card" style="margin-bottom: 2rem; border-left: 5px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="border:none; margin:0;">Horas Acumuladas (Mes Actual)</h3>
                    <p class="text-light">Según registros validados por el sistema.</p>
                </div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">
                    <?php echo number_format($horas_acumuladas, 2); ?>h
                </div>
            </div>
        </div>

        <div class="card">
            <h3>📅 Registros de Entrada y Salida</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Total Horas</th>
                            <th>IP / Dispositivo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($h = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><strong><?php echo $h['fecha']; ?></strong></td>
                            <td><?php echo $h['hora_entrada']; ?></td>
                            <td><?php echo $h['hora_salida'] ?? '--:--'; ?></td>
                            <td>
                                <strong><?php echo $h['horas_totales'] ? number_format($h['horas_totales'], 2) . ' h' : 'En curso...'; ?></strong>
                            </td>
                            <td>
                                <small class="text-light">
                                    <?php echo $h['ip_conexion']; ?><br>
                                    <span style="font-size: 10px;"><?php echo substr($h['dispositivo'], 0, 30); ?>...</span>
                                </small>
                            </td>
                            <td>
                                <?php if ($h['salida']): ?>
                                    <span class="badge badge-success">Completado</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #fef3c7; color: #92400e;">Abierto</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
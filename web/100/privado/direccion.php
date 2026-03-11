<?php
include('../config.php');

// 1. SEGURIDAD ADAPTADA: Permitir al Director (5003) y al Admin (5000/admin_db)
// Se han incluido los IDs numéricos que coinciden con el GID de LDAP
/*if (!isset($_SESSION['usuario_id']) || 
    ($_SESSION['rol'] != 'direccion' && $_SESSION['rol'] != 5003 && $_SESSION['rol'] != 5000 && $_SESSION['rol'] != 'admin_db')) {
    header("Location: ../index.php"); 
    exit();
}*/
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php"); 
    exit();
}

$user_id = $_SESSION['usuario_id'];
// Sincronizamos con el mes actual para los KPIs
$mes_actual = date('n');
$anio_actual = date('Y');

// --- 2. CONSULTA KPI: RESUMEN EJECUTIVO ---
$kpi_query = "SELECT 
    (SELECT COUNT(*) FROM usuarios WHERE estado = 1) as total_emp,
    (SELECT SUM(salario_base_mensual) FROM usuarios WHERE estado = 1) as coste_total,
    (SELECT SUM(horas_totales) FROM registros_horarios WHERE MONTH(entrada) = $mes_actual AND YEAR(entrada) = $anio_actual) as horas_mes";

$res_kpi = mysqli_query($conexion, $kpi_query);
$kpi = mysqli_fetch_assoc($res_kpi);

// --- 3. CONSULTA DEPARTAMENTOS: DESGLOSE DE COSTES ---
$sql_deptos = "SELECT 
    d.nombre, 
    COUNT(u.id) as num_empleados, 
    IFNULL(SUM(u.salario_base_mensual), 0) as total_gasto
    FROM departamentos d
    LEFT JOIN usuarios u ON d.id = u.departamento_id AND u.estado = 1
    GROUP BY d.id";
$res_deptos = mysqli_query($conexion, $sql_deptos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Dirección - PercyJackSon Security</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <nav class="navbar">
        <h1>CORP<span>NET</span> / Alta Dirección</h1>
        <div>
            
            <span style="margin-left:1rem;">Director: <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
            <a href="../logout.php" class="btn btn-outline" style="margin-left:1rem;">Salir</a>
        </div>
    </nav>

    <main class="container">
        <div class="grid">
            <div class="card" style="border-left: 5px solid #8b5cf6;">
                <small class="text-light">PLANTILLA ACTIVA</small>
                <h2 style="margin:0;"><?php echo $kpi['total_emp']; ?> Empleados</h2>
            </div>
            <div class="card" style="border-left: 5px solid #10b981;">
                <small class="text-light">MASA SALARIAL BRUTA</small>
                <h2 style="margin:0;"><?php echo number_format($kpi['coste_total'] ?? 0, 2); ?> €</h2>
            </div>
            <div class="card" style="border-left: 5px solid #3b82f6;">
                <small class="text-light">HORAS PRODUCIDAS (MES)</small>
                <h2 style="margin:0;"><?php echo number_format($kpi['horas_mes'] ?? 0, 1); ?> h</h2>
            </div>
        </div>

        <div class="card" style="margin-top:20px;">
            <h3>📊 Distribución de Costes por Departamento</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Departamento</th>
                            <th>Nº Empleados</th>
                            <th>Inversión Mensual</th>
                            <th>Peso Salarial</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($res_deptos)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                            <td><?php echo $row['num_empleados']; ?></td>
                            <td><?php echo number_format($row['total_gasto'], 2); ?> €</td>
                            <td>
                                <?php 
                                $porcentaje = ($kpi['coste_total'] > 0) ? ($row['total_gasto'] / $kpi['coste_total']) * 100 : 0;
                                ?>
                                <div style="background:#eee; height:8px; border-radius:4px; width:100px; display:inline-block; margin-right:10px;">
                                    <div style="background:#8b5cf6; height:100%; width:<?php echo $porcentaje; ?>%;"></div>
                                </div>
                                <small><?php echo round($porcentaje, 1); ?>%</small>
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
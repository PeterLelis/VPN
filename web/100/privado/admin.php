<?php
include('../config.php');

// 1. SEGURIDAD: Solo Administradores (Rol 5000 en LDAP)
/*if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 5000)) {
    header("Location: ../index.php"); 
    exit();
}*/
// Cambia la línea 5 por esto temporalmente:
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php"); 
    exit();
}
$user_id = $_SESSION['usuario_id'];
$mes_act = date('n');
$anio_act = date('Y');

// --- 2. CONSULTAS DE SUPERVISIÓN (Global) ---
// Total de horas trabajadas por toda la plantilla este mes
$res_total_horas = mysqli_query($conexion, "SELECT SUM(horas_totales) as total FROM registros_horarios WHERE MONTH(entrada) = $mes_act");
$total_horas_empresa = mysqli_fetch_assoc($res_total_horas)['total'] ?? 0;

// Últimos 5 fichajes de cualquier usuario (Monitorización en tiempo real)
$sql_monitor = "SELECT r.*, u.nombre, u.apellido FROM registros_horarios r 
                JOIN usuarios u ON r.usuario_id = u.id 
                ORDER BY r.entrada DESC LIMIT 5";
$monitor_fichajes = mysqli_query($conexion, $sql_monitor);

// --- 3. CONSULTAS PERSONALES (Para el Admin como empleado) ---
$sql_mis_nominas = "SELECT * FROM nominas WHERE usuario_id = '$user_id' ORDER BY anio DESC, mes DESC LIMIT 3";
$mis_nominas = mysqli_query($conexion, $sql_mis_nominas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - PercyJackSon Security</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <nav class="navbar">
        <h1>CORP<span>NET</span> / Admin Console (5000)</h1>
        <div>
            <a href="../trabajador/dashboard.php" class="btn btn-outline">Mi Panel Personal</a>
            <span style="margin-left:1rem;">Admin: <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
            <a href="../logout.php" class="btn btn-outline" style="margin-left:1rem;">Cerrar Sesión</a>
        </div>
    </nav>

    <?php include('componente_fichaje.php'); ?>

    <main class="container">
        <div class="grid" style="grid-template-columns: 2fr 1fr;">
            
            <div class="card">
                <h3>📊 Supervisión de Infraestructura</h3>
                <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
                    <div style="background:#f1f5f9; padding:15px; border-radius:8px;">
                        <small>Horas Totales Plantilla (Mes)</small>
                        <h2 style="margin:0; color:var(--primary);"><?php echo number_format($total_horas_empresa, 1); ?> h</h2>
                    </div>
                    <div style="background:#f1f5f9; padding:15px; border-radius:8px;">
                        <small>Estado VPN WireGuard</small>
                        <h2 style="margin:0; color:#10b981;">ACTIVO</h2>
                    </div>
                    <div style="margin-top: 20px; padding: 15px; background: #fff7ed; border: 1px solid #ffedd5; border-radius: 8px;">
                    <h4>📦 Mantenimiento de Datos</h4>
                    <p style="font-size: 0.8rem; color: #9a3412;">Crea un volcado SQL completo de la base de datos MySQL antes de realizar cambios en el LDAP.</p>
                    <a href="respaldar_db.php" class="btn btn-primary" style="background: #ea580c; border:none;">Generar Copia de Seguridad (.sql)</a>
                </div>
                </div>

                <h4>Últimos movimientos en el sistema (MySQL)</h4>
                <table style="width:100%; font-size:0.9rem;">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($f = mysqli_fetch_assoc($monitor_fichajes)): ?>
                        <tr>
                            <td><?php echo $f['nombre']." ".$f['apellido']; ?></td>
                            <td><?php echo date('d/m H:i', strtotime($f['entrada'])); ?></td>
                            <td><?php echo $f['salida'] ? date('H:i', strtotime($f['salida'])) : '---'; ?></td>
                            <td><strong><?php echo $f['horas_totales']; ?>h</strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3>📄 Mis Últimas Nóminas</h3>
                <?php if(mysqli_num_rows($mis_nominas) > 0): ?>
                    <?php while($n = mysqli_fetch_assoc($mis_nominas)): ?>
                        <div style="border-bottom: 1px solid #eee; padding: 10px 0; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <strong>Mes <?php echo $n['mes']."/".$n['anio']; ?></strong><br>
                                <small><?php echo number_format($n['salario_neto'], 2); ?> €</small>
                            </div>
                            <a href="../trabajador/ver_pdf.php?id=<?php echo $n['id']; ?>" class="btn btn-outline" style="font-size:0.8rem;">PDF</a>
                        </div>
                    <?php endwhile; ?>
                    <a href="../trabajador/mis_nominas.php" style="display:block; margin-top:15px; font-size:0.9rem; text-align:center;">Ver historial completo</a>
                <?php else: ?>
                    <p class="text-light">No hay nóminas personales emitidas.</p>
                <?php endif; ?>

                <hr style="margin:20px 0;">
                
                <h3>🛡️ Identidad LDAP</h3>
                <p style="font-size:0.8rem; color:#64748b;">
                    Tu cuenta está vinculada al <strong>UID: <?php echo $_SESSION['user']; ?></strong> con permisos de superusuario sobre el árbol <code>dc=percyjackson,dc=security</code>.
                </p>
            </div>

        </div>
    </main>
</body>
</html>
<?php
include('../config.php');


// 1. SEGURIDAD: Solo usuarios autenticados vía LDAP + MFA
// Tras el Paso 2 del login, el sistema guarda 'usuario_id' y 'user' (el UID de LDAP)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['usuario_id'];
$nombre_usuario = $_SESSION['nombre'];

// 2. CONSULTAS PARA EL RESUMEN DEL TRABAJADOR
// Obtener el último fichaje para saber si está en jornada
$sql_fichaje = "SELECT entrada, salida FROM registros_horarios 
                WHERE usuario_id = '$user_id' 
                ORDER BY entrada DESC LIMIT 1";
$res_fichaje = mysqli_query($conexion, $sql_fichaje);
$fichaje = mysqli_fetch_assoc($res_fichaje);
$en_jornada = ($fichaje && is_null($fichaje['salida']));

// Obtener horas totales del mes actual
$mes_act = date('n');
$anio_act = date('Y');
$sql_horas = "SELECT SUM(horas_totales) as total FROM registros_horarios 
              WHERE usuario_id = '$user_id' AND MONTH(entrada) = $mes_act AND YEAR(entrada) = $anio_act";
$res_horas = mysqli_query($conexion, $sql_horas);
$h_data = mysqli_fetch_assoc($res_horas);
$horas_mes = $h_data['total'] ?? 0;

// Obtener última nómina
$sql_nom = "SELECT salario_neto, mes, anio FROM nominas 
            WHERE usuario_id = '$user_id' ORDER BY anio DESC, mes DESC LIMIT 1";
$res_nom = mysqli_query($conexion, $sql_nom);
$ultima_nomina = mysqli_fetch_assoc($res_nom);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Empleado - CorpNet Security</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <nav class="navbar">
        <h1>CORP<span>NET</span> / Panel Empleado</h1>
        <div>
            <span>Hola, <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong></span>
            <a href="../logout.php" class="btn btn-outline" style="margin-left:1rem;">Salir</a>
        </div>
    </nav>

    <?php include('../privado/componente_fichaje.php'); ?>

    <main class="container">
        <div class="grid">
            
            <div class="card">
                <h3>⏱️ Mi Jornada</h3>
                <p>Estado actual: 
                    <span class="badge <?php echo $en_jornada ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $en_jornada ? 'TRABAJANDO' : 'FUERA DE SERVICIO'; ?>
                    </span>
                </p>
                <?php if($en_jornada): ?>
                    <p class="text-light" style="font-size:0.9rem;">Entrada registrada a las: <?php echo date('H:i', strtotime($fichaje['entrada'])); ?></p>
                <?php endif; ?>
                <hr>
                <p>Horas acumuladas este mes: <strong><?php echo number_format($horas_mes, 1); ?> h</strong></p>
            </div>

            <div class="card">
                <h3>💰 Último Pago</h3>
                <?php if($ultima_nomina): ?>
                    <p>Mes: <strong><?php echo $ultima_nomina['mes'] . "/" . $ultima_nomina['anio']; ?></strong></p>
                    <p>Importe neto: <span style="font-size: 1.4rem; color: var(--success); font-weight: bold;"><?php echo number_format($ultima_nomina['salario_neto'], 2); ?> €</span></p>
                    <a href="mis_nominas.php" class="btn btn-primary" style="display: block; text-align: center; margin-top: 10px;">Ver histórico</a>
                <?php else: ?>
                    <p class="text-light">No hay nóminas registradas todavía.</p>
                <?php endif; ?>
            </div>

            <div class="card" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                <h3>🛡️ Seguridad</h3>
                <p class="text-light" style="font-size: 0.85rem;">
                    Tu sesión está protegida por <strong>MFA (TOTP)</strong> y el túnel <strong>WireGuard</strong> de PercyJackson Security.
                </p>
                <ul style="font-size: 0.8rem; color: #64748b; padding-left: 1.2rem; margin-top: 10px;">
                    <li>Identidad: LDAP Verified</li>
                    <li>Acceso: Túnel VPN Activo</li>
                    <li>Rol: Empleado Standard</li>
                </ul>
            </div>

        </div>
    </main>
</body>
</html>
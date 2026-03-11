<?php
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    $user_id = $_SESSION['usuario_id'];
    $accion  = $_POST['accion'];
    $ip      = $_SERVER['REMOTE_ADDR'];
    $agent   = $_SERVER['HTTP_USER_AGENT'];

    if ($accion == 'entrada') {
        // Insertar nueva entrada
        $sql = "INSERT INTO registros_horarios (usuario_id, entrada, ip_conexion, dispositivo) 
                VALUES ('$user_id', NOW(), '$ip', '$agent')";
        mysqli_query($conexion, $sql);
    } 
    elseif ($accion == 'salida') {
        // Cerrar el último registro abierto y calcular horas_totales
        $sql = "UPDATE registros_horarios 
                SET salida = NOW(), 
                    horas_totales = TIMESTAMPDIFF(MINUTE, entrada, NOW()) / 60
                WHERE usuario_id = '$user_id' AND salida IS NULL 
                ORDER BY entrada DESC LIMIT 1";
        mysqli_query($conexion, $sql);
    }
}

// Al final del proceso de fichar...
$rol = $_SESSION['rol'];

if ($rol == 'trabajador') {
    header("Location: dashboard.php");
} elseif ($rol == 'rrhh') {
    header("Location: ../privado/rrhh.php");
} elseif ($rol == 'admin_db') {
    header("Location: ../privado/admin.php");
} else {
    // Si por algún error un director llega aquí, lo devolvemos a su sitio
    header("Location: ../privado/direccion.php");
}
exit();
exit();
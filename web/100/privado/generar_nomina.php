<?php
include('../config.php');

// SEGURIDAD: Solo RRHH (Rol 5002) o Admin (5000)
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] != 'rrhh' && $_SESSION['rol'] != 5002)) {
    header("Location: ../index.php"); exit();
}

$id_empleado = (int)$_GET['id'];
$mes_actual = date('n');
$anio_actual = date('Y');

// --- 1. CONTROL DE DUPLICADOS ---
$sql_check = "SELECT id FROM nominas 
              WHERE usuario_id = $id_empleado 
              AND mes = $mes_actual 
              AND anio = $anio_actual";
$res_check = mysqli_query($conexion, $sql_check);

if (mysqli_num_rows($res_check) > 0) {
    // Si ya existe, abortamos y avisamos
    header("Location: rrhh.php?error=La+nomina+de+este+mes+ya+ha+sido+emitida");
    exit();
}

// --- 2. OBTENER DATOS PARA EL CÁLCULO ---
$sql_user = "SELECT * FROM usuarios WHERE id = $id_empleado AND estado = 1";
$res_user = mysqli_query($conexion, $sql_user);
$u = mysqli_fetch_assoc($res_user);

if (!$u) { die("Empleado no encontrado."); }

// Calcular horas del mes actual
$sql_horas = "SELECT SUM(horas_totales) as total FROM registros_horarios 
              WHERE usuario_id = $id_empleado 
              AND MONTH(entrada) = $mes_actual 
              AND YEAR(entrada) = $anio_actual";
$res_horas = mysqli_query($conexion, $sql_horas);
$h = mysqli_fetch_assoc($res_horas);
$horas_trabajadas = $h['total'] ?? 0;

// --- 3. CÁLCULOS FINANCIEROS ---
$salario_base = $u['salario_base_mensual'];
$irpf_retenido = $salario_base * ($u['irpf_pct'] / 100);
$ss_retenida = $salario_base * ($u['seg_social_pct'] / 100);
$salario_neto = $salario_base - $irpf_retenido - $ss_retenida;

// --- 4. INSERCIÓN ---
$sql_ins = "INSERT INTO nominas (usuario_id, mes, anio, total_horas_ordinarias, salario_bruto, deduccion_irpf, deduccion_seg_social, salario_neto, estado) 
            VALUES ($id_empleado, $mes_actual, $anio_actual, $horas_trabajadas, $salario_base, $irpf_retenido, $ss_retenida, $salario_neto, 'Pendiente')";

if (mysqli_query($conexion, $sql_ins)) {
    header("Location: rrhh.php?exito=Nomina+generada+correctamente");
} else {
    echo "Error: " . mysqli_error($conexion);
}
?>
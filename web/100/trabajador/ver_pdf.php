<?php
include('../config.php');

// 1. SEGURIDAD: Solo usuarios autenticados vía LDAP/MFA
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

$id_nomina = (int)$_GET['id'];
$u_sesion = $_SESSION['usuario_id'];
$rol_sesion = $_SESSION['rol'];

// 2. CONSULTA DE SEGURIDAD: Solo puedes ver la nómina si es tuya O si eres RRHH/Admin
$sql = "SELECT n.*, u.nombre, u.apellido, u.dni_nie, d.nombre as depto_nombre 
        FROM nominas n
        JOIN usuarios u ON n.usuario_id = u.id
        JOIN departamentos d ON u.departamento_id = d.id
        WHERE n.id = $id_nomina";

$res = mysqli_query($conexion, $sql);
$n = mysqli_fetch_assoc($res);

// Bloqueo de acceso no autorizado
if (!$n || ($n['usuario_id'] != $u_sesion && $rol_sesion != 'rrhh' && $rol_sesion != 5002 && $rol_sesion != 5000)) {
    die("Acceso denegado: No tiene permisos para ver este documento.");
}

$meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nómina_<?php echo $n['apellido'] . "_" . $meses[$n['mes']]; ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 40px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #00d4ff; padding-bottom: 20px; }
        .company-info h2 { color: #00d4ff; margin: 0; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table th, .details-table td { padding: 12px; border: 1px solid #eee; text-align: left; }
        .details-table th { background: #f8fafc; }
        .total-row { font-size: 1.2rem; font-weight: bold; background: #0f172a; color: white; }
        .footer-note { font-size: 0.8rem; color: #666; margin-top: 30px; text-align: center; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor:pointer; background:#00d4ff; border:none; font-weight:bold;">🖨️ Imprimir o Guardar PDF</button>
        <a href="mis_nominas.php" style="margin-left:10px;">Volver</a>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="company-info">
                <h2>CORPNET SECURITY S.A.</h2>
                <p>CIF: A-12345678B<br>Calle Falsa 123, Madrid, España</p>
            </div>
            <div class="nomina-meta" style="text-align: right;">
                <p><strong>RECIBO DE SALARIOS</strong><br>
                Periodo: <?php echo $meses[$n['mes']] . " " . $n['anio']; ?><br>
                Fecha Emisión: <?php echo date('d/m/Y'); ?></p>
            </div>
        </div>

        <div style="margin: 20px 0; display: flex; justify-content: space-between;">
            <div>
                <strong>TRABAJADOR:</strong><br>
                <?php echo $n['nombre'] . " " . $n['apellido']; ?><br>
                DNI: <?php echo $n['dni_nie']; ?><br>
                Depto: <?php echo $n['depto_nombre']; ?>
            </div>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Devengos</th>
                    <th>Deducciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Salario Base (Horas: <?php echo number_format($n['total_horas_ordinarias'], 1); ?>)</td>
                    <td><?php echo number_format($n['salario_bruto'], 2); ?> €</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Retención IRPF</td>
                    <td></td>
                    <td>- <?php echo number_format($n['deduccion_irpf'], 2); ?> €</td>
                </tr>
                <tr>
                    <td>Seguridad Social Trabajador</td>
                    <td></td>
                    <td>- <?php echo number_format($n['deduccion_seg_social'], 2); ?> €</td>
                </tr>
                <tr class="total-row">
                    <td>LÍQUIDO TOTAL A PERCIBIR</td>
                    <td colspan="2" style="text-align: right;"><?php echo number_format($n['salario_neto'], 2); ?> €</td>
                </tr>
            </tbody>
        </table>

        <div class="footer-note">
            <p>Este documento ha sido validado electrónicamente mediante el sistema de identidad <strong>LDAP + MFA</strong> de CorpNet Security.</p>
            <p>Sello de la empresa conforme al Art. 29 del Estatuto de los Trabajadores.</p>
        </div>
    </div>
</body>
</html>
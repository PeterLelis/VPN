<?php
include('../config.php');

// 1. SEGURIDAD: Verificar sesión activa
// El sistema LDAP de la Fase 6 deja el nombre de usuario en $_SESSION['user']
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$nombre_empleado = $_SESSION['nombre'];

// 2. CONSULTA ADAPTADA: Obtener nóminas del usuario actual
// Ordenadas por las más recientes primero
$sql = "SELECT * FROM nominas 
        WHERE usuario_id = '$usuario_id' 
        ORDER BY anio DESC, mes DESC";

$resultado = mysqli_query($conexion, $sql);

// Función auxiliar para convertir número de mes a nombre
function nombreMes($n) {
    $meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    return $meses[$n];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Nóminas - CorpNet Security</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <style>
        .n-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .status-pagada { background: #dcfce7; color: #166534; }
        .status-pendiente { background: #fef9c3; color: #854d0e; }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>CORP<span>NET</span> / Mis Documentos</h1>
        <div>
            <span>Bienvenido, <strong><?php echo htmlspecialchars($nombre_empleado); ?></strong></span>
            <a href="dashboard.php" class="btn btn-outline" style="margin-left:1rem;">Volver</a>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 2rem;">
            <h2>📄 Histórico de Nóminas</h2>
            <p class="text-light">Desde aquí puedes consultar y descargar tus recibos de salarios validados por el sistema LDAP.</p>
        </header>

        <?php if (mysqli_num_rows($resultado) > 0): ?>
            <div class="n-grid">
                <?php while ($n = mysqli_fetch_assoc($resultado)): ?>
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div>
                                <h3 style="border:none; margin:0;"><?php echo nombreMes($n['mes']) . " " . $n['anio']; ?></h3>
                                <small class="text-light">ID Recibo: #<?php echo str_pad($n['id'], 5, "0", STR_PAD_LEFT); ?></small>
                            </div>
                            <span class="status-badge <?php echo ($n['estado'] == 'Pagada' || $n['estado'] == 'Emitida') ? 'status-pagada' : 'status-pendiente'; ?>">
                                <?php echo strtoupper($n['estado']); ?>
                            </span>
                        </div>

                        <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Salario Bruto:</span>
                                <strong><?php echo number_format($n['salario_bruto'], 2); ?> €</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; color: #ef4444; font-size: 0.9rem;">
                                <span>Deducciones (IRPF + SS):</span>
                                <span>- <?php echo number_format($n['deduccion_irpf'] + $n['deduccion_seg_social'], 2); ?> €</span>
                            </div>
                            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 0.5rem 0;">
                            <div style="display: flex; justify-content: space-between; color: #1e293b; font-weight: bold;">
                                <span>LÍQUIDO A PERCIBIR:</span>
                                <span style="font-size: 1.2rem;"><?php echo number_format($n['salario_neto'], 2); ?> €</span>
                            </div>
                        </div>

                        <a href="ver_pdf.php?id=<?php echo $n['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center; display: block;">
                            📥 Descargar PDF
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <p class="text-light">Aún no tienes nóminas generadas para el año <?php echo date('Y'); ?>.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
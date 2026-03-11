<?php
include('../config.php');

// 1. SEGURIDAD: Solo RRHH (Rol 5002 en LDAP) o Admin (5000)
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] != 'rrhh' && $_SESSION['rol'] != 5002)) {
    header("Location: ../index.php"); 
    exit();
}

$user_id = $_SESSION['usuario_id'];
$mes_actual = date('n');
$anio_actual = date('Y');
$error = $_GET['error'] ?? "";
$exito = $_GET['exito'] ?? "";

// --- 2. LÓGICA DE GESTIÓN: ALTA DE USUARIOS ---
if (isset($_POST['alta_usuario'])) {
    $nombre   = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $apellido = mysqli_real_escape_string($conexion, $_POST['apellido']);
    $email    = mysqli_real_escape_string($conexion, $_POST['email']); // UID para el "Cerebro" LDAP
    $dni      = mysqli_real_escape_string($conexion, $_POST['dni']);
    $pass     = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $rol      = $_POST['rol_id'];
    $depto    = $_POST['depto_id'];
    $salario  = $_POST['salario'];

    $sql_ins = "INSERT INTO usuarios (nombre, apellido, email, dni_nie, password, rol_id, departamento_id, salario_base_mensual, estado) 
                VALUES ('$nombre', '$apellido', '$email', '$dni', '$pass', '$rol', '$depto', '$salario', 1)";
    
    if (mysqli_query($conexion, $sql_ins)) { 
        $exito = "Empleado registrado. Sincroniza su UID en el contenedor OpenLDAP."; 
    } else { 
        $error = "Error: " . mysqli_error($conexion); 
    }
}

// --- 3. LÓGICA DE GESTIÓN: BAJA DE USUARIOS ---
if (isset($_GET['dar_baja'])) {
    $id_baja = (int)$_GET['dar_baja'];
    mysqli_query($conexion, "UPDATE usuarios SET estado = 0 WHERE id = $id_baja");
    header("Location: rrhh.php?exito=Empleado+desactivado");
    exit();
}

// --- 4. CONSULTAS PARA LA VISTA ---
$deptos = mysqli_query($conexion, "SELECT * FROM departamentos");
$roles  = mysqli_query($conexion, "SELECT * FROM roles");

// Lista de empleados con sus horas del mes actual
$sql_empleados = "SELECT u.*, d.nombre as depto_nombre, 
                  IFNULL((SELECT SUM(horas_totales) FROM registros_horarios 
                  WHERE usuario_id = u.id AND MONTH(entrada) = $mes_actual AND YEAR(entrada) = $anio_actual), 0) as horas_mes
                  FROM usuarios u
                  LEFT JOIN departamentos d ON u.departamento_id = d.id
                  WHERE u.estado = 1";
$empleados = mysqli_query($conexion, $sql_empleados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión RRHH - PercyJackSon Security</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <nav class="navbar">
        <h1>CORP<span>NET</span> / RRHH (5002)</h1>
        <div>
            <a href="../trabajador/dashboard.php" class="btn btn-outline" style="margin-right:1rem;">Mi Panel Personal</a>
        <a href="../trabajador/mis_nominas.php" class="btn btn-outline" style="margin-right:1rem;">Mis Nóminas</a>
            <span>Usuario: <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
            <a href="../logout.php" class="btn btn-outline" style="margin-left:1rem;">Cerrar Sesión</a>
        </div>
    </nav>


    <?php include('componente_fichaje.php'); ?>

    <main class="container">
        
        <?php if($exito): ?><div class="alert alert-info"><?php echo htmlspecialchars($exito); ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

        <div class="grid">
            <div class="card">
                <h3>👤 Alta de Personal</h3>
                <form method="POST">
                    <div class="form-group"><label>Nombre</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="form-group"><label>Apellido</label><input type="text" name="apellido" class="form-control" required></div>
                    <div class="form-group"><label>Email (UID LDAP)</label><input type="email" name="email" class="form-control" required></div>
                    <div class="form-group"><label>DNI</label><input type="text" name="dni" class="form-control" required></div>
                    <div class="form-group"><label>Password Inicial</label><input type="password" name="password" class="form-control" required></div>
                    
                    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label>Depto.</label>
                            <select name="depto_id" class="form-control">
                                <?php while($d = mysqli_fetch_assoc($deptos)) echo "<option value='{$d['id']}'>{$d['nombre']}</option>"; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Rol</label>
                            <select name="rol_id" class="form-control">
                                <?php while($r = mysqli_fetch_assoc($roles)) echo "<option value='{$r['id']}'>{$r['nombre']}</option>"; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group"><label>Salario Base Mensual (€)</label><input type="number" name="salario" class="form-control" step="0.01" required></div>
                    <button type="submit" name="alta_usuario" class="btn btn-primary" style="width:100%;">Registrar en Sistema</button>
                </form>
            </div>

            <div class="card">
                <h3>📋 Control de Nóminas (<?php echo date('F Y'); ?>)</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Horas</th>
                                <th>Estado Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($e = mysqli_fetch_assoc($empleados)): 
                                // Verificamos si ya existe nómina este mes para este empleado
                                $id_e = $e['id'];
                                $check_pago = mysqli_query($conexion, "SELECT id FROM nominas WHERE usuario_id = $id_e AND mes = $mes_actual AND anio = $anio_actual");
                                $pagado = mysqli_num_rows($check_pago) > 0;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($e['nombre']." ".$e['apellido']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($e['depto_nombre']); ?></small>
                                </td>
                                <td><?php echo number_format($e['horas_mes'], 1); ?>h</td>
                                <td style="display: flex; gap: 5px;">
                                    <?php if ($pagado): ?>
                                        <span class="badge" style="background:#10b981; color:white; padding:5px 10px;">PAGADO</span>
                                    <?php else: ?>
                                        <a href="generar_nomina.php?id=<?php echo $e['id']; ?>" class="btn btn-outline" style="font-size:10px; padding:5px;">PAGAR</a>
                                    <?php endif; ?>
                                    <a href="rrhh.php?dar_baja=<?php echo $e['id']; ?>" class="btn btn-danger" style="font-size:10px; padding:5px;" onclick="return confirm('¿Confirmar baja?')">BAJA</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
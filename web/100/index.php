<?php
require_once('config.php');

// Función de redirección mejorada para evitar bucles
function redirigirSegunRol($rol) {
    switch ($rol) {
        case 'trabajador':
            header("Location: trabajador/dashboard.php");
            break;
        case 'rrhh':
            header("Location: privado/rrhh.php");
            break;
        case 'admin_db':
        case 'admin':
            header("Location: privado/admin.php");
            break;
        case 'direccion':
        case 'director': // Añadido soporte para ambos nombres
            header("Location: privado/direccion.php");
            break;
        default:
            // CAMBIO CLAVE: Si el rol no se reconoce, destruir sesión y mostrar error
            // en lugar de redirigir al mismo sitio infinitamente.
            session_destroy();
            die("Error: Rol no reconocido ($rol). Contacte con soporte técnico.");
            break;
    }
    exit();
}

// Si ya hay sesión, redirigir automáticamente
if (isset($_SESSION['usuario_id']) && isset($_SESSION['2fa_verificado'])) {
    redirigirSegunRol($_SESSION['rol']);
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim(mysqli_real_escape_string($conexion, $_POST['email']));
    $pass  = trim($_POST['password']);

    // Traemos el nombre del rol para que el switch funcione correctamente
    $sql = "SELECT u.*, r.nombre as rol_nombre 
            FROM usuarios u 
            JOIN roles r ON u.rol_id = r.id 
            WHERE u.email = '$email' AND u.estado = 1 LIMIT 1";
    
    $res = mysqli_query($conexion, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $u = mysqli_fetch_assoc($res);

        if (password_verify($pass, $u['password'])) {
            // Configuración de Sesión
            $_SESSION['usuario_id'] = $u['id'];
            $_SESSION['rol']        = $u['rol_nombre'];
            $_SESSION['nombre']     = $u['nombre'] . " " . $u['apellido'];
            $_SESSION['2fa_verificado'] = true; // Bypass temporal para desarrollo

            redirigirSegunRol($u['rol_nombre']);
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado o inactivo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso CorpNet</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="login-body">
    <div class="card login-card" style="max-width:400px; margin: 100px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="text-align:center; margin-bottom:1.5rem;">CORP<span>NET</span></h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" style="color:red; margin-bottom:10px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group" style="margin-bottom:15px;">
                <label>Email Corporativo</label>
                <input type="email" name="email" class="form-control" style="width:100%;" placeholder="usuario@empresa.com" required>
            </div>
            <div class="form-group" style="margin-bottom:15px;">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" style="width:100%;" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:10px;">Acceder al Sistema</button>
        </form>
    </div>
</body>
</html>
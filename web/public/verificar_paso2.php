<?php
require_once '../auth/mfa_validator.php';
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['mfa_code'];
    $secret = $_SESSION['temp_mfa_secret'];
    $uid_ldap = $_SESSION['temp_user']; // El nombre de usuario que vino de LDAP

    // Aquí llamamos al script de Python de la Fase 6
    if (validar_mfa_php($secret, $code)) {




// --- CONEXIÓN CON MARIADB ---
        // Buscamos al usuario por su nuevo campo ldap_uid
        $stmt = $conexion->prepare("SELECT u.id, u.nombre, u.apellido, r.id as rol_id, r.nombre as rol_nombre 
                                   FROM usuarios u 
                                   JOIN roles r ON u.rol_id = r.id 
                                   WHERE u.ldap_uid = ? AND u.estado = 1");
        $stmt->bind_param("s", $uid_ldap);
        $stmt->execute();
        $resultado = $stmt->get_result();
 
        if ($usuario_db = $resultado->fetch_assoc()) {
            // ÉXITO: Creamos la sesión real con datos de la BD
            $_SESSION['usuario_id'] = $usuario_db['id'];
            $_SESSION['nombre']     = $usuario_db['nombre'] . " " . $usuario_db['apellido'];
            $_SESSION['rol']        = $usuario_db['rol_nombre'];
            $_SESSION['rol_id']     = $usuario_db['rol_id']; // El número (5000, 5001...)
            $_SESSION['2fa_verificado'] = true;
 
            // Limpiamos temporales
            unset($_SESSION['temp_user'], $_SESSION['temp_mfa_secret'], $_SESSION['temp_rol']);
 
            // Redirigir según el nuevo estándar de IDs
            if ($usuario_db['rol_id'] == 5000) header("Location: ../privado/admin.php");
            elseif ($usuario_db['rol_id'] == 5003) header("Location: ../privado/direccion.php");
            elseif ($usuario_db['rol_id'] == 5002) header("Location: ../privado/rrhh.php");
            else header("Location: ../trabajador/dashboard.php");
            
            exit();
        } else {
            die("Error: Usuario validado en LDAP pero no encontrado en MariaDB (UID: $uid_ldap)");
        }
    } else {
        header("Location: mfa_challenge.php?error=1");
        exit();
    }
}






/*

        // ÉXITO: Ahora sí, creamos la sesión real del usuario
        $_SESSION['user'] = $_SESSION['temp_user'];
        $_SESSION['rol'] = $_SESSION['temp_rol'];
        
        // Limpiamos los datos temporales por seguridad
        unset($_SESSION['temp_user'], $_SESSION['temp_mfa_secret']);
        
        header("Location: dashboard.php");
        exit();
    } else {
        // Si el código está mal, volvemos a pedirlo
        header("Location: mfa_challenge.php?error=1");
        exit();
    }
}*/
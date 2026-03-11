<?php
require_once '../auth/mfa_validator.php';
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['mfa_code'];
    $secret = $_SESSION['new_mfa_secret'];
    $user = $_SESSION['temp_user'];

    if (validar_mfa_php($secret, $code)) {
        // ÉXITO: Guardamos el secreto en la base de datos
        $stmt = $conexion->prepare("UPDATE usuarios SET mfa_secret = ? WHERE ldap_uid = ?");
        $stmt->bind_param("ss", $secret, $user);
        
        if ($stmt->execute()) {
            $_SESSION['temp_mfa_secret'] = $secret;
            // Ahora que está registrado, lo mandamos al flujo normal de verificación
            // o directamente al login final. Vamos a mandarlo a verificar_paso2.php
            header("Location: verificar_paso2.php", true, 307); // 307 mantiene el POST si fuera necesario
            // Para simplificar, mejor redirigir al paso 2 simulando el post
            echo "<form id='f' action='verificar_paso2.php' method='POST'>
                    <input type='hidden' name='mfa_code' value='$code'>
                  </form><script>document.getElementById('f').submit();</script>";
        }
    } else {
        header("Location: mfa_register.php?error=1");
    }
}

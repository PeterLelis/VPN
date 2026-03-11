<?php 
require_once '../config/config.php'; 
// Si alguien intenta entrar aquí sin pasar por LDAP, lo echamos
if (!isset($_SESSION['temp_user'])) { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PercyJackSon Security | MFA</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-card">
        <h2>Identidad Verificada</h2>
        <p style="text-align:center; color:#94a3b8; margin-bottom:1.5rem;">
            Se requiere el código de seguridad de su dispositivo móvil para continuar.
        </p>
        <form action="verificar_paso2.php" method="POST">
            <div class="input-group">
                <label>Código TOTP</label>
                <input type="text" name="mfa_code" placeholder="123456" maxlength="6" required autofocus>
            </div>
            <button type="submit">Verificar y Entrar</button>
        </form>
    </div>
</body>
</html>
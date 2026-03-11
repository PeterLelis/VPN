<?php
require_once '../config/config.php';
// session_start();

if (!isset($_SESSION['temp_user'])) { header("Location: login.php"); exit(); }

// Generar un secreto aleatorio de 16 caracteres (Base32 style)
if (!isset($_SESSION['new_mfa_secret'])) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < 16; $i++) { $secret .= $chars[rand(0, 31)]; }
    $_SESSION['new_mfa_secret'] = $secret;
}

$user = $_SESSION['temp_user'];
$secret = $_SESSION['new_mfa_secret'];
$titulo = "PercyJackSon_Security";
$qr_url = "https://quickchart.io/chart?cht=qr&chs=200x200&chl=otpauth://totp/$titulo:$user?secret=$secret%26issuer=$titulo";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro MFA</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-card">
        <h2>Configurar MFA</h2>
        <p>Escanea este código con Google Authenticator:</p>
        <img src="<?php echo $qr_url; ?>" style="margin: 20px 0;">
        <p>Tu secreto: <code><?php echo $secret; ?></code></p>
        
        <form action="verificar_registro.php" method="POST">
            <input type="text" name="mfa_code" placeholder="Código de 6 dígitos" required autofocus>
            <button type="submit">Verificar y Activar</button>
        </form>
    </div>
</body>
</html>

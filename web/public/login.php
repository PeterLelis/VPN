<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PercyJackSon Security | Identificación</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-card">
        <h2>Identificación</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="error-msg">Usuario o contraseña incorrectos.</div>
        <?php endif; ?>
        <form action="verificar_paso1.php" method="POST">
            <div class="input-group">
                <label>Usuario LDAP</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Siguiente</button>
        </form>
    </div>
</body>
</html>
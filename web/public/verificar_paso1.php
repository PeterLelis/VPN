<?php
require_once '../auth/ldap_auth.php';
require_once '../config/config.php'; // Añadimos la conexión a DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    $ldap_res = autenticar_ldap($user, $pass);

    if ($ldap_res['status']) {
        $_SESSION['temp_user'] = $user;
        $_SESSION['temp_rol'] = $ldap_res['rol'];

        // BUSCAR SECRETO EN MARIADB
        $stmt = $conexion->prepare("SELECT mfa_secret FROM usuarios WHERE ldap_uid = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $res = $stmt->get_result();
        $usuario_db = $res->fetch_assoc();

        if ($usuario_db && !empty($usuario_db['mfa_secret'])) {
            // Ya tiene secreto -> A verificar código
            $_SESSION['temp_mfa_secret'] = $usuario_db['mfa_secret'];
            header("Location: mfa_challenge.php");
        } else {
            // NO tiene secreto -> A registrarse
            header("Location: mfa_register.php");
        }
        exit();
    } else {
        header("Location: login.php?error=1");
        exit();
    }
}
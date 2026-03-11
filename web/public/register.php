<?php
require_once '../config/config.php';
// Lógica para generar un secreto aleatorio de 16 caracteres
$nuevo_secreto = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ234567"), 0, 16);

// Aquí se usaría ldap_add para insertar el usuario en ou=users
// con el atributo 'description' conteniendo el $nuevo_secreto
?>
<h3>Registro de Nuevo Usuario</h3>
<p>Tu secreto MFA es: <strong><?php echo $nuevo_secreto; ?></strong></p>
<p>Escanea este código en tu App antes de continuar.</p>

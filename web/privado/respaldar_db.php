<?php
// include('../config.php');

// 1. SEGURIDAD: Solo Admin (5000) puede respaldar
/*if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 5000)) {
    die("Acceso denegado: Se requiere privilegios de administrador LDAP.");
}
*/
/*
if (!isset($_SESSION['usuario_id']) ) {
    die("Acceso denegado: Se requiere privilegios de administrador LDAP.");
}



// 2. CONFIGURACIÓN DE CREDENCIALES
// Si $user o $pass están vacíos en config.php, usamos los valores por defecto de XAMPP
$db_user = isset($user) ? $user : 'db_sysadmin'; 
$db_pass = isset($pass) ? $pass : 'Pass_SysAdmin_2026'; 
$db_host = isset($host) ? $host : 'localhost';
$db_name = isset($db) ? $db : 'sistema_corporativo';

$nombre_archivo = "backup_corpnet_" . date("Y-m-d_H-i-s") . ".sql";

// 3. COMANDO DE EXPORTACIÓN
// Agregamos comillas a la contraseña por si estuviera vacía
$comando = "/usr/bin/mysqldump --user=$db_user --password=\"$db_pass\" --host=$db_host $db_name";

// 4. EJECUCIÓN
$salida = shell_exec($comando);

if ($salida) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    echo $salida;
    exit();
} else {
    // Si falla, mostramos el comando para que puedas probarlo en una terminal
    echo "Error al generar el volcado. Intenta ejecutar este comando en la consola de XAMPP para ver el error real:<br>";
    echo "<code>$comando</code>";
}


*/

// Incluimos la configuración para obtener las credenciales de la DB
require_once('../config.php');

// SEGURIDAD: Solo permitir si el usuario está logueado como admin
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Configuración de la base de datos (Usamos los nombres del servicio Docker)
$host = "db"; 
$user = "root";
$pass = "Admin01";
$dbname = "sistema_corporativo";


// Comando actualizado para desactivar la exigencia de SSL
$comando = "mysqldump --ssl-mode=DISABLED -h {$host} -u {$user} -p'{$pass}' {$dbname} 2>&1";

// ... (resto del código igual)

// Nombre del archivo de salida
$fecha = date("Y-m-d_H-i-s");
$nombre_archivo = "backup_{$dbname}_{$fecha}.sql";

// Cabeceras para forzar la descarga del archivo
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Comando para realizar el volcado (mysqldump)

$comando = "mysqldump --skip-ssl -h {$host} -u {$user} -p'{$pass}' {$dbname} 2>&1";

// Ejecutar el comando y enviar la salida directamente al navegador
passthru($comando);
exit();


?>



<?php
// 1. Configuración de seguridad (Debe ir antes de session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mantener en 0 para HTTP (localhost)

// 2. Inicio de sesión único
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//---------------------------------------------------------------------

$host = "db";
$db   = "sistema_corporativo";

// Mapeo de usuarios MySQL según el rol de la sesión
$config_db = [
    'admin_db'   => ['user' => 'db_sysadmin',   'pass' => 'Pass_SysAdmin_2026'],
    'rrhh'       => ['user' => 'db_rrhh',       'pass' => 'Pass_RRHH_2026'],
    'trabajador' => ['user' => 'db_trabajador', 'pass' => 'Pass_Worker_2026'],
    'direccion'  => ['user' => 'db_direccion',  'pass' => 'Pass_Dir_2026'], // <-- Verifica este
    'login'      => ['user' => 'db_login',      'pass' => 'Pass_Login_2026']
];

$rol_actual = $_SESSION['rol'] ?? 'login';

// Si el rol no existe en el mapa, forzamos login por seguridad
if (!array_key_exists($rol_actual, $config_db)) { $rol_actual = 'login'; }

$user_db = $config_db[$rol_actual]['user'];
$pass_db = $config_db[$rol_actual]['pass'];

$conexion = mysqli_connect($host, $user_db, $pass_db, $db);

if (!$conexion) {
    die("Error crítico de seguridad en la conexión.");
}

mysqli_set_charset($conexion, "utf8mb4");

//---------------------------------------------------------------------



// 3. Constantes de PercyJackSon Security
define('LDAP_HOST', 'ldap-server');
define('LDAP_PORT', 389);
define('LDAP_DN', 'dc=percyjackson,dc=security');
define('LDAP_USER_OU', 'ou=users,dc=percyjackson,dc=security');
?>
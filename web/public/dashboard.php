<?php
require_once '../config/config.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }

$rol = $_SESSION['rol'];
?>
<h1>Panel de Control - PercyJackSon_Security</h1>
<p>Bienvenido, <?php echo htmlspecialchars($_SESSION['user']); ?>.</p>

<div class="content">
    <?php if ($rol == 5000): // ADMIN ?>
        <div style="color: red;">
            <h3>MODO ADMINISTRADOR</h3>
            <p>Tienes acceso total a la infraestructura y gestión de red.</p>
            <button>Gestionar Servidores</button>
        </div>
    <?php elseif ($rol == 5001): // IT ?>
        <div style="color: blue;">
            <h3>MODO IT / SOPORTE</h3>
            [cite_start]<p>Acceso limitado a herramientas de diagnóstico e infraestructura IT[cite: 28].</p>
        </div>
    <?php else: // RRHH (5002) o otros ?>
        <div style="color: green;">
            <h3>PORTAL EMPLEADO (RRHH)</h3>
            <p>Acceso restringido a servicios específicos y nóminas.</p>
        </div>
    <?php endif; ?>
</div>
<a href="logout.php">Cerrar Sesión</a>

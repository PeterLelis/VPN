<?php
// Evitar acceso directo al componente si no hay sesión
if (!isset($_SESSION['usuario_id'])) { exit; }

$u_id = $_SESSION['usuario_id'];

// 1. Buscamos si el usuario (sea quien sea) tiene una jornada abierta
$sql_check = "SELECT id, entrada FROM registros_horarios 
              WHERE usuario_id = '$u_id' AND salida IS NULL 
              ORDER BY entrada DESC LIMIT 1";

$res_check = mysqli_query($conexion, $sql_check);

// Si la consulta falla, es que faltan los permisos GRANT que daremos luego
if (!$res_check) {
    echo "<div class='alert alert-danger'>Error de conexión: Verifica permisos de registros_horarios</div>";
} else {
    $fichaje = mysqli_fetch_assoc($res_check);
    $en_jornada = (bool)$fichaje;
?>

<div class="card" style="border-left: 5px solid <?php echo $en_jornada ? '#10b981' : '#3b82f6'; ?>; margin-bottom: 2rem; background: #fff;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px;">
        <div>
            <h4 style="margin:0; color: #1e293b;">🕒 Registro de Jornada Legal</h4>
            <p style="margin:0; font-size: 0.85rem; color: #64748b;">
                <?php 
                if ($en_jornada) {
                    echo "Sesión activa desde las <strong>" . date("H:i", strtotime($fichaje['entrada'])) . "</strong>";
                } else {
                    echo "Estado: <strong>Fuera de servicio</strong>";
                }
                ?>
            </p>
        </div>

        <form action="../trabajador/fichar.php" method="POST">
            <?php if (!$en_jornada): ?>
                <button type="submit" name="accion" value="entrada" class="btn btn-primary" style="background: #10b981; border:none;">
                    Iniciar Entrada
                </button>
            <?php else: ?>
                <button type="submit" name="accion" value="salida" class="btn btn-danger" style="background: #ef4444; border:none;">
                    Registrar Salida
                </button>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php } ?>
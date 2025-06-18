<?php
session_start();
include '../config.php';
include '../cabeceras-piePagina/Arriba_Usuario2.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$dni = $_SESSION['usuario'];

$stmt = $conn->prepare("SELECT Cantidad, Estado FROM vista_Usuarios WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

$plan_message = "";
$cantidad = null;
$estado = null;

if ($usuario) {
    $estado = $usuario['Estado'];
    $cantidad = $usuario['Cantidad'];

    if ($estado == 0) {
        $plan_message = "Tu plan está cancelado.";
    } else {
        switch ($cantidad) {
            case 30:
                $plan_message = "Estás suscrito al plan de 1 mes por \$30.";
                break;
            case 90:
                $plan_message = "Estás suscrito al plan de 3 meses por \$90.";
                break;
            case 120:
                $plan_message = "Estás suscrito al plan de 1 año por \$120.";
                break;
            default:
                $plan_message = "No se reconoce el plan actual (Cantidad: $cantidad).";
        }
    }
} else {
    $plan_message = "No tienes un plan activo.";
}
?>

<header>
  <a class="volver-btn" href="../InicioUsuario.php">Volver</a>
  <h1 class="welcome-title">Tu plan</h1>
  <a class="logout-btn" href="../publico/logout.php">Cerrar sesión</a>
</header>

<div class="plan-container">
  <div class="plan-details">
    <p><?= htmlspecialchars($plan_message) ?></p>

    <!-- Mostrar formulario de cambio de plan siempre, incluso si el plan está cancelado -->
    <form action="cambioPlan.php" method="post">
      <label for="nuevo_plan">Cambiar a otro plan:</label>
      <select name="nuevo_plan" id="nuevo_plan">
        <option value="30" <?= ($cantidad == 30) ? 'selected' : '' ?>>1 mes - $30</option>
        <option value="90" <?= ($cantidad == 90) ? 'selected' : '' ?>>3 meses - $90</option>
        <option value="120" <?= ($cantidad == 120) ? 'selected' : '' ?>>1 año - $120</option>
      </select>
    </form>
  </div>

  <!-- Mostrar botón de cambiar plan siempre -->
  <a class="plan" href="#" onclick="this.closest('.plan-container').querySelector('form').submit()">Cambiar plan</a>

  <!-- Mostrar botón de desapuntarse solo si el estado es 1 (activo) -->
  <?php if ($estado == 1): ?>
    <a class="unsubscribe-btn" href="#" onclick="confirmUnsubscribe()">Desapuntarse</a>
  <?php endif; ?>
</div>

<script>
function confirmUnsubscribe() {
  if (confirm("¿Estás seguro de que quieres darte de baja? Esta acción no se puede deshacer.")) {
    window.location.href = "bajaPlan.php";
  }
}
</script>
</body>
</html>

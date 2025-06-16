<?php
session_start();
include '../config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener el DNI del usuario desde la sesión (asumiendo que $_SESSION['usuario'] contiene el DNI)
$dni = $_SESSION['usuario'];

// Preparar la consulta para obtener la cantidad pagada
$stmt = $conn->prepare("SELECT Cantidad FROM vista_Usuarios WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// Determinar el mensaje del plan según la cantidad
$plan_message = "";
$subscription = false; // Para controlar si el usuario tiene una suscripción activa

if ($usuario && isset($usuario['Cantidad'])) {
    $cantidad = $usuario['Cantidad'];
    $subscription = true; // Hay una suscripción activa
    switch ($cantidad) {
        case 30:
            $plan_message = "Estás suscrito al plan de 1 mes por $30.";
            break;
        case 90:
            $plan_message = "Estás suscrito al plan de 3 meses por $90.";
            break;
        case 120:
            $plan_message = "Estás suscrito al plan de 1 año por $120.";
            break;
        default:
            $plan_message = "No se reconoce el plan actual (Cantidad: $cantidad).";
    }
} else {
    $plan_message = "No tienes un plan activo.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mi Plan - FitnessPro</title>
  <link rel="stylesheet" href="../styleUsuario.css" />
</head>
<body>
  <header>
    <a class="volver-btn" href="../InicioUsuario.php">Volver</a>
    <h1 class="welcome-title">Mi Plan</h1>
    <a class="logout-btn" href="../logout.php">Cerrar sesión</a>
  </header>

  <div class="plan-container">
    <div class="plan-details">
      <p><?php echo htmlspecialchars($plan_message); ?></p>
      <?php if ($subscription): ?>
        <form action="change_plan.php" method="post">
          <label for="nuevo_plan">Cambiar a otro plan:</label>
          <select name="nuevo_plan" id="nuevo_plan">
            <option value="30" <?php echo ($cantidad == 30) ? 'selected' : ''; ?>>1 mes - $30</option>
            <option value="90" <?php echo ($cantidad == 90) ? 'selected' : ''; ?>>3 meses - $90</option>
            <option value="120" <?php echo ($cantidad == 120) ? 'selected' : ''; ?>>1 año - $120</option>
          </select>
        </form>
      <?php endif; ?>
    </div>
    <?php if ($subscription): ?>
      <a class="plan" href="#" onclick="this.closest('.plan-container').querySelector('form').submit()">Cambiar plan</a>
      <a class="unsubscribe-btn" href="#" onclick="confirmUnsubscribe()">Desapuntarse</a>
    <?php endif; ?>
  </div>

  <script>
    function confirmUnsubscribe() {
      if (confirm("¿Estás seguro de que quieres darte de baja? Esta acción no se puede deshacer.")) {
        window.location.href = "unsubscribe.php";
      }
    }
  </script>
</body>
</html>
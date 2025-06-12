<?php
session_start();
include '../config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Enviar Incidencia - FitnessPro</title>
  <link rel="stylesheet" href="../styleUsuario.css" />
</head>
<body>
  <header>
    <a class="volver-btn" href="../InicioUsuario.php">Volver</a>
    <h1 class="welcome-title">Enviar Incidencia</h1>
    <a class="logout-btn" href="../logout.php">Cerrar sesión</a>
  </header>

  <div class="form-container" style="margin:  auto;">
    <h3>Formulario de Incidencia</h3>
    <form action="procesar_incidencia.php" method="POST">
      <label for="titulo">Título</label>
      <input type="text" id="titulo" name="titulo" required maxlength="50" />

      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="5" required></textarea>

      <label for="tipo">Tipo de Incidencia</label>
      <select id="tipo" name="tipo" required>
        <?php
          $tipos = $conn->query("SELECT id_tipo_incidencia, nombre FROM Tm_Tipo_Incidencias");
          while ($row = $tipos->fetch_assoc()) {
              echo "<option value='{$row['id_tipo_incidencia']}'>{$row['nombre']}</option>";
          }
        ?>
      </select>

      <button type="submit">Enviar</button>
    </form>
  </div>
</body>
</html>

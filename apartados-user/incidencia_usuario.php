<?php
session_start();
include '../config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

$usuario = $_SESSION['usuario']; // Este debería contener al menos el DNI o permitir obtenerlo
 // Asegúrate que 'dni' exista en $_SESSION['usuario']

$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$tipoIncidencia = $_POST['tipo'] ?? '';

if (empty($usuario) || empty($titulo) || empty($descripcion) || empty($tipoIncidencia)) {
    echo "Error: Todos los campos son obligatorios.";
    exit();
}

// Llamar al procedimiento almacenado
$stmt = $conn->prepare("CALL Crear_Incidencia(?, ?, ?, ?, @resultado)");
$stmt->bind_param("sssi", $usuario, $titulo, $descripcion, $tipoIncidencia);

if ($stmt->execute()) {
    $resultado = $conn->query("SELECT @resultado AS resultado")->fetch_assoc()['resultado'];

    switch ($resultado) {
        case 0:
            echo "✅ Incidencia registrada correctamente.";
            break;
        case -1:
            echo "❌ Error: Usuario no encontrado.";
            break;
        case -2:
            echo "❌ Error: Datos inválidos.";
            break;
        default:
            echo "⚠️ Error desconocido. Código: $resultado";
    }
} else {
    echo "⚠️ Error al ejecutar el procedimiento.";
}

$stmt->close();
$conn->close();
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

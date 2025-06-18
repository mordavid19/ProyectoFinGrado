<?php
session_start();
include '../config.php';
include '../cabeceras-piePagina/Arriba_Usuario2.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$mensaje = ''; // Variable para mostrar mensajes

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $tipoIncidencia = $_POST['tipo'] ?? '';

    if (empty($usuario) || empty($titulo) || empty($descripcion) || empty($tipoIncidencia)) {
        $mensaje = "❌ Error: Todos los campos son obligatorios.";
    } else {
        $stmt = $conn->prepare("CALL Crear_Incidencia(?, ?, ?, ?, @resultado)");
        $stmt->bind_param("sssi", $usuario, $titulo, $descripcion, $tipoIncidencia);

        if ($stmt->execute()) {
            $resultado = $conn->query("SELECT @resultado AS resultado")->fetch_assoc()['resultado'];

            switch ($resultado) {
                case 0:
                    // Obtener nombre del tipo de incidencia
                    $tipoNombreResult = $conn->query("SELECT nombre FROM Tm_Tipo_Incidencias WHERE id_tipo_incidencia = $tipoIncidencia");
                    $tipoNombre = $tipoNombreResult ? $tipoNombreResult->fetch_assoc()['nombre'] : 'Desconocido';
                    $mensaje = "✅ Incidencia registrada correctamente. Tipo: <strong>$tipoNombre</strong>";
                    break;
                case -1:
                    $mensaje = "❌ Error: Usuario no encontrado.";
                    break;
                case -2:
                    $mensaje = "❌ Error: Datos inválidos.";
                    break;
                default:
                    $mensaje = "⚠️ Error desconocido. Código: $resultado";
            }
        } else {
            $mensaje = "⚠️ Error al ejecutar el procedimiento.";
        }

        $stmt->close();
    }
}
?>

<header>
  <a class="volver-btn" href="../InicioUsuario.php">Volver</a>
  <h1 class="welcome-title">Crear Incidencia</h1>
  <a class="logout-btn" href="../publico/logout.php">Cerrar sesión</a>
</header>

  <div class="form-container" style="margin: auto;">
    <h3>Formulario de Incidencia</h3>

    <?php if (!empty($mensaje)): ?>
      <div class="mensaje-resultado" style="margin-bottom: 15px; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">
        <?= $mensaje ?>
      </div>
    <?php endif; ?>

    <form method="POST">
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

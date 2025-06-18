<?php
session_start();
include '../config.php';
include '../cabeceras-piePagina/Arriba_Usuario2.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

$dni = $_SESSION['usuario'];

// Obtener datos del usuario actual
$stmt = $conn->prepare("SELECT Nombre, Primer_Apellido, Segundo_Apellido, DNI, Contrasenna, Correo, Telefono, edad, Genero, Fecha_Registro FROM vista_Usuarios WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $nuevaPassword = $_POST['password'];

    // Validación simple de email
    if (strpos($email, '@') === false) {
        $mensaje = "El correo electrónico debe contener '@'.";
    } else {
        // Si hay nueva contraseña, hashearla
        $password_hash = null;
        if (!empty($nuevaPassword)) {
            $password_hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        }

        // Llamada al procedimiento almacenado
        $stmt = $conn->prepare("CALL Editar_Usuario(?, ?, ?, ?, @resultado)");
        $pass_param = $password_hash ?? '';  // Si es null, enviar cadena vacía
        $stmt->bind_param("sssi", $dni, $email, $pass_param, $telefono);
        $stmt->execute();
        $stmt->close();

        // Obtener el valor de salida
        $res = $conn->query("SELECT @resultado AS resultado")->fetch_assoc();
        $resultado = $res['resultado'];

        switch ($resultado) {
            case 0:
                $mensaje = "Datos actualizados correctamente.";
                // Refrescar datos
                $stmt = $conn->prepare("SELECT Nombre, Primer_Apellido, Segundo_Apellido, DNI, Contrasenna, Correo, Telefono, edad, Genero, Fecha_Registro FROM vista_Usuarios WHERE dni = ?");
                $stmt->bind_param("s", $dni);
                $stmt->execute();
                $result = $stmt->get_result();
                $usuario = $result->fetch_assoc();
                $stmt->close();
                break;
            case -1:
                $mensaje = "El correo no puede estar vacío.";
                break;
            case -3:
                $mensaje = "El teléfono no puede estar vacío.";
                break;
            case -4:
                $mensaje = "Formato de correo inválido.";
                break;
            case -5:
                $mensaje = "Usuario no encontrado.";
                break;
            case -6:
                $mensaje = "La nueva contraseña no puede ser igual a la actual.";
                break;
            default:
                $mensaje = "Error desconocido.";
        }
    }
}
?>

<header>
  <a class="volver-btn" href="../InicioUsuario.php">Volver</a>
  <h1 class="welcome-title">Tu Perfil</h1>
  <a class="logout-btn" href="../publico/logout.php">Cerrar sesión</a>
</header>
  <main class="main-layout">
    <div class="profile-view-container">
      <h2>Datos del Usuario</h2>

      <?php if (!empty($mensaje)): ?>
        <div class="profile-field" style="color: green;"><?= htmlspecialchars($mensaje) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="profile-field">
          <label class="profile-label">Nombre</label>
          <div class="profile-value"><?= htmlspecialchars($usuario['Nombre']) ?></div>
        </div>

        <div class="profile-field">
          <label class="profile-label">Primer Apellido</label>
          <div class="profile-value"><?= htmlspecialchars($usuario['Primer_Apellido']) ?></div>
        </div>

        <div class="profile-field">
          <label class="profile-label">Segundo Apellido</label>
          <div class="profile-value"><?= htmlspecialchars($usuario['Segundo_Apellido']) ?></div>
        </div>

        <div class="profile-field">
          <label class="profile-label" for="email">Correo Electrónico</label>
          <input class="profile-value" type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['Correo']) ?>" required>
        </div>

        <div class="profile-field">
          <label class="profile-label" for="telefono">Teléfono</label>
          <input class="profile-value" type="tel" name="telefono" id="telefono" value="<?= htmlspecialchars($usuario['Telefono']) ?>" required>
        </div>

        <div class="profile-field">
          <label class="profile-label" for="password">Contraseña</label>
          <input class="profile-value" type="password" name="password" id="password" placeholder="Nueva contraseña">
        </div>

        <div class="profile-field">
          <label class="profile-label">Edad</label>
          <div class="profile-value"><?= htmlspecialchars($usuario['edad']) ?></div>
        </div>

        <div class="profile-field">
          <label class="profile-label">Sexo</label>
          <div class="profile-value"><?= htmlspecialchars($usuario['Genero']) ?></div>
        </div>

        <div class="profile-field">
          <label class="profile-label">Fecha de Registro</label>
          <div class="profile-value"><?= htmlspecialchars($usuario['Fecha_Registro']) ?></div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
          <button type="submit" id="generate-btn">Guardar cambios</button>
        </div>
      </form>
    </div>
  </main>

</body>
</html>

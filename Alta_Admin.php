<?php
include 'config.php'; // Aquí debes tener la conexión $conn (mysqli)

// Inicializar mensaje de error/éxito
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = trim($_POST['nombreUsuario'] ?? '');
    $contrasenna = trim($_POST['contrasenna'] ?? '');
    $repetirContrasenna = trim($_POST['RepetirContrasenna'] ?? '');
    $rol = trim($_POST['rol']?? ''); // O define según tu lógica (ejemplo fijo)

    // Validar contraseñas coincidan
    if ($contrasenna !== $repetirContrasenna) {
        $error_message = "Las contraseñas no coinciden.";
    } elseif (empty($nombreUsuario) || empty($contrasenna)) {
        $error_message = "Por favor, completa todos los campos.";
    } else {
        $hashed_password = password_hash($contrasenna, PASSWORD_DEFAULT);
        // Preparar llamada al procedimiento almacenado
        $stmt = $conn->prepare("CALL Registrar_Admin(?, ?, ?, @resultado)");
        if ($stmt) {
            $stmt->bind_param("sss", $nombreUsuario, $hashed_password, $rol);
            $stmt->execute();
            $stmt->close();

            // Obtener valor de salida @resultado
            $res = $conn->query("SELECT @resultado as resultado");
            $row = $res->fetch_assoc();
            $resultado = (int)$row['resultado'];

            switch ($resultado) {
                case 0:
                    $success_message = "Registro exitoso. Ya puedes iniciar sesión.";
                    break;
                case -1:
                    $error_message = "Faltan campos obligatorios.";
                    break;
                case -2:
                    $error_message = "Rol inválido.";
                    break;
                case -3:
                    $error_message = "El nombre de usuario ya existe.";
                    break;
                case -4:
                default:
                    $error_message = "Error inesperado. Intenta más tarde.";
                    break;
            }
        } else {
            $error_message = "Error en la base de datos: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro - FitnessPro</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
  <header class="header">
    <h1 class="logo"><a href="admin.php" style="text-decoration: none; color: inherit;">Admin - FitnessPro</a></h1>
    <nav class="nav">
      <div class="nav-links">
        <a href="admin.php" class="nav-link">Inicio</a>
        <a href="Alta_Admin.php" class="nav-link">Alta Admin</a>
        <a href="obsvcn_Admin" class="nav-link">Consultar Observaciones</a>
        <a href="register.php" class="nav-link">Alta Usuario</a>
        <a href="login.php" class="nav-link">Cerrar sesión</a>
      </div>
    </nav>
  </header>

  <main class="main-content2">
  <div class="register-container">
    <h2>Crear Usuario</h2>

    <?php if ($success_message): ?>
      <div class="success-message" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <?= htmlspecialchars($success_message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off" onsubmit="return validateRegisterForm()">
      <label for="nombreUsuario">Nombre de Usuario</label>
      <input type="text" id="nombreUsuario" name="nombreUsuario" required maxlength="20" placeholder="Tu nombre de usuario" />

      <label for="contrasenna">Contraseña</label>
      <input type="password" id="contrasenna" name="contrasenna" required maxlength="20" placeholder="Contraseña" />

      <label for="RepetirContrasenna">Repita la contraseña</label>
      <input type="password" id="RepetirContrasenna" name="RepetirContrasenna" maxlength="20" placeholder="Repita la contraseña" />

      <label for="rol">Rol</label>
        <select id="rol" name="rol" required>
          <option value="">Selecciona</option>
          <option value="admin">Admin</option>
          <option value="monitor">Monitor</option>
        </select>

      <button type="submit" class="btn-login">Registrar</button>

      <p class="error-message" style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    </form>
  </div>
</main>


<?php
  include 'Abajo.php';
?>

  <script src="script.js"></script>
  <script>
    document.querySelector('.hamburger').addEventListener('click', () => {
      document.querySelector('.nav-links').classList.toggle('active');
    });

    document.querySelector('.toggle-password').addEventListener('click', () => {
      const passwordInput = document.querySelector('#password');
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      document.querySelector('.toggle-password').textContent = type === 'password' ? '👁️' : '🙈';
    });
  </script>
</body>
</html>
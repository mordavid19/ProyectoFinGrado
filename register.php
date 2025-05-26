<?php
include 'config.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $dni = $_POST['dni'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $genero = $_POST['genero'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $telefono_param = !empty($telefono) ? (int)$telefono : null;

    // Primero definimos la variable para el parámetro OUT
    $conn->query("SET @resultado = 0;");

    $stmt = $conn->prepare("CALL Registro_Usuario(?, ?, ?, ?, ?, ?, ?, ?, ?, @resultado)");

    if (!$stmt) {
        $error_message = "Error en la preparación: " . $conn->error;
    } else {
        // Bind de parámetros IN (9 parámetros)
        $stmt->bind_param(
            "ssssssiss",
            $nombre,
            $apellido1,
            $apellido2,
            $dni,
            $hashed_password,
            $email,
            $telefono_param,
            $fecha_nacimiento,
            $genero
        );

        if ($stmt->execute()) {
            $stmt->close();

            // Recuperamos el valor del OUT desde la variable @resultado
            $resultado = $conn->query("SELECT @resultado AS resultado")->fetch_assoc()['resultado'];

            if ($resultado == 0) {
                header("Location: index.html");
                exit();
            } elseif ($resultado == -1) {
                $error_message = "Error: Campos vacíos.";
            } elseif ($resultado == -2) {
                $error_message = "Error: Edad insuficiente.";
            } elseif ($resultado == -3) {
                $error_message = "Error: Email inválido.";
            } else {
                $error_message = "Error desconocido. Código: $resultado";
            }
        } else {
            $error_message = "Error al ejecutar el procedimiento: " . $stmt->error;
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
    <h1 class="logo"><a href="index.html" style="text-decoration: none; color: inherit;">FitnessPro</a></h1>
    <nav class="nav">
      <div class="hamburger">☰</div>
      <div class="nav-links">
        <a href="index.html" class="nav-link">Inicio</a>
        <a href="plans.html" class="nav-link">Planes</a>
        <a href="contact.html" class="nav-link">Contacto</a>
        <a href="login.php" class="nav-link">Ya eres socio</a>
      </div>
    </nav>
  </header>

  <main class="main-content2">
    <div class="register-container">
      <h2>Crear Cuenta</h2>
      <p class="subtitle">Únete a FitnessPro hoy</p>
      <form action="register.php" method="POST" autocomplete="off" onsubmit="return validateRegisterForm()">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required maxlength="20" placeholder="Tu nombre" />

        <label for="apellido1">Primer apellido</label>
        <input type="text" id="apellido1" name="apellido1" required maxlength="20" placeholder="Primer apellido" />

        <label for="apellido2">Segundo apellido</label>
        <input type="text" id="apellido2" name="apellido2" maxlength="20" placeholder="Segundo apellido (opcional)" />

        <label for="dni">DNI</label>
        <input type="text" id="dni" name="dni" required maxlength="9" placeholder="DNI (ej. 12345678A)" />

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required maxlength="100" placeholder="ejemplo@correo.com" />

        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" pattern="^[6789]\d{8}$" placeholder="Número de teléfono (ej. 612345678)" />

        <label for="fecha_nacimiento">Fecha de nacimiento</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required />

        <label for="genero">Género</label>
        <select id="genero" name="genero" required>
          <option value="">Selecciona</option>
          <option value="M">Masculino</option>
          <option value="F">Femenino</option>
        </select>

        <label for="password">Contraseña</label>
        <div class="input-wrapper">
          <input type="password" id="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres" />
          <span class="toggle-password">👁️</span>
        </div>

        <button type="submit" class="btn-login">Registrarse</button>
        <p class="error-message"><?php echo $error_message; ?></p>
        <a href="login.php">¿Ya tienes cuenta? Inicia sesión aquí</a>
      </form>
    </div>
  </main>

  <footer class="footer">
  <p>© 2025 FitnessPro. Todos los derechos reservados.</p>
    <div class="social-links">
      <a href="#">Facebook</a>
      <a href="#">Instagram</a>
      <a href="#">Twitter</a>
    </div>
    <p><a href="#">Contacto</a> | <a href="#">Términos y Condiciones</a></p>
  </footer>

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
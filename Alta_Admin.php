<?php
include 'config.php';

$error_message = '';



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
      <h2>Crear Usuario</h2>
      <form action="register.php" method="POST" autocomplete="off" onsubmit="return validateRegisterForm()">
        <label for="nombreUsuario">Nombre de Usuario</label>
        <input type="text" id="nombreUsuario" name="nombreUsuario" required maxlength="20" placeholder="Tu nombre de usuario" />

        <label for="contrasenna">Contraseña</label>
        <input type="text" id="contrasenna" name="contrasenna" required maxlength="20" placeholder="Contraseña" />

        <label for="RepetirContrasenna">Repita la contraseña</label>
        <input type="text" id="RepetirContrasenna" name="RepetirContrasenna" maxlength="20" placeholder="Repita la contraseña" />

       
        <button type="submit" class="btn-login">Registrar</button>
        <p class="error-message"><?php echo $error_message; ?></p>
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
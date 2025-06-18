<?php
session_start();
include '../config.php';
include '../cabeceras-piePagina/Arriba.php';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['usuario'], $_POST['password'])) {
        $usuario = $_POST['usuario'];
        $passwordIngresada = $_POST['password'];
        $rol = 'admin';

        // Primero: buscar en tabla staff
        $sql_admin = "SELECT username, password, rol FROM Tr_Staff WHERE username = ? AND rol = ?";
        $stmt = $conn->prepare($sql_admin);
        $stmt->bind_param("ss", $usuario, $rol);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hash = $row['password'];

            if (password_verify($passwordIngresada, $hash)) {
                // Contraseña correcta - admin
                $_SESSION['usuario'] = $usuario;
                $_SESSION['rol'] = 'admin';
                header("Location: ../apartados-admin/admin.php");
                exit();
            }
        }

        // Segundo: buscar en tabla usuarios
        $sql_user = "SELECT dni, password FROM Tr_Usuarios WHERE dni = ?";
        $stmt = $conn->prepare($sql_user);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hash = $row['password'];

            if (password_verify($passwordIngresada, $hash)) {
                // Contraseña correcta - cliente
                $_SESSION['usuario'] = $usuario;
                header("Location: ../InicioUsuario.php");
                exit();
            }
        }

        // Si no encontró o falló la verificación
        $error_message = "Usuario o contraseña incorrectos.";
    } else {
        $error_message = "Datos incompletos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Iniciar Sesión - FitnessPro</title>
  <link rel="stylesheet" href="../style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

  <main class="main-content2">
    <div class="login-container">
      <h2>Iniciar Sesión</h2>
      <p class="subtitle">Accede a tu cuenta FitnessPro</p>
      <form action="login.php" method="POST" onsubmit="return validateLoginForm()">
        <label for="usuario">Usuario (DNI o Nombre de usuario)</label>
        <div class="input-wrapper">
          <input type="text" name="usuario" id="usuario" placeholder="Usuario o DNI"/>
        </div>
        <label for="password">Contraseña</label>
        <div class="input-wrapper">
          <input type="password" name="password" id="password" placeholder="Contraseña"/>
          <span class="toggle-password">👁️</span>
        </div>
        <button type="submit" class="btn-login">Iniciar sesión</button>
        <p class="error-message"><?php echo $error_message; ?></p>
        <a href="register.php">¿No tienes cuenta? Regístrate aquí</a>
      </form>
    </div>
  </main>

<?php
  include '../cabeceras-piePagina/Abajo.php';
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
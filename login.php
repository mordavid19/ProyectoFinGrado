<?php
session_start();
include 'config.php';

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
                header("Location: admin.php");
                exit();
            }
        }

        // Segundo: buscar en tabla usuarios
        $sql_user = "SELECT dni, password FROM usuarios WHERE dni = ?";
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
                $_SESSION['rol'] = 'cliente';
                header("Location: index.html");
                exit();
            }
        }

        // Si no encontró o falló la verificación
        echo "<script>
            alert('Usuario o contraseña incorrectos.');
            window.location.href = 'login.php';
        </script>";
        exit();

    } else {
        echo "<script>
            alert('Datos incompletos.');
            window.location.href = 'login.php';
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
  
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Gym</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="login-container">
    <h2>FitnessPro</h2>
    <form action="login.php" method="POST" onsubmit="return validateForm()">
      <input type="text" name="usuario" id="usuario" placeholder="Usuario" required  />
      <input type="password" name="password" id="password" placeholder="Contraseña" required />
      <button type="submit" class="btn-login" name="accion" value="login">Iniciar sesión</button>
    </form>
    <p id="error" class="error-message"></p>
  </div>
  <script src="script.js"></script>
</body>
</html>


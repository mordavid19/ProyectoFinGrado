<?php
include 'config.php';

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
        die("Error en la preparación: " . $conn->error);
    }

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
             
              header("Location: index");
               
           
        } elseif ($resultado == -1) {
            echo "Error: Campos vacíos.";
        } elseif ($resultado == -2) {
            echo "Error: Edad insuficiente.";
        } elseif ($resultado == -3) {
            echo "Error: Email inválido.";
        } else {
            echo "Error desconocido. Código: $resultado";
        }

    } else {
        echo "Error al ejecutar el procedimiento: " . $stmt->error;
    }

}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="register-container">
  <h2>Crear cuenta</h2>
  <form action="" method="POST" autocomplete="off">
  <label for="nombre">Nombre</label>
  <input type="text" id="nombre" name="nombre" required maxlength="20" placeholder="Tu nombre" />

  <label for="apellido1">Primer apellido</label>
  <input type="text" id="apellido1" name="apellido1" required maxlength="20" placeholder="Primer apellido" />

  <label for="apellido2">Segundo apellido</label>
  <input type="text" id="apellido2" name="apellido2" maxlength="20" placeholder="Segundo apellido (opcional)" />

  <label for="dni">DNI</label>
  <input type="text" id="dni" name="dni" required maxlength="9" placeholder="DNI" />

  <label for="email">Correo electrónico</label>
  <input type="email" id="email" name="email" required maxlength="100" placeholder="ejemplo@correo.com" />

  <label for="telefono">Teléfono</label>
  <input type="tel" id="telefono" name="telefono" pattern="^[6789]\d{8}$" placeholder="Número de teléfono" />


  <label for="fecha_nacimiento">Fecha de nacimiento</label>
  <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" />

  <label for="genero">Género</label>
  <select id="genero" name="genero" required>
    <option value="">Selecciona</option>
    <option value="M">Masculino</option>
    <option value="F">Femenino</option>
  </select>

  <label for="password">Contraseña</label>
  <input type="password" id="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres" />

  <button type="submit">Registrarse</button>
</form>

  <a href="index">Volver al inicio</a>
</div>


</body>
</html>

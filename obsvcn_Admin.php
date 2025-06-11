<?php
include 'config.php';
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Obtener las observaciones
$sql = "SELECT * FROM vista_observaciones";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administración - FitnessPro</title>
  <link rel="stylesheet" href="admin-style.css">
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

    
<div class="admin-main-content" style="text-align: left;">
  <h2>Observaciones Registradas</h2>
  <div class="admin-table-container">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Correo Usuario</th>
          <th>ID</th>
          <th>Título</th>
          <th>Descripción</th>
          <th>Fecha Incidencia</th>
          <th>Tipo</th>
        </tr>
      </thead>
      <tbody>
        <?php while($fila = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($fila['Correo_Usuario']); ?></td>
          <td><?php echo htmlspecialchars($fila['ID']); ?></td>
          <td><?php echo htmlspecialchars($fila['Titulo']); ?></td>
          <td><?php echo htmlspecialchars($fila['Descripcion']); ?></td>
          <td><?php echo htmlspecialchars($fila['Fecha_Incidencia']); ?></td>
          <td><?php echo htmlspecialchars($fila['Tipo']); ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>


  <footer class="footer">
    <p>© 2025 FitnessPro. Todos los derechos reservados.</p>
    <div class="social-links">
      <a href="#"><img src="img/facebook.png" alt="Facebook"></a>
      <a href="#"><img src="img/instagram.png" alt="Instagram"></a>
      <a href="#"><img src="img/twitter.png" alt="Twitter"></a>
    </div>
    <p><a href="contact.html">Contacto</a> | <a href="terms.html">Términos y Condiciones</a></p>
  </footer>
</body>
</html>

<?php
$conn->close();
?>
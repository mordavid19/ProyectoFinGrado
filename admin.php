<?php
include 'config.php';
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Obtener los usuarios
$sql = "SELECT Nombre,Apellidos,DNI,Email,Telefono,Fecha_Registro,Pago,Fin_Pago FROM vista_usuarios_admin where Activo = 1" ;
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
    <h1 class="logo"><a href="index.html" style="text-decoration: none; color: inherit;">Admin - FitnessPro</a></h1>
    <nav class="nav">
      <div class="nav-links">
        <a href="index.html" class="nav-link">Inicio</a>
        <a href="Alta_Admin.php" class="nav-link">Alta Admin</a>
        <a href="obsvcn_Admin" class="nav-link">Consultar Observaciones</a>
        <a href="register.php" class="nav-link">Alta Usuario</a>
        <a href="login.php" class="nav-link">Cerrar sesión</a>
      </div>
    </nav>
  </header>

  <main class="admin-main-content">
    <h2>Usuarios Registrados</h2>

    <div class="admin-table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>DNI</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Fecha de Registro</th>
            <th>Fecha de Inicio Pago</th>
            <th>Fecha de Fin Pago</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($resultado->num_rows > 0): ?>
            <?php while ($row = $resultado->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['Nombre']) ?></td>
                <td><?= htmlspecialchars($row['Apellidos']) ?></td>
                <td><?= htmlspecialchars($row['DNI']) ?></td>
                <td><?= htmlspecialchars($row['Email']) ?></td>
                <td><?= htmlspecialchars($row['Telefono']) ?></td>
                <td>
                  <?php
                  $fecha_registro = DateTime::createFromFormat('d m Y', $row['Fecha_Registro']);
                  if ($fecha_registro !== false) {
                      echo htmlspecialchars($fecha_registro->format('d-m-Y'));
                  } else {
                      echo 'N/A';
                  }
                  ?>
                </td>
                <td>
                  <?php
                  $fecha_pago = DateTime::createFromFormat('d m Y', $row['Pago']);
                  if ($fecha_pago !== false) {
                      echo htmlspecialchars($fecha_pago->format('d-m-Y'));
                  } else {
                      echo 'N/A';
                  }
                  ?>
                </td>
                <td>
                  <?php
                  $fecha_fin_pago = DateTime::createFromFormat('d m Y', $row['Fin_Pago']);
                  if ($fecha_fin_pago !== false) {
                      echo htmlspecialchars($fecha_fin_pago->format('d-m-Y'));
                  } else {
                      echo 'N/A';
                  }
                  ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8">No hay usuarios registrados.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

<?php
  include 'Abajo.php';
?>
</body>
</html>

<?php
$conn->close();
?>
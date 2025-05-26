<?php
include 'config.php';
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}


// Obtener los usuarios
$sql = "SELECT * FROM vista_usuarios";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración - FitnessPro</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="grid-layout">

<header class="header">
    <h1 class="logo">Admin - FitnessPro</h1>
    <nav class="nav">
        <a href="index.html" class="nav-link">Inicio</a>
        <a href="" class="nav-link">Alta Admin</a>
        <a href="" class="nav-link">Consultar Observaciones</a>
        <a href="register.php" class="nav-link">Alta Usuario</a>
        <a href="login.php" class="nav-link">Cerrar sesión</a>
    </nav>
</header>

<main class="main-content">
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
                    <th>Fecha de Fin Pago </th>
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
                            <td><?= htmlspecialchars($row['Fecha_Registro']) ?></td>
                            <td><?= htmlspecialchars($row['Pago']) ?></td>
                            <td><?= htmlspecialchars($row['Fin_Pago']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No hay usuarios registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>

<?php
$conn->close();
?>

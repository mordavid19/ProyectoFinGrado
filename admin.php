<?php
include 'config.php';
include 'cabeceras-piePagina/Arriba_Admin.php';
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Obtener los usuarios
$sql = "SELECT Nombre,Apellidos,DNI,Email,Telefono,Fecha_Registro,Pago,Fin_Pago FROM vista_usuarios_admin where Activo = 1" ;
$resultado = $conn->query($sql);
?>
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
  include 'cabeceras-piePagina/Abajo.php';
?>
</body>
</html>

<?php
$conn->close();
?>
<?php
include 'config.php';
include 'Arriba_Admin.php';
session_start();


if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Obtener las observaciones
$sql = "SELECT * FROM vista_incidencias ";
$resultado = $conn->query($sql);
?>

    
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
<?php
session_start();
include '../config.php';
include '../cabeceras-piePagina/Arriba_Usuario2.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

$dni = $_SESSION['usuario'];
$success_message = '';
$error = '';

// Mostrar mensajes flash (si existen)
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Obtener id_usuario
$stmt = $conn->prepare("SELECT id_usuario FROM Tr_usuarios WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$stmt->bind_result($id_usuario);
$stmt->fetch();
$stmt->close();

if (!$id_usuario) {
    die("Usuario no encontrado.");
}

// Procesar envío del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nuevo_peso'])) {
    $nuevo_peso = floatval($_POST['nuevo_peso']);

    $stmt = $conn->prepare("CALL Introducir_Pesos(?, ?, @resultado)");
    $stmt->bind_param("sd", $dni, $nuevo_peso);
    $stmt->execute();
    $stmt->close();

    $res = $conn->query("SELECT @resultado as resultado");
    $row = $res->fetch_assoc();
    $resultado = intval($row['resultado']);
    $res->free();

    if ($resultado === 0) {
        $_SESSION['success_message'] = "Peso registrado correctamente.";
    } else {
        if ($resultado == -1) {
            $_SESSION['error'] = "Parámetros inválidos.";
        } elseif ($resultado == -2) {
            $_SESSION['error'] = "Usuario no encontrado.";
        } else {
            $_SESSION['error'] = "Error desconocido.";
        }
    }

    // Redirigir para evitar reenvío con F5
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Obtener historial de pesos
$stmt = $conn->prepare("SELECT Peso, Fecha_Pesaje FROM vista_pesos WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();
?>



<?php if ($success_message): ?>
    <div class="success-message" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin: 15px; border-radius: 4px;">
        <?= htmlspecialchars($success_message) ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="error-message" style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin: 15px; border-radius: 4px;">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<header>
  <a class="volver-btn" href="../InicioUsuario.php">Volver</a>
  <h1 class="welcome-title">Tu Peso</h1>
  <a class="logout-btn" href="../publico/logout.php">Cerrar sesión</a>
</header>
<div class="main-layout">
    <!-- Formulario de nuevo peso -->
    <div class="form-container">
        <h3>Registrar Peso</h3>
        <form method="POST">
            <label for="nuevo_peso">Peso (kg):</label>
            <input type="number" name="nuevo_peso" step="0.1" required>
            <button type="submit">Guardar</button>
        </form>
    </div>

    <!-- Tabla de historial -->
    <div class="table-container">
        <h3>Historial de Pesos</h3>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Peso</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Fecha_Pesaje']) ?></td>
                        <td><?= htmlspecialchars($row['Peso']) ?> kg</td>
                    </tr>
                <?php endwhile; $result->free(); unset($row); ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

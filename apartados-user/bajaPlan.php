<?php
session_start();
include '../config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$dni = $_SESSION['usuario'];

$stmt = $conn->prepare("CALL BajaPlan(?, @resultado)");
$stmt->bind_param("s", $dni);
$stmt->execute();
$stmt->close();

// Obtener el resultado
$res = $conn->query("SELECT @resultado AS resultado");
$row = $res->fetch_assoc();
$resultado = $row['resultado'];

switch ($resultado) {
    case -1:
        $mensaje = "Usuario no encontrado.";
        break;
    case 0:
        $mensaje = "Te has dado de baja correctamente o ya estabas dado de baja.";
        break;
    default:
        $mensaje = "Ocurrió un error inesperado.";
        break;
}

// Puedes redirigir con mensaje por GET o mostrar directamente
header("Location: plan_usuario.php?mensaje=" . urlencode($mensaje));
exit();
?>

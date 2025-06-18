<?php
session_start();
include '../config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$dni = $_SESSION['usuario'];
$nueva_cantidad = intval($_POST['nuevo_plan'] ?? 0);

if ($nueva_cantidad <= 0) {
    header("Location: plan_usuario.php?mensaje=" . urlencode("Cantidad inválida."));
    exit();
}

// Llamar procedimiento para modificar plan
$stmt = $conn->prepare("CALL ModificarPlan(?, ?, @resultado)");
$stmt->bind_param("si", $dni, $nueva_cantidad);
$stmt->execute();
$stmt->close();

// Obtener resultado
$res = $conn->query("SELECT @resultado AS resultado");
$row = $res->fetch_assoc();
$resultado = $row['resultado'];

switch ($resultado) {
    case -1:
        $mensaje = "No se pudo cambiar el plan. Verifica que el nuevo plan sea válido.";
        break;
    case 0:
        $mensaje = "¡Plan actualizado con éxito!";
        break;
    default:
        $mensaje = "Error inesperado al cambiar el plan.";
        break;
}

header("Location: plan_usuario.php?mensaje=" . urlencode($mensaje));
exit();
?>

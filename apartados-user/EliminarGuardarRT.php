<?php
session_start();
include '../config.php';
if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    exit("No autorizado");
}

$dni = $_SESSION['usuario'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['modo'])) {
    http_response_code(400);
    exit("Falta el modo de acción");
}

// Obtener ID usuario
$stmt = $conn->prepare("SELECT id_usuario FROM Tr_usuarios WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$stmt->bind_result($id_usuario);
$stmt->fetch();
$stmt->close();

// Obtener ID día
$stmt = $conn->prepare("SELECT id_dia FROM Tm_Dias WHERE nombre = ?");
$stmt->bind_param("s", $data['dia']);
$stmt->execute();
$stmt->bind_result($id_dia);
$stmt->fetch();
$stmt->close();

// Obtener o crear rutina
$stmt = $conn->prepare("SELECT id_rutina FROM Tr_Rutinas WHERE id_usuario = ? AND id_dia = ?");
$stmt->bind_param("ii", $id_usuario, $id_dia);
$stmt->execute();
$stmt->bind_result($id_rutina);
if (!$stmt->fetch()) {
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO Tr_Rutinas (id_usuario, id_dia) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_usuario, $id_dia);
    $stmt->execute();
    $id_rutina = $stmt->insert_id;
}
$stmt->close();

// Obtener o crear ID ejercicio
$stmt = $conn->prepare("SELECT id_ejercicio FROM Tr_Ejercicios WHERE nombre = ?");
$stmt->bind_param("s", $data['nombre']);
$stmt->execute();
$stmt->bind_result($id_ejercicio);
if (!$stmt->fetch()) {
    $stmt->close();
    $id_grupo_muscular = 7; // genérico
    $stmt = $conn->prepare("INSERT INTO Tr_Ejercicios (nombre, id_grupo_muscular) VALUES (?, ?)");
    $stmt->bind_param("si", $data['nombre'], $id_grupo_muscular);
    $stmt->execute();
    $id_ejercicio = $stmt->insert_id;
}
$stmt->close();

if ($data['modo'] === 'guardar') {
    // Guardar ejercicio
    $stmt = $conn->prepare("INSERT INTO Tr_Detalle_Rutina (id_rutina, id_ejercicio, series, repeticiones, peso) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiid", $id_rutina, $id_ejercicio, $data['series'], $data['repeticiones'], $data['peso']);
    $stmt->execute();
    $stmt->close();
    echo "GUARDADO";
} elseif ($data['modo'] === 'eliminar') {
    // Eliminar ejercicio exacto
    $stmt = $conn->prepare("DELETE FROM Tr_Detalle_Rutina 
        WHERE id_rutina = ? AND id_ejercicio = ? AND series = ? AND repeticiones = ? AND peso = ?");
    $stmt->bind_param("iiiid", $id_rutina, $id_ejercicio, $data['series'], $data['repeticiones'], $data['peso']);
    $stmt->execute();
    $stmt->close();
    echo "ELIMINADO";
} else {
    http_response_code(400);
    exit("Modo no válido");
}

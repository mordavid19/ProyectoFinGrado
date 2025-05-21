<?php
$host = "localhost";      // o 127.0.0.1
$user = "root";           // tu usuario de MySQL
$pass = "";               // tu contraseña de MySQL
$db   = "gimnasio_app"; // nombre de tu base de datos

$conn = new mysqli($host, $user, $pass, $db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Puedes definir otras configuraciones aquí si quieres
?>

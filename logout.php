<?php
session_start();
session_unset();     // Elimina todas las variables de sesión
session_destroy();   // Destruye la sesión actual

header("Location: index.php"); // Redirige al inicio de sesión
exit();

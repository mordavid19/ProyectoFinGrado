<?php
session_start();
session_unset();     // Elimina todas las variables de sesión
session_destroy();   // Destruye la sesión actual

header("Location: index.html"); // Redirige al inicio de sesión
exit();

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitnessPro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>FitnessPro</h2>
        <form action="profile.php" method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required />
            <input type="password" name="password" placeholder="Contraseña" required />
            <button type="submit" class="btn-login">Iniciar sesión</button>
        </form>
    </div>
</body>
</html>

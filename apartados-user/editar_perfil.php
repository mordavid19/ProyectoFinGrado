<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Perfil - FitnessPro</title>
  <link rel="stylesheet" href="../styleUsuario.css" />
</head>
<body>
  <header>
    <a href="../InicioUsuario.php" class="volver-btn">Volver</a>
    <h1 class="welcome-title">Modificar datos de usuario</h1>
    <a href="../logout.php" class="logout-btn">Cerrar sesión</a>
  </header>

  <main class="main-layout" style="padding-top: 120px; justify-content: center;">
    <div class="card" style="max-width: 400px; width: 100%;">
      <form id="editProfileForm" action="updateProfile.php" method="POST">
        <label for="name" style="display: block; font-weight: 700; margin-bottom: 6px;">Nombre:</label>
        <input type="text" id="name" name="name" placeholder="Tu nombre" required style="width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 1em;" />

        <label for="email" style="display: block; font-weight: 700; margin-bottom: 6px;">Correo electrónico:</label>
        <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required style="width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 1em;" />

        <label for="password" style="display: block; font-weight: 700; margin-bottom: 6px;">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Nueva contraseña" style="width: 100%; padding: 10px; margin-bottom: 25px; border-radius: 6px; border: 1px solid #ccc; font-size: 1em;" />

        <button type="submit" style="width: 100%; padding: 14px; background-color: #111; color: #fff; font-weight: 700; border: none; border-radius: 10px; font-size: 1.3em; cursor: pointer; transition: transform 0.2s ease;">
          Guardar cambios
        </button>
      </form>
    </div>
  </main>
</body>
</html>

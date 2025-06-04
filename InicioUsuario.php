<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mi Perfil - FitnessPro</title>
  <link rel="stylesheet" href="styleUsuario.css" />
</head>
<body>
  <header>
    <h1 class="welcome-title">Bienvenido a tu perfil</h1>
    <a href="logout.php" class="logout-btn">Cerrar sesión</a>
  </header>

  <main class="main-layout">
    <div class="grid-container">
      <div class="card">Editar perfil</div>

      <div class="qr-wrapper">
        <div class="qr-card">
          Mi código QR<br>
          <img id="qrCode" src="" alt="QR dinámico">
        </div>
        <div class="qr-overlay" id="qrOverlay">Se agotó el tiempo</div>
        <div class="qr-controls">
          <div id="timer">15:00</div>
          <button id="generate-btn" onclick="handleGenerate()">	Generar código de acceso</button>
        </div>
      </div>

      <div class="card">Ver dieta</div>
      <div class="card">Crear rutina</div>
      <div class="card">Progreso de peso</div>
    </div>
  </main>

  <script>
    let countdown;
    let remainingTime = 15 * 60;
    const qr = document.getElementById('qrCode');
    const timerDisplay = document.getElementById('timer');
    const generateBtn = document.getElementById('generate-btn');
    const qrOverlay = document.getElementById('qrOverlay');

    function updateTimerDisplay() {
      const minutes = Math.floor(remainingTime / 60);
      const seconds = remainingTime % 60;
      timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    function tick() {
      remainingTime--;
      updateTimerDisplay();
      if (remainingTime <= 0) {
        clearInterval(countdown);
        generateBtn.disabled = false;
        qr.style.filter = 'grayscale(100%)';
        qrOverlay.style.display = 'flex'; // Mostrar capa roja
      }
    }

    function startCountdown() {
      clearInterval(countdown);
      remainingTime = 1 * 60;
      updateTimerDisplay();
      countdown = setInterval(tick, 1000);
      generateBtn.disabled = false;
      qr.style.filter = 'none';
      qrOverlay.style.display = 'none'; // Ocultar capa roja
    }

    function handleGenerate() {
      if (remainingTime <= 0) {
        startCountdown();
      }
      qr.src = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + Date.now();
      startCountdown();
    }

    // Inicial
    handleGenerate();
  </script>
</body>
</html>

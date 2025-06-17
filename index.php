<?php
  include 'cabeceras-piePagina/Arriba.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gimnasio FitnessPro</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>


  <main class="main-content1">
    <h2>Bienvenido a FitnessPro</h2>
    <p class="subtitle">Tu gimnasio para transformar tu vida</p>
    <p>
      En FitnessPro te ofrecemos un ambiente moderno y motivador para alcanzar tus metas de salud y fitness.
      Únete a nuestra comunidad y disfruta de entrenadores profesionales, equipos de última generación y planes personalizados.
    </p>
    <p>
      ¿Listo para comenzar? ¡Date de alta ahora o inicia sesión si ya eres parte de nuestra familia!
    </p>

    <!-- Tarjetas de planes -->
    <div class="pricing-row">
      <div class="pricing-card">
        <div class="pricing-header">CUOTA MENSUAL <span class="tooltip">?</span></div>
        <div class="pricing-body">
          <div class="price"><span class="amount">30,00€</span><span class="vat">IVA incluido</span></div>
          <h3>Cuota mensual</h3>
          <ul class="features">
            <li>Sin permanencia</li>
            <li>Empieza hoy mismo.</li>
            <li>Entrenador por +15€</li>
          </ul>
        </div>
        <div class="pricing-footer">
          <a href="register.php?precio=30" class="cta-button">¡QUIERO ESTA!</a>
        </div>
      </div>

      <div class="pricing-card">
        <div class="pricing-header">CUOTA TRIMESTRAL <span class="tooltip">?</span></div>
        <div class="pricing-body">
          <div class="price"><span class="amount">90,00€</span><span class="vat">IVA incluido</span></div>
          <h3>Cuota trimestral</h3>
          <ul class="features">
            <li>Ahorra 10%</li>
            <li>Acceso ilimitado</li>
            <li>Soporte preferente</li>
          </ul>
        </div>
        <div class="pricing-footer">
          <a href="register.php?precio=90"  class="cta-button">¡LA QUIERO!</a>
        </div>
      </div>

      <div class="pricing-card recommended">
        <div class="pricing-header">CUOTA ANUAL <span class="tooltip">?</span><span class="popular">Más Popular</span></div>
        <div class="pricing-body">
          <div class="price"><span class="amount">150,00€</span><span class="vat">IVA incluido</span></div>
          <h3>Cuota anual</h3>
          <ul class="features">
            <li>Ahorra 20%</li>
            <li>Actividades Totalmente Gratis</li>
            <li>Pack de bienvenida</li>
          </ul>
        </div>
        <div class="pricing-footer">
          <a href="register.php?precio=150" class="cta-button">¡ME APUNTO!</a>
        </div>
      </div>
    </div>

    <!-- Sección de Testimonios -->
    <section class="testimonials">
      <h2>¿Qué dicen nuestros socios?</h2>
      <div class="testimonial-row">
        <div class="testimonial">
          <p>"FitnessPro cambió mi vida. Los entrenadores son increíbles y el ambiente es súper motivador."</p>
          <p><strong>— Ana G.</strong></p>
        </div>
        <div class="testimonial">
          <p>"El plan anual vale cada euro. Las clases grupales son lo mejor."</p>
          <p><strong>— Carlos M.</strong></p>
        </div>
      </div>
    </section>

    <!-- Galería de Imágenes -->
    <section class="gallery">
      <h2>Nuestras Instalaciones</h2>
      <div class="gallery-row">
        <img src="img/gym1.jpg" alt="Gimnasio FitnessPro" />
        <img src="img/gym2.png" alt="Clases grupales" />
        <img src="img/gym3.jpg" alt="Equipos modernos" />
      </div>
    </section>

    <!-- Llamada a la Acción -->
    <section class="cta">
      <h2>¡Únete a FitnessPro hoy!</h2>
      <a href="register.php" class="cta-button large">Comienza Ahora</a>
    </section>

    <!-- Formulario de Contacto -->
    <section class="contact">
      <h2>Contáctanos</h2>
      <form action="Contacto_mail.php" method="post">
        <input type="text" name="name" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <textarea name="message" placeholder="Tu mensaje" required></textarea>
        <button type="submit">Enviar</button>
      </form>
    </section>
  </main>
<?php
  include 'cabeceras-piePagina/Abajo.php';
?>

  <script>
    document.querySelector('.hamburger').addEventListener('click', () => {
      document.querySelector('.nav-links').classList.toggle('active');
    });
  </script>
</body>
</html>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Crear instancia de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP (usando Gmail como ejemplo)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jorgeandresbacho@gmail.com'; // Tu correo Gmail
        $mail->Password = 'xhfn kevg ojvk mtgc'; // Contraseña de aplicación (no tu contraseña normal)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('jorgeandresbacho@gmail.com', 'FitnessPro');
        $mail->addAddress($email); // Correo del usuario

        // Contenido del correo
        $mail->isHTML(false); // Correo en texto plano
        $mail->Subject = "Confirmacion de tu mensaje - FitnessPro";
        $mail->Body = "Hola $name,\n\nHemos recibido tu mensaje:\n$message\n\nGracias por contactarnos. Te responderemos pronto. ¡Un saludo desde FitnessPro!\n\nEquipo FitnessPro";

        // Enviar correo
        $mail->send();
        header("Location: index.php?success=1");
        exit();
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        header("Location: index.php?error=1");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
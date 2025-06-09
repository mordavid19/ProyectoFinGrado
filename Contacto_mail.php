<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'david.belmonte.moreno@gmail.com';
        $mail->Password = 'riym vfho roln fsoq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 25;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('david.belmonte.moreno@gmail.com', $name);
        $mail->addAddress('david.belmonte.moreno@gmail.com');
        $mail->Subject = 'Mensaje del formulario';
        $mail->Body    = "Nombre: $name\nCorreo visitante: $email\n\nMensaje:\n$message";

        $mail->send();
        echo 'Mensaje enviado correctamente.';
    } catch (Exception $e) {
        echo "No se pudo enviar el mensaje. Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Método no permitido.";
}
?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP para Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'divinasuper9@gmail.com'; // Tu dirección de correo de Gmail
        $mail->Password = 'gzsj vhew vqbv cnmx'; // Tu contraseña de aplicación de Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinatarios
        $mail->setFrom('divinasuper9@gmail.com', 'Facturación');
        $mail->addAddress('divinasuper9@gmail.com');

        // Archivos adjuntos
        if (isset($_FILES['tax-situation']) && $_FILES['tax-situation']['error'] == UPLOAD_ERR_OK) {
            $mail->addAttachment($_FILES['tax-situation']['tmp_name'], $_FILES['tax-situation']['name']);
        }
        if (isset($_FILES['ticket-image']) && $_FILES['ticket-image']['error'] == UPLOAD_ERR_OK) {
            $mail->addAttachment($_FILES['ticket-image']['tmp_name'], $_FILES['ticket-image']['name']);
        }
        if (isset($_FILES['voucher-image']) && $_FILES['voucher-image']['error'] == UPLOAD_ERR_OK) {
            $mail->addAttachment($_FILES['voucher-image']['tmp_name'], $_FILES['voucher-image']['name']);
        }
        if (isset($_FILES['transfer-image']) && $_FILES['transfer-image']['error'] == UPLOAD_ERR_OK) {
            $mail->addAttachment($_FILES['transfer-image']['tmp_name'], $_FILES['transfer-image']['name']);
        }

        // Contenido del correo
        $fiscalRegime = $_POST['fiscal-regime'];
        $paymentMethod = $_POST['payment-method'];
        $userEmail = '';

        $message = "Régimen Fiscal: $fiscalRegime\n";
        $message .= "Forma de pago: $paymentMethod\n";

        if ($paymentMethod == 'efectivo') {
            $userEmail = $_POST['user-email'];
            $message .= "Correo Electrónico para enviar factura: $userEmail\n";
        } elseif ($paymentMethod == 'tarjeta') {
            $userEmail = $_POST['user-email-tarjeta'];
            $cardType = $_POST['card-type'];
            $message .= "Tipo de Tarjeta: $cardType\n";
            $message .= "Correo Electrónico para enviar factura: $userEmail\n";
        } elseif ($paymentMethod == 'transferencia') {
            $userEmail = $_POST['user-email-transferencia'];
            $message .= "Correo Electrónico para enviar factura: $userEmail\n";
        }

        $mail->isHTML(false);
        $mail->Subject = 'Nueva Solicitud de Facturación';
        $mail->Body    = $message;

        $mail->send();
        echo 'Formulario enviado correctamente.';
    } catch (Exception $e) {
        echo "Error al enviar el formulario: {$mail->ErrorInfo}";
    }
} else {
    echo 'Método no permitido.';
}

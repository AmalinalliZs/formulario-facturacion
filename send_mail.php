<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $to = 'divinasuper9@gmail.com';
    $subject = 'Nueva Solicitud de Facturación';

    // Datos del formulario
    $taxSituation = $_POST['tax-situation'];
    $fiscalRegime = $_POST['fiscal-regime'];
    $paymentMethod = $_POST['payment-method'];
    $userEmail = isset($_POST['user-email']) ? $_POST['user-email'] : $_POST['user-email-tarjeta-transferencia'];

    $message = "Constancia de situación fiscal: $taxSituation\n";
    $message .= "Régimen Fiscal: $fiscalRegime\n";
    $message .= "Forma de pago: $paymentMethod\n";
    $message .= "Correo Electrónico para enviar factura: $userEmail\n";

    $headers = "From: $userEmail";

    // Manejo de archivos adjuntos
    $attachments = [];
    if (!empty($_FILES['ticket-image']['tmp_name'])) {
        $attachments[] = $_FILES['ticket-image'];
    }
    if (!empty($_FILES['ticket-image-tarjeta-transferencia']['tmp_name'])) {
        $attachments[] = $_FILES['ticket-image-tarjeta-transferencia'];
    }
    if (!empty($_FILES['payment-proof']['tmp_name'])) {
        $attachments[] = $_FILES['payment-proof'];
    }

    $boundary = md5(time());

    $headers .= "\r\nMIME-Version: 1.0\r\n" .
                "Content-Type: multipart/mixed; boundary=\"{$boundary}\"";

    $body = "--{$boundary}\r\n" .
            "Content-Type: text/plain; charset=\"utf-8\"\r\n" .
            "Content-Transfer-Encoding: 7bit\r\n\r\n" .
            $message . "\r\n";

    foreach ($attachments as $attachment) {
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: " . $attachment['type'] . "; name=\"" . basename($attachment['name']) . "\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "Content-Disposition: attachment; filename=\"" . basename($attachment['name']) . "\"\r\n\r\n";
        $body .= chunk_split(base64_encode(file_get_contents($attachment['tmp_name']))) . "\r\n";
    }

    $body .= "--{$boundary}--";

    if (mail($to, $subject, $body, $headers)) {
        echo 'Formulario enviado correctamente.';
    } else {
        echo 'Error al enviar el formulario.';
    }
} else {
    echo 'Método no permitido.';
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $to = 'divinasuper9@gmail.com';
    $subject = 'Nueva Solicitud de Facturación';

    $fiscalRegime = $_POST['fiscal-regime'];
    $paymentMethod = $_POST['payment-method'];
    $userEmail = '';

    $message = "Régimen Fiscal: $fiscalRegime\n";
    $message .= "Forma de pago: $paymentMethod\n";

    $headers = "From: $userEmail";

    // Manejo de archivos adjuntos
    $attachments = [];
    if (isset($_FILES['tax-situation']) && $_FILES['tax-situation']['error'] == UPLOAD_ERR_OK) {
        $attachments[] = $_FILES['tax-situation'];
    }
    if ($paymentMethod == 'efectivo') {
        $userEmail = $_POST['user-email'];
        $message .= "Correo Electrónico para enviar factura: $userEmail\n";
        if (isset($_FILES['ticket-image']) && $_FILES['ticket-image']['error'] == UPLOAD_ERR_OK) {
            $attachments[] = $_FILES['ticket-image'];
        }
    } elseif ($paymentMethod == 'tarjeta') {
        $userEmail = $_POST['user-email-tarjeta'];
        $cardType = $_POST['card-type'];
        $message .= "Tipo de Tarjeta: $cardType\n";
        $message .= "Correo Electrónico para enviar factura: $userEmail\n";
        if (isset($_FILES['voucher-image']) && $_FILES['voucher-image']['error'] == UPLOAD_ERR_OK) {
            $attachments[] = $_FILES['voucher-image'];
        }
    } elseif ($paymentMethod == 'transferencia') {
        $userEmail = $_POST['user-email-transferencia'];
        $message .= "Correo Electrónico para enviar factura: $userEmail\n";
        if (isset($_FILES['transfer-image']) && $_FILES['transfer-image']['error'] == UPLOAD_ERR_OK) {
            $attachments[] = $_FILES['transfer-image'];
        }
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

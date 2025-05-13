<?php
require __DIR__ . '/../../vendor/autoload.php';
require 'config/email_config.php';

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = SMTP_HOST;
$mail->SMTPAuth = true;
$mail->Username = SMTP_USER;
$mail->Password = SMTP_PASS;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = SMTP_PORT;
$mail->SMTPDebug = 2;
$mail->Debugoutput = function($str, $level) { echo "$level: $str\n"; };

try {
    $mail->setFrom(SMTP_FROM_EMAIL, 'Test');
    $mail->addAddress(SMTP_USER); // Envoyez à vous-même
    $mail->Subject = 'Test SMTP';
    $mail->Body = 'Ceci est un test';
    
    if ($mail->send()) {
        echo "Email envoyé avec succès!";
    } else {
        echo "Erreur: " . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}

<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($toEmail, $toName, $verificationCode, $debug = false) {
    $mail = new PHPMailer(true);

    try {
        // disable output by default; enable only when debugging
        $mail->SMTPDebug = $debug ? 2 : 0;
        if (!$debug) {
            // make sure debug output goes to error_log rather than stdout
            $mail->Debugoutput = function($str, $level) {
                error_log("PHPMailer debug ($level): $str");
            };
        }

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '@gmail.com';
        $mail->Password   = '..............'; // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('@gmail.com', 'Truck System');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Code de vérification';
        $mail->Body    = "Votre code de vérification est : $verificationCode";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
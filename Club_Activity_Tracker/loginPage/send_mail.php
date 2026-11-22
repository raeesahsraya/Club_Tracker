<<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendEmail($recipientEmail, $subject, $bodyHTML) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->SMTPDebug  = 0;  // CHANGE: Set to 0 to hide debugging logs on the screen
        $mail->Debugoutput = 'html';
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        
        // ---------------------------------
        
        // --- CHANGED: Use SSL and Port 465 instead of TLS 587 ---
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;
        
        // --- ADDED: Bypass strict SSL certificate checks for XAMPP ---
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        // ----------------------------------------------------------

        // Recipients
        $mail->setFrom('mystery.occult7@gmail.com', 'Club Tracker');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHTML;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}
?>

<?php
// Test email functionality
require_once 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer/PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer/PHPMailer-master/src/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'foodsaver71@gmail.com';
    $mail->Password = 'hpvgedgupcruhtxc';
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    $mail->setFrom('foodsaver71@gmail.com', 'Food Saver Test');
    $mail->addAddress('foodsaver71@gmail.com', 'Test Recipient'); // Send to yourself for testing
    
    $mail->isHTML(true);
    $mail->Subject = 'Email Test - ' . date('Y-m-d H:i:s');
    $mail->Body = '<h1>Test Email</h1><p>If you receive this, email setup is working!</p>';
    
    $mail->send();
    echo "✅ Test email sent successfully!";
    
} catch (Exception $e) {
    echo "❌ Email failed: " . $e->getMessage();
}
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Direct Email Test</h1>";

require_once 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer/PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer/PHPMailer-master/src/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'foodsaver71@gmail.com';
    $mail->Password = 'hpvgedgupcruhtxc';
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SMTPDebug = 2; // Enable verbose debug output
    
    // Additional settings
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Email content
    $mail->setFrom('foodsaver71@gmail.com', 'Food Saver Test');
    $mail->addAddress('navaneethanrs106@gmail.com', 'Test Recipient'); // Your test email
    $mail->isHTML(true);
    $mail->Subject = 'Direct Test Email - ' . date('Y-m-d H:i:s');
    $mail->Body = '<h2>Test Email</h2><p>This is a direct test to verify email delivery. Time: ' . date('Y-m-d H:i:s') . '</p>';

    $mail->send();
    echo "<p style='color: green;'><strong>✅ SUCCESS!</strong> Email sent successfully!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ ERROR:</strong> {$mail->ErrorInfo}</p>";
    echo "<p><strong>Exception:</strong> {$e->getMessage()}</p>";
}

echo "<hr>";
echo "<h2>Troubleshooting:</h2>";
echo "<ol>";
echo "<li>Check if you received the email at navaneethanrs106@gmail.com</li>";
echo "<li>Check spam/junk folder</li>";
echo "<li>Verify Gmail app password is correct: hpvgedgupcruhtxc</li>";
echo "<li>Make sure 2-factor authentication is enabled on foodsaver71@gmail.com</li>";
echo "</ol>";
?>
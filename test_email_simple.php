<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Email Test - Food Saver</h1>";

// Include PHPMailer
require_once 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer/PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer/PHPMailer-master/src/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'foodsaver71@gmail.com'; // Your Gmail
    $mail->Password = 'lwdchonlfimdnrgm'; // Your app password (remove spaces)
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SMTPDebug = 2; // Enable debug output

    // Email content
    $mail->setFrom('foodsaver71@gmail.com', 'Food Saver');
    $mail->addAddress('navaneethanrs106@gmail.com', 'Test User'); // Test recipient
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from Food Saver';
    $mail->Body = '<h2>Test Email</h2><p>This is a test email to verify PHPMailer is working correctly.</p>';

    $mail->send();
    echo "<p style='color: green;'><strong>✅ SUCCESS!</strong> Test email sent successfully!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ ERROR:</strong> {$mail->ErrorInfo}</p>";
    echo "<p><strong>Exception:</strong> {$e->getMessage()}</p>";
}

echo "<hr>";
echo "<h2>Troubleshooting Tips:</h2>";
echo "<ul>";
echo "<li>Make sure your Gmail app password is exactly: <code>lwdchonlfimdnrgm</code> (no spaces)</li>";
echo "<li>Verify 2-factor authentication is enabled on your Gmail account</li>";
echo "<li>Check if 'Less secure app access' is enabled (if needed)</li>";
echo "<li>Make sure XAMPP Apache is running</li>";
echo "</ul>";

echo "<p><a href='donate.html'>Back to Donation Form</a></p>";
?>
<?php
// Email Test for Food Saver
echo "<h2>📧 Email Configuration Test</h2>";

// Test basic mail function
$to = "test@example.com";
$subject = "Test Email from Food Saver";
$message = "This is a test email to check if PHP mail function is working.";
$headers = "From: foodsaver@localhost";

echo "<h3>Email Function Test:</h3>";
if (function_exists('mail')) {
    echo "<p style='color: green;'>✅ PHP mail() function is available</p>";
    
    // Try to send test email
    if (mail($to, $subject, $message, $headers)) {
        echo "<p style='color: green;'>✅ Test email sent successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to send test email</p>";
        echo "<p><strong>Note:</strong> This is normal in local development. You need to configure SMTP.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ PHP mail() function is not available</p>";
}

echo "<h3>📋 Email Setup Instructions:</h3>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Option 1: Local Testing (Recommended for Development)</h4>";
echo "<ol>";
echo "<li>Install <strong>MailHog</strong> or <strong>Fake Sendmail</strong> for local email testing</li>";
echo "<li>Configure php.ini to use local SMTP</li>";
echo "<li>All emails will be captured locally for testing</li>";
echo "</ol>";

echo "<h4>Option 2: Real Email Service (For Production)</h4>";
echo "<ol>";
echo "<li>Use <strong>PHPMailer</strong> with Gmail SMTP</li>";
echo "<li>Configure with your Gmail app password</li>";
echo "<li>Emails will be sent to real recipients</li>";
echo "</ol>";
echo "</div>";

echo "<h3>🔧 Current Configuration Status:</h3>";
echo "<ul>";
echo "<li><strong>SMTP Host:</strong> " . ini_get('SMTP') . "</li>";
echo "<li><strong>SMTP Port:</strong> " . ini_get('smtp_port') . "</li>";
echo "<li><strong>Sendmail Path:</strong> " . ini_get('sendmail_path') . "</li>";
echo "</ul>";

echo "<h3>🧪 Test NGO Email Notification:</h3>";
echo "<p>To test the email notification system:</p>";
echo "<ol>";
echo "<li>Register a test NGO with your real email address</li>";
echo "<li>Submit a donation form</li>";
echo "<li>Check if you receive the notification email</li>";
echo "</ol>";

echo "<a href='registerngo.html' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Register Test NGO</a>";
echo "<a href='donate.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Test Donation</a>";
echo "<a href='check_data.php' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Check Results</a>";
?>
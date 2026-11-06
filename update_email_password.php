<?php
// UPDATE THIS WITH YOUR NEW APP PASSWORD
$NEW_APP_PASSWORD = "hpvg edgu pcru htxc";

// Remove spaces from the password
$NEW_APP_PASSWORD = str_replace(' ', '', $NEW_APP_PASSWORD);

echo "<h1>Email Password Update</h1>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Create app password in Google Account</li>";
echo "<li>Copy the 16-character password</li>";
echo "<li>Replace 'PASTE_YOUR_NEW_16_DIGIT_PASSWORD_HERE' in this file</li>";
echo "<li>Run this script to update all email files</li>";
echo "</ol>";

if ($NEW_APP_PASSWORD === "PASTE_YOUR_NEW_16_DIGIT_PASSWORD_HERE") {
    echo "<p style='color: red;'><strong>⚠️ Please update the password in this file first!</strong></p>";
    exit;
}

// Files to update
$files_to_update = [
    'save_donation_fixed_gmail.php',
    'test_direct_email.php',
    'save_donation_email.php'
];

$old_password = 'lwdchonlfimdnrgm';

foreach ($files_to_update as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $updated_content = str_replace($old_password, $NEW_APP_PASSWORD, $content);
        file_put_contents($file, $updated_content);
        echo "<p>✅ Updated: $file</p>";
    } else {
        echo "<p>⚠️ File not found: $file</p>";
    }
}

echo "<hr>";
echo "<p><strong>Next step:</strong> <a href='test_direct_email.php'>Test Email Now</a></p>";
?>
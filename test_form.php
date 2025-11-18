<?php
// Simple test to check if form data is being received

echo "<h2>Form Data Test</h2>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h3>✅ Form data received!</h3>";
    echo "<p>Method: " . $_SERVER["REQUEST_METHOD"] . "</p>";
    
    echo "<h4>Received Data:</h4>";
    echo "<ul>";
    foreach ($_POST as $key => $value) {
        echo "<li><strong>$key:</strong> " . htmlspecialchars($value) . "</li>";
    }
    echo "</ul>";
    
    // Test database connection
    echo "<h4>Testing Database Insert:</h4>";
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "food_saver";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ Database connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Database connected successfully</p>";
        
        // Insert the data
        $ngo_name = $conn->real_escape_string($_POST['ngo_name']);
        $contact_person = $conn->real_escape_string($_POST['contact_person']);
        $phone_number = $conn->real_escape_string($_POST['phone_number']);
        $email = $conn->real_escape_string($_POST['email']);
        $address = $conn->real_escape_string($_POST['address']);
        $operating_hours = $conn->real_escape_string($_POST['operating_hours']);
        
        $sql = "INSERT INTO ngo_registrations (ngo_name, contact_person, phone_number, email, address, operating_hours) 
                VALUES ('$ngo_name', '$contact_person', '$phone_number', '$email', '$address', '$operating_hours')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>✅ Data inserted successfully! ID: " . $conn->insert_id . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Error inserting data: " . $conn->error . "</p>";
        }
        
        $conn->close();
    }
    
} else {
    echo "<h3>❌ No form data received</h3>";
    echo "<p>Method: " . $_SERVER["REQUEST_METHOD"] . "</p>";
    echo "<p>This page should be accessed via POST request from the form.</p>";
}

echo "<hr>";
echo "<p><a href='registerngo.html'>← Back to Registration Form</a></p>";
echo "<p><a href='view_ngos.php'>View All NGOs</a></p>";
?>

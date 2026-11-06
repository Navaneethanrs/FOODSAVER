<?php
$conn = new mysqli("localhost", "root", "", "food_saver", 3307);

if ($conn->connect_error) {
    die("Connection failed");
}

echo "Tables in database:<br>";
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    echo $row[0] . "<br>";
}

$conn->close();
?>

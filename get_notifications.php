<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "food_saver", 3307);

if ($conn->connect_error) {
    echo json_encode(['count' => 0]);
    exit;
}

$sql = "SELECT COUNT(*) as count FROM donations WHERE status = 'accepted'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode(['count' => $row['count']]);
$conn->close();
?>
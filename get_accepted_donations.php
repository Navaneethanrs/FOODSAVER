<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "food_saver", 3307);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT donor_name, food_type, quantity, location, accepted_by, accepted_at 
        FROM donations 
        WHERE status = 'accepted' 
        ORDER BY accepted_at DESC 
        LIMIT 10";

$result = $conn->query($sql);
$donations = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $donations[] = $row;
    }
}

echo json_encode($donations);
$conn->close();
?>
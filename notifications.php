<!DOCTYPE html>
<html>
<head>
    <title>Notifications - Food Saver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2><i class="fas fa-bell"></i> Donation Notifications</h2>
        
        <?php
        $conn = new mysqli("localhost", "root", "", "food_saver", 3307);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $sql = "SELECT * FROM donations WHERE status = 'accepted' ORDER BY accepted_at DESC LIMIT 10";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='alert alert-success'>";
                echo "<i class='fas fa-check-circle'></i> ";
                echo "<strong>{$row['accepted_by']}</strong> accepted donation from <strong>{$row['donor_name']}</strong>";
                echo "<br><small>Food: {$row['food_type']} | Location: {$row['location']} | " . date('M d, H:i', strtotime($row['accepted_at'])) . "</small>";
                echo "</div>";
            }
        } else {
            echo "<div class='alert alert-info'>No recent acceptances.</div>";
        }
        
        $conn->close();
        ?>
        
        <a href="index.html" class="btn btn-primary">Back to Home</a>
    </div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>View Donations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Food Donations</h2>
        
        <?php
        // Use the exact same connection as the working donation form
        $servername = "localhost";
        $username = "root";
        $password = "";
        $port = 3307;

        $conn = new mysqli($servername, $username, $password, "", $port);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $conn->select_db("food_saver");
        $result = $conn->query("SELECT * FROM donations ORDER BY id DESC");
        
        if ($result->num_rows > 0) {
            echo "<table class='table table-striped'>";
            echo "<tr><th>ID</th><th>Name</th><th>Food</th><th>Quantity</th><th>Location</th><th>Contact</th><th>Time</th><th>Date</th></tr>";
            
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["donor_name"] . "</td>";
                echo "<td>" . $row["food_type"] . "</td>";
                echo "<td>" . $row["quantity"] . "</td>";
                echo "<td>" . $row["location"] . "</td>";
                echo "<td>" . $row["donor_contact"] . "</td>";
                echo "<td>" . $row["pickup_time"] . "</td>";
                echo "<td>" . $row["created_at"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No donations found.</p>";
        }
        
        $conn->close();
        ?>
        
        <a href="donate.html" class="btn btn-primary">Add Donation</a>
    </div>
</body>
</html>
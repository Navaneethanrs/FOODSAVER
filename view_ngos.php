<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registered NGOs - Food Saver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ff7f50, #ff6347);
            min-height: 100vh;
            padding: 20px 0;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .table thead th {
            background: linear-gradient(90deg, #ff7f50, #ff6347);
            color: white;
            border: none;
            font-weight: 600;
        }
        .table tbody tr:hover {
            background-color: #fff5f2;
        }
        .badge {
            font-size: 0.8em;
        }
        .btn-back {
            background: linear-gradient(90deg, #ff7f50, #ff6347);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.html" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
        
        <h2 class="text-center mb-4">
            <i class="fas fa-users"></i> Registered NGOs
        </h2>
        
        <?php
        // Database configuration
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "food_saver";

        // Create connection (explicit port 3307)
        $conn = new mysqli($servername, $username, $password, $dbname, 3307);

        // Check connection
        if ($conn->connect_error) {
            die("<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>");
        }

        // Get all NGO registrations
        $sql = "SELECT * FROM ngo_registrations ORDER BY registration_date DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<div class='table-responsive'>";
            echo "<table class='table table-striped'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th><i class='fas fa-building'></i> NGO Name</th>";
            echo "<th><i class='fas fa-user'></i> Contact Person</th>";
            echo "<th><i class='fas fa-phone'></i> Phone</th>";
            echo "<th><i class='fas fa-envelope'></i> Email</th>";
            echo "<th><i class='fas fa-map-marker-alt'></i> Address</th>";
            echo "<th><i class='fas fa-clock'></i> Operating Hours</th>";
            echo "<th><i class='fas fa-calendar'></i> Registration Date</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($row["ngo_name"]) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row["contact_person"]) . "</td>";
                echo "<td><a href='tel:" . htmlspecialchars($row["phone_number"]) . "'>" . htmlspecialchars($row["phone_number"]) . "</a></td>";
                echo "<td><a href='mailto:" . htmlspecialchars($row["email"]) . "'>" . htmlspecialchars($row["email"]) . "</a></td>";
                echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
                echo "<td><span class='badge bg-info'>" . htmlspecialchars($row["operating_hours"]) . "</span></td>";
                echo "<td><small>" . date('M d, Y H:i', strtotime($row["registration_date"])) . "</small></td>";
                echo "</tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            
            echo "<div class='text-center mt-3'>";
            echo "<p class='text-muted'>Total NGOs registered: <strong>" . $result->num_rows . "</strong></p>";
            echo "</div>";
        } else {
            echo "<div class='alert alert-info text-center'>";
            echo "<i class='fas fa-info-circle'></i> No NGOs have been registered yet.";
            echo "</div>";
        }

        $conn->close();
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

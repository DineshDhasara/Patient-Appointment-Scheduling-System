<?php
session_start();
include 'db.php';

// Check if logged in as patient
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

$today = date('Y-m-d');

// Fetch all doctors with hospital and daily_limit
$doctor_query = "SELECT id, name, specialization, hospital, daily_limit FROM doctors";
$doctor_result = $conn->query($doctor_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>🩺 View Doctors</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f8f9fa, #dfe6e9);
            padding: 40px;
            margin: 0;
        }
        .container {
            max-width: 1000px;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #2d3436;
            margin-bottom: 30px;
        }
        .dashboard-btn {
            display: block;
            width: fit-content;
            margin: 0 auto 30px;
            padding: 10px 20px;
            background: #636e72;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .dashboard-btn:hover {
            background: #2d3436;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: 0.3s ease;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card h3 {
            margin: 0;
            color: #0984e3;
        }
        .card p {
            margin: 8px 0;
            color: #2d3436;
        }
        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 16px;
            background-color: #00b894;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.3s;
        }
        .btn:hover {
            background-color: #55efc4;
        }
        .btn:disabled {
            background: #d63031;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🩺 Available Doctors</h2>
    <a href="dashboard_patient.php" class="dashboard-btn">🔙 Back to Dashboard</a>

    <div class="grid">
        <?php
        if ($doctor_result && $doctor_result->num_rows > 0) {
            while ($doctor = $doctor_result->fetch_assoc()) {
                $doctor_id = $doctor['id'];
                $name = htmlspecialchars($doctor['name']);
                $specialization = htmlspecialchars($doctor['specialization']);
                $hospital = trim($doctor['hospital']);
                $hospital_display = !empty($hospital) ? htmlspecialchars($hospital) : "Not specified";
                $daily_limit = is_numeric($doctor['daily_limit']) ? (int)$doctor['daily_limit'] : 5;

                // Count today's appointments
                $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM appointments WHERE doctor_id = ? AND appointment_date = ?");
                $stmt->bind_param("is", $doctor_id, $today);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                $remaining = $daily_limit - $count;
                $full = $remaining <= 0;

                echo "<div class='card'>";
                echo "<h3>Dr. $name</h3>";
                echo "<p><strong>Specialization:</strong> $specialization</p>";
                echo "<p><strong>Hospital:</strong> $hospital_display</p>";
                echo "<p><strong>Booked Today:</strong> $count / $daily_limit</p>";

                if ($full) {
                    echo "<button class='btn' disabled>Booking Full</button>";
                } else {
                    echo "<a class='btn' href='schedule_appointment.php?doctor_id=$doctor_id'>📅 Book Now</a>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No doctors available right now.</p>";
        }
        ?>
    </div>
</div>
</body>
</html>

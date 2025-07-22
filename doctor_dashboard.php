<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Get doctor info and ID
$stmt = $conn->prepare("
    SELECT d.id, d.name AS doctor_name, d.hospital 
    FROM doctors d
    JOIN users u ON d.user_id = u.id
    WHERE u.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

$doctor_id = $doctor['id'];
$doctor_name = $doctor['doctor_name'];
$hospital = $doctor['hospital'] ?? "Not specified";

// Get today's and tomorrow's appointments
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$reminder_sql = "
    SELECT a.appointment_date, a.appointment_time, u.username AS patient_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE a.doctor_id = ? AND a.appointment_date IN (?, ?)
    ORDER BY a.appointment_date, a.appointment_time
";
$reminder_stmt = $conn->prepare($reminder_sql);
$reminder_stmt->bind_param("iss", $doctor_id, $today, $tomorrow);
$reminder_stmt->execute();
$reminders = $reminder_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #74ebd5, #9face6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            width: 450px;
        }

        h2, h4 {
            margin: 10px 0;
            color: #2d3436;
        }

        .btn {
            display: block;
            margin: 10px auto;
            padding: 10px 20px;
            background-color: #00b894;
            color: white;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #55efc4;
        }

        .reminder {
            background: #dfe6e9;
            padding: 15px;
            margin: 20px 0;
            border-radius: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>👨‍⚕ Welcome, Dr. <?= htmlspecialchars($doctor_name) ?></h2>
    <h4>🏥 Hospital: <?= htmlspecialchars($hospital) ?></h4>

    <div class="reminder">
        <h4>🔔 Appointment Reminders</h4>
        <?php if ($reminders->num_rows > 0): ?>
            <ul>
                <?php while ($r = $reminders->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($r['appointment_date']) ?> at <?= date("h:i A", strtotime($r['appointment_time'])) ?> - <?= htmlspecialchars($r['patient_name']) ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No appointments for today or tomorrow.</p>
        <?php endif; ?>
    </div>

    <a href="doctor_appointments.php" class="btn">📅 View Appointments</a>
    <a href="set_limit.php" class="btn">⚙️ Set Daily Limit</a>
    <a href="logout.php" class="btn">🔓 Logout</a>
</div>

</body>
</html>

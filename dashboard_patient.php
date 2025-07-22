<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Get user ID from users table
$user_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$user_stmt->bind_param("s", $username);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    die("❌ User not found in users table.");
}
$user_id = $user['id'];

// Get patient details
$stmt = $conn->prepare("SELECT id, name FROM patients WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    die("❌ Patient not found in patients table.");
}

$patient_id = $patient['id'];
$patient_name = $patient['name'];

// Get reminders for today and tomorrow
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$reminder_sql = "
    SELECT a.appointment_date, a.appointment_time, d.name AS doctor_name 
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ? AND a.appointment_date IN (?, ?)
    ORDER BY a.appointment_date, a.appointment_time
";
$reminder_stmt = $conn->prepare($reminder_sql);
$reminder_stmt->bind_param("iss", $patient_id, $today, $tomorrow);
$reminder_stmt->execute();
$reminders = $reminder_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #fdfbfb, #ebedee);
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

        h2 {
            margin-bottom: 5px;
        }

        .reminder {
            margin: 20px 0;
            background: #dff9fb;
            padding: 10px;
            border-radius: 10px;
            text-align: left;
        }

        .btn {
            display: block;
            padding: 12px;
            margin: 12px 0;
            background: #0984e3;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        .btn:hover {
            background: #74b9ff;
        }

        .logout {
            background: #d63031;
        }

        .logout:hover {
            background: #ff7675;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Welcome <?= htmlspecialchars($patient_name) ?></h2>

    <div class="reminder">
        <h4>🔔 Appointment Reminders</h4>
        <?php if ($reminders->num_rows > 0): ?>
            <ul>
                <?php while ($r = $reminders->fetch_assoc()): ?>
                    <li>
                        <?= htmlspecialchars($r['appointment_date']) ?> at <?= date("h:i A", strtotime($r['appointment_time'])) ?> with Dr. <?= htmlspecialchars($r['doctor_name']) ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No upcoming appointments for today or tomorrow.</p>
        <?php endif; ?>
    </div>

    <a href="my_appointments.php" class="btn">📅 My Appointments</a>
    <a href="view_doctors.php" class="btn">🩺 View Doctors</a>
    <a href="logout.php" class="btn logout">🚪 Logout</a>
</div>

</body>
</html>

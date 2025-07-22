<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
$patient_username = $_SESSION['username'];
$message = "";

// ✅ Get patient ID
$p_stmt = $conn->prepare("SELECT id FROM patients WHERE name = ?");
$p_stmt->bind_param("s", $patient_username);
$p_stmt->execute();
$p_result = $p_stmt->get_result();
$patient = $p_result->fetch_assoc();

if (!$patient) {
    die("❌ Patient not found.");
}
$patient_id = $patient['id'];

// ✅ Get doctor details including limit
$d_stmt = $conn->prepare("SELECT name, hospital, daily_limit FROM doctors WHERE id = ?");
$d_stmt->bind_param("i", $doctor_id);
$d_stmt->execute();
$d_result = $d_stmt->get_result();
$doctor = $d_result->fetch_assoc();

if (!$doctor) {
    die("❌ Doctor not found.");
}

$doctor_name = $doctor['name'];
$hospital = $doctor['hospital'] ?? 'Not specified';
$daily_limit = is_numeric($doctor['daily_limit']) ? (int)$doctor['daily_limit'] : 5;

// ✅ Handle booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Count bookings already done
    $check_stmt = $conn->prepare("SELECT COUNT(*) AS count FROM appointments WHERE doctor_id = ? AND appointment_date = ?");
    $check_stmt->bind_param("is", $doctor_id, $appointment_date);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count >= $daily_limit) {
        $message = "❌ Doctor has reached the limit of $daily_limit appointments on $appointment_date.";
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (doctor_id, patient_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'Scheduled')");
        $stmt->bind_param("iiss", $doctor_id, $patient_id, $appointment_date, $appointment_time);

        if ($stmt->execute()) {
            $message = "✅ Appointment scheduled successfully!";
        } else {
            $message = "❌ Error scheduling appointment.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>📅 Schedule Appointment</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .form-box {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #2d3436;
        }
        p {
            text-align: center;
            font-size: 0.95rem;
            color: #636e72;
        }
        label {
            font-weight: bold;
            margin-top: 12px;
            display: block;
        }
        input[type="date"],
        input[type="time"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            font-size: 1rem;
        }
        input[type="submit"] {
            background-color: #00b894;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #55efc4;
        }
        .msg {
            text-align: center;
            font-weight: bold;
        }
        .msg.success { color: green; }
        .msg.error { color: red; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #0984e3;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="form-box">
    <h2>📅 Schedule with Dr. <?= htmlspecialchars($doctor_name) ?></h2>
    <p>🏥 Hospital: <?= htmlspecialchars($hospital) ?></p>

    <?php if ($message): ?>
        <p class="msg <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Appointment Date:</label>
        <input type="date" name="appointment_date" required min="<?= date('Y-m-d') ?>">

        <label>Appointment Time:</label>
        <input type="time" name="appointment_time" required>

        <input type="submit" value="Confirm Appointment">
    </form>

    <a class="back-link" href="view_doctors.php">🔙 Back to Doctors</a>
</div>
</body>
</html>

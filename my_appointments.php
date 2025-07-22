<?php
session_start();
include 'db.php'; // Include your database connection

// ✅ Check if the user is logged in and is a patient
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ✅ Use prepared statement to get patient ID
$stmt = $conn->prepare("SELECT id FROM patients WHERE name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$p_result = $stmt->get_result();

if ($p_result->num_rows === 1) {
    $patient = $p_result->fetch_assoc();
    $patient_id = $patient['id'];

    // ✅ Fetch patient's appointments with hospital included
    $sql = "SELECT 
                a.id, 
                a.appointment_date, 
                a.appointment_time, 
                a.status, 
                d.name AS doctor_name,
                d.hospital AS hospital_name
            FROM appointments a
            JOIN doctors d ON a.doctor_id = d.id
            WHERE a.patient_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("i", $patient_id);
    $stmt2->execute();
    $result = $stmt2->get_result();
} else {
    die("❌ Patient not found in database. Please check your registration.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📅 My Appointments</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #cfd9df, #e2ebf0);
            padding: 40px;
        }
        h2 {
            text-align: center;
            color: #2d3436;
            margin-bottom: 30px;
        }
        .dashboard-btn {
            display: block;
            width: 200px;
            margin: 0 auto 20px auto;
            padding: 10px 20px;
            text-align: center;
            background-color: #2d3436;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #636e72;
            color: white;
        }
        td {
            color: #2d3436;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #d63031;
        }
    </style>
</head>
<body>
    <h2>📋 My Appointments</h2>

    <a href="dashboard_patient.php" class="dashboard-btn">🔙 Back to Dashboard</a>

    <table>
        <tr>
            <th>👨‍⚕ Doctor</th>
            <th>🏥 Hospital</th>
            <th>📅 Date</th>
            <th>⏰ Time</th>
            <th>📌 Status</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                <td><?= htmlspecialchars($row['hospital_name']) ?></td>
                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                <td><?= htmlspecialchars(date("h:i A", strtotime($row['appointment_time']))) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="no-data">❗ No appointments found.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>

<?php 
session_start();
include 'db.php';

// Check login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username']; // user's email

// Get doctor ID using a join to match user email
$query = "
    SELECT d.id AS doctor_id 
    FROM doctors d 
    JOIN users u ON d.user_id = u.id 
    WHERE u.username = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("❌ Doctor not found in database.");
}

$row = $result->fetch_assoc();
$doctor_id = $row['doctor_id'];

// ✅ Get appointments including time
$sql = "
    SELECT a.id, a.appointment_date, a.appointment_time, a.status, u.username AS patient_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
";
$appt_stmt = $conn->prepare($sql);
$appt_stmt->bind_param("i", $doctor_id);
$appt_stmt->execute();
$appointments = $appt_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
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
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #0984e3;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .back-button:hover {
            background-color: #74b9ff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
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
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .complete {
            background-color: #00b894;
        }
        .cancel {
            background-color: #d63031;
        }
    </style>
</head>
<body>
    <h2>📋 My Appointments</h2>

    <!-- Back to Dashboard Button -->
    <div style="text-align: left;">
        <a href="doctor_dashboard.php" class="back-button">← Back to Dashboard</a>
    </div>

    <!-- Appointment Table -->
    <table>
        <tr>
            <th>👤 Patient</th>
            <th>📅 Date</th>
            <th>⏰ Time</th>
            <th>📌 Status</th>
            <th>✅ Complete</th>
            <th>❌ Cancel</th>
        </tr>
        <?php while ($row = $appointments->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['patient_name']) ?></td>
            <td><?= htmlspecialchars($row['appointment_date']) ?></td>
            <td><?= htmlspecialchars(date("h:i A", strtotime($row['appointment_time']))) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <?php if ($row['status'] !== 'Completed'): ?>
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="status" value="Completed">
                        <button type="submit" class="btn complete">Mark as Completed</button>
                    </form>
                <?php else: ?>
                    ✅
                <?php endif; ?>
            </td>
            <td>
                <?php if ($row['status'] !== 'Cancelled'): ?>
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="status" value="Cancelled">
                        <button type="submit" class="btn cancel">Cancel</button>
                    </form>
                <?php else: ?>
                    ❌
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

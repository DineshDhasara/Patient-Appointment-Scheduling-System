
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$sql = "SELECT a.id, a.appointment_date, a.status, 
               d.name AS doctor_name, 
               p.name AS patient_name 
        FROM appointments a 
        JOIN doctors d ON a.doctor_id = d.id 
        JOIN patients p ON a.patient_id = p.id 
        ORDER BY a.appointment_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>🗂️ Manage Appointments</title>
    <style>
        body { font-family: Arial; background: #dfe6e9; padding: 30px; }
        h2 { text-align: center; color: #2d3436; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
        th, td { padding: 14px; border-bottom: 1px solid #ccc; text-align: center; }
        th { background-color: #636e72; color: white; }
        .btn-cancel {
            background-color: #d63031; color: white; border: none;
            padding: 6px 12px; border-radius: 6px; cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>📋 All Appointments</h2>
    <table>
    <tr>
        <th>👨‍⚕️ Doctor</th>
        <th>📅 Date</th>
        <th>📌 Status</th>
        <th>🔁 Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['doctor_name']) ?></td>
        <td><?= htmlspecialchars($row['appointment_date']) ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td>
            <?php if ($row['status'] === 'Scheduled'): ?>
                <a href="reschedule.php?id=<?= $row['id'] ?>" class="btn complete">Reschedule</a>
            <?php else: ?>
                <span style="color: gray;">N/A</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

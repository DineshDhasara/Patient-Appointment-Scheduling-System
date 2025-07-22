<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

$appointment_id = $_GET['id'] ?? null;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_date = $_POST['new_date'];

    $stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, status = 'Rescheduled' WHERE id = ?");
    $stmt->bind_param("si", $new_date, $appointment_id);
    
    if ($stmt->execute()) {
        $message = "✅ Appointment rescheduled successfully!";
    } else {
        $message = "❌ Failed to reschedule appointment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reschedule Appointment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ecf0f1;
            padding: 40px;
        }
        .container {
            background: white;
            padding: 30px;
            max-width: 400px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2d3436;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="date"] {
            padding: 10px;
            margin-top: 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            padding: 10px;
            background: #0984e3;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .message {
            text-align: center;
            margin-top: 20px;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔁 Reschedule Appointment</h2>
        <form method="POST">
            <label for="new_date">📅 Select New Date:</label>
            <input type="date" name="new_date" required min="<?= date('Y-m-d') ?>">
            <button type="submit">Update Appointment</button>
        </form>
        <?php if ($message): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

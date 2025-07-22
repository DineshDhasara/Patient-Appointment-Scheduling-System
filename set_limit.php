<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Get doctor ID
$query = "
    SELECT d.id, d.daily_limit 
    FROM doctors d
    JOIN users u ON d.user_id = u.id
    WHERE u.username = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$limit = $doctor['daily_limit'] ?? 5;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_limit = intval($_POST['limit']);
    $update = $conn->prepare("UPDATE doctors SET daily_limit = ? WHERE id = ?");
    $update->bind_param("ii", $new_limit, $doctor['id']);
    $update->execute();
    header("Location: doctor_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Daily Limit</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #dfe6e9;
            display: flex; justify-content: center; align-items: center;
            height: 100vh;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        input[type="number"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        input[type="submit"] {
            background: #00b894;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>🛠 Set Your Daily Appointment Limit</h2>
        <form method="POST">
            <label>Enter Limit:</label>
            <input type="number" name="limit" value="<?= htmlspecialchars($limit) ?>" min="1" required>
            <input type="submit" value="Update Limit">
        </form>
    </div>
</body>
</html>

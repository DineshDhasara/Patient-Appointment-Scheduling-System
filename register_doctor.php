<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $hospital = $_POST['hospital'];
    $daily_limit = $_POST['daily_limit'];

    // 1. Insert into users table
    $stmt1 = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'doctor')");
    $stmt1->bind_param("ss", $username, $password);
    $stmt1->execute();

    $user_id = $conn->insert_id;

    // 2. Insert into doctors table
    $stmt2 = $conn->prepare("INSERT INTO doctors (user_id, name, email, specialization, hospital, daily_limit) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("issssi", $user_id, $name, $email, $specialization, $hospital, $daily_limit);
    $stmt2->execute();

    echo "<script>alert('Doctor registered successfully!'); window.location.href='admin_dashboard.php';</script>";
    exit;
}
?>

<!-- HTML form -->
<!DOCTYPE html>
<html>
<head>
    <title>Register Doctor</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; }
        .form-container {
            width: 400px; margin: auto; margin-top: 60px;
            background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, select {
            width: 100%; padding: 10px; margin: 10px 0;
            border: 1px solid #ccc; border-radius: 5px;
        }
        button {
            width: 100%; padding: 10px;
            background: #28a745; color: white; border: none;
            border-radius: 5px; cursor: pointer;
        }
        button:hover { background: #218838; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Register Doctor</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="name" placeholder="Doctor Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="specialization" placeholder="Specialization" required>
        <input type="text" name="hospital" placeholder="Hospital Name" required>
        <input type="number" name="daily_limit" placeholder="Daily Appointment Limit" required>
        <button type="submit">Register Doctor</button>
    </form>
</div>
</body>
</html>

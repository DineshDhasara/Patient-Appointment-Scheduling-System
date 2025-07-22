<?php
session_start();
include 'db.php';

// Check admin login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Get Counts
$doctor_count = $conn->query("SELECT COUNT(*) AS total FROM doctors")->fetch_assoc()['total'];
$patient_count = $conn->query("SELECT COUNT(*) AS total FROM patients")->fetch_assoc()['total'];
$appointment_count = $conn->query("SELECT COUNT(*) AS total FROM appointments")->fetch_assoc()['total'];
$today = date('Y-m-d');
$today_sessions = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = '$today'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            height: 100vh;
            padding: 20px;
            position: fixed;
            transition: 0.3s;
        }
        .sidebar h2 {
            text-align: center;
            color: #2d3436;
            margin-bottom: 50px;
            font-weight: bold;
        }
        .sidebar a {
            display: block;
            padding: 12px;
            margin: 10px 0;
            color: #2d3436;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 18px;
        }
        .sidebar a:hover {
            background: #dfe6e9;
            padding-left: 20px;
        }
        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 30px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .top-bar h1 {
            color: #2d3436;
            font-size: 28px;
        }
        .top-bar .date {
            color: #636e72;
            font-size: 16px;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .card {
            background: white;
            padding: 30px 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 24px rgba(0,0,0,0.15);
        }
        .card h1 {
            font-size: 40px;
            color: #0984e3;
        }
        .card p {
            font-size: 18px;
            margin-top: 10px;
            color: #636e72;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin</h2>
    <a href="admin_dashboard.php">🏠 Dashboard</a>
    <a href="manage_users.php">👥 Manage Patients</a>
    <a href="manage_doctors.php">🩺 Manage Doctors</a>
    <a href="manage_appointments.php">📅 Manage Appointments</a>
    <a href="register_doctor.php">
    <button style="padding: 10px 20px; background-color: green; color: white; border: none; border-radius: 5px;">
        ➕ Register New Doctor
    </button>
</a>

    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
    <div class="top-bar">
        <h1>Dashboard</h1>
        <div class="date"><?= date('l, d M Y') ?></div>
    </div>

    <div class="cards">
        <div class="card">
            <h1><?= $doctor_count ?></h1>
            <p>Doctors</p>
        </div>
        <div class="card">
            <h1><?= $patient_count ?></h1>
            <p>Patients</p>
        </div>
        <div class="card">
            <h1><?= $appointment_count ?></h1>
            <p>Appointments</p>
        </div>
        <div class="card">
            <h1><?= $today_sessions ?></h1>
            <p>Today Sessions</p>
        </div>
    </div>
</div>

</body>
</html>
<?php
session_start();
include 'db.php';

// Check admin login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    
    // Insert into database
    $query = "INSERT INTO patients (name, email, phone, dob) VALUES ('$name', '$email', '$dob')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Patient registered successfully.'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Patient</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 40px;
        }
        h1 {
            text-align: center;
            color: #2d3436;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #0984e3;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #74b9ff;
        }
    </style>
</head>
<body>

<h1>Register Patient</h1>

<form action="register_patient.php" method="POST">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" required>
    
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>
    
    
    
    <label for="dob">Date of Birth</label>
    <input type="date" id="dob" name="dob" required>
    
    <button type="submit">Register Patient</button>
</form>

</body>
</html>

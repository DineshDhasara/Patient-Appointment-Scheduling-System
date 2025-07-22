<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];

    // Fetch user with matching username and role
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Support both hashed and old plaintext passwords
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];

            // Redirect based on role
            if ($role === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($role === 'doctor') {
                header("Location: doctor_dashboard.php");
            } elseif ($role === 'patient') {
                header("Location: dashboard_patient.php");
            }
            exit;
        } else {
            $error = "❌ Invalid password!";
        }
    } else {
        $error = "❌ Invalid credentials or role mismatch!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Patient System</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #f5f7fa);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            width: 320px;
        }
        .login-box h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .login-box input, .login-box select {
            width: 100%;
            padding: 10px 12px;
            margin: 10px 0;
            border-radius: 10px;
            border: 1px solid #ccc;
            background: rgba(255, 255, 255, 0.4);
        }
        .login-box input[type="submit"] {
            background-color: #4b7bec;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .login-box input[type="submit"]:hover {
            background-color: #3867d6;
        }
        .login-box .register-link {
            text-align: center;
            margin-top: 12px;
            font-size: 14px;
        }
        .login-box .register-link a {
            color: #4b7bec;
            text-decoration: none;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>🔐 User Login</h2>
    <form method="post">
        <input type="text" name="username" placeholder="👤 Username" required>
        <input type="password" name="password" placeholder="🔒 Password" required>
        <select name="role" required>
            <option value="">🔽 Select Role</option>
            <option value="admin">🧑‍💼 Admin</option>
            <option value="doctor">👨‍⚕ Doctor</option>
            <option value="patient">🧍 Patient</option>
        </select>
        <input type="submit" value="Login">
    </form>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <div class="register-link">
        ➕ Not registered? <a href="register_user.php">Register now</a>
    </div>
</div>

</body>
</html>
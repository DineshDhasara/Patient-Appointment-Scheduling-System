<?php
include 'db.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = "patient"; // Hardcoded as patient only

    // Check if username already exists
    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "❌ Username already exists!";
    } else {
        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Insert into patients table
            $insertPatient = $conn->prepare("INSERT INTO patients (user_id, name, email) VALUES (?, ?, ?)");
            $insertPatient->bind_param("iss", $user_id, $username, $username); // Using username as email
            $insertPatient->execute();

            $success = "✅ Registered successfully as Patient!";
        } else {
            $error = "❌ Registration failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>📝 Register Patient</title>
    <style>
        body {
            font-family: Arial;
            background: #e8f5e9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .reg-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        .reg-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .reg-box input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .reg-box input[type="submit"] {
            background: #2e7d32;
            color: white;
            cursor: pointer;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #0984e3;
            text-decoration: none;
        }
        .msg {
            text-align: center;
            color: green;
        }
        .error {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>
    <form class="reg-box" method="post">
        <h2>Register Patient</h2>
        <input type="text" name="username" placeholder="👤 Username" required>
        <input type="password" name="password" placeholder="🔒 Password" required>
        <input type="submit" value="Register">

        <a href="login.php" class="back-link">🔙 Back to Login</a>

        <?php if ($success) echo "<div class='msg'>$success</div>"; ?>
        <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    </form>
</body>
</html>

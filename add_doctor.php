<?php
session_start();
include 'db.php';

// Only allow admin to access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $specialization = $_POST["specialization"];
    $availability = $_POST["availability"];

    $user_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'doctor')";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("ss", $name, $password); 

    if($stmt->execute()){
    $user_id = $stmt->insert_id;
    $doctor_sql = "INSERT INTO doctor (user_id, username, specialization, availability)
            VALUES ('?', '?' , '?', '?')";
            $stmt2 = $conn->prepare($doctor_sql);
            $stmt2->bind_param("sss", $user_id, $name, $specialization, $availability);

}

    

  

    if ($conn->query($sql)) {
        $message = "✅ Doctor added successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>➕ Add Doctor</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f1f2f6;
            padding: 40px;
        }

        .form-container {
            max-width: 500px;
            margin: auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #2d3436;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .btn {
            width: 100%;
            background-color: #00b894;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #55efc4;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>➕ Add a New Doctor</h2>
        <form method="POST">
            <label>👨‍⚕️ Doctor Name:</label>
            <input type="text" name="name" required>

            <label>💼 Specialization:</label>
            <input type="text" name="specialization" required>

            <label>📆 Availability:</label>
            <select name="availability" required>
                <option value="">-- Choose --</option>
                <option value="Mon to Fri">Mon to Fri</option>
                <option value="Weekends">Weekends</option>
                <option value="All Days">All Days</option>
            </select>

            <button class="btn" type="submit">✅ Add Doctor</button>
        </form>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
//Dinesh Tvd

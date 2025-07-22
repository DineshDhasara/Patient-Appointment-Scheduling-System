<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

// Check admin login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Get user ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid user ID.";
    exit;
}

$user_id = intval($_GET['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($name)) {
        // Fetch the user_id from the patients table
        $stmt = $conn->prepare("SELECT user_id FROM patients WHERE id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();

        if ($patient) {
            $user_id_from_patient = $patient['user_id'];

            // Update the `patients` table
            $stmt1 = $conn->prepare("UPDATE patients SET name=?, email=? WHERE id=?");
            $stmt1->bind_param("ssi", $name, $email, $user_id);

            // Update the `users` table
            $stmt2 = $conn->prepare("UPDATE users SET username=? WHERE id=?");
            $stmt2->bind_param("si", $name, $user_id_from_patient);

            // Execute both statements and check for success
            if ($stmt1->execute() && $stmt2->execute()) {
                header("Location: manage_users.php");
                exit;
            } else {
                $error = "Failed to update user.";
            }
        } else {
            $error = "Patient not found.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT name, email FROM patients WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f9fc;
            padding: 40px;
        }
        .form-container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2d3436;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            border: none;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            color: #2d3436;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit User</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        
        
        <button type="submit">Update User</button>
    </form>
</div>

</body>
</html>

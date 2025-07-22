<?php
session_start();
include 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Get doctor ID from URL
if (!isset($_GET['id'])) {
    echo "❌ No doctor ID provided!";
    exit;
}

$doctor_id = intval($_GET['id']);
$success = "";
$error = "";

// ✅ Fetch existing doctor info including hospital
$stmt = $conn->prepare("SELECT d.id, d.name, d.specialization, d.hospital, u.username AS email 
                        FROM doctors d
                        JOIN users u ON d.user_id = u.id
                        WHERE d.id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ Doctor not found!";
    exit;
}

$doctor = $result->fetch_assoc();

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $specialization = $_POST["specialization"];
    $hospital = $_POST["hospital"];

    $update = $conn->prepare("UPDATE doctors SET name = ?, specialization = ?, hospital = ? WHERE id = ?");
    $update->bind_param("sssi", $name, $specialization, $hospital, $doctor_id);

    if ($update->execute()) {
        $success = "✅ Doctor updated successfully!";
        $doctor['name'] = $name;
        $doctor['specialization'] = $specialization;
        $doctor['hospital'] = $hospital;
    } else {
        $error = "❌ Failed to update doctor.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Doctor</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-box {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2d3436;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background: #0984e3;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        .msg {
            text-align: center;
            color: green;
        }
        .error {
            text-align: center;
            color: red;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #0984e3;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            color: #2d3436;
        }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Edit Doctor</h2>

    <form method="post">
        <label>Doctor Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($doctor['name']) ?>" required>

        <label>Specialization</label>
        <input type="text" name="specialization" value="<?= htmlspecialchars($doctor['specialization']) ?>" required>

        <label>Hospital</label>
        <input type="text" name="hospital" value="<?= htmlspecialchars($doctor['hospital']) ?>" required>

        <input type="submit" value="Update Doctor">
    </form>

    <?php if ($success) echo "<div class='msg'>$success</div>"; ?>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>

    <!-- 🔙 Back Button -->
    <a href="manage_doctors.php" class="back-link">🔙 Back to Manage Doctors</a>
</div>

</body>
</html>

<?php
session_start();
include 'db.php';

// ✅ Check admin login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// ✅ Fetch all patients
$result = $conn->query("SELECT * FROM patients ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Patients</title>
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
        .back-btn {
            display: inline-block;
            margin: 0 auto 20px auto;
            padding: 10px 18px;
            background-color: #636e72;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }
        .back-btn:hover {
            background-color: #2d3436;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        th, td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            color: #2d3436;
        }
        tr:hover {
            background-color: #f1f2f6;
        }
        a.action-btn {
            padding: 6px 12px;
            background: #0984e3;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            margin-right: 8px;
            font-size: 14px;
        }
        a.action-btn:hover {
            background: #74b9ff;
        }
        .no-users {
            text-align: center;
            padding: 40px;
            color: #636e72;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <h1>Manage Patients</h1>
    <a href="admin_dashboard.php" class="back-btn">🔙 Back to Dashboard</a>
</div>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $row['id'] ?>" class="action-btn">Edit</a>
                        <a href="delete_user.php?id=<?= $row['id'] ?>" class="action-btn" style="background: #d63031;" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="no-users">No patients found.</p>
<?php endif; ?>

</body>
</html>

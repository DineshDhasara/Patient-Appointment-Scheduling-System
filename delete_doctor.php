<?php
session_start();
include 'db.php'; // ✅ make sure this path is correct

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $doctor_id = $_GET['id'];

    // Step 1: Get user_id linked to doctor
    $stmt1 = $conn->prepare("SELECT user_id FROM doctors WHERE id = ?");
    $stmt1->bind_param("i", $doctor_id);
    $stmt1->execute();
    $result = $stmt1->get_result();

    if ($result->num_rows === 1) {
        $doctor = $result->fetch_assoc();
        $user_id = $doctor['user_id'];

        // Step 2: Delete doctor
        $stmt2 = $conn->prepare("DELETE FROM doctors WHERE id = ?");
        $stmt2->bind_param("i", $doctor_id);
        $stmt2->execute();

        // Step 3: Delete associated user account
        $stmt3 = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt3->bind_param("i", $user_id);
        $stmt3->execute();

        // Step 4: Redirect with success
        echo "<script>
                alert('✅ Doctor deleted successfully!');
                window.location.href = 'manage_doctors.php';
              </script>";
    } else {
        echo "<script>alert('❌ Doctor not found.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('❌ No doctor ID provided.'); window.history.back();</script>";
}
?>

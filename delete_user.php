<?php
session_start();
include 'db.php'; // ✅ FIXED PATH

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];

    // Fetch the user_id linked to this patient
    $stmt1 = $conn->prepare("SELECT user_id FROM patients WHERE id = ?");
    $stmt1->bind_param("i", $patient_id);
    $stmt1->execute();
    $result = $stmt1->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['user_id'];

        // ✅ First delete appointments linked to this patient
        $stmt_delete_appts = $conn->prepare("DELETE FROM appointments WHERE patient_id = ?");
        $stmt_delete_appts->bind_param("i", $patient_id);
        $stmt_delete_appts->execute();

        // ✅ Now delete patient record
        $stmt2 = $conn->prepare("DELETE FROM patients WHERE id = ?");
        $stmt2->bind_param("i", $patient_id);
        $stmt2->execute();

        // ✅ Then delete user account
        $stmt3 = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt3->bind_param("i", $user_id);
        $stmt3->execute();

        header("Location: manage_users.php");
        exit;
    } else {
        echo "❌ Patient not found.";
    }
} else {
    echo "❌ No ID provided.";
}
?>

<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];
    
    $sql = "UPDATE appointments SET status='Cancelled' WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();

    header("Location: admin_appointments.php");
    exit;
}
?>

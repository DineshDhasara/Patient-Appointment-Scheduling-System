<?php
include 'db.php';

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $appointment_id = $_POST['appointment_id'] ?? null;
    $status = $_POST['status'] ?? null;

    // Ensure both appointment_id and status are set
    if ($appointment_id && $status) {
        // Use prepared statement to avoid SQL injection
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $appointment_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to doctor's appointments page
            header("Location: doctor_appointments.php");
            exit;
        } else {
            echo "❌ Error updating appointment status: " . $stmt->error;
        }
    } else {
        echo "❌ Missing appointment ID or status!";
    }
} else {
    echo "❌ Invalid request method.";
}
?>

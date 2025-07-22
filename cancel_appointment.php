<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Cancel Appointment</title>
</head>
<body>
    <h2>Cancel Appointment</h2>
    <form action="cancel_appointment.php" method="post">
        <label>Appointment ID:</label>
        <input type="text" name="appointment_id" required><br>
        <button type="submit" name="cancel">Cancel</button>
    </form>

    <?php
    if(isset($_POST['cancel'])) {
        $appointment_id = $_POST['appointment_id'];

        $sql = "UPDATE appointments SET status='Cancelled' WHERE id='$appointment_id'";
        if ($conn->query($sql) === TRUE) {
            echo "Appointment Cancelled!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    ?>
</body>
</html>

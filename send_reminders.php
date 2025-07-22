<?php 
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'db.php';

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'dineshprasath001@gmail.com';  // ✅ Your Gmail address
    $mail->Password = 'aywbglyuwlsuhlwe';            // ✅ Your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Dates to check for reminders
    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));

    // ✅ Fetch appointments with patient and doctor email directly from their own tables
    $stmt = $conn->prepare("
        SELECT 
            a.appointment_date, 
            a.appointment_time, 
            p.email AS patient_email, 
            d.name AS doctor_name 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.appointment_date IN (?, ?) AND a.status = 'Scheduled'
    ");
    $stmt->bind_param("ss", $today, $tomorrow);
    $stmt->execute();
    $result = $stmt->get_result();

    $count = 0;

    while ($row = $result->fetch_assoc()) {
        $email = $row['patient_email'];
        $date = $row['appointment_date'];
        $time = date("h:i A", strtotime($row['appointment_time']));
        $doctor = $row['doctor_name'];

        if (!empty($email)) {
            // Setup and send email
            $mail->clearAddresses();
            $mail->setFrom('dineshprasath001@gmail.com', 'Appointment Reminder');
            $mail->addAddress($email);
            $mail->Subject = "Reminder: Your Appointment on $date";
            $mail->Body = "Dear Patient,\n\nThis is a reminder for your appointment with Dr. $doctor on $date at $time.\n\nThank you.";

            $mail->send();
            $count++;
        }
    }

    echo "✅ $count reminders sent successfully.";
} catch (Exception $e) {
    echo "❌ Failed to send reminders: " . $mail->ErrorInfo;
}
?>

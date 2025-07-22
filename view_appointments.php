<?php
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Appointments</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #444;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        h2 {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<h2>📅 Scheduled Appointments</h2>

<table>
    <tr>
        <th>Appointment ID</th>
        <th>Patient Name</th>
        <th>Doctor Name</th>
        <th>Date</th>
    </tr>

<?php
$sql = "SELECT a.id, p.name AS patient_name, d.name AS doctor_name, a.appointment_date 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        ORDER BY a.appointment_date ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row['id']."</td>
                <td>".$row['patient_name']."</td>
                <td>".$row['doctor_name']."</td>
                <td>".$row['appointment_date']."</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No appointments found.</td></tr>";
}
?>

</table>

</body>
</html>

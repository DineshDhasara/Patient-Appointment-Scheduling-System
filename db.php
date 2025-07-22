<?php
// Database connection details
$servername = "localhost";
$username = "root";  // Your MySQL username
$password = "";      // Your MySQL password (empty for XAMPP by default)
$database = "patient_appointment";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // Optional: you can output this line if you want to confirm the connection was successful
    // echo "Connected successfully to the database!";
}
?>


<?php

$servername = "localhost";
$username = "root";
$password = ""; // default password for XAMPP is empty
$dbname = "basecuritydb";

$conn = new mysqli($servername, $username, $password, $dbname);

// CHECK CONNECTION
if ($conn->connect_error) {
    // LOG ERROR TO SERVER LOGS BUT DONT DISPLAY THIS TO THE USER 
    error_log("Database connection failed: " . $conn->connect_error);
    // SET THE $conn TO NULL!!! SO OTHERS KNOW THAT ITS INVALID 
    $conn = null;
}

?>

<?php
$servername = "localhost";
$username = "root"; // Default for XAMPP
$password = ""; // Default is empty
$dbname = "basecuritydb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection silently
if ($conn->connect_error) {
    // Log error to server logs, but don't display to user
    error_log("Database connection failed: " . $conn->connect_error);
    // Set $conn to null so other scripts know it’s invalid
    $conn = null;
}
?>

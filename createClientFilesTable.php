<?php
// Script to create ClientFiles table
include('database.php');

// Create ClientFiles table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS ClientFiles (
    fileID INT(11) NOT NULL AUTO_INCREMENT,
    clientID INT(11) NOT NULL,
    fileName VARCHAR(255) NOT NULL,
    fileType VARCHAR(100) NOT NULL,
    filePath VARCHAR(500) NOT NULL,
    fileSize INT(11) NOT NULL,
    fileDateTime DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (fileID),
    FOREIGN KEY (clientID) REFERENCES Clients(clientID)
)";

if ($conn->query($sql) === TRUE) {
    echo "ClientFiles table created successfully or already exists";
    
    // Create uploads directory if it doesn't exist
    $uploadsDir = __DIR__ . '/uploads';
    if (!file_exists($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
        echo "<br>Uploads directory created";
    } else {
        echo "<br>Uploads directory already exists";
    }
} else {
    echo "Error creating table: " . $conn->error;
}
?>

<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include('database.php');

// Check if file ID, schedule ID, and client ID are provided
if (!isset($_GET['fileID']) || !is_numeric($_GET['fileID']) || 
    !isset($_GET['scheduleID']) || !is_numeric($_GET['scheduleID']) ||
    !isset($_GET['clientID']) || !is_numeric($_GET['clientID'])) {
    header("Location: scheduleDiary.php");
    exit();
}

$fileID = intval($_GET['fileID']);
$scheduleID = intval($_GET['scheduleID']);
$clientID = intval($_GET['clientID']);

// Get file information
$stmt = $conn->prepare("SELECT * FROM ScheduleFiles WHERE fileID = ? AND scheduleID = ?");
$stmt->bind_param("ii", $fileID, $scheduleID);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc();
$stmt->close();

if ($file) {
    // Check if ClientFiles table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'ClientFiles'");
    if ($tableCheck && $tableCheck->num_rows === 0) {
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
            FOREIGN KEY (clientID) REFERENCES Clients(clientID) ON DELETE CASCADE
        )";
        $conn->query($sql);
    }
    
    // Copy the file to a new location with a unique name
    $sourcePath = $file['filePath'];
    $fileName = $file['fileName'];
    $fileType = $file['fileType'];
    $fileSize = $file['fileSize'];
    $targetDir = "uploads/";
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Generate unique filename to prevent overwriting
    $uniqueFileName = time() . '_client_' . $fileName;
    $targetPath = $targetDir . $uniqueFileName;
    
    if (copy($sourcePath, $targetPath)) {
        // Insert file info into ClientFiles database
        $stmt = $conn->prepare("INSERT INTO ClientFiles (clientID, fileName, fileType, filePath, fileSize) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $clientID, $fileName, $fileType, $targetPath, $fileSize);
        $stmt->execute();
        $stmt->close();
        
        // Set success message
        $_SESSION['message'] = "File successfully copied to client profile.";
        $_SESSION['message_type'] = "success";
    } else {
        // Set error message
        $_SESSION['message'] = "Error copying file to client profile.";
        $_SESSION['message_type'] = "error";
    }
}

// Redirect back to the schedule diary add page
header("Location: scheduleDiaryAdd.php?scheduleID=" . $scheduleID);
exit();
?>

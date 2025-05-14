<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}
include('database.php');

// Check if required data is provided
if (!isset($_POST['fileID']) || !is_numeric($_POST['fileID']) || 
    !isset($_POST['scheduleID']) || !is_numeric($_POST['scheduleID']) ||
    !isset($_POST['clientID']) || !is_numeric($_POST['clientID'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

$fileID = intval($_POST['fileID']);
$scheduleID = intval($_POST['scheduleID']);
$clientID = intval($_POST['clientID']);
$response = ['success' => false];

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
        
        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'File successfully copied to client profile'
            ];
        } else {
            $response['message'] = 'Error saving file information to database';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Error copying file';
    }
} else {
    $response['message'] = 'File not found';
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>

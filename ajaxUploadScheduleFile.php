<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log request information
$logFile = 'upload_log.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Request received\n", FILE_APPEND);
file_put_contents($logFile, "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents($logFile, "FILES: " . print_r($_FILES, true) . "\n", FILE_APPEND);

if (!isset($_SESSION['user'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    file_put_contents($logFile, "Error: Not authenticated\n", FILE_APPEND);
    exit();
}
include('database.php');

// Check if required data is provided
if (!isset($_POST['scheduleID']) || !is_numeric($_POST['scheduleID'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid schedule ID']);
    file_put_contents($logFile, "Error: Invalid schedule ID\n", FILE_APPEND);
    exit();
}

$scheduleID = intval($_POST['scheduleID']);
$response = ['success' => false];

// Handle file upload
if (isset($_FILES['file'])) {
    // Log file upload details
    file_put_contents($logFile, "File found in request: {$_FILES['file']['name']}\n", FILE_APPEND);
    file_put_contents($logFile, "File error code: {$_FILES['file']['error']}\n", FILE_APPEND);
    
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $fileName = basename($_FILES['file']['name']);
    $fileType = $_FILES['file']['type'];
    $fileSize = $_FILES['file']['size'];
    $targetDir = "uploads/";
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Generate unique filename to prevent overwriting
    $uniqueFileName = time() . '_' . $fileName;
    $targetFile = $targetDir . $uniqueFileName;
    
    // Check if ScheduleFiles table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'ScheduleFiles'");
    if ($tableCheck && $tableCheck->num_rows === 0) {
        // Create ScheduleFiles table if it doesn't exist
        $sql = "CREATE TABLE IF NOT EXISTS ScheduleFiles (
            fileID INT(11) NOT NULL AUTO_INCREMENT,
            scheduleID INT(11) NOT NULL,
            fileName VARCHAR(255) NOT NULL,
            fileType VARCHAR(100) NOT NULL,
            filePath VARCHAR(500) NOT NULL,
            fileSize INT(11) NOT NULL,
            fileDateTime DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (fileID),
            FOREIGN KEY (scheduleID) REFERENCES ScheduleDiary(scheduleID) ON DELETE CASCADE
        )";
        $conn->query($sql);
    }
    
    // Move the uploaded file to the target directory
    file_put_contents($logFile, "Attempting to move file from {$_FILES['file']['tmp_name']} to {$targetFile}\n", FILE_APPEND);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        file_put_contents($logFile, "File moved successfully\n", FILE_APPEND);
        // Insert file info into database
        $stmt = $conn->prepare("INSERT INTO ScheduleFiles (scheduleID, fileName, fileType, filePath, fileSize) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $scheduleID, $fileName, $fileType, $targetFile, $fileSize);
        
        $result = $stmt->execute();
        file_put_contents($logFile, "Database insert result: " . ($result ? "success" : "failed") . "\n", FILE_APPEND);
        if ($result) {
            $fileID = $conn->insert_id;
            $fileDateTime = date('Y-m-d H:i');
            
            // Get client ID for this schedule
            $clientStmt = $conn->prepare("SELECT clientID FROM ScheduleDiary WHERE scheduleID = ?");
            $clientStmt->bind_param("i", $scheduleID);
            $clientStmt->execute();
            $clientResult = $clientStmt->get_result();
            $clientRow = $clientResult->fetch_assoc();
            $clientID = $clientRow ? $clientRow['clientID'] : 0;
            $clientStmt->close();
            
            $response = [
                'success' => true,
                'message' => 'File uploaded successfully',
                'file' => [
                    'fileID' => $fileID,
                    'fileName' => $fileName,
                    'filePath' => $targetFile,
                    'fileSize' => round($fileSize/1024, 2),
                    'fileDateTime' => $fileDateTime,
                    'scheduleID' => $scheduleID,
                    'clientID' => $clientID
                ]
            ];
        } else {
            $response['message'] = 'Error saving file information to database';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Error moving uploaded file';
    }
    } else {
        // Handle specific upload errors
        $uploadErrors = array(
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        );
        
        $errorCode = $_FILES['file']['error'];
        $errorMessage = isset($uploadErrors[$errorCode]) ? $uploadErrors[$errorCode] : 'Unknown upload error';
        
        $response['message'] = $errorMessage;
        file_put_contents($logFile, "File upload error: {$errorMessage}\n", FILE_APPEND);
    }
} else {
    $response['message'] = 'No file uploaded';
    file_put_contents($logFile, "No file in request\n", FILE_APPEND);
}

// Log the final response
file_put_contents($logFile, "Final response: " . print_r($response, true) . "\n\n", FILE_APPEND);

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>

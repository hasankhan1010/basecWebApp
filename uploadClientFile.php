<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include('database.php');

// Check if client ID is provided
if (!isset($_POST['clientID']) || !is_numeric($_POST['clientID'])) {
    echo "Invalid client ID.";
    exit();
}
$clientID = intval($_POST['clientID']);

// Handle file upload
$response = [];
if (isset($_FILES['clientFile']) && $_FILES['clientFile']['error'] === UPLOAD_ERR_OK) {
    $fileName = basename($_FILES['clientFile']['name']);
    $fileType = $_FILES['clientFile']['type'];
    $fileSize = $_FILES['clientFile']['size'];
    $targetDir = "uploads/";
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Generate unique filename to prevent overwriting
    $uniqueFileName = time() . '_' . $fileName;
    $targetFile = $targetDir . $uniqueFileName;
    
    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['clientFile']['tmp_name'], $targetFile)) {
        // Insert file info into database
        $stmt = $conn->prepare("INSERT INTO ClientFiles (clientID, fileName, fileType, filePath, fileSize) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $clientID, $fileName, $fileType, $targetFile, $fileSize);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "File uploaded successfully.";
        } else {
            $response['success'] = false;
            $response['message'] = "Error saving file information to database.";
        }
        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = "Error uploading file.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "No file uploaded or file upload error.";
}

// Redirect back to the client service edit page
header("Location: clientServiceEdit.php?clientID=" . $clientID . "&uploadStatus=" . ($response['success'] ? 'success' : 'error') . "&message=" . urlencode($response['message']));
exit();
?>

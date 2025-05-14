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
    !isset($_POST['scheduleID']) || !is_numeric($_POST['scheduleID'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid file or schedule ID']);
    exit();
}

$fileID = intval($_POST['fileID']);
$scheduleID = intval($_POST['scheduleID']);
$response = ['success' => false];

// Get file path before deleting record
$stmt = $conn->prepare("SELECT filePath FROM ScheduleFiles WHERE fileID = ? AND scheduleID = ?");
$stmt->bind_param("ii", $fileID, $scheduleID);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc();
$stmt->close();

if ($file) {
    // Delete file from filesystem
    if (file_exists($file['filePath'])) {
        unlink($file['filePath']);
    }
    
    // Delete record from database
    $stmt = $conn->prepare("DELETE FROM ScheduleFiles WHERE fileID = ? AND scheduleID = ?");
    $stmt->bind_param("ii", $fileID, $scheduleID);
    
    if ($stmt->execute()) {
        $response = [
            'success' => true,
            'message' => 'File deleted successfully',
            'fileID' => $fileID
        ];
    } else {
        $response['message'] = 'Error deleting file from database';
    }
    $stmt->close();
} else {
    $response['message'] = 'File not found';
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>

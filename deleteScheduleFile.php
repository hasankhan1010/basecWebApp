<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include('database.php');

// Check if file ID and schedule ID are provided
if (!isset($_GET['fileID']) || !is_numeric($_GET['fileID']) || !isset($_GET['scheduleID']) || !is_numeric($_GET['scheduleID'])) {
    header("Location: scheduleDiary.php");
    exit();
}

$fileID = intval($_GET['fileID']);
$scheduleID = intval($_GET['scheduleID']);

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
    $stmt->execute();
    $stmt->close();
}

// Redirect back to the schedule diary add page
header("Location: scheduleDiaryAdd.php?scheduleID=" . $scheduleID);
exit();
?>

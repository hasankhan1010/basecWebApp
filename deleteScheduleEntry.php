<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include('database.php');

// Check if schedule ID is provided
if (!isset($_GET['scheduleID']) || !is_numeric($_GET['scheduleID'])) {
    $_SESSION['message'] = "Invalid schedule ID.";
    $_SESSION['message_type'] = "error";
    header("Location: scheduleDiary.php");
    exit();
}

$scheduleID = intval($_GET['scheduleID']);

// Get schedule details before deletion (for confirmation message)
$stmt = $conn->prepare("SELECT scheduleDate, scheduleJobType FROM ScheduleDiary WHERE scheduleID = ?");
$stmt->bind_param("i", $scheduleID);
$stmt->execute();
$result = $stmt->get_result();
$schedule = $result->fetch_assoc();
$stmt->close();

if (!$schedule) {
    $_SESSION['message'] = "Schedule entry not found.";
    $_SESSION['message_type'] = "error";
    header("Location: scheduleDiary.php");
    exit();
}

// Delete any associated files first
$fileStmt = $conn->prepare("SELECT filePath FROM ScheduleFiles WHERE scheduleID = ?");
$fileStmt->bind_param("i", $scheduleID);
$fileStmt->execute();
$fileResult = $fileStmt->get_result();

while ($file = $fileResult->fetch_assoc()) {
    if (file_exists($file['filePath'])) {
        unlink($file['filePath']);
    }
}
$fileStmt->close();

// Delete file records from database
$deleteFilesStmt = $conn->prepare("DELETE FROM ScheduleFiles WHERE scheduleID = ?");
$deleteFilesStmt->bind_param("i", $scheduleID);
$deleteFilesStmt->execute();
$deleteFilesStmt->close();

// Delete the schedule entry
$deleteStmt = $conn->prepare("DELETE FROM ScheduleDiary WHERE scheduleID = ?");
$deleteStmt->bind_param("i", $scheduleID);
$deleteStmt->execute();
$deleteStmt->close();

// Set success message
$_SESSION['message'] = "Schedule entry for " . date('M d, Y', strtotime($schedule['scheduleDate'])) . " (" . $schedule['scheduleJobType'] . ") has been deleted.";
$_SESSION['message_type'] = "success";

// Redirect back to schedule diary
header("Location: scheduleDiary.php");
exit();
?>

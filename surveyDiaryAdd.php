<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}

include('database.php');

$surveyID = isset($_GET['surveyID']) ? $_GET['surveyID'] : "";
$surveyDate = "";
$surveyTime = "";
$surveyCreatedByID = "";
$surveyNotes = "";
$surveyStatus = "";

// Pre-fill date and time if provided via GET (for a new entry)
if (isset($_GET['date'])) {
    $surveyDate = $_GET['date'];
}
if (isset($_GET['hour'])) {
    // Convert the hour (e.g., "08") into a time string "08:00:00"
    $surveyTime = $_GET['hour'] . ":00:00";
}

// If editing an existing entry, load its values
if ($surveyID && $conn) {
    $query = "SELECT * FROM SurveyDiary WHERE surveyID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $surveyID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()){
        $surveyDate = $row['surveyDate'];
        $surveyTime = $row['surveyTime'];
        $surveyCreatedByID = $row['surveyCreatedByID'];
        $surveyNotes = $row['surveyNotes'];
        $surveyStatus = $row['surveyStatus'];
    }
    $stmt->close();
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $surveyID = isset($_POST['surveyID']) ? $_POST['surveyID'] : "";
    $surveyDate = $_POST['surveyDate'];
    $surveyTime = $_POST['surveyTime'];
    $surveyCreatedByID = $_POST['surveyCreatedByID'];
    $surveyNotes = $_POST['surveyNotes'];
    $surveyStatus = $_POST['surveyStatus'];
    
    if ($surveyID) {
        // Update existing record
        $query = "UPDATE SurveyDiary SET surveyDate=?, surveyTime=?, surveyCreatedByID=?, surveyNotes=?, surveyStatus=? WHERE surveyID=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $surveyDate, $surveyTime, $surveyCreatedByID, $surveyNotes, $surveyStatus, $surveyID);
        $stmt->execute();
        $stmt->close();
    } else {
        // Insert new record
        $query = "INSERT INTO SurveyDiary (surveyDate, surveyTime, surveyCreatedByID, surveyNotes, surveyStatus) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $surveyDate, $surveyTime, $surveyCreatedByID, $surveyNotes, $surveyStatus);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: surveyDiary.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo ($surveyID ? "Edit" : "Add"); ?> Survey Diary Entry</title>
    <link rel="stylesheet" href="surveyDiaryAdd.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-left">
            <a href="portal.php">Home</a>
            <a href="clientServices.php">Client Services</a>
            <a href="scheduleDiary.php">Schedule Diary</a>
            <a href="surveyDiary.php" class="active">Survey Diary</a>
            <a href="adminControl.php">Admin Control</a>
            <a href="feedback.php">Feedback</a>
            <a href="notifications.php">Notifications</a>
            <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
            <a href="payments.php">Payments</a>
        </div>
        <div class="nav-right">
            <a href="home.php">Logout</a>
        </div>
    </nav>
    
    <div class="form-container">
        <h1><?php echo ($surveyID ? "Edit" : "Add"); ?> Survey Diary Entry</h1>
        <form method="post" action="surveyDiaryAdd.php">
            <?php if ($surveyID): ?>
                <input type="hidden" name="surveyID" value="<?php echo htmlspecialchars($surveyID); ?>">
            <?php endif; ?>
            
            <label for="surveyDate">Date:</label>
            <input type="date" id="surveyDate" name="surveyDate" value="<?php echo htmlspecialchars($surveyDate); ?>" required>
            
            <label for="surveyTime">Time:</label>
            <input type="time" id="surveyTime" name="surveyTime" value="<?php echo htmlspecialchars($surveyTime); ?>" required>
            
            <label for="surveyCreatedByID">Created By:</label>
            <input type="text" id="surveyCreatedByID" name="surveyCreatedByID" value="<?php echo htmlspecialchars($surveyCreatedByID); ?>" required>
            
            <label for="surveyNotes">Notes:</label>
            <textarea id="surveyNotes" name="surveyNotes" required><?php echo htmlspecialchars($surveyNotes); ?></textarea>
            
            <label for="surveyStatus">Status:</label>
            <input type="text" id="surveyStatus" name="surveyStatus" value="<?php echo htmlspecialchars($surveyStatus); ?>" required>
            
            <button type="submit"><?php echo ($surveyID ? "Update" : "Add"); ?> Entry</button>
        </form>
    </div>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Company Name. All rights reserved.</p>
    </footer>
</body>
</html>

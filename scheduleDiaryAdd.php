<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}

include('database.php');

/*
  Expected columns in ScheduleDiary:
    scheduleID (INT, PK)
    scheduleDate (DATE)
    scheduleStartTime (TIME)
    scheduleEndTime (TIME)
    engineerID (INT) -- FK to Engineer(engineerID)
    clientID (INT)   -- FK to Clients(clientID)
    scheduleJobType (VARCHAR)
    scheduleIsAnnualService (TINYINT/BOOLEAN)
    scheduleStatus (VARCHAR)
    scheduleDetails (TEXT/VARCHAR)
    scheduleNotes (TEXT/VARCHAR)
*/

// Load list of valid engineers for the dropdown
$engineers = [];
$engQuery = "SELECT engineerID, engineerFirstName, engineerLastName FROM Engineer";
$engResult = mysqli_query($conn, $engQuery);
if ($engResult) {
    while ($row = mysqli_fetch_assoc($engResult)) {
        $engineers[] = $row;
    }
}

// Load list of valid clients for the dropdown
$clients = [];
$clientQuery = "SELECT clientID, clientFirstName, clientLastName FROM Clients";
$clientResult = mysqli_query($conn, $clientQuery);
if ($clientResult) {
    while ($row = mysqli_fetch_assoc($clientResult)) {
        $clients[] = $row;
    }
}

// Initialize variables
$scheduleID              = isset($_GET['scheduleID']) ? intval($_GET['scheduleID']) : 0;
$scheduleDate            = "";
$scheduleStartTime       = "";
$scheduleEndTime         = "";
$engineerID              = "";
$clientID                = "";
$scheduleJobType         = "";
$scheduleIsAnnualService = 0; // 0 = false, 1 = true
$scheduleStatus          = "";
$scheduleDetails         = "";
$scheduleNotes           = "";

// If creating a new entry, pre-fill from clicked slot (GET params)
if (isset($_GET['date'])) {
    $scheduleDate = $_GET['date'];
}
if (isset($_GET['hour'])) {
    // Convert the clicked hour into a valid "HH:00:00" string
    $clickedHour = intval($_GET['hour']);
    if ($clickedHour >= 0 && $clickedHour < 24) {
        $scheduleStartTime = str_pad($clickedHour, 2, '0', STR_PAD_LEFT) . ":00:00";
    }
}

// If editing an existing entry, load its values from the DB (overriding any GET pre-fill)
if ($scheduleID > 0 && $conn) {
    $query = "SELECT * FROM ScheduleDiary WHERE scheduleID = ?";
    $stmt  = $conn->prepare($query);
    $stmt->bind_param("i", $scheduleID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $scheduleDate            = $row['scheduleDate'];
        $scheduleStartTime       = $row['scheduleStartTime'];
        $scheduleEndTime         = $row['scheduleEndTime'];
        $engineerID              = $row['engineerID'];
        $clientID                = $row['clientID'];
        $scheduleJobType         = $row['scheduleJobType'];
        $scheduleIsAnnualService = $row['scheduleIsAnnualService'];
        $scheduleStatus          = $row['scheduleStatus'];
        $scheduleDetails         = $row['scheduleDetails'];
        $scheduleNotes           = $row['scheduleNotes'];
    }
    $stmt->close();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $scheduleID              = isset($_POST['scheduleID']) ? intval($_POST['scheduleID']) : 0;
    $scheduleDate            = $_POST['scheduleDate'];
    $scheduleStartTime       = $_POST['scheduleStartTime'];
    $scheduleEndTime         = $_POST['scheduleEndTime'];
    $engineerID              = intval($_POST['engineerID']);
    $clientID                = intval($_POST['clientID']);
    $scheduleJobType         = $_POST['scheduleJobType'];
    $scheduleIsAnnualService = isset($_POST['scheduleIsAnnualService']) ? 1 : 0;
    $scheduleStatus          = $_POST['scheduleStatus'];
    $scheduleDetails         = $_POST['scheduleDetails'];
    $scheduleNotes           = $_POST['scheduleNotes'];
    
    // Placeholder for file handling (no logic yet)
    // $file = $_FILES['scheduleFile'] ?? null;
    
    if ($scheduleID > 0) {
        // UPDATE existing record (11 placeholders)
        $query = "UPDATE ScheduleDiary
                  SET scheduleDate = ?,
                      scheduleStartTime = ?,
                      scheduleEndTime = ?,
                      engineerID = ?,
                      clientID = ?,
                      scheduleJobType = ?,
                      scheduleIsAnnualService = ?,
                      scheduleStatus = ?,
                      scheduleDetails = ?,
                      scheduleNotes = ?
                  WHERE scheduleID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssiisisssi",
            $scheduleDate,
            $scheduleStartTime,
            $scheduleEndTime,
            $engineerID,
            $clientID,
            $scheduleJobType,
            $scheduleIsAnnualService,
            $scheduleStatus,
            $scheduleDetails,
            $scheduleNotes,
            $scheduleID
        );
        $stmt->execute();
        $stmt->close();
    } else {
        // INSERT new record (10 placeholders)
        $query = "INSERT INTO ScheduleDiary (
                    scheduleDate,
                    scheduleStartTime,
                    scheduleEndTime,
                    engineerID,
                    clientID,
                    scheduleJobType,
                    scheduleIsAnnualService,
                    scheduleStatus,
                    scheduleDetails,
                    scheduleNotes
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssiisisss",
            $scheduleDate,
            $scheduleStartTime,
            $scheduleEndTime,
            $engineerID,
            $clientID,
            $scheduleJobType,
            $scheduleIsAnnualService,
            $scheduleStatus,
            $scheduleDetails,
            $scheduleNotes
        );
        $stmt->execute();
        $stmt->close();
    }
    header("Location: scheduleDiary.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo ($scheduleID > 0 ? "Edit" : "Add"); ?> Schedule Diary Entry</title>
    <link rel="stylesheet" href="scheduleDiaryAdd.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-left">
            <a href="portal.php">Home</a>
            <a href="clientServices.php">Client Services</a>
            <a href="scheduleDiary.php" class="active">Schedule Diary</a>
            <a href="surveyDiary.php">Survey Diary</a>
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
        <h1><?php echo ($scheduleID > 0 ? "Edit" : "Add"); ?> Schedule Diary Entry</h1>
        <form method="post" action="scheduleDiaryAdd.php" enctype="multipart/form-data">
            <?php if ($scheduleID > 0): ?>
                <input type="hidden" name="scheduleID" value="<?php echo htmlspecialchars($scheduleID); ?>">
            <?php endif; ?>
            
            <label for="scheduleDate">Date:</label>
            <input type="date" id="scheduleDate" name="scheduleDate" value="<?php echo htmlspecialchars($scheduleDate); ?>" required>
            
            <label for="scheduleStartTime">Start Time:</label>
            <input type="time" id="scheduleStartTime" name="scheduleStartTime" value="<?php echo htmlspecialchars($scheduleStartTime); ?>" required>
            
            <label for="scheduleEndTime">End Time:</label>
            <input type="time" id="scheduleEndTime" name="scheduleEndTime" value="<?php echo htmlspecialchars($scheduleEndTime); ?>" required>
            
            <label for="engineerID">Engineer:</label>
            <select id="engineerID" name="engineerID" required>
                <option value="">-- Select Engineer --</option>
                <?php foreach ($engineers as $eng): ?>
                    <option value="<?php echo $eng['engineerID']; ?>" <?php if ($eng['engineerID'] == $engineerID) echo "selected"; ?>>
                        <?php echo htmlspecialchars($eng['engineerFirstName'] . " " . $eng['engineerLastName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="clientID">Client:</label>
            <select id="clientID" name="clientID" required>
                <option value="">-- Select Client --</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo $client['clientID']; ?>" <?php if ($client['clientID'] == $clientID) echo "selected"; ?>>
                        <?php echo htmlspecialchars($client['clientFirstName'] . " " . $client['clientLastName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="scheduleJobType">Job Type:</label>
            <input type="text" id="scheduleJobType" name="scheduleJobType" value="<?php echo htmlspecialchars($scheduleJobType); ?>" required>
            
            <label for="scheduleIsAnnualService">Annual Service:</label>
            <input type="checkbox" id="scheduleIsAnnualService" name="scheduleIsAnnualService" <?php if($scheduleIsAnnualService) echo 'checked'; ?>>
            
            <label for="scheduleStatus">Status:</label>
            <input type="text" id="scheduleStatus" name="scheduleStatus" value="<?php echo htmlspecialchars($scheduleStatus); ?>" required>
            
            <label for="scheduleDetails">Details:</label>
            <textarea id="scheduleDetails" name="scheduleDetails" required><?php echo htmlspecialchars($scheduleDetails); ?></textarea>
            
            <label for="scheduleNotes">Notes:</label>
            <textarea id="scheduleNotes" name="scheduleNotes"><?php echo htmlspecialchars($scheduleNotes); ?></textarea>
            
            <!-- Placeholder for file upload -->
            <label for="scheduleFile">Add File (placeholder):</label>
            <input type="file" id="scheduleFile" name="scheduleFile">
            
            <button type="submit"><?php echo ($scheduleID > 0 ? "Update" : "Add"); ?> Entry</button>
        </form>
    </div>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Company Name. All rights reserved.</p>
    </footer>
</body>
</html>

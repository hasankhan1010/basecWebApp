<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include('database.php');

// MAKE SURE CLIENT ID IS PROVIDED 
if (!isset($_GET['clientID'])) {
    echo "No client specified.";
    exit();
}
$clientID = intval($_GET['clientID']);

// START THE ARRAYS 
$client = [];
$jobs   = [];
$files  = [];

// GET ALL THE CLIENT DETAILS 
$stmt = $conn->prepare("SELECT * FROM Clients WHERE clientID = ?");
$stmt->bind_param("i", $clientID);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc() ?? [];
$stmt->close();

// GET THE JOB HISTORY FROM SCHEDULE DIARY::::::::::::::
$stmt = $conn->prepare("SELECT * FROM ScheduleDiary WHERE clientID = ? ORDER BY scheduleDate DESC");
$stmt->bind_param("i", $clientID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
$stmt->close();

// IF TABLE EXISTS, GET THE CLIENT FILESSSS
$tableCheck = $conn->query("SHOW TABLES LIKE 'ClientFiles'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $stmt = $conn->prepare("SELECT * FROM ClientFiles WHERE clientID = ? ORDER BY fileDateTime DESC");
    if ($stmt) {
        $stmt->bind_param("i", $clientID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $files[] = $row;
        }
        $stmt->close();
    }
}

// PROCESS THE FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // UPDATE THE CLIENT DETAILS 
    $clientFirstName = $_POST['clientFirstName'] ?? null;
    $clientLastName  = $_POST['clientLastName'] ?? null;
    $clientAddress1  = $_POST['clientAddress1'] ?? null;
    $clientAddress2  = $_POST['clientAddress2'] ?? null;
    $clientEmail     = $_POST['clientEmail'] ?? null;
    $clientPhone     = $_POST['clientPhone'] ?? null;
    $clientNotes     = $_POST['clientNotes'] ?? null;
    
    $stmt = $conn->prepare("UPDATE Clients SET clientFirstName=?, clientLastName=?, clientAddress1=?, clientAddress2=?, clientEmail=?, clientPhone=?, clientNotes=? WHERE clientID=?");
    $stmt->bind_param("sssssssi", $clientFirstName, $clientLastName, $clientAddress1, $clientAddress2, $clientEmail, $clientPhone, $clientNotes, $clientID);
    $stmt->execute();
    $stmt->close();
    
    // UPDATE EACH JOB RECORD IN SCHEDULE DIARY
    if (isset($_POST['jobs']) && is_array($_POST['jobs'])) {
        foreach ($_POST['jobs'] as $job) {
            $scheduleID     = intval($job['scheduleID']);
            $feedbackRating = $job['feedbackRating'] ?? "";
            $paymentStatus  = $job['paymentStatus'] ?? "";
            $paymentNotes   = $job['paymentNotes'] ?? "";
            
            $stmt = $conn->prepare("UPDATE ScheduleDiary SET feedbackRating=?, paymentStatus=?, paymentNotes=? WHERE scheduleID=?");
            $stmt->bind_param("sssi", $feedbackRating, $paymentStatus, $paymentNotes, $scheduleID);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    header("Location: clientServiceEdit.php?clientID=" . $clientID);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Client Services</title>
    <link rel="stylesheet" href="clientServiceEdit.css">
</head>
<body>
 
    <nav class="navbar">
        <div class="nav-left">
            <a href="portal.php">Home</a>
            <a href="clientServices.php" class="active">Client Services</a>
            <a href="scheduleDiary.php">Schedule Diary</a>
            <a href="surveyDiary.php">Survey Diary</a>
            <a href="adminControl.php">Admin Control</a>
            <a href="feedback.php">Feedback</a>
            <a href="notifications.php">Notifications</a>
            <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
            <a href="payments.php">Payments</a>
        </div>
        <div class="nav-right">
            <a href="logout.php">Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <h1>Edit Client Services</h1>
        
        <!-- CLIENT DETAILS SECTIONN -->
        <form method="post" action="clientServiceEdit.php?clientID=<?php echo $clientID; ?>">
            <fieldset class="client-details">
                <legend>Client Details</legend>
                <div class="form-row">
                    <label for="clientFirstName">First Name:</label>
                    <input type="text" id="clientFirstName" name="clientFirstName" value="<?php echo htmlspecialchars($client['clientFirstName'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label for="clientLastName">Last Name:</label>
                    <input type="text" id="clientLastName" name="clientLastName" value="<?php echo htmlspecialchars($client['clientLastName'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label for="clientAddress1">Address Line 1:</label>
                    <input type="text" id="clientAddress1" name="clientAddress1" value="<?php echo htmlspecialchars($client['clientAddress1'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label for="clientAddress2">Address Line 2:</label>
                    <input type="text" id="clientAddress2" name="clientAddress2" value="<?php echo htmlspecialchars($client['clientAddress2'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label for="clientEmail">Email:</label>
                    <input type="email" id="clientEmail" name="clientEmail" value="<?php echo htmlspecialchars($client['clientEmail'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label for="clientPhone">Phone:</label>
                    <input type="text" id="clientPhone" name="clientPhone" value="<?php echo htmlspecialchars($client['clientPhone'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label for="clientNotes">Notes:</label>
                    <textarea id="clientNotes" name="clientNotes"><?php echo htmlspecialchars($client['clientNotes'] ?? ''); ?></textarea>
                </div>
            </fieldset>
            
            <!-- JOB HISTORY SECTION -->
            <fieldset class="job-history">
                <legend>Job History (Schedule Diary)</legend>
                <?php if (!empty($jobs)): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Job Type</th>
                                <th>Engineer ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Details</th>
                                <th>Feedback Rating</th>
                                <th>Paid Status</th>
                                <th>Payment Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $index => $job): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['scheduleJobType'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($job['engineerID'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($job['scheduleDate'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars(($job['scheduleStartTime'] ?? '') . " - " . ($job['scheduleEndTime'] ?? '')); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($job['scheduleDetails'] ?? '')); ?></td>
                                <td>
                                    <input type="text" name="jobs[<?php echo $index; ?>][feedbackRating]" value="<?php echo htmlspecialchars($job['feedbackRating'] ?? ''); ?>">
                                </td>
                                <td>
                                    <select name="jobs[<?php echo $index; ?>][paymentStatus]">
                                        <option value="Unpaid" <?php if (($job['paymentStatus'] ?? '') === "Unpaid") echo "selected"; ?>>Unpaid</option>
                                        <option value="Paid" <?php if (($job['paymentStatus'] ?? '') === "Paid") echo "selected"; ?>>Paid</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="jobs[<?php echo $index; ?>][paymentNotes]" value="<?php echo htmlspecialchars($job['paymentNotes'] ?? ''); ?>">
                                </td>
                                <input type="hidden" name="jobs[<?php echo $index; ?>][scheduleID]" value="<?php echo $job['scheduleID']; ?>">
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p>No job history records found.</p>
                <?php endif; ?>
            </fieldset>
            
            <div class="form-actions">
                <button type="submit">Save Changes</button>
            </div>
        </form>
        
        <!-- FILES SECTION::::::::::::::::::::::: -->
        <fieldset class="client-files">
            <legend>Client Files</legend>
            <?php if (!empty($files)): ?>
                <ul>
                    <?php foreach ($files as $file): ?>
                        <li><a href="<?php echo htmlspecialchars($file['filePath']); ?>" download><?php echo htmlspecialchars($file['fileName']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No files uploaded.</p>
            <?php endif; ?>
            <form method="post" action="uploadClientFile.php" enctype="multipart/form-data">
                <input type="hidden" name="clientID" value="<?php echo $clientID; ?>">
                <label for="clientFile">Add File:</label>
                <input type="file" id="clientFile" name="clientFile">
                <button type="submit">Upload File</button>
            </form>
        </fieldset>
        
    </div>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
    </footer>
</body>
</html>

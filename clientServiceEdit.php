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
    FOREIGN KEY (clientID) REFERENCES Clients(clientID)
)";
$conn->query($sql);

// Create uploads directory if it doesn't exist
$uploadsDir = __DIR__ . '/uploads';
if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

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

// Get filter parameters
$paymentFilter = isset($_GET['paymentFilter']) ? $_GET['paymentFilter'] : 'all';

// GET THE JOB HISTORY FROM SCHEDULE DIARY WITH FEEDBACK AND PAYMENT DATA
$query = "SELECT sd.*, f.feedbackRating, f.feedbackComments,
          COALESCE(p.paymentID, 0) as paymentID, 
          COALESCE(p.paymentAmount, 0) as paymentAmount, 
          COALESCE(p.paymentIsPaid, 0) as paymentIsPaid, 
          p.paymentNotes 
          FROM ScheduleDiary sd 
          LEFT JOIN Feedback f ON sd.scheduleID = f.feedbackID 
          LEFT JOIN Payments p ON sd.scheduleID = p.invoiceReference 
          WHERE sd.clientID = ? ";

// Apply payment filter if selected
if ($paymentFilter === 'paid') {
    $query .= "AND p.paymentIsPaid = 1 ";
} elseif ($paymentFilter === 'unpaid') {
    $query .= "AND p.paymentIsPaid = 0 ";
}

$query .= "ORDER BY sd.scheduleDate DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $clientID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
$stmt->close();

// GET THE CLIENT FILES
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

// Handle upload status messages
$uploadMessage = "";
if (isset($_GET['uploadStatus'])) {
    if ($_GET['uploadStatus'] === 'success') {
        $uploadMessage = isset($_GET['message']) ? urldecode($_GET['message']) : "File uploaded successfully.";
    } else {
        $uploadMessage = isset($_GET['message']) ? urldecode($_GET['message']) : "Error uploading file.";
    }
}

// Handle file deletion
if (isset($_GET['deleteFile']) && is_numeric($_GET['deleteFile'])) {
    $fileID = intval($_GET['deleteFile']);
    
    // Get file path before deleting record
    $stmt = $conn->prepare("SELECT filePath FROM ClientFiles WHERE fileID = ? AND clientID = ?");
    $stmt->bind_param("ii", $fileID, $clientID);
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
        $stmt = $conn->prepare("DELETE FROM ClientFiles WHERE fileID = ? AND clientID = ?");
        $stmt->bind_param("ii", $fileID, $clientID);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to remove the deleteFile parameter
        header("Location: clientServiceEdit.php?clientID=" . $clientID);
        exit();
    }
}

// PROCESS THE FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_FILES['clientFile'])) {
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
            $scheduleID = intval($job['scheduleID']);
            $feedbackRating = $job['feedbackRating'] ?? "";
            $feedbackComments = $job['feedbackComments'] ?? "";
            $paymentIsPaid = isset($job['paymentIsPaid']) ? (int)$job['paymentIsPaid'] : 0;
            $paymentNotes = $job['paymentNotes'] ?? "";
            $paymentDateTime = date('Y-m-d H:i:s');
            
            // Update feedback if provided
            if (!empty($feedbackRating)) {
                // Check if feedback exists
                $checkFeedbackStmt = $conn->prepare("SELECT feedbackID FROM Feedback WHERE feedbackID = ?");
                $checkFeedbackStmt->bind_param("i", $scheduleID);
                $checkFeedbackStmt->execute();
                $feedbackResult = $checkFeedbackStmt->get_result();
                
                if ($feedbackResult->num_rows > 0) {
                    // Update existing feedback
                    $updateFeedbackStmt = $conn->prepare("UPDATE Feedback SET feedbackRating = ?, feedbackComments = ?, feedbackRecievedDateTime = ? WHERE feedbackID = ?");
                    $updateFeedbackStmt->bind_param("issi", $feedbackRating, $feedbackComments, $paymentDateTime, $scheduleID);
                    $updateFeedbackStmt->execute();
                    $updateFeedbackStmt->close();
                } else {
                    // Insert new feedback
                    $insertFeedbackStmt = $conn->prepare("INSERT INTO Feedback (feedbackID, feedbackRating, feedbackComments, feedbackRecievedDateTime) VALUES (?, ?, ?, ?)");
                    $insertFeedbackStmt->bind_param("iiss", $scheduleID, $feedbackRating, $feedbackComments, $paymentDateTime);
                    $insertFeedbackStmt->execute();
                    $insertFeedbackStmt->close();
                }
                
                $checkFeedbackStmt->close();
            }
            
            // Check if a payment record exists for this schedule
            $checkStmt = $conn->prepare("SELECT paymentID FROM Payments WHERE invoiceReference = ?");
            $checkStmt->bind_param("i", $scheduleID);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                // Update existing payment record
                $paymentRow = $checkResult->fetch_assoc();
                $paymentID = $paymentRow['paymentID'];
                $checkStmt->close();
                
                $updateStmt = $conn->prepare("UPDATE Payments SET paymentIsPaid = ?, paymentNotes = ?, paymentDateTime = ? WHERE paymentID = ?");
                $updateStmt->bind_param("issi", $paymentIsPaid, $paymentNotes, $paymentDateTime, $paymentID);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                $checkStmt->close();
                
                // Get client ID for this schedule
                $clientStmt = $conn->prepare("SELECT clientID FROM ScheduleDiary WHERE scheduleID = ?");
                $clientStmt->bind_param("i", $scheduleID);
                $clientStmt->execute();
                $clientResult = $clientStmt->get_result();
                
                if ($clientResult->num_rows > 0) {
                    $clientRow = $clientResult->fetch_assoc();
                    $clientID = $clientRow['clientID'];
                    $clientStmt->close();
                    
                    // Create new payment record
                    $insertStmt = $conn->prepare("INSERT INTO Payments (clientID, invoiceReference, paymentIsPaid, paymentNotes, paymentDateTime) VALUES (?, ?, ?, ?, ?)");
                    $insertStmt->bind_param("iiiss", $clientID, $scheduleID, $paymentIsPaid, $paymentNotes, $paymentDateTime);
                    $insertStmt->execute();
                    $insertStmt->close();
                } else {
                    $clientStmt->close();
                }
            }
        }
    }
    
    // Set success message
    $_SESSION['client_edit_message'] = "Client information and payment status have been updated successfully.";
    
    header("Location: clientServiceEdit.php?clientID=" . $clientID . (isset($_GET['paymentFilter']) ? '&paymentFilter=' . $_GET['paymentFilter'] : ''));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Service Edit</title>
    <link rel="stylesheet" href="clientServiceEdit.css">
    <link rel="stylesheet" href="fileUpload.css">
    <link rel="stylesheet" href="css/feedback-popup.css">
    <link rel="stylesheet" href="darkmode.css">
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
            <a href="notifications.php">Map Routes</a>
            <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
            <a href="payments.php">Payments</a>
            <a href="reminders.php">Reminders</a>
        </div>
        <div class="nav-right">
            <a href="logout.php">Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <h1>Edit Client Services</h1>
        <?php if (!empty($uploadMessage)): ?>
        <div class="upload-message"><?php echo $uploadMessage; ?></div>
        <?php endif; ?>
        
        <!-- CLIENT DETAILS SECTIONN -->
        <form method="post" action="clientServiceEdit.php?clientID=<?php echo $clientID; ?>" id="clientDetailsForm">
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
                
                <!-- Payment Filter Controls -->
                <div class="filter-controls">
                    <div class="filter-group">
                        <span class="filter-label">Filter by payment status:</span>
                        <a href="clientServiceEdit.php?clientID=<?php echo $clientID; ?>&paymentFilter=all" class="filter-btn <?php echo $paymentFilter === 'all' ? 'active' : ''; ?>">All</a>
                        <a href="clientServiceEdit.php?clientID=<?php echo $clientID; ?>&paymentFilter=paid" class="filter-btn <?php echo $paymentFilter === 'paid' ? 'active' : ''; ?>">Paid</a>
                        <a href="clientServiceEdit.php?clientID=<?php echo $clientID; ?>&paymentFilter=unpaid" class="filter-btn <?php echo $paymentFilter === 'unpaid' ? 'active' : ''; ?>">Unpaid</a>
                    </div>
                    <a href="clientServiceEdit.php?clientID=<?php echo $clientID; ?>" class="clear-filter-btn">Clear Filters</a>
                </div>
                
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
                                <th>Feedback</th>
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
                                    <?php if (!empty($job['feedbackRating'])): ?>
                                        <a href="javascript:void(0)" class="feedback-link" onclick="showFeedbackDetails('<?php echo $job['scheduleID']; ?>', '<?php echo htmlspecialchars($job['scheduleJobType']); ?>', '<?php echo htmlspecialchars($job['scheduleDate']); ?>', '<?php echo htmlspecialchars($job['feedbackRating']); ?>', '<?php echo htmlspecialchars($job['feedbackComments'] ?? ''); ?>')">
                                            <?php echo htmlspecialchars($job['feedbackRating']); ?> / 5
                                            <?php if ($job['feedbackRating'] <= 2): ?>
                                                <span class="bad-feedback-indicator">Low Rating</span>
                                            <?php endif; ?>
                                        </a>
                                        <input type="hidden" name="jobs[<?php echo $index; ?>][feedbackRating]" value="<?php echo htmlspecialchars($job['feedbackRating'] ?? ''); ?>">
                                    <?php else: ?>
                                        <div class="table-input-wrapper">
                                            <input type="text" name="jobs[<?php echo $index; ?>][feedbackRating]" value="<?php echo htmlspecialchars($job['feedbackRating'] ?? ''); ?>" placeholder="No feedback">
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-input-wrapper">
                                        <select name="jobs[<?php echo $index; ?>][paymentIsPaid]" class="payment-status-select <?php echo (($job['paymentIsPaid'] ?? 0) == 1) ? 'status-paid' : 'status-unpaid'; ?>">
                                            <option value="0" <?php if (($job['paymentIsPaid'] ?? 0) == 0) echo "selected"; ?>>Unpaid</option>
                                            <option value="1" <?php if (($job['paymentIsPaid'] ?? 0) == 1) echo "selected"; ?>>Paid</option>
                                        </select>
                                        <span class="payment-status-indicator <?php echo (($job['paymentIsPaid'] ?? 0) == 1) ? 'paid' : 'unpaid'; ?>"></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-input-wrapper">
                                        <input type="text" name="jobs[<?php echo $index; ?>][paymentNotes]" value="<?php echo htmlspecialchars($job['paymentNotes'] ?? ''); ?>">
                                    </div>
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
                <?php if (isset($_SESSION['client_edit_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['client_edit_message']; 
                        unset($_SESSION['client_edit_message']); 
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </form>
        
        <!-- FILES SECTION::::::::::::::::::::::: -->
        <fieldset class="client-files">
            <legend>Client Files</legend>
            <?php if (!empty($files)): ?>
                <ul class="file-list">
                    <?php foreach ($files as $file): ?>
                        <li>
                            <div class="file-item">
                                <a href="<?php echo htmlspecialchars($file['filePath']); ?>" download class="file-link"><?php echo htmlspecialchars($file['fileName']); ?></a>
                                <span class="file-info"><?php echo date('Y-m-d H:i', strtotime($file['fileDateTime'])); ?> - <?php echo round($file['fileSize']/1024, 2); ?> KB</span>
                                <a href="clientServiceEdit.php?clientID=<?php echo $clientID; ?>&deleteFile=<?php echo $file['fileID']; ?>" class="delete-file" onclick="return confirm('Are you sure you want to delete this file?');">Delete</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No files uploaded.</p>
            <?php endif; ?>
            <form method="post" action="uploadClientFile.php" enctype="multipart/form-data">
                <div class="file-upload-container">
                    <input type="hidden" name="clientID" value="<?php echo $clientID; ?>">
                    <label for="clientFile">Add File:</label>
                    <input type="file" id="clientFile" name="clientFile" required>
                    <button type="submit" class="orange-button">Upload File</button>
                </div>
            </form>
            <?php if (!empty($uploadMessage)): ?>
                <p class="upload-message"><?php echo $uploadMessage; ?></p>
            <?php endif; ?>
        </fieldset>
        
    </div>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
    </footer>
    <!-- Feedback Modal -->
    <div id="feedbackModal" class="modal">
        <div id="feedbackModalContent" class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-header">
                <h2>Feedback Details</h2>
            </div>
            <div class="modal-body">
                <p><span class="modal-label">Job Type:</span> <span id="modal-job-type"></span></p>
                <p><span class="modal-label">Date:</span> <span id="modal-job-date"></span></p>
                <p><span class="modal-label">Rating:</span> <span id="modal-rating"></span> / 5</p>
                <div id="modal-stars" class="modal-stars"></div>
                <p><span class="modal-label">Comments:</span></p>
                <p id="modal-comments"></p>
            </div>
        </div>
    </div>
    
    <script src="alerts.js"></script>
    <script src="darkmode.js"></script>
    <script src="js/feedback-popup.js"></script>
    <script>
        // Add event listeners to payment status selects to update the class
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelects = document.querySelectorAll('.payment-status-select');
            
            statusSelects.forEach(select => {
                select.addEventListener('change', function() {
                    // Update the select class
                    if (this.value === '1') {
                        this.classList.remove('status-unpaid');
                        this.classList.add('status-paid');
                        
                        // Update the indicator span
                        const indicator = this.nextElementSibling;
                        indicator.classList.remove('unpaid');
                        indicator.classList.add('paid');
                    } else {
                        this.classList.remove('status-paid');
                        this.classList.add('status-unpaid');
                        
                        // Update the indicator span
                        const indicator = this.nextElementSibling;
                        indicator.classList.remove('paid');
                        indicator.classList.add('unpaid');
                    }
                });
            });
        });
    </script>
</body>
</html>

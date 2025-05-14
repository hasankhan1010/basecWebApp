<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include('database.php');

/**
 * EXPECTED COLUMNS IN SCHEDULE DIARY:
 *     scheduleID (INT, PK)
 *     scheduleDate (DATE)
 *     scheduleStartTime (TIME)
 *     scheduleEndTime (TIME)
 *     engineerID (INT) -- FK to Engineer(engineerID)
 *     clientID (INT)   -- FK to Clients(clientID)
 *     scheduleJobType (VARCHAR)
 *     scheduleIsAnnualService (TINYINT/BOOLEAN)
 *     scheduleStatus (VARCHAR)
 *     scheduleDetails (TEXT/VARCHAR)
 *     scheduleNotes (TEXT/VARCHAR)
 */

// LOAD THE LIST OF VALID ENGINEERS IN A DROP-DOWN
!
$engineers = [];
$engQuery = "SELECT engineerID, engineerFirstName, engineerLastName FROM Engineer";
$engResult = mysqli_query($conn, $engQuery);
if ($engResult) {
    while ($row = mysqli_fetch_assoc($engResult)) {
        $engineers[] = $row;
    }
}

// THE SAME BUT WITH THE CLIENTS

$clients = [];
$clientQuery = "SELECT clientID, clientFirstName, clientLastName FROM Clients";
$clientResult = mysqli_query($conn, $clientQuery);
if ($clientResult) {
    while ($row = mysqli_fetch_assoc($clientResult)) {
        $clients[] = $row;
    }
}

// THE VARIABLES - INITIALIZE

$scheduleID              = isset($_GET['scheduleID']) ? intval($_GET['scheduleID']) : 0;
$scheduleDate            = "";
$scheduleStartTime       = "";
$scheduleEndTime         = "";
$engineerID              = "";
$clientID                = "";
$scheduleJobType         = "";
$scheduleIsAnnualService = 0; // 0 = FALSE, 1 = TRUE!
$scheduleStatus          = "";
$scheduleDetails         = "";
$scheduleNotes           = "";

// Get schedule files!
$files = [];
if ($scheduleID > 0) {
    // Check if ScheduleFiles table exists!
    $tableCheck = $conn->query("SHOW TABLES LIKE 'ScheduleFiles'");
    if ($tableCheck && $tableCheck->num_rows === 0) {
        // Create ScheduleFiles table if it doesn't exist!
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
    
    // Get files for this schedule entry!
    $fileStmt = $conn->prepare("SELECT * FROM ScheduleFiles WHERE scheduleID = ? ORDER BY fileDateTime DESC");
    if ($fileStmt) {
        $fileStmt->bind_param("i", $scheduleID);
        $fileStmt->execute();
        $fileResult = $fileStmt->get_result();
        while ($file = $fileResult->fetch_assoc()) {
            $files[] = $file;
        }
        $fileStmt->close();
    }
}

// PRE-FILL FROM CLICKED SLOT IF CREATING A NEW ENTRY!
if (isset($_GET['date'])) {
    $scheduleDate = $_GET['date'];
}
if (isset($_GET['hour'])) {
    $clickedHour = intval($_GET['hour']);
    if ($clickedHour >= 0 && $clickedHour < 24) {
        $scheduleStartTime = str_pad($clickedHour, 2, '0', STR_PAD_LEFT) . ":00:00";
    }
}

// IF EDITING EXISTING ENTRY - LOAD VALUES FROM DB (OVERRIDE ANY GET PRE-FILLS)! - love this part
if ($scheduleID > 0 && $conn) {
    // LIST COLUMNS INSTEAD OF SELECT - FASTER AND MORE EFFICIENT *!
    $query = "SELECT
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
              FROM ScheduleDiary
              WHERE scheduleID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $scheduleID);
    $stmt->execute();

    // USE STORE RESULT AND GET RESULT INSTEAD OF THE OTHER ONE !
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result(
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
        $stmt->fetch();
    }
    $stmt->close();
}

// PROCESS FORM SUBMISSION!
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
    $createReminder          = isset($_POST['createReminder']) ? 1 : 0;
    
    // Force job type to be Annual Service if the toggle is on!
    if ($scheduleIsAnnualService) {
        $scheduleJobType = 'Annual Service';
    }
    
    // We no longer check for existing entries at the same time slot
    // This allows multiple entries to be added for different engineers working on different jobs
    // for different clients at the same time slot
    
    // File uploads are now handled via AJAX in a separate request
    // This allows for instantaneous uploads with immediate options for deleting, adding another file, and copying to client profile
    
    if ($scheduleID > 0) {
        // UPDATE THE EXISTING RECORD!
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
        // INSERT NEW RECORD! - this is important
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

    // Create annual service reminder if requested!
    if ($scheduleIsAnnualService && $createReminder && $scheduleID > 0) {
        // Calculate the date for next year!
        $reminderDate = date('Y-m-d', strtotime($scheduleDate . ' +1 year'));
        
        // Check if Reminders table exists!
        $tableCheck = $conn->query("SHOW TABLES LIKE 'Reminders'");
        if ($tableCheck && $tableCheck->num_rows === 0) {
            // Create Reminders table if it doesn't exist! - love this part
            $sql = "CREATE TABLE IF NOT EXISTS Reminders (
                reminderID INT(11) NOT NULL AUTO_INCREMENT,
                clientID INT(11) NOT NULL,
                reminderDate DATE NOT NULL,
                reminderType VARCHAR(100) NOT NULL,
                reminderDetails TEXT,
                reminderStatus VARCHAR(50) DEFAULT 'Active',
                createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (reminderID),
                FOREIGN KEY (clientID) REFERENCES Clients(clientID)
            )";
            $conn->query($sql);
        }
        
        // Insert the reminder!
        $reminderType = 'Annual Service';
        $reminderDetails = "Annual service reminder for client #$clientID. Previous service on $scheduleDate.";
        $reminderStatus = 'Active';
        
        $stmt = $conn->prepare("INSERT INTO Reminders (clientID, reminderDate, reminderType, reminderDetails, reminderStatus) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $clientID, $reminderDate, $reminderType, $reminderDetails, $reminderStatus);
        $stmt->execute();
        $stmt->close();
    }
    
    // Redirect to the new URL!
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
    <link rel="stylesheet" href="fileUpload.css">
    <link rel="stylesheet" href="darkmode.css">
    <!-- Select2 CSS and JS for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        /**
 * Toggle button styling
 */
        .toggle-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            margin-right: 10px;
        }
        
        .toggle-switch input { 
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #ffa500;
        }
        
        input:focus + .slider {
            box-shadow: 0 0 1px #ffa500;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="portal.php">Home</a>
            <a href="clientServices.php">Client Services</a>
            <a href="scheduleDiary.php" class="active">Schedule Diary</a>
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
    
    <div class="form-container">
        <h1><?php echo ($scheduleID > 0 ? "Edit" : "Add"); ?> Schedule Diary Entry</h1>
        <form method="post" action="scheduleDiaryAdd.php" enctype="multipart/form-data">
            <?php if ($scheduleID > 0): ?>
                <input type="hidden" name="scheduleID" value="<?php echo htmlspecialchars($scheduleID); ?>">
            <?php endif; ?>
            
            <label for="scheduleDate">Date:</label>
            <input type="date" id="scheduleDate" name="scheduleDate" 
                   value="<?php echo htmlspecialchars($scheduleDate); ?>" required>
            
            <label for="scheduleStartTime">Start Time:</label>
            <input type="time" id="scheduleStartTime" name="scheduleStartTime" 
                   value="<?php echo htmlspecialchars($scheduleStartTime); ?>" required>
            
            <label for="scheduleEndTime">End Time:</label>
            <input type="time" id="scheduleEndTime" name="scheduleEndTime" 
                   value="<?php echo htmlspecialchars($scheduleEndTime); ?>" required>
            
            <label for="engineerID">Engineer:</label>
            <select id="engineerID" name="engineerID" required>
                <option value="">-- Select Engineer --</option>
                <?php foreach ($engineers as $eng): ?>
                    <option value="<?php echo $eng['engineerID']; ?>" 
                        <?php if ($eng['engineerID'] == $engineerID) echo "selected"; ?>>
                        <?php echo htmlspecialchars($eng['engineerFirstName'] . " " . $eng['engineerLastName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="clientID">Client:</label>
            <select id="clientID" name="clientID" class="searchable-select" required>
                <option value="">-- Select Client --</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo $client['clientID']; ?>" 
                        <?php if ($client['clientID'] == $clientID) echo "selected"; ?>
                        data-client-id="<?php echo $client['clientID']; ?>">
                        <?php echo htmlspecialchars($client['clientFirstName'] . " " . $client['clientLastName']); ?> (ID: <?php echo $client['clientID']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="scheduleJobType">Job Type:</label>
            <input type="text" id="scheduleJobType" name="scheduleJobType" 
                   value="<?php echo htmlspecialchars($scheduleJobType); ?>" required>
            
            <div class="toggle-container">
                <label for="scheduleIsAnnualService">Annual Service:</label>
                <label class="toggle-switch">
                    <input type="checkbox" id="scheduleIsAnnualService" name="scheduleIsAnnualService"
                        <?php if($scheduleIsAnnualService) echo 'checked'; ?> onchange="toggleAnnualService(this.checked)">
                    <span class="slider"></span>
                </label>
            </div>
            
            <div class="toggle-container" id="reminderToggleContainer">
                <label for="createReminder">Create Annual Reminder:</label>
                <label class="toggle-switch">
                    <input type="checkbox" id="createReminder" name="createReminder" 
                           <?php if($scheduleIsAnnualService) echo 'checked'; else echo 'disabled'; ?>>
                    <span class="slider"></span>
                </label>
            </div>
            
            <label for="scheduleStatus">Status:</label>
            <input type="text" id="scheduleStatus" name="scheduleStatus" 
                   value="<?php echo htmlspecialchars($scheduleStatus); ?>" required>
            
            <label for="scheduleDetails">Details:</label>
            <textarea id="scheduleDetails" name="scheduleDetails" required><?php
                echo htmlspecialchars($scheduleDetails);
            ?></textarea>
            
            <label for="scheduleNotes">Notes:</label>
            <textarea id="scheduleNotes" name="scheduleNotes"><?php
                echo htmlspecialchars($scheduleNotes);
            ?></textarea>
            
            <?php if ($scheduleID > 0): ?>
            <!-- File Upload Section with AJAX support -->
            <fieldset class="schedule-files">
                <legend>Schedule Files</legend>
                <div id="file-list-container">
                    <?php if (!empty($files)): ?>
                        <ul class="file-list" id="file-list">
                            <?php foreach ($files as $file): ?>
                                <li id="file-item-<?php echo $file['fileID']; ?>">
                                    <div class="file-item">
                                        <a href="<?php echo htmlspecialchars($file['filePath']); ?>" download class="file-link"><?php echo htmlspecialchars($file['fileName']); ?></a>
                                        <span class="file-info"><?php echo date('Y-m-d H:i', strtotime($file['fileDateTime'])); ?> - <?php echo round($file['fileSize']/1024, 2); ?> KB</span>
                                        <div class="file-actions">
                                            <button type="button" class="delete-file-btn" data-file-id="<?php echo $file['fileID']; ?>" data-schedule-id="<?php echo $scheduleID; ?>">Delete</button>
                                            <button type="button" class="copy-file-btn" data-file-id="<?php echo $file['fileID']; ?>" data-schedule-id="<?php echo $scheduleID; ?>" data-client-id="<?php echo $clientID; ?>">Copy to Client Profile</button>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p id="no-files-message">No files uploaded.</p>
                    <?php endif; ?>
                </div>
                
                <div class="file-upload-container">
                    <div class="ajax-upload-container">
                        <form id="ajax-file-upload-form" enctype="multipart/form-data">
                            <input type="hidden" name="scheduleID" value="<?php echo $scheduleID; ?>">
                            <label for="ajax-file-input" class="file-upload-label">Select File:</label>
                            <input type="file" id="ajax-file-input" name="file" class="file-upload-input">
                            <button type="button" id="upload-file-btn" class="upload-btn">Upload File</button>
                        </form>
                        <div id="upload-status" class="upload-status"></div>
                    </div>
                </div>
            </fieldset>
            <?php endif; ?>
            
            <button type="submit"><?php echo ($scheduleID > 0 ? "Update" : "Add"); ?> Entry</button>
        </form>
    </div>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Company Name. All rights reserved.</p>
    </footer>
    <script src="alerts.js"></script>
    <script src="darkmode.js"></script>
    <script>
        // Function to handle annual service toggle!
        function toggleAnnualService(isChecked) {
            const jobTypeField = document.getElementById('scheduleJobType');
            const reminderCheckbox = document.getElementById('createReminder');
            const reminderContainer = document.getElementById('reminderToggleContainer');
            
            if (isChecked) {
                // Save the current value if it's not already Annual Service!
                if (jobTypeField.value !== 'Annual Service') {
                    jobTypeField.dataset.previousValue = jobTypeField.value;
                }
                jobTypeField.value = 'Annual Service';
                jobTypeField.readOnly = true;
                jobTypeField.style.backgroundColor = '#f0f0f0';
                
                // Enable and check the reminder checkbox! - this is important
                reminderCheckbox.disabled = false;
                reminderCheckbox.checked = true;
                reminderContainer.style.opacity = '1';
            } else {
                // Restore previous value if available!
                if (jobTypeField.dataset.previousValue) {
                    jobTypeField.value = jobTypeField.dataset.previousValue;
                } else {
                    jobTypeField.value = '';
                }
                jobTypeField.readOnly = false;
                jobTypeField.style.backgroundColor = '';
                
                // Disable and uncheck the reminder checkbox!
                reminderCheckbox.disabled = true;
                reminderCheckbox.checked = false;
                reminderContainer.style.opacity = '0.5';
            }
        }
        
        // Initialize on page load!
        $(document).ready(function() {
            // Initialize Select2 for searchable client dropdown
            $('#clientID').select2({
                placeholder: 'Search for a client by name or ID',
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true,
                minimumInputLength: 1,
                templateResult: formatClientOption,
                templateSelection: formatClientOption
            });
            
            // Apply dark mode compatible styles to Select2
            applySelect2DarkModeStyles();
            
            // Handle dark mode toggle to update Select2 styles
            $('#darkModeToggle').on('change', function() {
                setTimeout(applySelect2DarkModeStyles, 100);
            });
            
            // Format client options with highlighted search terms
            function formatClientOption(client) {
                if (!client.id) return client.text;
                return $('<span>' + client.text + '</span>');
            }
            
            // Apply dark mode compatible styles to Select2
            function applySelect2DarkModeStyles() {
                if (document.body.classList.contains('dark-mode')) {
                    $('.select2-container--default .select2-selection--single').css({
                        'background-color': '#333',
                        'color': '#eee',
                        'border-color': '#555'
                    });
                    $('.select2-container--default .select2-selection__rendered').css('color', '#eee');
                    $('.select2-dropdown').css({
                        'background-color': '#333',
                        'color': '#eee',
                        'border-color': '#555'
                    });
                    $('.select2-search__field').css({
                        'background-color': '#444',
                        'color': '#eee',
                        'border-color': '#666'
                    });
                    $('.select2-results__option').css('color', '#eee');
                    $('.select2-container--default .select2-results__option--highlighted[aria-selected]').css({
                        'background-color': '#ffa500',
                        'color': '#fff'
                    });
                } else {
                    $('.select2-container--default .select2-selection--single').css({
                        'background-color': '#fff',
                        'color': '#333',
                        'border-color': '#ccc'
                    });
                    $('.select2-container--default .select2-selection__rendered').css('color', '#333');
                    $('.select2-dropdown').css({
                        'background-color': '#fff',
                        'color': '#333',
                        'border-color': '#ccc'
                    });
                    $('.select2-search__field').css({
                        'background-color': '#fff',
                        'color': '#333',
                        'border-color': '#ccc'
                    });
                    $('.select2-results__option').css('color', '#333');
                    $('.select2-container--default .select2-results__option--highlighted[aria-selected]').css({
                        'background-color': '#ffa500',
                        'color': '#fff'
                    });
                }
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const annualServiceCheckbox = document.getElementById('scheduleIsAnnualService');
            if (annualServiceCheckbox && annualServiceCheckbox.checked) {
                toggleAnnualService(true);
            } else if (annualServiceCheckbox) {
                toggleAnnualService(false);
            }
            
            // AJAX file upload functionality
            if (document.getElementById('upload-file-btn')) {
                console.log('Setting up file upload handlers');
                setupFileUploadHandlers();
            }
        });
        
        function setupFileUploadHandlers() {
            // File upload button click handler
            const uploadBtn = document.getElementById('upload-file-btn');
            if (uploadBtn) {
                console.log('Adding click handler to upload button');
                uploadBtn.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent any default behavior
                    console.log('Upload button clicked');
                    
                    const fileInput = document.getElementById('ajax-file-input');
                    if (!fileInput || fileInput.files.length === 0) {
                        showUploadStatus('Please select a file first', 'error');
                        return;
                    }
                    
                    console.log('File selected:', fileInput.files[0].name);
                    const formData = new FormData();
                    formData.append('file', fileInput.files[0]);
                    formData.append('scheduleID', document.querySelector('input[name="scheduleID"]').value);
                    
                    uploadFile(formData);
                });
            } else {
                console.error('Upload button not found');
            }
            
            // Setup event delegation for delete and copy buttons
            document.addEventListener('click', function(e) {
                // Delete file button
                if (e.target.classList.contains('delete-file-btn')) {
                    const fileID = e.target.getAttribute('data-file-id');
                    const scheduleID = e.target.getAttribute('data-schedule-id');
                    if (confirm('Are you sure you want to delete this file?')) {
                        deleteFile(fileID, scheduleID);
                    }
                }
                
                // Copy to client profile button
                if (e.target.classList.contains('copy-file-btn')) {
                    const fileID = e.target.getAttribute('data-file-id');
                    const scheduleID = e.target.getAttribute('data-schedule-id');
                    const clientID = e.target.getAttribute('data-client-id');
                    copyFileToClient(fileID, scheduleID, clientID);
                }
            });
        }
        
        function uploadFile(formData) {
            showUploadStatus('Uploading...', 'info');
            
            // Debug - log form data contents
            console.log('Form data being sent:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            fetch('ajaxUploadScheduleFile.php', {
                method: 'POST',
                body: formData,
                // Don't set Content-Type header, browser will set it with boundary for multipart/form-data
                credentials: 'same-origin' // Include cookies in the request
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showUploadStatus('File uploaded successfully!', 'success');
                    addFileToList(data.file);
                    // Clear the file input for another upload
                    document.getElementById('ajax-file-input').value = '';
                } else {
                    showUploadStatus('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showUploadStatus('Error: ' + error.message, 'error');
            });
        }
        
        function deleteFile(fileID, scheduleID) {
            const formData = new FormData();
            formData.append('fileID', fileID);
            formData.append('scheduleID', scheduleID);
            
            fetch('ajaxDeleteScheduleFile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the file item from the list
                    const fileItem = document.getElementById('file-item-' + fileID);
                    if (fileItem) {
                        fileItem.remove();
                        showUploadStatus('File deleted successfully!', 'success');
                        
                        // Check if there are any files left
                        const fileList = document.getElementById('file-list');
                        if (fileList && fileList.children.length === 0) {
                            const container = document.getElementById('file-list-container');
                            container.innerHTML = '<p id="no-files-message">No files uploaded.</p>';
                        }
                    }
                } else {
                    showUploadStatus('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showUploadStatus('Error: ' + error.message, 'error');
            });
        }
        
        function copyFileToClient(fileID, scheduleID, clientID) {
            showUploadStatus('Copying file to client profile...', 'info');
            
            const formData = new FormData();
            formData.append('fileID', fileID);
            formData.append('scheduleID', scheduleID);
            formData.append('clientID', clientID);
            
            fetch('ajaxCopyScheduleFileToClient.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showUploadStatus('File copied to client profile successfully!', 'success');
                } else {
                    showUploadStatus('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showUploadStatus('Error: ' + error.message, 'error');
            });
        }
        
        function addFileToList(file) {
            const fileList = document.getElementById('file-list');
            const noFilesMessage = document.getElementById('no-files-message');
            
            // Remove the 'no files' message if it exists
            if (noFilesMessage) {
                noFilesMessage.remove();
            }
            
            // Create a new file list if it doesn't exist
            if (!fileList) {
                const container = document.getElementById('file-list-container');
                container.innerHTML = '<ul class="file-list" id="file-list"></ul>';
            }
            
            // Create the new file item HTML
            const fileItem = document.createElement('li');
            fileItem.id = 'file-item-' + file.fileID;
            fileItem.innerHTML = `
                <div class="file-item">
                    <a href="${file.filePath}" download class="file-link">${file.fileName}</a>
                    <span class="file-info">${file.fileDateTime} - ${file.fileSize} KB</span>
                    <div class="file-actions">
                        <button type="button" class="delete-file-btn" data-file-id="${file.fileID}" data-schedule-id="${file.scheduleID}">Delete</button>
                        <button type="button" class="copy-file-btn" data-file-id="${file.fileID}" data-schedule-id="${file.scheduleID}" data-client-id="${file.clientID}">Copy to Client Profile</button>
                    </div>
                </div>
            `;
            
            // Add the new file item to the list
            document.getElementById('file-list').appendChild(fileItem);
        }
        
        function showUploadStatus(message, type) {
            console.log('Status update:', type, message);
            const statusElement = document.getElementById('upload-status');
            if (!statusElement) {
                console.error('Status element not found');
                return;
            }
            
            statusElement.textContent = message;
            statusElement.className = 'upload-status ' + type;
            
            // Clear the status after 5 seconds for success messages
            if (type === 'success') {
                setTimeout(() => {
                    statusElement.textContent = '';
                    statusElement.className = 'upload-status';
                }, 5000);
            }
        }
    </script>
</body>
</html>

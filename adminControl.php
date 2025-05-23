<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include('database.php');

// THE PROCESS OF DELETING::::::::::::::::::::::::::::::
if (isset($_GET['action']) && $_GET['action'] === 'deleteUser' && isset($_GET['role']) && isset($_GET['id'])) {
    $role = $_GET['role'];
    $id = intval($_GET['id']);
    $success = false;
    
    // Use prepared statements to prevent SQL injection
    try {
        if ($role === 'engineer') {
            $stmt = $conn->prepare("DELETE FROM Engineer WHERE engineerID = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
        } elseif ($role === 'admin') {
            $stmt = $conn->prepare("DELETE FROM Administrator WHERE adminID = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
        } elseif ($role === 'manager') {
            $stmt = $conn->prepare("DELETE FROM Manager WHERE managerID = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
        } elseif ($role === 'sales') {
            $stmt = $conn->prepare("DELETE FROM SalesTeam WHERE SalesID = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
        }
        
        // Try to log the event if possible, but don't fail if logging fails
        try {
            // Check if ActivityLog table exists
            $tableCheckResult = $conn->query("SHOW TABLES LIKE 'ActivityLog'");
            if ($tableCheckResult->num_rows > 0) {
                $userRole = ucfirst($role);
                $date = date('Y-m-d H:i:s');
                $adminUser = $_SESSION['user']['username'] ?? 'Unknown';
                $logStmt = $conn->prepare("INSERT INTO ActivityLog (activityType, activityDescription, activityDate, activityUser) VALUES (?, ?, ?, ?)");
                $activityType = 'User Deletion';
                $activityDescription = "$userRole with ID $id was deleted by $adminUser";
                $logStmt->bind_param("ssss", $activityType, $activityDescription, $date, $adminUser);
                $logStmt->execute();
                $logStmt->close();
            }
        } catch (Exception $e) {
            // Silently ignore logging errors
        }
    } catch (Exception $e) {
        // Set an error message if deletion fails
        $_SESSION['deleteError'] = "Error deleting user: " . $e->getMessage();
    }
    
    // Redirect back to admin control page
    header("Location: adminControl.php" . ($success ? "?deleted=1" : "?error=1"));
    exit();
}

// ADDING NEW USERS:::::::::::::::::
$addUserMessage = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['addUser'])) {
    $role = $_POST['userRole'] ?? '';
    // ALL THE FIELDS 
    $firstName = $_POST['firstName'] ?? '';
    $lastName  = $_POST['lastName'] ?? '';
    $email     = $_POST['email'] ?? '';
    $phone     = $_POST['phone'] ?? '';
    $username  = $_POST['username'] ?? '';
    $password  = $_POST['password'] ?? ''; // SHOULD I USE HASHING? - IF NOT THEN I EXPLAIN WHY
    $status    = $_POST['status'] ?? '';
    $notes     = $_POST['notes'] ?? '';
    
    // ROLE-SPECIFIC FIELDS - EASIER TO DO IT LIKE THIS:::::
    $speciality = $_POST['speciality'] ?? '';
    
    if ($role === 'engineer') {
        $stmt = $conn->prepare("INSERT INTO Engineer (engineerFirstName, engineerLastName, engineerEmail, engineerPhone, engineerSpeciality, engineerUsername, engineerPassword, engineerStatus, engineerNotes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $firstName, $lastName, $email, $phone, $speciality, $username, $password, $status, $notes);
    } elseif ($role === 'admin') {
        $stmt = $conn->prepare("INSERT INTO Administrator (adminFirstName, adminLastName, adminEmail, adminPhone, adminUsername, adminPassword, adminStatus, adminNotes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $firstName, $lastName, $email, $phone, $username, $password, $status, $notes);
    } elseif ($role === 'manager') {
        $stmt = $conn->prepare("INSERT INTO Manager (managerFirstName, managerLastName, managerEmail, managerPhone, managerUsername, managerPassword, managerStatus, managerNotes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $firstName, $lastName, $email, $phone, $username, $password, $status, $notes);
    } elseif ($role === 'sales') {
        $stmt = $conn->prepare("INSERT INTO SalesTeam (salesFirstName, salesLastName, salesEmail, salesPhone, salesUsername, salesPassword, salesStatus, salesNotes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $firstName, $lastName, $email, $phone, $username, $password, $status, $notes);
    }
    if (isset($stmt) && $stmt) {
        if ($stmt->execute()) {
            $addUserMessage = "New $role added successfully.";
        } else {
            $addUserMessage = "Error adding new $role.";
        }
        $stmt->close();
    }
}

// MANUALLY ADDING A NEW CLIENT
$addClientMessage = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['addClient'])) {
    $clientFirstName = $_POST['clientFirstName'] ?? '';
    $clientLastName  = $_POST['clientLastName'] ?? '';
    $clientAddress1  = $_POST['clientAddress1'] ?? '';
    $clientAddress2  = $_POST['clientAddress2'] ?? '';
    $clientPhone     = $_POST['clientPhone'] ?? '';
    $clientEmail     = $_POST['clientEmail'] ?? '';
    $clientNotes     = $_POST['clientNotes'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO Clients (clientFirstName, clientLastName, clientAddress1, clientAddress2, clientPhone, clientEmail, clientNotes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $clientFirstName, $clientLastName, $clientAddress1, $clientAddress2, $clientPhone, $clientEmail, $clientNotes);
    if ($stmt->execute()) {
        $addClientMessage = "Client added successfully.";
    } else {
        $addClientMessage = "Error adding client.";
    }
    $stmt->close();
}

// CSV IMPORT - LIKE FROM OLD SYSTEM TO NEW - DB IMPORT
$importMessage = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['importClients'])) {
    if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
        $csvFile = $_FILES['csvFile']['tmp_name'];
        $handle = fopen($csvFile, 'r');
        if ($handle !== FALSE) {
            // SKIPPING THE HEADER ROW 
            $headers = fgetcsv($handle);
            $importCount = 0;
            $errorCount = 0;
            $errors = [];
            
            // Display the detected headers for debugging
            $headerDebug = "Detected headers: " . implode(", ", $headers);
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                // Check if we have enough data in the row
                if (count($data) >= 6) {
                    try {
                        // Map data based on header positions or use default positions
                        $clientFirstName = trim($data[0]);
                        $clientLastName = trim($data[1]);
                        $clientAddress1 = trim($data[2]);
                        $clientAddress2 = trim($data[3]);
                        $clientPhone = trim($data[4]);
                        $clientEmail = trim($data[5]);
                        $clientNotes = isset($data[6]) ? trim($data[6]) : '';
                        
                        // Validate essential data
                        if (empty($clientFirstName) || empty($clientLastName)) {
                            throw new Exception("Missing required name fields");
                        }
                        
                        // Check if client already exists to avoid duplicates
                        $checkStmt = $conn->prepare("SELECT clientID FROM Clients WHERE clientFirstName = ? AND clientLastName = ? AND clientEmail = ?");
                        $checkStmt->bind_param("sss", $clientFirstName, $clientLastName, $clientEmail);
                        $checkStmt->execute();
                        $checkResult = $checkStmt->get_result();
                        
                        if ($checkResult->num_rows > 0) {
                            // Client already exists, you could update instead if needed
                            $checkStmt->close();
                            continue;
                        }
                        $checkStmt->close();
                        
                        // Insert the new client
                        $stmt = $conn->prepare("INSERT INTO Clients (clientFirstName, clientLastName, clientAddress1, clientAddress2, clientPhone, clientEmail, clientNotes) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssss", $clientFirstName, $clientLastName, $clientAddress1, $clientAddress2, $clientPhone, $clientEmail, $clientNotes);
                        
                        if ($stmt->execute()) {
                            $importCount++;
                        } else {
                            throw new Exception("Database error: " . $stmt->error);
                        }
                        $stmt->close();
                    } catch (Exception $e) {
                        $errorCount++;
                        $errors[] = "Row error: " . $e->getMessage();
                        // Limit the number of errors we store to avoid overwhelming the user
                        if (count($errors) > 5) {
                            $errors[] = "Additional errors not shown...";
                            break;
                        }
                    }
                } else {
                    $errorCount++;
                    $errors[] = "Row has insufficient data: " . implode(", ", $data);
                }
            }
            fclose($handle);
            
            if ($importCount > 0) {
                $importMessage = "<span class='success-message'>$importCount clients imported successfully.</span>";
                if ($errorCount > 0) {
                    $importMessage .= "<br><span class='error-message'>$errorCount rows had errors.</span>";
                    $importMessage .= "<div class='error-details'><ul>";
                    foreach ($errors as $error) {
                        $importMessage .= "<li>" . htmlspecialchars($error) . "</li>";
                    }
                    $importMessage .= "</ul></div>";
                }
            } else {
                $importMessage = "<span class='error-message'>No clients were imported. Please check your CSV file format.</span>";
                $importMessage .= "<br>" . htmlspecialchars($headerDebug);
                $importMessage .= "<div class='error-details'><ul>";
                foreach ($errors as $error) {
                    $importMessage .= "<li>" . htmlspecialchars($error) . "</li>";
                }
                $importMessage .= "</ul></div>";
            }
        } else {
            $importMessage = "<span class='error-message'>Error reading CSV file. Please make sure it's a valid CSV format.</span>";
        }
    } else {
        $errorCode = $_FILES['csvFile']['error'];
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form",
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded",
            UPLOAD_ERR_NO_FILE => "No file was uploaded",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
        ];
        $errorMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : "Unknown error";
        $importMessage = "<span class='error-message'>Error uploading CSV file: $errorMessage</span>";
    }
}

// Set up messages for user deletion feedback
$deleteMessage = "";
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $deleteMessage = "<div class='message-container'><div class='success-message'>User has been successfully deleted.</div></div>";
} else if (isset($_GET['error']) && $_GET['error'] == 1) {
    $deleteMessage = "<div class='message-container'><div class='error-message'>Error deleting user. Please try again.</div></div>";
}
if (isset($_SESSION['deleteError'])) {
    $deleteMessage = "<div class='message-container'><div class='error-message'>{$_SESSION['deleteError']}</div></div>";
    unset($_SESSION['deleteError']);
}

// GETTING ALL ACTIVE USER INFO - FOR LOGBOOK VIEWING
$activeUsers = [];
// ACTIVE MANAGERS 
$query = "SELECT managerID AS userID, managerUsername AS username, managerFirstName AS firstName, managerLastName AS lastName, 'Manager' AS role FROM Manager";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $activeUsers[] = $row;
    }
}
// ACTIVE ENGINEERS
$query = "SELECT engineerID AS userID, engineerUsername AS username, engineerFirstName AS firstName, engineerLastName AS lastName, 'Engineer' AS role FROM Engineer";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $activeUsers[] = $row;
    }
}
// ACTIVE ADMINISTRATORS 
$query = "SELECT adminID AS userID, adminUsername AS username, adminFirstName AS firstName, adminLastName AS lastName, 'Admin' AS role FROM Administrator";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $activeUsers[] = $row;
    }
}
// ACTIVE SALES TEAM
$query = "SELECT SalesID AS userID, salesUsername AS username, salesFirstName AS firstName, salesLastName AS lastName, 'Sales' AS role FROM SalesTeam";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $activeUsers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Control Panel</title>
    <link rel="stylesheet" href="adminControl.css">
    <link rel="stylesheet" href="darkmode.css">
    <script src="js/jquery.min.js"></script>
    <script>
    $(document).ready(function(){
        // ADD AJAX HERE TO UPDATE LOGS IN REAL TIME - OPTIONAL!??
    });
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="portal.php">Home</a>
            <a href="clientServices.php">Client Services</a>
            <a href="scheduleDiary.php">Schedule Diary</a>
            <a href="surveyDiary.php">Survey Diary</a>
            <a href="adminControl.php" class="active">Admin Control</a>
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
        <h1>Admin Control Panel</h1>
        
        <!-- USER MANAGEMENT FIRST -->
        <section class="user-management">
            <h2>User Management</h2>
            <div class="user-forms">
                <!-- ADDING NEW USER FORM -->
                <form method="post" action="adminControl.php">
                    <h3>Add New User</h3>
                    <label for="userRole">Role:</label>
                    <select name="userRole" id="userRole" required>
                        <option value="">Select Role</option>
                        <option value="engineer">Engineer</option>
                        <option value="admin">Administrator</option>
                        <option value="manager">Manager</option>
                        <option value="sales">Sales Team</option>
                    </select>
                    <label for="firstName">First Name:</label>
                    <input type="text" name="firstName" id="firstName" required>
                    <label for="lastName">Last Name:</label>
                    <input type="text" name="lastName" id="lastName" required>
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email">
                    <label for="phone">Phone:</label>
                    <input type="text" name="phone" id="phone">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                    <!-- NEED TO INCLUDE SPECIALTY FOR ENGINEERS  -->
                    <div id="specialityField" style="display:none;">
                        <label for="speciality">Speciality:</label>
                        <input type="text" name="speciality" id="speciality">
                    </div>
                    <label for="status">Status:</label>
                    <input type="text" name="status" id="status">
                    <label for="notes">Notes:</label>
                    <textarea name="notes" id="notes"></textarea>
                    <button type="submit" name="addUser">Add User</button>
                </form>
            </div>
            <!-- ACTIVE USERS TABLE !!! -->
            <div class="active-users-section">
                <h3>Active Users</h3>
                <?php if(!empty($deleteMessage)): ?>
                    <?php echo $deleteMessage; ?>
                <?php endif; ?>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($activeUsers)): ?>
                            <?php foreach ($activeUsers as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['userID']); ?></td>
                                <td><?php echo htmlspecialchars($u['username']); ?></td>
                                <td><?php echo htmlspecialchars($u['firstName'] . " " . $u['lastName']); ?></td>
                                <td><?php echo htmlspecialchars($u['role']); ?></td>
                                <td>
                                    <a href="adminControl.php?action=deleteUser&role=<?php echo strtolower($u['role']); ?>&id=<?php echo $u['userID']; ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="delete-btn">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No active users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        
        <!-- CLIENT MANAGEMENT:::::::: -->
        <section class="client-management">
            <h2>Client Management</h2>
            <div class="client-forms">
                <!-- ADD NEW CLIENT FORM::::::::::: -->
                <form method="post" action="adminControl.php">
                    <h3>Add New Client</h3>
                    <label for="clientFirstName">First Name:</label>
                    <input type="text" name="clientFirstName" id="clientFirstName" required>
                    <label for="clientLastName">Last Name:</label>
                    <input type="text" name="clientLastName" id="clientLastName" required>
                    <label for="clientAddress1">Address Line 1:</label>
                    <input type="text" name="clientAddress1" id="clientAddress1">
                    <label for="clientAddress2">Address Line 2:</label>
                    <input type="text" name="clientAddress2" id="clientAddress2">
                    <label for="clientPhone">Phone:</label>
                    <input type="text" name="clientPhone" id="clientPhone">
                    <label for="clientEmail">Email:</label>
                    <input type="email" name="clientEmail" id="clientEmail" required>
                    <label for="clientNotes">Notes:</label>
                    <textarea name="clientNotes" id="clientNotes"></textarea>
                    <button type="submit" name="addClient">Add Client</button>
                </form>
                
                <!-- CSV IMPORTING::::::::::::: -->
                <form method="post" action="adminControl.php" enctype="multipart/form-data">
                    <h3>Import Clients (CSV)</h3>
                    <label for="csvFile">CSV File:</label>
                    <input type="file" name="csvFile" id="csvFile" accept=".csv" required>
                    <button type="submit" name="importClients">Import Clients</button>
                </form>
                <?php if(isset($importMessage)): ?>
                    <p class="message"><?php echo htmlspecialchars($importMessage); ?></p>
                <?php endif; ?>
            </div>
        </section>
        

    </div>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
    </footer>
    
    <script>
    // ADD  A SHOW SPECIALTY FIELD FOR ENGINEERS WHEN A ROLE CHANGES
    document.getElementById('userRole').addEventListener('change', function(){
        if (this.value === 'engineer') {
            document.getElementById('specialityField').style.display = 'block';
        } else {
            document.getElementById('specialityField').style.display = 'none';
        }
    });
    </script>
    <script src="alerts.js"></script>
    <script src="darkmode.js"></script>
</body>
</html>

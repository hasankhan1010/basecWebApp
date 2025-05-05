<?php
// reminders.php - Service Reminders with site-wide popups
session_start();
require_once 'database.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// The table structure is now properly set up with all necessary columns:
// reminderID, serviceName, clientID, dueDate, isAnnual, reminderNotes, createdAt, isActive

// Get all clients for the dropdown
$clients = $conn->query("SELECT clientID, clientFirstName, clientLastName FROM Clients ORDER BY clientLastName, clientFirstName")->fetch_all(MYSQLI_ASSOC);

// JSON endpoint: list active reminders, newest first
if (isset($_GET['action']) && $_GET['action'] === 'list') {
    header('Content-Type: application/json');
    $stmt = $conn->prepare(
        "SELECT r.reminderID, r.serviceName, r.dueDate, r.isAnnual, r.reminderNotes, r.clientID, 
                CONCAT(c.clientFirstName, ' ', c.clientLastName) as clientName
         FROM annualreminders r
         LEFT JOIN Clients c ON r.clientID = c.clientID
         WHERE r.isActive = 1
         ORDER BY r.dueDate ASC"
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    echo json_encode($rows);
    exit;
}

// JSON endpoint: reschedule a reminder
if (isset($_GET['action']) && $_GET['action'] === 'reschedule' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['id']);
    $minutes = isset($_GET['minutes']) ? intval($_GET['minutes']) : 15;
    
    // Get the reminder details
    $stmt = $conn->prepare("SELECT * FROM annualreminders WHERE reminderID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $reminder = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($reminder) {
        // Calculate new due date (current time + minutes)
        $newDueDate = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));
        
        // Update the reminder
        $updateStmt = $conn->prepare("UPDATE annualreminders SET dueDate = ? WHERE reminderID = ?");
        $updateStmt->bind_param('si', $newDueDate, $id);
        $success = $updateStmt->execute();
        $updateStmt->close();
        
        echo json_encode(['success' => $success, 'newDueDate' => $newDueDate]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Reminder not found']);
    }
    exit;
}

// JSON endpoint: mark reminder as completed
if (isset($_GET['action']) && $_GET['action'] === 'complete' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['id']);
    
    // Update the reminder to inactive
    $updateStmt = $conn->prepare("UPDATE annualreminders SET isActive = 0 WHERE reminderID = ?");
    $updateStmt->bind_param('i', $id);
    $success = $updateStmt->execute();
    $updateStmt->close();
    
    echo json_encode(['success' => $success]);
    exit;
}

// JSON endpoint: reactivate a reminder
if (isset($_GET['action']) && $_GET['action'] === 'reactivate' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['id']);
    
    // Update the reminder to active
    $updateStmt = $conn->prepare("UPDATE annualreminders SET isActive = 1 WHERE reminderID = ?");
    $updateStmt->bind_param('i', $id);
    $success = $updateStmt->execute();
    $updateStmt->close();
    
    echo json_encode(['success' => $success]);
    exit;
}

// JSON endpoint: add sustainability metric reminder
if (isset($_GET['action']) && $_GET['action'] === 'addReminder' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }
    
    // Extract data with defaults
    $reminderType = $data['reminderType'] ?? 'sustainability';
    $reminderTitle = $data['reminderTitle'] ?? 'Sustainability Alert';
    $reminderContent = $data['reminderContent'] ?? '';
    $reminderSeverity = $data['reminderSeverity'] ?? 'warning';
    $reminderDate = $data['reminderDate'] ?? date('Y-m-d H:i:s');
    $isCompleted = $data['isCompleted'] ?? 0;
    
    // Insert into the reminders table
    $stmt = $conn->prepare(
        "INSERT INTO annualreminders
         (serviceName, dueDate, reminderNotes, isActive, isAnnual)
         VALUES (?, ?, ?, ?, 0)"
    );
    
    // Format the title to include severity
    $formattedTitle = $reminderTitle . ' - ' . ($reminderSeverity === 'critical' ? 'CRITICAL' : 'Warning');
    
    // Format the due date (current time)
    $dueDate = date('Y-m-d H:i:s');
    
    // Active status (opposite of isCompleted)
    $isActive = $isCompleted ? 0 : 1;
    
    $stmt->bind_param(
        'sssi',
        $formattedTitle,
        $dueDate,
        $reminderContent,
        $isActive
    );
    
    $success = $stmt->execute();
    $newId = $success ? $conn->insert_id : 0;
    $stmt->close();
    
    if ($success) {
        echo json_encode(['success' => true, 'id' => $newId]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to add reminder: ' . $conn->error]);
    }
    exit;
}

// Handle new reminder form
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !empty($_POST['serviceName'])
    && !empty($_POST['dueDate'])
) {
    $svc = trim($_POST['serviceName']);
    $due = $_POST['dueDate']; // ISO datetime-local
    $ann = isset($_POST['isAnnual']) ? 1 : 0;
    $notes = isset($_POST['reminderNotes']) ? trim($_POST['reminderNotes']) : null;
    $clientID = !empty($_POST['clientID']) ? intval($_POST['clientID']) : null;

    $ins = $conn->prepare(
        "INSERT INTO annualreminders
         (serviceName, clientID, dueDate, isAnnual, reminderNotes, isActive)
         VALUES (?, ?, ?, ?, ?, 1)"
    );
    $ins->bind_param('sissi', $svc, $clientID, $due, $ann, $notes);
    if ($ins->execute()) {
        $success = htmlspecialchars($svc) . ' scheduled for ' . htmlspecialchars($due) . '.';
        if ($ann) {
            $next = date('Y-m-d H:i:s', strtotime("$due +1 year -1 day"));
            $i2 = $conn->prepare(
                "INSERT INTO annualreminders
                 (serviceName, clientID, dueDate, isAnnual, reminderNotes, isActive)
                 VALUES (?, ?, ?, 1, ?, 1)"
            );
            $i2->bind_param('siss', $svc, $clientID, $next, $notes);
            $i2->execute();
            $i2->close();
        }
    }
    $ins->close();
}

// Handle pagination and filtering
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 10; // Show 10 reminders per page
$offset = ($page - 1) * $itemsPerPage;
$status = isset($_GET['status']) ? $_GET['status'] : 'active';

// Count total reminders for pagination
$countQuery = "SELECT COUNT(*) as total FROM annualreminders WHERE ";
if ($status === 'active') {
    $countQuery .= "isActive = 1";
} elseif ($status === 'completed') {
    $countQuery .= "isActive = 0";
} else { // 'all'
    $countQuery .= "1=1"; // Count all reminders
}

$totalItems = $conn->query($countQuery)->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch reminders for display with pagination
$query = "SELECT r.reminderID, r.serviceName, r.dueDate, r.isAnnual, r.reminderNotes, r.clientID,
        CONCAT(c.clientFirstName, ' ', c.clientLastName) as clientName,
        r.reminderNotes
 FROM annualreminders r
 LEFT JOIN Clients c ON r.clientID = c.clientID
 WHERE ";

if ($status === 'active') {
    $query .= "r.isActive = 1";
} elseif ($status === 'completed') {
    $query .= "r.isActive = 0";
} else { // 'all'
    $query .= "1=1"; // Show all reminders
}

$query .= " ORDER BY r.reminderID DESC LIMIT $offset, $itemsPerPage"; // Order by ID descending (most recent first)
$list = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="darkmode.js"></script>
  <title>Service Reminders</title>
  <link rel="stylesheet" href="portal.css">
  <link rel="stylesheet" href="reminders.css">
  <link rel="stylesheet" href="darkmode.css">
  <link rel="stylesheet" href="powerAlerts.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-left">
      <div class="dark-mode-toggle">
        <label class="toggle-switch">
          <input type="checkbox" id="darkModeToggle">
          <span class="slider"></span>
        </label>
        <span class="toggle-label">Dark Mode</span>
      </div>
      <a href="portal.php">Home</a>
      <a href="clientServices.php">Client Services</a>
      <a href="scheduleDiary.php">Schedule Diary</a>
      <a href="surveyDiary.php">Survey Diary</a>
      <a href="adminControl.php">Admin Control</a>
      <a href="feedback.php">Feedback</a>
      <a href="notifications.php">Map Routes</a>
      <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
      <a href="payments.php">Payments</a>
      <a href="reminders.php" class="active">Reminders</a>
    </div>
    <div class="nav-right">
      <a href="logout.php">Logout</a>
    </div>
  </nav>

  <div class="container">
    <h1>Service Reminders</h1>
    <?php if ($success): ?>
      <div class="notification"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" class="form-group">
      <div class="form-row">
        <div class="form-field">
          <label for="serviceName">Service Name</label>
          <input id="serviceName" type="text" name="serviceName" class="input-field"
                 placeholder="e.g. Annual HVAC Check" required>
        </div>
        
        <div class="form-field">
          <label for="clientID">Client</label>
          <select id="clientID" name="clientID" class="input-field">
            <option value="">-- Select Client (Optional) --</option>
            <?php foreach ($clients as $client): ?>
              <option value="<?= $client['clientID'] ?>">
                <?= htmlspecialchars($client['clientLastName'] . ', ' . $client['clientFirstName']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-field">
          <label for="dueDate">Due Date &amp; Time</label>
          <input id="dueDate" type="datetime-local" name="dueDate" class="input-field" required>
        </div>
        
        <div class="form-field toggle-container">
          <label for="isAnnual" class="toggle-label">Annual Service</label>
          <label class="switch">
            <input id="isAnnual" type="checkbox" name="isAnnual">
            <span class="slider round"></span>
          </label>
          <span class="toggle-text" id="annualStatus">No</span>
        </div>
      </div>
      
      <div class="form-row full-width">
        <div class="form-field">
          <label for="reminderNotes">Notes</label>
          <textarea id="reminderNotes" name="reminderNotes" class="input-field" 
                    placeholder="Add any additional notes about this reminder" rows="3"></textarea>
        </div>
      </div>
      
      <button type="submit" class="button">Add Reminder</button>
    </form>

    <!-- Filter controls -->
    <div class="filter-controls">
      <h3>Reminder History</h3>
      <div class="filter-options">
        <label>Status:</label>
        <select id="status-filter" onchange="window.location.href='reminders.php?status='+this.value">
          <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
          <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All</option>
        </select>
      </div>
    </div>

    <div class="table-responsive">
      <table class="reminders-table">
        <thead>
          <tr>
            <th>Service</th>
            <th>Client</th>
            <th>Due Date</th>
            <th>Type</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($list) > 0): ?>
            <?php foreach ($list as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['serviceName']) ?></td>
                <td>
                  <?php if (!empty($r['clientName'])): ?>
                    <?= htmlspecialchars($r['clientName']) ?>
                  <?php else: ?>
                    <span class="no-client">No Client</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="due-date"><?= date('d M Y', strtotime($r['dueDate'])) ?></span>
                  <span class="due-time"><?= date('H:i', strtotime($r['dueDate'])) ?></span>
                </td>
                <td>
                  <?php if ($r['isAnnual']): ?>
                    <span class="reminder-type annual">Annual</span>
                  <?php else: ?>
                    <span class="reminder-type custom">Custom</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($r['reminderNotes'])): ?>
                    <div class="notes-content"><?= nl2br(htmlspecialchars($r['reminderNotes'])) ?></div>
                  <?php else: ?>
                    <span class="no-notes">No notes</span>
                  <?php endif; ?>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="no-reminders">No reminders found. Add a reminder above.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination controls -->
    <div class="pagination-controls">
      <ul>
        <?php if ($page > 1): ?>
          <li><a href="reminders.php?page=<?= $page - 1 ?>">&laquo; Previous</a></li>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li><a href="reminders.php?page=<?= $i ?>" <?= $i === $page ? 'class="active"' : '' ?>><?= $i ?></a></li>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
          <li><a href="reminders.php?page=<?= $page + 1 ?>">Next &raquo;</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; <?= date('Y') ?> BA Security. All rights reserved.</p>
  </footer>

  <!-- Reminder notifications are now handled by alerts.js -->
  
  <script>
    // Toggle functionality for annual service switch
    document.addEventListener('DOMContentLoaded', function() {
      const annualToggle = document.getElementById('isAnnual');
      const annualStatus = document.getElementById('annualStatus');
      
      if (annualToggle && annualStatus) {
        // Set initial state
        annualStatus.textContent = annualToggle.checked ? 'Yes' : 'No';
        
        // Add event listener for changes
        annualToggle.addEventListener('change', function() {
          annualStatus.textContent = this.checked ? 'Yes' : 'No';
        });
        
        // Make sure the toggle is visible and working in dark mode
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (darkModeToggle) {
          darkModeToggle.addEventListener('change', function() {
            // Ensure toggle state is preserved when switching modes
            setTimeout(() => {
              annualStatus.textContent = annualToggle.checked ? 'Yes' : 'No';
            }, 100);
          });
        }
      }
    });
  </script>
  <script src="alerts.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  
  <script>
    // Handle action buttons
    document.addEventListener('DOMContentLoaded', function() {
      // Mark reminder as completed
      document.querySelectorAll('.btn-complete').forEach(btn => {
        btn.addEventListener('click', async function() {
          const id = this.getAttribute('data-id');
          if (confirm('Mark this reminder as completed?')) {
            try {
              const response = await fetch('reminders.php?action=complete&id=' + id, {
                method: 'POST'
              });
              const result = await response.json();
              if (result.success) {
                window.location.reload();
              }
            } catch (error) {
              console.error('Error completing reminder:', error);
            }
          }
        });
      });
      
      // Reschedule reminder
      document.querySelectorAll('.btn-reschedule').forEach(btn => {
        btn.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const minutes = prompt('Reschedule for how many minutes from now?', '15');
          if (minutes && !isNaN(minutes) && parseInt(minutes) > 0) {
            window.location.href = `reminders.php?action=reschedule&id=${id}&minutes=${minutes}`;
          }
        });
      });
      
      // Reactivate reminder
      document.querySelectorAll('.btn-reactivate').forEach(btn => {
        btn.addEventListener('click', async function() {
          const id = this.getAttribute('data-id');
          if (confirm('Reactivate this reminder?')) {
            try {
              const response = await fetch('reminders.php?action=reactivate&id=' + id, {
                method: 'POST'
              });
              const result = await response.json();
              if (result.success) {
                window.location.reload();
              }
            } catch (error) {
              console.error('Error reactivating reminder:', error);
            }
          }
        });
      });
    });
  </script>
  <?php include('includes/power-monitor.php'); ?>
</body>
</html>

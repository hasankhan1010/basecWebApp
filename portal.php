<?php
session_start();
include('database.php');

// ——————————————————————————————————————————————
// AJAX ENDPOINTS

// 1) Return logged-in user info as JSON
if (isset($_GET['action']) && $_GET['action'] === 'getUser') {
    header('Content-Type: application/json');
    if (!empty($_SESSION['user']) && !empty($_SESSION['role'])) {
        $user  = $_SESSION['user'];
        $role  = $_SESSION['role'];
        $map   = [
            'engineer' => ['U'=>'engineerUsername','F'=>'engineerFirstName','L'=>'engineerLastName'],
            'admin'    => ['U'=>'adminUsername',   'F'=>'adminFirstName',   'L'=>'adminLastName'],
            'manager'  => ['U'=>'managerUsername', 'F'=>'managerFirstName', 'L'=>'managerLastName'],
            'sales'    => ['U'=>'salesUsername',   'F'=>'salesFirstName',   'L'=>'salesLastName']
        ];
        $username = 'Unknown';
        $first    = '';
        $last     = '';
        if (isset($map[$role])) {
            $m = $map[$role];
            $username = $user[$m['U']] ?? 'Unknown';
            $first    = $user[$m['F']] ?? '';
            $last     = $user[$m['L']] ?? '';
        }
        echo json_encode([
            'username'  => $username,
            'firstName' => $first,
            'lastName'  => $last,
            'role'      => $role
        ]);
    } else {
        echo json_encode([]);
    }
    exit;
}

// 2) Persist a metric into the DB
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_GET['action']) && $_GET['action'] === 'addMetric'
) {
    header('Content-Type: application/json');
    
    // Support for both regular POST and beacon API
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error'=>'Invalid JSON']);
        exit;
    }
    
    // Check if the database table has the sustainabilityScore column
    $checkColumn = $conn->query("SHOW COLUMNS FROM sustainabilitymetrics LIKE 'sustainabilityScore'");
    
    if ($checkColumn->num_rows == 0) {
        // Add the column if it doesn't exist
        $conn->query("ALTER TABLE sustainabilitymetrics ADD COLUMN sustainabilityScore INT DEFAULT 0");
    }
    
    // Insert with the new sustainabilityScore field
    $stmt = $conn->prepare("
      INSERT INTO sustainabilitymetrics
        (energyUsageWh, carbonOffsetKg, waterUsageMl, performanceScore, sustainabilityScore, metricNotes, userAgent)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    // Get user agent for better tracking
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    // Default value for sustainability score if not provided
    $sustainabilityScore = $data['sustainabilityScore'] ?? 0;
    
    $stmt->bind_param(
        'dddsiss',
        $data['energyUsageWh'],
        $data['carbonOffsetKg'],
        $data['waterUsageMl'],
        $data['performanceScore'],
        $sustainabilityScore,
        $data['metricNotes'],
        $userAgent
    );
    
    if ($stmt->execute()) {
        echo json_encode(['status'=>'ok']);
    } else {
        http_response_code(500);
        echo json_encode(['error'=>'Insert failed: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// ——————————————————————————————————————————————
// Redirect unauthenticated users
if (empty($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>CRM System Portal</title>
  <link rel="stylesheet" href="portal.css">
  <link rel="stylesheet" href="darkmode.css">
  <link rel="stylesheet" href="portalMetrics.css">
  <script src="js/jquery.min.js"></script>
  <link rel="stylesheet" href="powerAlerts.css">
</head>
<body>
  <header>
    <h1>CRM System Portal</h1>
    <div class="greeting">
      <?php echo 'Hello ' . ucfirst(htmlspecialchars($_SESSION['role'])); ?>
    </div>
    <div id="userInfo"></div>
    <div class="theme-switch-wrapper">
      <label class="toggle-switch">
        <input type="checkbox" id="darkModeToggle">
        <span class="slider"></span>
        <span class="toggle-label">Dark Mode</span>
      </label>
    </div>
  </header>

  <main>
    <nav class="portal-nav">
      <button onclick="location.href='clientServices.php'">Client Services</button>
      <button onclick="location.href='scheduleDiary.php'">Schedule Diary</button>
      <button onclick="location.href='surveyDiary.php'">Survey Diary</button>
      <button onclick="location.href='adminControl.php'">Admin Control</button>
      <button onclick="location.href='feedback.php'">Feedback</button>
      <button onclick="location.href='notifications.php'">Map Routes</button>
      <button onclick="location.href='sustainabilityMetrics.php'">Sustainability Metrics</button>
      <button onclick="location.href='payments.php'">Payments</button>
      <button onclick="location.href='reminders.php'">Reminders</button>
      <button onclick="location.href='logout.php'">Logout</button>
    </nav>

    <!-- Real-time Metrics Dashboard -->
    <div class="portal-metrics"></div>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
  </footer>

  <!-- Scripts -->
  <script src="alerts.js"></script>
  <script src="darkmode.js"></script>
  <script src="portalMetrics.js"></script>
  <?php include('includes/power-monitor.php'); ?>
</body>
</html>

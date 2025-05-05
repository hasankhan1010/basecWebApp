<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include('database.php');

// SEARCH QUERY:::::
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
}

// Get filter parameters
$feedbackFilter = isset($_GET['feedbackFilter']) ? $_GET['feedbackFilter'] : 'all';

// QUERY ALL JOBS FROM SCHEDULE DIARY WITH FILTERING OPTIONS
// LEFT JOIN THE CLIENTS TO GET CLIENT DETAILS AND FEEDBACK DATA

$query = "SELECT sd.scheduleID, sd.scheduleJobType, sd.engineerID, sd.scheduleDate, 
                 sd.scheduleStartTime, sd.scheduleEndTime, sd.scheduleDetails, sd.scheduleStatus,
                 c.clientID, c.clientFirstName, c.clientLastName, c.clientEmail,
                 f.feedbackRating, f.feedbackComments, f.feedbackSentDateTime, f.feedbackRecievedDateTime
          FROM ScheduleDiary sd
          JOIN Clients c ON sd.clientID = c.clientID
          LEFT JOIN Feedback f ON sd.scheduleID = f.feedbackID";

// Start WHERE clause for filters
$query .= " WHERE 1=1"; // Always true condition for easier filter chaining

// Apply feedback filter if selected
if ($feedbackFilter === 'received') {
    $query .= " AND f.feedbackRating IS NOT NULL";
} elseif ($feedbackFilter === 'pending') {
    $query .= " AND f.feedbackRating IS NULL";
}

if ($search !== "") {
    // YOU CAN SEARCH BY CLIENT NAME, FEEDBACK ETC:::::::::
    $query .= " AND (c.clientFirstName LIKE '%$search%' 
                  OR c.clientLastName LIKE '%$search%' 
                  OR sd.scheduleJobType LIKE '%$search%' 
                  OR f.feedbackRating LIKE '%$search%' 
                  OR f.feedbackComments LIKE '%$search%')";
}
$query .= " ORDER BY sd.scheduleDate DESC";
$result = mysqli_query($conn, $query);
$entries = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $entries[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feedback</title>
  <link rel="stylesheet" href="feedback.css">
  <link rel="stylesheet" href="darkmode.css">
</head>
<body>
 
  <nav class="navbar">
    <div class="nav-left">
      <a href="portal.php">Home</a>
      <a href="clientServices.php">Client Services</a>
      <a href="scheduleDiary.php">Schedule Diary</a>
      <a href="surveyDiary.php">Survey Diary</a>
      <a href="adminControl.php">Admin Control</a>
      <a href="feedback.php" class="active">Feedback</a>
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
    <h1>Job Feedback</h1>
    <!--     !!!!!!!!!!!!!!!!!!!!!!!!!!!!!1                SEARCH BAR AND A REFRESH BUTTON -->
    <div class="search-refresh">
      <form method="get" action="feedback.php" class="search-form">
        <input type="text" name="search" placeholder="Search feedback..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if ($feedbackFilter !== 'all'): ?>
          <input type="hidden" name="feedbackFilter" value="<?php echo $feedbackFilter; ?>">
        <?php endif; ?>
      </form>
      
      <div class="filter-controls">
        <div class="filter-group">
          <span class="filter-label">Feedback Status:</span>
          <a href="?feedbackFilter=all<?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="filter-btn <?php echo $feedbackFilter === 'all' ? 'active' : ''; ?>">All</a>
          <a href="?feedbackFilter=received<?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="filter-btn <?php echo $feedbackFilter === 'received' ? 'active' : ''; ?>">Received</a>
          <a href="?feedbackFilter=pending<?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="filter-btn <?php echo $feedbackFilter === 'pending' ? 'active' : ''; ?>">Pending</a>
        </div>
        
        <a href="feedback.php" class="clear-filters-btn">Clear Filters</a>
      </div>
      
      <button onclick="location.reload()" class="refresh-btn">Refresh</button>
    </div>
    
    <!-- TABLE FOR JOB ENTRIES  -->
    <div class="feedback-list">
      <?php if (!empty($entries)): ?>
      <table>
        <thead>
          <tr>
            <th>Client ID</th>
            <th>Client Name</th>
            <th>Job Type</th>
            <th>Engineer ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Feedback</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($entries as $entry): 
            $clientName = htmlspecialchars($entry['clientFirstName'] . " " . $entry['clientLastName']);
            $jobType = htmlspecialchars($entry['scheduleJobType'] ?? '');
            $engineerID = htmlspecialchars($entry['engineerID'] ?? '');
            $date = htmlspecialchars($entry['scheduleDate'] ?? '');
            $time = htmlspecialchars(($entry['scheduleStartTime'] ?? '') . " - " . ($entry['scheduleEndTime'] ?? ''));
            $status = htmlspecialchars($entry['scheduleStatus'] ?? '');
            $feedback = "";
            if (!empty($entry['feedbackRating'])) {
                $feedback .= "Rating: " . htmlspecialchars($entry['feedbackRating']) . "<br>";
                $feedback .= "Comments: " . htmlspecialchars($entry['feedbackComments'] ?? '');
            } else {
                $feedback = "No feedback";
            }
            // IF NO FEEDBACK ECISTS, MAILTO THE SURVEY
            $action = "";
            if (empty($entry['feedbackRating'])) {
                $subject = rawurlencode("BASecurity Feedback Survey");
                // Use localhost URL instead of Azure for local testing
                $localUrl = "http://localhost/basec%20crm/feedbackSurvey.php?jobID=" . $entry['scheduleID'];
                $body = rawurlencode("Dear " . $clientName . ",\n\nPlease provide your feedback for your recent job (" . $jobType . ") by visiting the link below:\n\n" . $localUrl . "\n\nThank you!");
                $mailto = "mailto:" . htmlspecialchars($entry['clientEmail']) . "?subject={$subject}&body={$body}";
                $action = "<a href='{$mailto}' class='email-link'>Send Survey</a>";
            } else {
                $action = "<span class='feedback-complete'>Feedback Received</span>";
            }
          ?>
          <tr>
            <td><?php echo htmlspecialchars($entry['clientID']); ?></td>
            <td><?php echo $clientName; ?></td>
            <td><?php echo $jobType; ?></td>
            <td><?php echo $engineerID; ?></td>
            <td><?php echo $date; ?></td>
            <td><?php echo $time; ?></td>
            <td><?php echo $status; ?></td>
            <td><?php echo $feedback; ?></td>
            <td><?php echo $action; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p>No job entries found.</p>
      <?php endif; ?>
    </div>
  </div>
  
  <footer>
    <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
  </footer>
  <script src="alerts.js"></script>
  <script src="darkmode.js"></script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}
include('database.php');

// SEARCH QUERY:::::
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
}

// ONLY QUERY THE RECENT JOBS WITH 'COMPLETE' OR 'COMPLETED'
// LEFT JOIN THE CLIENTS TO GET CLIENT DETAILS - NEEDS TO BE DONE VIA FEEDBACK TABLE ON SCHEDULEID = FEEDBACKID

$query = "SELECT sd.scheduleID, sd.scheduleJobType, sd.engineerID, sd.scheduleDate, 
                 sd.scheduleStartTime, sd.scheduleEndTime, sd.scheduleDetails, sd.scheduleStatus,
                 c.clientFirstName, c.clientLastName, c.clientEmail,
                 f.feedbackRating, f.feedbackComments, f.feedbackSentDateTime, f.feedbackRecievedDateTime
          FROM ScheduleDiary sd
          JOIN Clients c ON sd.clientID = c.clientID
          LEFT JOIN Feedback f ON sd.scheduleID = f.feedbackID
          WHERE sd.scheduleStatus IN ('complete', 'completed')";

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
      <a href="notifications.php">Notifications</a>
      <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
      <a href="payments.php">Payments</a>
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
      </form>
      <button onclick="location.reload()">Refresh</button>
    </div>
    
    <!-- TABLE FOR JOB ENTRIES  -->
    <div class="feedback-list">
      <?php if (!empty($entries)): ?>
      <table>
        <thead>
          <tr>
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
                $body = rawurlencode("Dear " . $clientName . ",\n\nPlease provide your feedback for your recent job (" . $jobType . ") by visiting the link below:\n\nhttp://yourdomain.com/feedbackSurvey.php?jobID=" . $entry['scheduleID'] . "\n\nThank you!");
                $mailto = "mailto:" . htmlspecialchars($entry['clientEmail']) . "?subject={$subject}&body={$body}";
                $action = "<a href='{$mailto}' class='email-link'>Send Survey</a>";
            } else {
                $action = "<span class='feedback-complete'>Feedback Received</span>";
            }
          ?>
          <tr>
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
</body>
</html>

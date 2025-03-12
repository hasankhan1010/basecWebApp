<?php
session_start();

// Verify user is logged in; if not, redirect to home.php
if (!isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}

include('database.php');

// Determine week offset (0 = current week; negative for past, positive for future)
$weekOffset = isset($_GET['week']) ? intval($_GET['week']) : 0;

// Calculate Monday for the current week, then adjust by the offset
$monday = new DateTime();
$monday->modify('Monday this week');
if ($weekOffset !== 0) {
    $interval = new DateInterval('P' . (abs($weekOffset) * 7) . 'D');
    if ($weekOffset > 0) {
        $monday->add($interval);
    } else {
        $monday->sub($interval);
    }
}

// Calculate Sunday (6 days after Monday)
$sunday = clone $monday;
$sunday->modify('+6 days');

$startDate = $monday->format('Y-m-d');
$endDate   = $sunday->format('Y-m-d');

// Retrieve schedule diary entries for the week
$query = "SELECT * FROM ScheduleDiary WHERE scheduleDate BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {
    // Use composite key: scheduleDate and the first two digits of the start time (e.g., "2025-03-04_08")
    // Note: If your column name is stored with different case, adjust accordingly.
    $startTime = isset($row['ScheduleStartTime']) ? $row['ScheduleStartTime'] : $row['scheduleStartTime'];
    $key = $row['scheduleDate'] . '_' . substr($startTime, 0, 2);
    if (!isset($schedules[$key])) {
        $schedules[$key] = [];
    }
    $schedules[$key][] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Diary</title>
    <link rel="stylesheet" href="scheduleDiary.css">
    <script>
      // Wait for the DOM to load before attaching events
      document.addEventListener("DOMContentLoaded", function() {
          var modal = document.getElementById("slotModal");
          var modalClose = document.querySelector(".modal-close");

          // Attach click events to all timetable cells
          var cells = document.getElementsByClassName("timeslot");
          Array.from(cells).forEach(function(cell) {
              cell.addEventListener("click", function() {
                  var key = cell.getAttribute("data-key");
                  var data = scheduleData[key];
                  var content = "";
                  if (data && data.length > 0) {
                      data.forEach(function(entry) {
                          content += "<div class='entry'>";
                          content += "<p><strong>Engineer ID:</strong> " + entry.engineerID + "</p>";
                          content += "<p><strong>Client ID:</strong> " + entry.clientID + "</p>";
                          content += "<p><strong>Job Type:</strong> " + entry.scheduleJobType + "</p>";
                          content += "<p><strong>Status:</strong> " + entry.scheduleStatus + "</p>";
                          content += "<p><strong>Details:</strong> " + entry.scheduleDetails + "</p>";
                          content += "<p><strong>Notes:</strong> " + entry.scheduleNotes + "</p>";
                          content += "</div><hr>";
                      });
                  } else {
                      content = "<p>No schedule entry for this slot.</p>";
                  }
                  document.getElementById("modalBody").innerHTML = content;
                  
                  // Set the Edit button URL with the date and hour from the key
                  var parts = key.split("_");
                  var datePart = parts[0];
                  var hourPart = parts[1];
                  document.getElementById("editButton").href = "scheduleDiaryAdd.php?date=" + encodeURIComponent(datePart) + "&hour=" + encodeURIComponent(hourPart);
                  
                  modal.style.display = "block";
              });
          });

          // Close modal when the close icon is clicked
          modalClose.onclick = function() {
              modal.style.display = "none";
          };

          // Close modal if user clicks outside the modal content
          window.onclick = function(event) {
              if (event.target == modal) {
                  modal.style.display = "none";
              }
          };
      });
    </script>
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
    
    <div class="schedule-diary-container">
        <header>
            <h1>Schedule Diary</h1>
            <div class="week-navigation">
                <a href="scheduleDiary.php?week=<?php echo $weekOffset - 1; ?>" class="nav-link">Previous Week</a>
                <span class="week-label">
                    <?php echo $monday->format('M d, Y'); ?> - <?php echo $sunday->format('M d, Y'); ?>
                </span>
                <a href="scheduleDiary.php?week=<?php echo $weekOffset + 1; ?>" class="nav-link">Next Week</a>
            </div>
        </header>
        
        <!-- Weekly Timetable -->
        <table class="timetable">
            <thead>
                <tr>
                    <th>Time</th>
                    <?php
                        // Build table headers for Monday to Sunday
                        $days = [];
                        for ($i = 0; $i < 7; $i++) {
                            $day = clone $monday;
                            $day->modify("+$i days");
                            $days[] = $day;
                            echo "<th>" . $day->format('l<br>M d') . "</th>";
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Create one-hour slots from 8 AM to 6 PM
                    for ($hour = 8; $hour < 18; $hour++) {
                        echo "<tr>";
                        echo "<td>" . sprintf("%02d:00 - %02d:00", $hour, $hour + 1) . "</td>";
                        foreach ($days as $day) {
                            $date = $day->format('Y-m-d');
                            $key = $date . '_' . sprintf("%02d", $hour);
                            echo "<td class='timeslot' data-key='$key'>";
                            if (isset($schedules[$key])) {
                                foreach ($schedules[$key] as $schedule) {
                                    // Truncate schedule details to 50 characters for a clean preview
                                    $details = htmlspecialchars($schedule['scheduleDetails']);
                                    if (strlen($details) > 50) {
                                        $details = substr($details, 0, 50) . '...';
                                    }
                                    echo "<div class='schedule-entry'>";
                                    echo "<strong>" . htmlspecialchars($schedule['scheduleJobType']) . "</strong><br>";
                                    echo $details . "<br>";
                                    echo "<em>" . htmlspecialchars($schedule['scheduleStatus']) . "</em>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<div class='empty-slot'>&nbsp;</div>";
                            }
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Modal Pop-up for Schedule Slot Details -->
    <div id="slotModal" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2>Schedule Details</h2>
            <div id="modalBody">
                <!-- Details will be dynamically inserted here -->
            </div>
            <a id="editButton" class="edit-btn" href="scheduleDiaryAdd.php">Edit / Add Entry</a>
        </div>
    </div>
    
    <!-- Pass PHP schedule data to JavaScript -->
    <script>
      var scheduleData = <?php echo json_encode($schedules); ?>;
    </script>
</body>
</html>

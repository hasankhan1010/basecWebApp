<?php
session_start();

// Verify a user is logged in; otherwise, redirect to home.php
if (!isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}

include('database.php');

// Determine week offset (0 = current week); allows viewing past and future weeks.
$weekOffset = isset($_GET['week']) ? intval($_GET['week']) : 0;

// Calculate Monday for the current week, then adjust by offset.
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

// Retrieve survey diary entries for the week using a prepared statement
$query = "SELECT * FROM SurveyDiary WHERE surveyDate BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

$surveys = [];
while ($row = $result->fetch_assoc()) {
    // Composite key: date_hour (e.g., "2025-03-04_08")
    $key = $row['surveyDate'] . '_' . substr($row['surveyTime'], 0, 2);
    if (!isset($surveys[$key])) {
        $surveys[$key] = [];
    }
    $surveys[$key][] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Survey Diary</title>
    <link rel="stylesheet" href="surveyDiary.css">
    <script>
      // Wait for the DOM to load before attaching events
      document.addEventListener("DOMContentLoaded", function() {
          var modal = document.getElementById("slotModal");
          var modalClose = document.querySelector(".modal-close");

          // Attach click event to every timeslot cell
          var cells = document.getElementsByClassName("timeslot");
          Array.from(cells).forEach(function(cell) {
              cell.addEventListener("click", function() {
                  var key = cell.getAttribute("data-key");
                  var data = surveyData[key];
                  var content = "";
                  if (data && data.length > 0) {
                      data.forEach(function(entry) {
                          content += "<div class='entry'>";
                          content += "<p><strong>Created By:</strong> " + entry.surveyCreatedByID + "</p>";
                          content += "<p><strong>Notes:</strong> " + entry.surveyNotes + "</p>";
                          content += "<p><strong>Status:</strong> " + entry.surveyStatus + "</p>";
                          content += "</div><hr>";
                      });
                  } else {
                      content = "<p>No survey entry for this slot.</p>";
                  }
                  document.getElementById("modalBody").innerHTML = content;
                  
                  // Set the Edit button URL with the date and hour from the key
                  var parts = key.split("_");
                  var datePart = parts[0];
                  var hourPart = parts[1];
                  document.getElementById("editButton").href = "surveyDiaryAdd.php?date=" + encodeURIComponent(datePart) + "&hour=" + encodeURIComponent(hourPart);
                  
                  modal.style.display = "block";
              });
          });

          // Close the modal when the close icon is clicked
          modalClose.onclick = function() {
              modal.style.display = "none";
          };

          // Close modal when clicking outside the modal content
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
            <a href="scheduleDiary.php">Schedule Diary</a>
            <a href="surveyDiary.php" class="active">Survey Diary</a>
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
    
    <div class="survey-diary-container">
        <header>
            <h1>Survey Diary</h1>
            <div class="week-navigation">
                <a href="surveyDiary.php?week=<?php echo $weekOffset - 1; ?>" class="nav-link">Previous Week</a>
                <span class="week-label">
                    <?php echo $monday->format('M d, Y'); ?> - <?php echo $sunday->format('M d, Y'); ?>
                </span>
                <a href="surveyDiary.php?week=<?php echo $weekOffset + 1; ?>" class="nav-link">Next Week</a>
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
                            if (isset($surveys[$key])) {
                                foreach ($surveys[$key] as $survey) {
                                    // Truncate notes to 50 characters for presentation
                                    $notes = htmlspecialchars($survey['surveyNotes']);
                                    if (strlen($notes) > 50) {
                                        $notes = substr($notes, 0, 50) . '...';
                                    }
                                    echo "<div class='survey-entry'>";
                                    echo "<strong>" . htmlspecialchars($survey['surveyCreatedByID']) . "</strong><br>";
                                    echo $notes . "<br>";
                                    echo "<em>" . htmlspecialchars($survey['surveyStatus']) . "</em>";
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
    
    <!-- Modal Pop-up for Survey Slot Details -->
    <div id="slotModal" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2>Survey Details</h2>
            <div id="modalBody">
                <!-- Details will be dynamically inserted here -->
            </div>
            <a id="editButton" class="edit-btn" href="surveyDiaryAdd.php">Edit / Add Entry</a>
        </div>
    </div>
    
    <!-- Pass PHP survey data to JavaScript -->
    <script>
      var surveyData = <?php echo json_encode($surveys); ?>;
    </script>
</body>
</html>

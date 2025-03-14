<?php
session_start();

// USER VERIFY
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include('database.php');

// FIND OUT CURRENT WEEK - ALSO ALLOWING PAST AND FUTURE WEEKS 
$weekOffset = isset($_GET['week']) ? intval($_GET['week']) : 0;

// CALCULATE MONDAY - ADJUST IT THO AS OFFSETS EXIST
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

// 6DAYS AFTER MONDAY IS SUNDAY - CALCULATE USING THIS 
$sunday = clone $monday;
$sunday->modify('+6 days');

$startDate = $monday->format('Y-m-d');
$endDate   = $sunday->format('Y-m-d');

//  RETRIEVE THE ENTRIES, FOR THE WHOLE WEEK USING A PREPARED STATEMENT
$query = "SELECT * FROM surveydiary WHERE surveyDate BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

$surveys = [];
while ($row = $result->fetch_assoc()) {
    // COMPOSITE KEY: DATE_HOUR (LIKE 2205-01-01_08)
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
      // MUST WAIT FOR DOM TO LOAD BEFORE ATTACHING EVENTS 
      document.addEventListener("DOMContentLoaded", function() {
          var modal = document.getElementById("slotModal");
          var modalClose = document.querySelector(".modal-close");

          // A CLICK EVENT ATTACH FOR EVERY TIME SLOT
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
                  
                  // MAKE SURE EDIT BUTTON URL IS MATCHING WITH DATE 
                  var parts = key.split("_");
                  var datePart = parts[0];
                  var hourPart = parts[1];
                  document.getElementById("editButton").href = "surveyDiaryAdd.php?date=" + encodeURIComponent(datePart) + "&hour=" + encodeURIComponent(hourPart);
                  
                  modal.style.display = "block";
              });
          });

          // CLOSE MODAL WITH CLOSE ICON - MAKE SURE IT WORKS 
          modalClose.onclick = function() {
              modal.style.display = "none";
          };

          // ALSO CLOSE MODAL WHEN CLICKED OUTSIDE MODAL CONTENT 
          window.onclick = function(event) {
              if (event.target == modal) {
                  modal.style.display = "none";
              }
          };
      });
    </script>
</head>
<body>
    <!-- NAV BAR HAS BEEN MADE!!!!!!!!!!  -->
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
            <a href="logout.php">Logout</a>
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
        
        <!-- WEEKLY TIMETABLEEEEE -->
        <table class="timetable">
            <thead>
                <tr>
                    <th>Time</th>
                    <?php
                        // TABLE HEADERS - BUILD IT - MON-FRI 
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
                    // SLOT TIMES FROM 8AM-6PM 
                    for ($hour = 8; $hour < 18; $hour++) {
                        echo "<tr>";
                        echo "<td>" . sprintf("%02d:00 - %02d:00", $hour, $hour + 1) . "</td>";
                        foreach ($days as $day) {
                            $date = $day->format('Y-m-d');
                            $key = $date . '_' . sprintf("%02d", $hour);
                            echo "<td class='timeslot' data-key='$key'>";
                            if (isset($surveys[$key])) {
                                foreach ($surveys[$key] as $survey) {
                                    // PRESENTATION WISE - TRUNCATE THE NOTES TO 50CHAARCTERS - TRY 100 THEN 50
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
    
    <!-- MODAL POP-UP - SURVEY SLOT DETAILS -->
    <div id="slotModal" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2>Survey Details</h2>
            <div id="modalBody">
                <!-- DETAILS WILL DYNAMICALLY BE INSERTED -->
            </div>
            <a id="editButton" class="edit-btn" href="surveyDiaryAdd.php">Edit / Add Entry</a>
        </div>
    </div>
    
    <!-- PHP DATA SURVEY DATA TO JAVASCRIPT --> 
    <script>
      var surveyData = <?php echo json_encode($surveys); ?>;
    </script>
</body>
</html>

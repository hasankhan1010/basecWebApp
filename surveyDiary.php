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
    <link rel="stylesheet" href="darkmode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                          // Safely get values with fallbacks
                          var createdBy = entry.surveyCreatedByID || 'Unknown';
                          var notes = entry.surveyNotes || '';
                          var status = entry.surveyStatus || 'Pending';
                          var surveyId = entry.surveyID || '0';
                          
                          // Determine status class for visual indicator
                          var statusClass = '';
                          var statusLower = status.toLowerCase();
                          if (statusLower.includes('complete')) {
                              statusClass = 'status-complete';
                          } else if (statusLower.includes('progress')) {
                              statusClass = 'status-progress';
                          } else if (statusLower.includes('cancel')) {
                              statusClass = 'status-cancelled';
                          } else if (statusLower.includes('pending')) {
                              statusClass = 'status-pending';
                          }
                          
                          content += "<div class='entry " + statusClass + "'>";
                          content += "<div class='entry-header'>";
                          content += "<h3><i class='fas fa-clipboard-list'></i> Survey Entry</h3>";
                          content += "<span class='entry-status'>" + status + "</span>";
                          content += "</div>";
                          
                          content += "<div class='entry-details'>";
                          content += "<div class='detail-row'><span class='detail-label'>Created By:</span> <span class='detail-value'>" + createdBy + "</span></div>";
                          
                          if (notes && notes.trim() !== "") {
                              content += "<div class='detail-section'><span class='detail-label'>Notes:</span>";
                              content += "<div class='detail-text'>" + notes + "</div></div>";
                          }
                          content += "</div>";
                          
                          content += "<div class='entry-actions'>";
                          content += "<a href='surveyDiaryAdd.php?surveyID=" + surveyId + "' class='edit-entry-btn'><i class='fa fa-edit'></i> Edit</a>";
                          content += "<a href='deleteSurveyEntry.php?surveyID=" + surveyId + "' class='delete-entry-btn' onclick='return confirm(\"Are you sure you want to delete this entry?\");'><i class='fa fa-trash'></i> Delete</a>";
                          content += "</div>";
                          
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
            <a href="notifications.php">Map Routes</a>
            <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
            <a href="payments.php">Payments</a>
            <a href="reminders.php">Reminders</a>
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
                                    // Safely get values with error checking
                                    $notes = isset($survey['surveyNotes']) ? $survey['surveyNotes'] : '';
                                    $notes = htmlspecialchars($notes);
                                    if (strlen($notes) > 40) {
                                        $notes = substr($notes, 0, 40) . '...';
                                    }
                                    
                                    // Safely get survey status
                                    $surveyStatus = isset($survey['surveyStatus']) ? $survey['surveyStatus'] : 'Pending';
                                    $createdBy = isset($survey['surveyCreatedByID']) ? $survey['surveyCreatedByID'] : 'Unknown';
                                    
                                    // Determine status class for visual indicator
                                    $statusClass = '';
                                    $status = strtolower($surveyStatus);
                                    if (strpos($status, 'complete') !== false) {
                                        $statusClass = 'status-complete';
                                    } elseif (strpos($status, 'progress') !== false) {
                                        $statusClass = 'status-progress';
                                    } elseif (strpos($status, 'cancel') !== false) {
                                        $statusClass = 'status-cancelled';
                                    } elseif (strpos($status, 'pending') !== false) {
                                        $statusClass = 'status-pending';
                                    }
                                    
                                    echo "<div class='survey-entry $statusClass'>";
                                    echo "<div class='entry-title'><i class='fas fa-clipboard-list'></i> <strong>" . htmlspecialchars($createdBy) . "</strong></div>";
                                    echo "<div class='entry-details'>" . $notes . "</div>";
                                    echo "<div class='entry-status'><span>" . htmlspecialchars($surveyStatus) . "</span></div>";
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
        <script src="alerts.js"></script>
        <script src="darkmode.js"></script>
</body>
</html>

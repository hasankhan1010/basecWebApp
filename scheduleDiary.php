<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include('database.php');


$weekOffset = isset($_GET['week']) ? intval($_GET['week']) : 0;


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


$sunday = clone $monday;
$sunday->modify('+6 days');

$startDate = $monday->format('Y-m-d');
$endDate   = $sunday->format('Y-m-d');


// ALL THE ABOVE IS BASICALLY THE SAME FROM THE SURVEY DIARY !!!!!!!!!!!!!!!!!!!!11
!

// Check if Engineers table exists

$engineersTableExists = false;
$clientsTableExists = false;

$tablesResult = $conn->query("SHOW TABLES");
while ($row = $tablesResult->fetch_array()) {
    if ($row[0] === 'Engineers') {
        $engineersTableExists = true;
    }
    if ($row[0] === 'Clients') {
        $clientsTableExists = true;
    }
}

// RETRIEVE THE ENTRIES WITH ENGINEER AND CLIENT NAMES IF TABLES EXIST
!
if ($engineersTableExists && $clientsTableExists) {
    $query = "SELECT sd.*, 
              e.engineerFirstName, e.engineerLastName,
              c.clientFirstName, c.clientLastName
              FROM ScheduleDiary sd
              LEFT JOIN Engineers e ON sd.engineerID = e.engineerID
              LEFT JOIN Clients c ON sd.clientID = c.clientID
              WHERE sd.scheduleDate BETWEEN ? AND ?";
} else {
    // Fallback query if tables don't exist
!
    $query = "SELECT * FROM ScheduleDiary WHERE scheduleDate BETWEEN ? AND ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {

    // IF COLUMN NAME STORED WITH A DIFF CASE, ADJUST IT ACCORDINGLU - USER MAUAL!!!!!!
!
    $startTime = isset($row['ScheduleStartTime']) ? $row['ScheduleStartTime'] : $row['scheduleStartTime'];
    $endTime = isset($row['ScheduleEndTime']) ? $row['ScheduleEndTime'] : $row['scheduleEndTime'];
    
    // Extract hours for duration calculation
!
    $startHour = (int)substr($startTime, 0, 2);
    $endHour = (int)substr($endTime, 0, 2);
    
    // Handle case where end time is on the same day but later hour
!
    if ($endHour <= $startHour) {
        $endHour = $startHour + 1; // Default to 1 hour if end time is invalid
!
    }
    
    // Add the entry to all hour slots it spans
!
    for ($hour = $startHour; $hour < $endHour; $hour++) {
        $key = $row['scheduleDate'] . '_' . sprintf("%02d", $hour);
        if (!isset($schedules[$key])) {
            $schedules[$key] = [];
        }
        $schedules[$key][] = $row;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Diary</title>
    <link rel="stylesheet" href="scheduleDiary.css">
    <link rel="stylesheet" href="darkmode.css">
    <link rel="stylesheet" href="https:// cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
! - love this part
    <script>
      
      document.addEventListener("DOMContentLoaded", function() {
          var modal = document.getElementById("slotModal");
          var modalClose = document.querySelector(".modal-close");

          
          var cells = document.getElementsByClassName("timeslot");
          Array.from(cells).forEach(function(cell) {
              cell.addEventListener("click", function() {
                  var key = cell.getAttribute("data-key");
                  var data = scheduleData[key];
                  var content = "";
                  if (data && data.length > 0) {
                      data.forEach(function(entry) {
                          // Safely get values with fallbacks
!
                          var jobType = entry.scheduleJobType || 'Task';
                          var status = entry.scheduleStatus || 'Unknown';
                          var engineerId = entry.engineerID || '0';
                          var clientId = entry.clientID || '0';
                          var startTime = entry.scheduleStartTime || '00:00:00';
                          var endTime = entry.scheduleEndTime || '00:00:00';
                          var details = entry.scheduleDetails || '';
                          var notes = entry.scheduleNotes || '';
                          var scheduleId = entry.scheduleID || '0';
                          
                          content += "<div class='entry'>";
                          content += "<div class='entry-header'>";
                          content += "<h3>" + jobType + "</h3>";
                          content += "<span class='entry-status'>" + status + "</span>";
                          content += "</div>";
                          
                          content += "<div class='entry-details'>";
                          // Get engineer name if available, otherwise show ID
!
                          var engineerName = "";
                          if (entry.engineerFirstName && entry.engineerLastName) {
                              engineerName = entry.engineerFirstName + " " + entry.engineerLastName;
                          } else {
                              engineerName = "Engineer ID: " + engineerId;
                          }
                          
                          // Get client name if available, otherwise show ID
!
                          var clientName = "";
                          if (entry.clientFirstName && entry.clientLastName) {
                              clientName = entry.clientFirstName + " " + entry.clientLastName;
                          } else {
                              clientName = "Client ID: " + clientId;
                          }
                          
                          content += "<div class='detail-row'><span class='detail-label'>Engineer:</span> <span class='detail-value'>" + engineerName + "</span></div>";
                          content += "<div class='detail-row'><span class='detail-label'>Client:</span> <span class='detail-value'>" + clientName + "</span></div>";
                          content += "<div class='detail-row'><span class='detail-label'>Time:</span> <span class='detail-value'>" + startTime.substring(0, 5) + " - " + endTime.substring(0, 5) + "</span></div>";
                          
                          if (details && details.trim() !== "") {
                              content += "<div class='detail-section'><span class='detail-label'>Details:</span>";
                              content += "<div class='detail-text'>" + details + "</div></div>";
                          }
                          
                          if (notes && notes.trim() !== "") {
                              content += "<div class='detail-section'><span class='detail-label'>Notes:</span>";
                              content += "<div class='detail-text'>" + notes + "</div></div>";
                          }
                          content += "</div>";
                          
                          content += "<div class='entry-actions'>";
                          content += "<a href='scheduleDiaryAdd.php?scheduleID=" + scheduleId + "' class='edit-entry-btn'><i class='fa fa-edit'></i> Edit</a>";
                          content += "<a href='deleteScheduleEntry.php?scheduleID=" + scheduleId + "' class='delete-entry-btn' onclick='return confirm(\"Are you sure you want to delete this entry?\");'><i class='fa fa-trash'></i> Delete</a>";
                          content += "</div>";
                          
                          content += "</div><hr>";
                      });
                  } else {
                      content = "<p>No schedule entry for this slot.</p>";
                  }
                  document.getElementById("modalBody").innerHTML = content;
                  
                  
                  var parts = key.split("_");
                  var datePart = parts[0];
                  var hourPart = parts[1];
                  // Update the Add New Entry button
!
                  var addButton = document.getElementById("editButton");
                  if (data && data.length > 0) {
                      // If there are entries, change the button text to indicate it will create a new entry
! - love this part
                      addButton.textContent = "Add New Entry";
                  } else {
                      // If no entries, it's a simple add
!
                      addButton.textContent = "Add Entry";
                  }
                  addButton.href = "scheduleDiaryAdd.php?date=" + encodeURIComponent(datePart) + "&hour=" + encodeURIComponent(hourPart);
                  
                  modal.style.display = "block";
              });
          });

          
          modalClose.onclick = function() {
              modal.style.display = "none";
          };

         
          window.onclick = function(event) {
              if (event.target == modal) {
                  modal.style.display = "none";
              }
          };
      });
    </script>
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
        
        
        <table class="timetable">
            <thead>
                <tr>
                    <th>Time</th>
                    <?php
                        
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
                   
                    for ($hour = 8; $hour < 18; $hour++) {
                        echo "<tr>";
                        echo "<td>" . sprintf("%02d:00", $hour) . "</td>";
                        foreach ($days as $day) {
                            $date = $day->format('Y-m-d');
                            $key = $date . '_' . sprintf("%02d", $hour);
                            echo "<td class='timeslot' data-key='$key'>";
                            if (isset($schedules[$key])) {
                                foreach ($schedules[$key] as $schedule) {
                                    // Safely get schedule details with error checking
!
                                    $details = isset($schedule['scheduleDetails']) ? $schedule['scheduleDetails'] : '';
                                    $details = htmlspecialchars($details);
                                    if (strlen($details) > 40) {
                                        $details = substr($details, 0, 40) . '...';
                                    }
                                    
                                    // Safely get schedule status
!
                                    $scheduleStatus = isset($schedule['scheduleStatus']) ? $schedule['scheduleStatus'] : 'Unknown';
                                    
                                    // Determine status class for visual indicator
! - love this part
                                    $statusClass = '';
                                    $status = strtolower($scheduleStatus);
                                    if (strpos($status, 'complete') !== false) {
                                        $statusClass = 'status-complete';
                                    } elseif (strpos($status, 'progress') !== false) {
                                        $statusClass = 'status-progress';
                                    } elseif (strpos($status, 'cancel') !== false) {
                                        $statusClass = 'status-cancelled';
                                    } elseif (strpos($status, 'pending') !== false) {
                                        $statusClass = 'status-pending';
                                    }
                                    
                                    // Safely get job type
!
                                    $scheduleJobType = isset($schedule['scheduleJobType']) ? $schedule['scheduleJobType'] : 'Task';
                                    
                                    // Determine icon based on job type
! - omg
                                    $icon = 'fa-tools';
                                    $jobType = strtolower($scheduleJobType);
                                    if (strpos($jobType, 'annual') !== false) {
                                        $icon = 'fa-calendar-check';
                                    } elseif (strpos($jobType, 'install') !== false) {
                                        $icon = 'fa-wrench';
                                    } elseif (strpos($jobType, 'repair') !== false) {
                                        $icon = 'fa-tools';
                                    } elseif (strpos($jobType, 'survey') !== false) {
                                        $icon = 'fa-clipboard-list';
                                    } elseif (strpos($jobType, 'maintenance') !== false) {
                                        $icon = 'fa-cogs';
                                    }
                                    
                                    // Safely get time values
!
                                    $startTime = isset($schedule['scheduleStartTime']) ? substr($schedule['scheduleStartTime'], 0, 5) : '00:00';
                                    $endTime = isset($schedule['scheduleEndTime']) ? substr($schedule['scheduleEndTime'], 0, 5) : '00:00';
                                    
                                    echo "<div class='schedule-entry $statusClass'>";
                                    echo "<div class='entry-title'><i class='fas $icon'></i> <strong>" . htmlspecialchars($scheduleJobType) . "</strong></div>";
                                    echo "<div class='entry-time'>" . $startTime . " - " . $endTime . "</div>";
                                    echo "<div class='entry-details'>" . $details . "</div>";
                                    echo "<div class='entry-status'><span>" . htmlspecialchars($scheduleStatus) . "</span></div>";
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
    
    
    <div id="slotModal" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2>Schedule Details</h2>
            <div id="modalBody">
                <!-- DETAILS DYNAMICALLY INSERTED - HERE  -->
            </div>
            <a id="editButton" class="edit-btn" href="scheduleDiaryAdd.php">Edit / Add Entry</a>
        </div>
    </div>
    
    <footer> 
    <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p> 
  </footer>
    <script>
      var scheduleData = <?php echo json_encode($schedules); ?>;
    </script>
        <script src="alerts.js"></script>
        <script src="darkmode.js"></script>
</body>
</html>

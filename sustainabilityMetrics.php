<?php
session_start();
// Optional: if you require user login to view metrics, uncomment:
// if (!isset($_SESSION['user'])) {
//     header("Location: home.php");
//     exit();
// }

include('database.php');

/*
  CREATE TABLE SustainabilityMetrics (
    metricID INT AUTO_INCREMENT PRIMARY KEY,
    energyUsage DOUBLE,
    carbonOffset DOUBLE,
    metricMeasurementDateTime DATETIME DEFAULT CURRENT_TIMESTAMP,
    metricNotes VARCHAR(250)
  ) ENGINE=InnoDB;
*/

// If you want to process a new metric insertion via this page (optional):
if (isset($_GET['action']) && $_GET['action'] === 'addMetric') {
    // Example: Insert a random row (simulate data from sensors).
    $energyUsage = rand(1, 100) + (rand(0,99)/100.0);
    $carbonOffset = rand(1, 10) + (rand(0,99)/100.0);
    $notes = "Auto-generated sample data";
    $stmt = $conn->prepare("INSERT INTO SustainabilityMetrics (energyUsage, carbonOffset, metricNotes) VALUES (?, ?, ?)");
    $stmt->bind_param("dds", $energyUsage, $carbonOffset, $notes);
    $stmt->execute();
    $stmt->close();
    exit("Metric added");
}

// If an AJAX request to fetch metrics data:
if (isset($_GET['action']) && $_GET['action'] === 'fetchData') {
    header('Content-Type: application/json');

    // 1. Retrieve the most recent metrics (e.g., last 20 rows).
    $latestQuery = "SELECT * FROM SustainabilityMetrics ORDER BY metricMeasurementDateTime DESC LIMIT 20";
    $latestResult = mysqli_query($conn, $latestQuery);
    $latestMetrics = [];
    while ($row = mysqli_fetch_assoc($latestResult)) {
        $latestMetrics[] = $row;
    }

    // 2. Calculate hourly average (last 60 minutes).
    $hourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
    $hourQuery = "SELECT AVG(energyUsage) AS avgEnergy, AVG(carbonOffset) AS avgCarbon
                  FROM SustainabilityMetrics
                  WHERE metricMeasurementDateTime >= '$hourAgo'";
    $hourResult = mysqli_query($conn, $hourQuery);
    $hourData = mysqli_fetch_assoc($hourResult);

    // 3. Calculate weekly average (last 7 days).
    $weekAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
    $weekQuery = "SELECT AVG(energyUsage) AS avgEnergy, AVG(carbonOffset) AS avgCarbon
                  FROM SustainabilityMetrics
                  WHERE metricMeasurementDateTime >= '$weekAgo'";
    $weekResult = mysqli_query($conn, $weekQuery);
    $weekData = mysqli_fetch_assoc($weekResult);

    // Return JSON
    echo json_encode([
        'latestMetrics' => $latestMetrics,
        'hourlyAvg' => [
            'energy' => $hourData['avgEnergy'] ?? 0,
            'carbon' => $hourData['avgCarbon'] ?? 0
        ],
        'weeklyAvg' => [
            'energy' => $weekData['avgEnergy'] ?? 0,
            'carbon' => $weekData['avgCarbon'] ?? 0
        ]
    ]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sustainability Metrics</title>
  <link rel="stylesheet" href="sustainabilityMetrics.css">
  <script src="js/jquery.min.js"></script>
  <script>
    // Periodically fetch data from the server and update the page
    function fetchMetrics() {
      $.ajax({
        url: 'sustainabilityMetrics.php',
        type: 'GET',
        data: { action: 'fetchData' },
        dataType: 'json',
        success: function(data) {
          // Update Hourly & Weekly Averages
          $('#hourlyEnergy').text( parseFloat(data.hourlyAvg.energy).toFixed(2) );
          $('#hourlyCarbon').text( parseFloat(data.hourlyAvg.carbon).toFixed(2) );
          $('#weeklyEnergy').text( parseFloat(data.weeklyAvg.energy).toFixed(2) );
          $('#weeklyCarbon').text( parseFloat(data.weeklyAvg.carbon).toFixed(2) );

          // Update Latest Metrics Table
          var tbody = $('#latestMetricsTable tbody');
          tbody.empty();
          data.latestMetrics.forEach(function(row) {
            var dateTime = row.metricMeasurementDateTime;
            var usage = parseFloat(row.energyUsage).toFixed(2);
            var offset = parseFloat(row.carbonOffset).toFixed(2);
            var notes = row.metricNotes ? row.metricNotes : '';
            var tr = '<tr>' +
                        '<td>' + dateTime + '</td>' +
                        '<td>' + usage + '</td>' +
                        '<td>' + offset + '</td>' +
                        '<td>' + notes + '</td>' +
                     '</tr>';
            tbody.append(tr);
          });
        },
        error: function() {
          console.log("Error fetching metrics data");
        }
      });
    }

    $(document).ready(function(){
      // Fetch initial data
      fetchMetrics();
      // Update every 30 seconds
      setInterval(fetchMetrics, 30000);

      // If you want to simulate adding new metric data, you can do it here
      // setInterval(function(){
      //   $.get('sustainabilityMetrics.php', { action: 'addMetric' });
      // }, 60000); // e.g. every 60 seconds
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
      <a href="surveyDiary.php">Survey Diary</a>
      <a href="adminControl.php">Admin Control</a>
      <a href="feedback.php">Feedback</a>
      <a href="notifications.php">Notifications</a>
      <a href="sustainabilityMetrics.php" class="active">Sustainability Metrics</a>
      <a href="payments.php">Payments</a>
    </div>
    <div class="nav-right">
      <a href="logout.php">Logout</a>
    </div>
  </nav>

  <div class="container">
    <h1>Sustainability Metrics</h1>
    
    <!-- Hourly & Weekly Averages -->
    <fieldset class="averages-fieldset">
      <legend>Real-Time Averages</legend>
      <div class="average-row">
        <h3>Hourly Average</h3>
        <p>Energy Usage: <span id="hourlyEnergy">0.00</span> kWh</p>
        <p>Carbon Offset: <span id="hourlyCarbon">0.00</span> kg CO<sub>2</sub></p>
      </div>
      <div class="average-row">
        <h3>Weekly Average</h3>
        <p>Energy Usage: <span id="weeklyEnergy">0.00</span> kWh</p>
        <p>Carbon Offset: <span id="weeklyCarbon">0.00</span> kg CO<sub>2</sub></p>
      </div>
    </fieldset>
    
    <!-- Latest Metrics Table -->
    <fieldset class="latest-metrics-fieldset">
      <legend>Latest Metrics</legend>
      <table id="latestMetricsTable">
        <thead>
          <tr>
            <th>Date / Time</th>
            <th>Energy Usage (kWh)</th>
            <th>Carbon Offset (kg CO2)</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <!-- Populated by AJAX -->
        </tbody>
      </table>
    </fieldset>
  </div>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
  </footer>
</body>
</html>

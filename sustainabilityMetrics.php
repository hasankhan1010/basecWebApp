<?php
session_start();
include('database.php');

/**
 * 
 * RETRIEVE THE WEBSITE METRICS 
 * NUMBER OF LOGGED IN USERS (FROM USERS TABLE)
 * AMOUNT OF DATA ON THE WEBSITE (FROM A CONTENT TABLE)
 * 
 */
function getWebsiteMetrics($conn) {
    // QUERY FOR LOGGED IN USERS 
    $activeUsersQuery = "SELECT COUNT(*) AS count FROM Users WHERE isLoggedIn = 1";
    $resultUsers = mysqli_query($conn, $activeUsersQuery);
    $rowUsers = mysqli_fetch_assoc($resultUsers);
    $activeUsers = (int)$rowUsers['count'];
    
    // QUERY FOR AMOUNT OF DATA 
    $dataQuery = "SELECT COUNT(*) AS count FROM Content";
    $resultData = mysqli_query($conn, $dataQuery);
    $rowData = mysqli_fetch_assoc($resultData);
    $dataCount = (int)$rowData['count'];
    
    return [
        'activeUsers' => $activeUsers,
        'dataCount' => $dataCount
    ];
}
/**
 * ESTIMATE ENERGY USAGE (IN KWH) BASED ON WEBSITE METRICS
 * ADJUST THE FACTORS AS NEEDED 
 */
function calculateEnergyUsageFromWebsiteMetrics($metrics) {
    $userFactor = 0.05; // KWH PER ACTIVE USER - AVERAGE ESTIMATES 
    $dataFactor = 0.001; // KWH PER CONTENT RECORD - AVERAGE ESTIMATE 
    return ($metrics['activeUsers'] * $userFactor) + ($metrics['dataCount'] * $dataFactor);
}
/**
 * ESTIMATE CARBON OFFSET BASED ON ENERGY USAGE 
 */
function calculateCarbonOffsetFromEnergyUsage($energyUsage) {
    $emissionFactor = 0.4; // KG CO2 PER KWH
    return $energyUsage * $emissionFactor;
}


// PROCESS METRIC INSERTION OR DATA RETRIEVAL BASED ON THE ACTION PARAMETER
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'addMetric') {
        // GET WEBSITE METRICS 
        $metrics = getWebsiteMetrics($conn);

        // CALCULATE ESTIMATED ENERGY USAGE AND CARBON OFFSET 
        $energyUsage = calculateEnergyUsageFromWebsiteMetrics($metrics);
        $carbonOffset = calculateCarbonOffsetFromEnergyUsage($energyUsage);
        $notes = "Estimated from website metrics: " . $metrics['activeUsers'] . " active users, " . $metrics['dataCount'] . " data records";
        
        // INSERT THE CALCULATED VALUES INTO THE DATABASE 
        $stmt = $conn->prepare("INSERT INTO SustainabilityMetrics (energyUsage, carbonOffset, metricNotes) VALUES (?, ?, ?)");
        $stmt->bind_param("dds", $energyUsage, $carbonOffset, $notes);
        $stmt->execute();
        $stmt->close();
        exit("Metric added");
    }

    if ($_GET['action'] === 'fetchData') {
        header('Content-Type: application/json');

        // RETRIEVE THE 20 MOST RECENT METRIC ENTRIES:::::::
        $latestQuery = "SELECT * FROM SustainabilityMetrics ORDER BY metricMeasurementDateTime DESC LIMIT 20";
        $latestResult = mysqli_query($conn, $latestQuery);
        $latestMetrics = [];
        while ($row = mysqli_fetch_assoc($latestResult)) {
            $latestMetrics[] = $row;
        }

        // CALCULATE HOURLY AVERAGE::::::::::::::
        $hourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $hourQuery = "SELECT AVG(energyUsage) AS avgEnergy, AVG(carbonOffset) AS avgCarbon
                      FROM SustainabilityMetrics
                      WHERE metricMeasurementDateTime >= '$hourAgo'";
        $hourResult = mysqli_query($conn, $hourQuery);
        $hourData = mysqli_fetch_assoc($hourResult);

        // CALCULATE WEEKLY AVERAGE:::::::
        $weekAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
        $weekQuery = "SELECT AVG(energyUsage) AS avgEnergy, AVG(carbonOffset) AS avgCarbon
                      FROM SustainabilityMetrics
                      WHERE metricMeasurementDateTime >= '$weekAgo'";
        $weekResult = mysqli_query($conn, $weekQuery);
        $weekData = mysqli_fetch_assoc($weekResult);

        // RETURN THE DATA IN JSON FORMAT 
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
 
    // PREIODICSALLY FETCH AND UPDATE METRICS ON THE PAGE 
    function fetchMetrics() {
      $.ajax({
        url: 'sustainabilityMetrics.php',
        type: 'GET',
        data: { action: 'fetchData' },
        dataType: 'json',
        success: function(data) {
          // UPDATE HOURLY AND WEEKLY AVERAGES 
          $('#hourlyEnergy').text(parseFloat(data.hourlyAvg.energy).toFixed(2));
          $('#hourlyCarbon').text(parseFloat(data.hourlyAvg.carbon).toFixed(2));
          $('#weeklyEnergy').text(parseFloat(data.weeklyAvg.energy).toFixed(2));
          $('#weeklyCarbon').text(parseFloat(data.weeklyAvg.carbon).toFixed(2));

          // UPDATE THE LATEST METRICS TABLE 
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

      // INITIAL FETCH AND THEN REFRESH EVERY 30 SECONDS 
      fetchMetrics();
      setInterval(fetchMetrics, 30000);
    });
  </script>
</head>
<body>
  
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
    
    <!-- REAL TIME AVERAGES  -->
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
    
    <!-- LATEST METRICS TABLE  -->
    <fieldset class="latest-metrics-fieldset">
      <legend>Latest Metrics</legend>
      <table id="latestMetricsTable">
        <thead>
          <tr>
            <th>Date / Time</th>
            <th>Energy Usage (kWh)</th>
            <th>Carbon Offset (kg CO₂)</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <!-- DATA POPULATED VIA AJAX  -->
        </tbody>
      </table>
    </fieldset>
  </div>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
  </footer>
</body>
</html>

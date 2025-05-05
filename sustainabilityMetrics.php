<?php
session_start();
include('database.php');

// ——————————————————————————————————————————————
// AJAX ENDPOINTS

// 1) Persist a metric into the DB
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_GET['action']) && $_GET['action'] === 'addMetric'
) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error'=>'Invalid JSON']);
        exit;
    }
    $stmt = $conn->prepare("
      INSERT INTO sustainabilitymetrics
        (energyUsageWh, carbonOffsetKg, waterUsageMl, performanceScore, metricNotes)
      VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        'dddss',
        $data['energyUsageWh'],
        $data['carbonOffsetKg'],
        $data['waterUsageMl'],
        $data['performanceScore'],
        $data['metricNotes']
    );
    if ($stmt->execute()) {
        echo json_encode(['status'=>'ok']);
    } else {
        http_response_code(500);
        echo json_encode(['error'=>'Insert failed']);
    }
    exit;
}

// 2) Fetch estimates & averages
if (isset($_GET['action']) && $_GET['action'] === 'fetchData') {
    header('Content-Type: application/json');

    // Check if sustainabilityScore column exists
    $checkColumn = $conn->query("SHOW COLUMNS FROM sustainabilitymetrics LIKE 'sustainabilityScore'");
    $hasSustainabilityScore = $checkColumn->num_rows > 0;

    // 1-minute average
    $minAgo = date('Y-m-d H:i:s', strtotime('-1 minute'));
    $stmt = $conn->prepare("
        SELECT
          AVG(energyUsageWh)    AS energy,
          AVG(carbonOffsetKg)   AS carbon,
          AVG(waterUsageMl)     AS water,
          AVG(performanceScore) AS performance" . 
          ($hasSustainabilityScore ? ",
          AVG(sustainabilityScore) AS sustainability" : "") . "
        FROM sustainabilitymetrics
        WHERE metricMeasurementDateTime >= ?
    ");
    $stmt->bind_param('s', $minAgo);
    $stmt->execute();
    $minData = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // 1-hour average
    $hourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
    $stmt = $conn->prepare("
        SELECT
          AVG(energyUsageWh)    AS energy,
          AVG(carbonOffsetKg)   AS carbon,
          AVG(waterUsageMl)     AS water,
          AVG(performanceScore) AS performance" . 
          ($hasSustainabilityScore ? ",
          AVG(sustainabilityScore) AS sustainability" : "") . "
        FROM sustainabilitymetrics
        WHERE metricMeasurementDateTime >= ?
    ");
    $stmt->bind_param('s', $hourAgo);
    $stmt->execute();
    $hourData = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Daily averages for past 7 days
    $stmt = $conn->prepare("
        SELECT
          DATE(metricMeasurementDateTime) AS date,
          AVG(energyUsageWh)    AS energy,
          AVG(carbonOffsetKg)   AS carbon,
          AVG(waterUsageMl)     AS water,
          AVG(performanceScore) AS performance" . 
          ($hasSustainabilityScore ? ",
          AVG(sustainabilityScore) AS sustainability" : "") . "
        FROM sustainabilitymetrics
        WHERE metricMeasurementDateTime >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(metricMeasurementDateTime)
        ORDER BY DATE(metricMeasurementDateTime) DESC
        LIMIT 7
    ");
    $stmt->execute();
    $dailyData = [];
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $dailyData[] = $row;
    }
    $stmt->close();

    // If no real data, generate low demo estimates
    if (empty($dailyData) || !$minData['energy']) {
        // fallback minute & hour
        $demoEnergy = 0.1;
        $demoCarbon = $demoEnergy * 0.0005;
        $demoWater  = $demoEnergy * 200;
        $demoPerf   = 85;
        $demoSustain = 75; // Default sustainability score
        
        $minData = [
            'energy' => $demoEnergy,
            'carbon' => $demoCarbon,
            'water' => $demoWater,
            'performance' => $demoPerf,
            'sustainability' => $demoSustain
        ];
        $hourData = $minData;
        
        // fallback 7-day series
        $dailyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $e = $demoEnergy + ($i * 0.02);
            $p = $demoPerf - ($i * 0.5); // Performance decreases slightly over time
            $s = $demoSustain + ($i * 1); // Sustainability increases over time
            
            $dailyData[] = [
                'date'        => $date,
                'energy'      => round($e,2),
                'carbon'      => round($e * 0.0005,3),
                'water'       => round($e * 200),
                'performance' => round($p),
                'sustainability' => round($s)
            ];
        }
    } else {
        // Ensure sustainability exists in all records
        if (!isset($minData['sustainability'])) {
            // Calculate a sustainability score based on other metrics
            $minData['sustainability'] = round((100 - $minData['energy'] * 20) * 0.6 + ($minData['performance'] * 0.5) * 0.4);
            $hourData['sustainability'] = round((100 - $hourData['energy'] * 20) * 0.6 + ($hourData['performance'] * 0.5) * 0.4);
            
            // Calculate for daily data too
            foreach ($dailyData as &$day) {
                if (!isset($day['sustainability'])) {
                    $day['sustainability'] = round((100 - $day['energy'] * 20) * 0.6 + ($day['performance'] * 0.5) * 0.4);
                }
            }
        }
    }

    echo json_encode([
        'minuteAvg' => [
            'energy'      => round($minData['energy'],2),
            'carbon'      => round($minData['carbon'],2),
            'water'       => round($minData['water'],0),
            'performance' => round($minData['performance']),
            'sustainability' => round($minData['sustainability'])
        ],
        'hourAvg' => [
            'energy'      => round($hourData['energy'],2),
            'carbon'      => round($hourData['carbon'],2),
            'water'       => round($hourData['water'],0),
            'performance' => round($hourData['performance']),
            'sustainability' => round($hourData['sustainability'])
        ],
        'dailyAvg' => $dailyData,
        'systemInfo' => [
            'hasSustainabilityScore' => $hasSustainabilityScore,
            'serverTime' => date('Y-m-d H:i:s'),
            'dataPoints' => count($dailyData)
        ]
    ]);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sustainability Dashboard</title>
  <link rel="stylesheet" href="sustainabilityMetrics.css">
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
      <a href="feedback.php">Feedback</a>
      <a href="notifications.php">Map Routes</a>
      <a href="sustainabilityMetrics.php" class="active">Sustainability Metrics</a>
      <a href="payments.php">Payments</a>
      <a href="reminders.php">Reminders</a>
    </div>
    <div class="nav-right">
      <a href="logout.php">Logout</a>
    </div>
  </nav>

  <div class="container">
    <h1>Sustainability Dashboard</h1>
    
    <div class="refresh-info">
      <span id="sessionInfo">Session duration: calculating...</span>
      <span>Last updated: <span id="lastRefreshTime">-</span></span>
    </div>
    
    <div id="trendVisualization" class="trend-visualization">
      <!-- Trend visualization will be inserted here by JS -->
    </div>
    
    <div class="spacer" style="height: 30px;"></div>

    <div class="averages">
      <div class="avg-card">
        <h3>Last Minute (Avg)</h3>
        <p>Energy: <span id="minEnergy">--</span> Wh</p>
        <p>Carbon: <span id="minCarbon">--</span> kg</p>
        <p>Water: <span id="minWater">--</span> mL</p>
        <p>Performance: <span id="minPerf">--</span></p>
        <p>Sustainability: <span id="minSustain">--</span></p>
      </div>
      <div class="avg-card">
        <h3>Last Hour (Avg)</h3>
        <p>Energy: <span id="hrEnergy">--</span> Wh</p>
        <p>Carbon: <span id="hrCarbon">--</span> kg</p>
        <p>Water: <span id="hrWater">--</span> mL</p>
        <p>Performance: <span id="hrPerf">--</span></p>
        <p>Sustainability: <span id="hrSustain">--</span></p>
      </div>
    </div>

    <h2>Daily Averages (Past 7 Days)</h2>
    <table class="daily-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Energy (Wh)</th>
          <th>Carbon (kg)</th>
          <th>Water (mL)</th>
          <th>Performance</th>
          <th>Sustainability</th>
        </tr>
      </thead>
      <tbody id="dailyBody">
      <!-- Table rows will be inserted here by JS -->
      </tbody>
    </table>
  </div>
  <script>
      var surveyData = <?php echo json_encode($surveys); ?>;
    </script>
  
<footer>
  <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
</footer>

        <script src="alerts.js"></script>
        <script src="darkmode.js"></script>
  <script src="sustainabilityMetrics.js"></script>
  <?php include('includes/power-monitor.php'); ?>
</body>
</html>

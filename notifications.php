<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Database connection
require_once 'database.php';
if (!($conn instanceof mysqli)) {
    die('<p style="color:red; text-align:center;">Database connection failed.</p>');
}

// 1) Determine date to display
$rawDate = $_GET['date'] ?? date('Y-m-d');
$dt = DateTime::createFromFormat('Y-m-d', $rawDate);
if (!($dt && $dt->format('Y-m-d') === $rawDate)) {
    $rawDate = date('Y-m-d');
}
$selectedDate = new DateTime($rawDate);
$prevDate = (clone $selectedDate)->modify('-1 day')->format('Y-m-d');
$nextDate = (clone $selectedDate)->modify('+1 day')->format('Y-m-d');
$displayDate = htmlspecialchars($selectedDate->format('l, F j, Y'), ENT_QUOTES);

// 2) Fetch today's jobs + client addresses
$jobs = [];
$sql = "
  SELECT sd.ScheduleStartTime, c.clientFirstName, c.clientLastName, c.clientAddress1, c.clientAddress2
    FROM schedulediary sd
    JOIN clients      c  ON sd.clientID = c.clientID
   WHERE sd.scheduleDate = ?
   ORDER BY sd.ScheduleStartTime
";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('s', $rawDate);
    $stmt->execute();
    $stmt->bind_result($time, $fn, $ln, $a1, $a2);
    while ($stmt->fetch()) {
        $addr = trim("$a1 $a2");
        if ($addr === '') continue;
        $jobs[] = ['label' => "$time – $fn $ln", 'address' => $addr];
    }
    $stmt->close();
} else {
    die('<p style="color:red; text-align:center;">Failed to prepare query.</p>');
}

// 3) Encode jobs for JavaScript
$jobsJson = json_encode($jobs, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?: '[]';
$apiKey = 'AIzaSyATOYFbuh6knVD6DmCPbpPwqxODprL9uCs';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notifications & Daily Route – <?= $displayDate ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Stylesheet -->
  <link rel="stylesheet" href="notifications.css">
  <link rel="stylesheet" href="darkmode.css">
  <!-- Inline map styles to ensure visibility -->
  <style>
    #route-container { display:flex; gap:1rem; flex-wrap:wrap; margin:1.5rem 0; }
    #map { flex:2; width:100%; height:400px; border:2px solid #ccc; border-radius:4px; }
    #directions-panel {
      flex:1; width:100%; height:400px;
      background:#fff; padding:1rem;
      border:2px solid #ccc; border-radius:4px;
      overflow-y:auto;
    }
  </style>

  <!-- Google Maps JS API -->
  <script
    src="https://maps.googleapis.com/maps/api/js?key=<?= $apiKey ?>&callback=initMap"
    async defer>
  </script>
  <script>
    'use strict';
    const office = '2A Reginald St, Luton LU2 7QZ';
    const jobs   = <?= $jobsJson ?>;

    function initMap() {
      const mapEl = document.getElementById('map');
      const panel = document.getElementById('directions-panel');
      const map = new google.maps.Map(mapEl, {
        center: { lat: 51.8825, lng: -0.4179 },
        zoom: 10
      });
      const service = new google.maps.DirectionsService();
      const renderer = new google.maps.DirectionsRenderer({ map, panel });

      if (!jobs.length) {
        panel.innerHTML = '<p>No jobs scheduled for this date.</p>';
        return;
      }

      const waypoints = jobs.map(j => ({
        location: j.address,
        stopover: true
      }));

      service.route({
        origin: office,
        destination: jobs[jobs.length - 1].address,
        waypoints,
        optimizeWaypoints: true,
        travelMode: 'DRIVING'
      }, (res, status) => {
        if (status === 'OK') {
          renderer.setDirections(res);
        } else {
          panel.innerHTML = `<p>Error loading route: ${status}</p>`;
        }
      });
    }

    // Re-init map when navigating back/forward
    window.addEventListener('pageshow', e => {
      if (e.persisted) initMap();
    });
  </script>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-left">
      <a href="portal.php">Home</a>
      <a href="clientServices.php">Client Services</a>
      <a href="scheduleDiary.php">Schedule Diary</a>
      <a href="surveyDiary.php">Survey Diary</a>
      <a href="adminControl.php">Admin Control</a>
      <a href="feedback.php">Feedback</a>
      <a href="notifications.php" class="active">Map Routes</a>
      <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
      <a href="payments.php">Payments</a>
      <a href="reminders.php">Reminders</a>
    </div>
    <div class="nav-right">
      <a href="logout.php">Logout</a>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container">
    <h1>Route for <?= $displayDate ?></h1>
    <div class="route-controls">
      <a href="notifications.php?date=<?= $prevDate ?>">&laquo; Previous</a>
      <a href="notifications.php?date=<?= $nextDate ?>">Next &raquo;</a>
    </div>
    <div id="route-container">
      <div id="map"></div>
      <div id="directions-panel"></div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <p>&copy; <?= date('Y') ?> BA Security. All rights reserved.</p>
  </footer>
  <script src="alerts.js"></script>
  <script src="darkmode.js"></script>
</body>
</html>

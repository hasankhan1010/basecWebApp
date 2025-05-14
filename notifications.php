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

// 2) Fetch today's jobs grouped by engineer

$engineerJobs = [];
$sql = "
  SELECT 
    sd.engineerID,
    e.engineerFirstName,
    e.engineerLastName,
    sd.ScheduleStartTime, 
    c.clientFirstName, 
    c.clientLastName, 
    c.clientAddress1, 
    c.clientAddress2,
    sd.scheduleJobType
  FROM schedulediary sd
  JOIN clients c ON sd.clientID = c.clientID
  LEFT JOIN Engineer e ON sd.engineerID = e.engineerID
  WHERE sd.scheduleDate = ?
  ORDER BY sd.engineerID, sd.ScheduleStartTime
";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('s', $rawDate);
    $stmt->execute();
    $stmt->bind_result($engineerID, $engineerFirstName, $engineerLastName, $time, $clientFirstName, $clientLastName, $a1, $a2, $jobType);
    
    while ($stmt->fetch()) {
        $addr = trim("$a1 $a2");
        if ($addr === '') continue;
        
        // Create engineer name (or use ID if name not available)
        $engineerName = "Unassigned";
        if ($engineerID) {
            $engineerName = $engineerFirstName && $engineerLastName ? 
                "$engineerFirstName $engineerLastName" : 
                "Engineer #$engineerID";
        }
        
        // Initialize engineer array if not exists
        if (!isset($engineerJobs[$engineerID])) {
            $engineerJobs[$engineerID] = [
                'name' => $engineerName,
                'jobs' => []
            ];
        }
        
        // Add job to engineer's job list
        $engineerJobs[$engineerID]['jobs'][] = [
            'label' => "$time – $clientFirstName $clientLastName ($jobType)",
            'address' => $addr
        ];
    }
    $stmt->close();
} else {
    die('<p style="color:red; text-align:center;">Failed to prepare query.</p>');
}

// 3) Encode engineer jobs for JavaScript
$engineerJobsJson = json_encode($engineerJobs, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?: '{}';
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
    .engineer-route-section { 
      margin-bottom: 2rem; 
      border: 1px solid #ddd; 
      border-radius: 8px; 
      padding: 1rem; 
      background-color: var(--bg-secondary); 
    }
    .engineer-name {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      color: var(--primary);
      border-bottom: 2px solid var(--primary);
      padding-bottom: 0.5rem;
    }
    .route-container { 
      display: flex; 
      gap: 1rem; 
      flex-wrap: wrap; 
      margin: 1rem 0; 
    }
    .map-container { 
      flex: 2; 
      min-width: 300px; 
      height: 400px; 
      border: 2px solid #ccc; 
      border-radius: 4px; 
    }
    .directions-panel {
      flex: 1;
      min-width: 250px;
      height: 400px;
      background: var(--bg-primary);
      padding: 1rem;
      border: 2px solid #ccc;
      border-radius: 4px;
      overflow-y: auto;
    }
    .no-jobs {
      padding: 2rem;
      text-align: center;
      font-style: italic;
      color: var(--text-muted);
    }
    .dark-mode .directions-panel {
      background: var(--bg-secondary);
      color: var(--text-primary);
    }
    @media (max-width: 768px) {
      .route-container {
        flex-direction: column;
      }
      .map-container, .directions-panel {
        width: 100%;
        height: 350px;
      }
    }
  </style>

  <!-- Google Maps JS API -->
  <script
    src="https://maps.googleapis.com/maps/api/js?key=<?= $apiKey ?>&callback=initMaps"
    async defer>
  </script>
  <script>
    'use strict';
    const office = '2A Reginald St, Luton LU2 7QZ';
    const engineerJobs = <?= $engineerJobsJson ?>;

    // Initialize all maps when Google Maps API is loaded
    function initMaps() {
      // If no engineers with jobs, show message
      if (Object.keys(engineerJobs).length === 0) {
        document.getElementById('maps-container').innerHTML = 
          '<div class="no-jobs">No jobs scheduled for this date.</div>';
        return;
      }

      // Create a map for each engineer
      Object.keys(engineerJobs).forEach(engineerId => {
        createEngineerMap(engineerId, engineerJobs[engineerId]);
      });
    }

    // Create a map section for a specific engineer
    function createEngineerMap(engineerId, engineerData) {
      const { name, jobs } = engineerData;
      
      // Create container elements
      const sectionEl = document.createElement('div');
      sectionEl.className = 'engineer-route-section';
      sectionEl.innerHTML = `
        <h2 class="engineer-name">${name}</h2>
        <div class="route-container" id="route-container-${engineerId}">
          <div class="map-container" id="map-${engineerId}"></div>
          <div class="directions-panel" id="directions-${engineerId}"></div>
        </div>
      `;
      
      // Add to page
      document.getElementById('maps-container').appendChild(sectionEl);
      
      // Initialize the map
      const mapEl = document.getElementById(`map-${engineerId}`);
      const panel = document.getElementById(`directions-${engineerId}`);
      
      // Create map
      const map = new google.maps.Map(mapEl, {
        center: { lat: 51.8825, lng: -0.4179 },
        zoom: 10
      });
      
      // Set up directions
      const service = new google.maps.DirectionsService();
      const renderer = new google.maps.DirectionsRenderer({ 
        map, 
        panel,
        suppressMarkers: false,
        suppressInfoWindows: false
      });

      // Handle no jobs case
      if (!jobs.length) {
        panel.innerHTML = '<p>No jobs assigned to this engineer for this date.</p>';
        return;
      }

      // Create waypoints from job addresses
      const waypoints = jobs.slice(0, -1).map(j => ({
        location: j.address,
        stopover: true
      }));

      // Create route
      service.route({
        origin: office,
        destination: jobs[jobs.length - 1].address,
        waypoints,
        optimizeWaypoints: true,
        travelMode: 'DRIVING'
      }, (res, status) => {
        if (status === 'OK') {
          renderer.setDirections(res);
          
          // Add labels to the route
          const route = res.routes[0];
          const legs = route.legs;
          
          // Add a marker for the office (starting point)
          new google.maps.Marker({
            position: legs[0].start_location,
            map: map,
            label: { text: 'S', color: 'white' },
            title: 'Office - Starting Point'
          });
          
          // Add markers with labels for each job location
          jobs.forEach((job, i) => {
            const position = i < legs.length ? 
              (i === legs.length - 1 ? legs[i].end_location : legs[i].end_location) : 
              null;
            
            if (position) {
              new google.maps.Marker({
                position: position,
                map: map,
                label: { text: `${i+1}`, color: 'white' },
                title: job.label
              });
            }
          });
        } else {
          panel.innerHTML = `<p>Error loading route: ${status}</p>`;
        }
      });
    }

    // Re-init maps when navigating back/forward
    window.addEventListener('pageshow', e => {
      if (e.persisted) initMaps();
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
    <h1>Engineer Routes for <?= $displayDate ?></h1>
    <div class="route-controls">
      <a href="notifications.php?date=<?= $prevDate ?>">&laquo; Previous</a>
      <a href="notifications.php?date=<?= $nextDate ?>">Next &raquo;</a>
    </div>
    
    <!-- Maps container - will be populated by JavaScript -->
    <div id="maps-container">
      <?php if (empty($engineerJobs)): ?>
        <div class="no-jobs">No jobs scheduled for this date.</div>
      <?php endif; ?>
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

<?php
session_start();

// AJAX ENDPPOINT - INFO RETURNED VIA JSON BY THE LOGGED IN USER
if (isset($_GET['action']) && $_GET['action'] === 'getUser') {
    header('Content-Type: application/json');
    if (isset($_SESSION['user']) && isset($_SESSION['role'])) {
        $user = $_SESSION['user'];
        $role = $_SESSION['role'];
        $username = "";
        $firstName = "";
        $lastName = "";
        switch ($role) {
            case 'engineer':
                $username  = $user['engineerUsername'];
                $firstName = $user['engineerFirstName'];
                $lastName  = $user['engineerLastName'];
                break;
            case 'admin':
                $username  = $user['adminUsername'];
                $firstName = $user['adminFirstName'];
                $lastName  = $user['adminLastName'];
                break;
            case 'manager':
                $username  = $user['managerUsername'];
                $firstName = $user['managerFirstName'];
                $lastName  = $user['managerLastName'];
                break;
            case 'sales':
                $username  = $user['salesUsername'];
                $firstName = $user['salesFirstName'];
                $lastName  = $user['salesLastName'];
                break;
            default:
                $username = "Unknown";
        }
        echo json_encode([
            'username'  => $username,
            'firstName' => $firstName,
            'lastName'  => $lastName,
            'role'      => $role
        ]);
    } else {
        echo json_encode([]);
    }
    exit();
}

// NOT LOGGED IN - THEN RETURN TO HOME
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRM Portal</title>
  <link rel="stylesheet" href="portal.css">
  <script src="js/jquery.min.js"></script>
  <script>
    $(document).ready(function(){
      // UPDATES USER INFO VIA AJAX - JQUERY - YOUTUBE VID !!!!
      // PHP WONT WORK?????????????????????
      function updateUserInfo() {
          $.ajax({
              url: 'portal.php',
              type: 'GET',
              data: { action: 'getUser' },
              dataType: 'json',
              success: function(data) {
                  if (data && data.username) {
                      $('#userInfo').html(
                        'Logged in as: ' + data.username + ' (' + data.firstName + ' ' + data.lastName + ')'
                      );
                  } else {
                      $('#userInfo').html('');
                  }
              }
          });
      }
      // CALL AND UPDATE EVERY 5SECS
      updateUserInfo();
      setInterval(updateUserInfo, 5000);
    });

//ADD A FOOTER WITH BASECURTIY WEBSITE FOOTER - ALL RIGHTS RESERVED!!!!!!!!!!!!!!!!!!!!!1

  </script>
</head>
<body>
  <header>
    <h1>CRM System Portal</h1>
    <div id="userInfo"></div>
  </header>
  
  <main>
    <nav class="portal-nav">
      <button onclick="location.href='clientServices.php'">Client Services</button>
      <button onclick="location.href='scheduleDiary.php'">Schedule Diary</button>
      <button onclick="location.href='surveyDiary.php'">Survey Diary</button>
      <button onclick="location.href='adminControl.php'">Admin Control</button>
      <button onclick="location.href='feedback.php'">Feedback</button>
      <button onclick="location.href='notifications.php'">Notifications</button>
      <button onclick="location.href='sustainabilityMetrics.php'">Sustainability Metrics</button>
      <button onclick="location.href='payments.php'">Payments</button>
      <button onclick="location.href='logout.php'">Logout</button>
    </nav>
  </main>
  
  <footer> 
    <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p> 
  </footer>
</body>
</html>
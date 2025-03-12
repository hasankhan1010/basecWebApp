<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}
include('database.php');

// Get logged-in user info
$userInfo = [];
if (isset($_SESSION['user']) && isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    $user = $_SESSION['user'];
    switch ($role) {
        case 'engineer':
            $userInfo = [
                'username'  => $user['engineerUsername'],
                'firstName' => $user['engineerFirstName'],
                'lastName'  => $user['engineerLastName']
            ];
            break;
        case 'admin':
            $userInfo = [
                'username'  => $user['adminUsername'],
                'firstName' => $user['adminFirstName'],
                'lastName'  => $user['adminLastName']
            ];
            break;
        case 'manager':
            $userInfo = [
                'username'  => $user['managerUsername'],
                'firstName' => $user['managerFirstName'],
                'lastName'  => $user['managerLastName']
            ];
            break;
        case 'sales':
            $userInfo = [
                'username'  => $user['salesUsername'],
                'firstName' => $user['salesFirstName'],
                'lastName'  => $user['salesLastName']
            ];
            break;
        default:
            $userInfo = [
                'username'  => "Unknown",
                'firstName' => "Unknown",
                'lastName'  => "Unknown"
            ];
    }
}

// Optional: Process a search query.
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
}

// Retrieve notifications for Low Feedback and Sustainability Alert.
$query = "SELECT * FROM Notifications 
          WHERE notificationType IN ('Low Feedback', 'Sustainability Alert')";
if ($search !== "") {
    $query .= " AND (notificationDetails LIKE '%$search%' OR notificationDateTime LIKE '%$search%')";
}
$query .= " ORDER BY notificationDateTime DESC";
$result = mysqli_query($conn, $query);
$notifications = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="notifications.css">
    <script src="js/jquery.min.js"></script>
    <script>
      $(document).ready(function(){
          // When a notification checkbox is toggled, update read status via AJAX.
          $(".read-checkbox").change(function(){
              var notificationID = $(this).data("notificationid");
              var isRead = $(this).is(":checked") ? 1 : 0;
              $.ajax({
                  url: "updateNotification.php",
                  type: "POST",
                  data: { notificationID: notificationID, isRead: isRead },
                  success: function(response) {
                      console.log("Notification updated.");
                  },
                  error: function() {
                      alert("Error updating notification.");
                  }
              });
          });
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
        <a href="notifications.php" class="active">Notifications</a>
        <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
        <a href="payments.php">Payments</a>
      </div>
      <div class="nav-right">
        <a href="logout.php">Logout</a>
      </div>
    </nav>
    
    <div class="container">
        <h1>Notifications</h1>
        
        <section class="logged-in-user">
            <h2>Logged In User</h2>
            <p><?php echo htmlspecialchars($userInfo['firstName'] . " " . $userInfo['lastName'] . " (" . $userInfo['username'] . ")"); ?></p>
        </section>
        
        <section class="search-refresh">
            <form method="get" action="notifications.php" class="search-form">
                <input type="text" name="search" placeholder="Search notifications..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
            <button onclick="location.reload()">Refresh</button>
        </section>
        
        <section class="notifications-list">
            <h2>Recent Notifications</h2>
            <?php if(!empty($notifications)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date / Time</th>
                        <th>Type</th>
                        <th>Details</th>
                        <th>Action</th>
                        <th>Read?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($notifications as $note): 
                        // For action button: if Low Feedback, link to clientServiceEdit.php using clientID;
                        // if Sustainability Alert, link to sustainabilityMetrics.php (or a dedicated metrics page).
                        $clientID = $note['clientID'];
                        if($note['notificationType'] === 'Low Feedback'){
                            $actionLink = "<a href='clientServiceEdit.php?clientID={$clientID}' class='action-btn'>View Client</a>";
                        } else {
                            $actionLink = "<a href='sustainabilityMetrics.php' class='action-btn'>View Metrics</a>";
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($note['notificationDateTime'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($note['notificationType'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($note['notificationDetails'] ?? ''); ?></td>
                        <td><?php echo $actionLink; ?></td>
                        <td>
                            <input type="checkbox" class="read-checkbox" data-notificationid="<?php echo $note['notificationID']; ?>" <?php echo ($note['notificationIsRead']) ? "checked" : ""; ?>>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No notifications found.</p>
            <?php endif; ?>
        </section>
    </div>
    
    <footer>
      <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
    </footer>
</body>
</html>

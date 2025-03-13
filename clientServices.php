<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include('database.php');

// PROCESS THE OPTIONAL SEARCH QUERY:::::::::
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
}

// QUERY ALL CLIENTS - DISPLAYING THE RELATIVE INFO::::
$query = "SELECT clientID, clientFirstName, clientLastName, clientEmail, clientPhone FROM Clients";
if ($search !== "") {
    $query .= " WHERE clientFirstName LIKE '%$search%' OR clientLastName LIKE '%$search%' OR clientEmail LIKE '%$search%' OR clientPhone LIKE '%$search%'";
}
$query .= " ORDER BY clientID ASC";


$result = mysqli_query($conn, $query);
$clients = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $clients[] = $row;
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Services</title>
    <link rel="stylesheet" href="clientServices.css">
    <script src="js/jquery.min.js"></script>
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <a href="portal.php">Home</a>
            <a href="clientServices.php" class="active">Client Services</a>
            <a href="scheduleDiary.php">Schedule Diary</a>
            <a href="surveyDiary.php">Survey Diary</a>
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
    
    <div class="container">
        <header>
            <h1>Client Services</h1>
            <div class="search-refresh">
                <form method="get" action="clientServices.php" class="search-form">
                    <input type="text" name="search" placeholder="Search clients..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Search</button>
                </form>
                <button id="refreshBtn" onclick="location.reload()">Refresh</button>
            </div>
        </header>
        
        <div class="clients-list">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($client['clientID']); ?></td>
                                <td><?php echo htmlspecialchars($client['clientFirstName']); ?></td>
                                <td><?php echo htmlspecialchars($client['clientLastName']); ?></td>
                                <td><?php echo htmlspecialchars($client['clientEmail']); ?></td>
                                <td><?php echo htmlspecialchars($client['clientPhone']); ?></td>
                                <td>
                                    <a href="clientServiceEdit.php?clientID=<?php echo $client['clientID']; ?>" class="select-btn">Select</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No clients found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

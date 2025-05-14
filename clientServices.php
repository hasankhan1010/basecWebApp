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

// Check if we're filtering for bad feedback or payment status
$filterBadFeedback = isset($_GET['badFeedback']) && $_GET['badFeedback'] === '1';
$paymentFilter = isset($_GET['paymentFilter']) ? $_GET['paymentFilter'] : '';

// QUERY ALL CLIENTS WITH FEEDBACK DATA AND PAYMENT STATUS
$query = "SELECT c.clientID, c.clientFirstName, c.clientLastName, c.clientEmail, c.clientPhone, 
          (SELECT MIN(f.feedbackRating) 
           FROM Feedback f 
           JOIN ScheduleDiary sd ON f.feedbackID = sd.scheduleID 
           WHERE sd.clientID = c.clientID) AS lowestFeedback,
          (SELECT EXISTS(SELECT 1 
                        FROM Feedback f2 
                        JOIN ScheduleDiary sd2 ON f2.feedbackID = sd2.scheduleID 
                        WHERE sd2.clientID = c.clientID AND f2.feedbackRating <= 2)) AS hasBadFeedback,
          (SELECT COUNT(*) FROM ScheduleDiary sd3 
           LEFT JOIN Payments p ON sd3.scheduleID = p.invoiceReference 
           WHERE sd3.clientID = c.clientID AND (p.paymentIsPaid = 0 OR p.paymentID IS NULL)) AS unpaidCount
          FROM Clients c";

// Apply search filter
if ($search !== "") {
    $query .= " WHERE (c.clientFirstName LIKE '%$search%' OR c.clientLastName LIKE '%$search%' OR c.clientEmail LIKE '%$search%' OR c.clientPhone LIKE '%$search%')";
}

// Apply bad feedback filter if requested
if ($filterBadFeedback) {
    if ($search !== "") {
        $query .= " AND";
    } else {
        $query .= " WHERE";
    }
    $query .= " EXISTS (SELECT 1 
                FROM Feedback f 
                JOIN ScheduleDiary sd ON f.feedbackID = sd.scheduleID 
                WHERE sd.clientID = c.clientID AND f.feedbackRating <= 2)";
}

// Apply payment filter if requested
if ($paymentFilter === 'paid' || $paymentFilter === 'unpaid') {
    if ($search !== "" || $filterBadFeedback) {
        $query .= " AND";
    } else {
        $query .= " WHERE";
    }
    
    if ($paymentFilter === 'paid') {
        // For paid clients: those who have jobs and all are paid
        $query .= " (SELECT COUNT(*) FROM ScheduleDiary sd2 WHERE sd2.clientID = c.clientID) > 0 
                  AND (SELECT COUNT(*) FROM ScheduleDiary sd3 
                       LEFT JOIN Payments p ON sd3.scheduleID = p.invoiceReference 
                       WHERE sd3.clientID = c.clientID AND (p.paymentIsPaid = 0 OR p.paymentID IS NULL)) = 0";
    } else {
        // For unpaid clients: those who have at least one unpaid job
        $query .= " (SELECT COUNT(*) FROM ScheduleDiary sd2 
                     LEFT JOIN Payments p ON sd2.scheduleID = p.invoiceReference 
                     WHERE sd2.clientID = c.clientID AND (p.paymentIsPaid = 0 OR p.paymentID IS NULL)) > 0";
    }
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
    <link rel="stylesheet" href="css/feedback-popup.css">
    <link rel="stylesheet" href="darkmode.css">
    <script src="js/jquery.min.js"></script>
  <link rel="stylesheet" href="powerAlerts.css">
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
            <a href="notifications.php">Map Routes</a>
            <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
            <a href="payments.php">Payments</a>
            <a href="reminders.php">Reminders</a>
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
                    <?php if ($filterBadFeedback): ?>
                        <input type="hidden" name="badFeedback" value="1">
                    <?php endif; ?>
                    <?php if ($paymentFilter): ?>
                        <input type="hidden" name="paymentFilter" value="<?php echo $paymentFilter; ?>">
                    <?php endif; ?>
                </form>
                <div class="filter-buttons">
                    <button id="filterBadFeedbackBtn" class="filter-btn <?php echo $filterBadFeedback ? 'active' : ''; ?>" onclick="toggleBadFeedbackFilter()">Filter Bad Feedback</button>
                    <button id="filterPaidBtn" class="filter-btn <?php echo $paymentFilter === 'paid' ? 'active' : ''; ?>" onclick="applyPaymentFilter('paid')">Paid Clients</button>
                    <button id="filterUnpaidBtn" class="filter-btn <?php echo $paymentFilter === 'unpaid' ? 'active' : ''; ?>" onclick="applyPaymentFilter('unpaid')">Unpaid Clients</button>
                    <button id="clearFiltersBtn" class="clear-filters-btn" onclick="clearAllFilters()">Clear Filters</button>
                </div>
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
                        <th>Payment Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): 
                            $hasBadFeedback = isset($client['hasBadFeedback']) && $client['hasBadFeedback'] == 1;
                        ?>
                            <tr class="<?php echo $hasBadFeedback ? 'bad-feedback' : ''; ?>">
                                <td><?php echo htmlspecialchars($client['clientID']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($client['clientFirstName']); ?>
                                    <?php if ($hasBadFeedback): ?>
                                        <span class="bad-feedback-indicator">Low Rating</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($client['clientLastName']); ?></td>
                                <td><?php echo htmlspecialchars($client['clientEmail']); ?></td>
                                <td><?php echo htmlspecialchars($client['clientPhone']); ?></td>
                                <td>
                                    <?php if (isset($client['unpaidCount']) && $client['unpaidCount'] > 0): ?>
                                        <span class="payment-status unpaid">Unpaid (<?php echo $client['unpaidCount']; ?>)</span>
                                    <?php else: ?>
                                        <?php 
                                        // Check if client has any jobs at all
                                        $clientID = $client['clientID'];
                                        $jobCountQuery = "SELECT COUNT(*) as jobCount FROM ScheduleDiary WHERE clientID = $clientID";
                                        $jobCountResult = mysqli_query($conn, $jobCountQuery);
                                        $jobCountRow = mysqli_fetch_assoc($jobCountResult);
                                        $hasJobs = $jobCountRow['jobCount'] > 0;
                                        
                                        if ($hasJobs): ?>
                                            <span class="payment-status paid">Paid</span>
                                        <?php else: ?>
                                            <span class="payment-status no-jobs">No Jobs</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
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
    
  <footer> 
    <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p> 
  </footer>
    <script src="alerts.js"></script>
    <script src="darkmode.js"></script>
    <script>
        // Function to toggle bad feedback filter
        function toggleBadFeedbackFilter() {
            const currentUrl = new URL(window.location.href);
            const searchParams = currentUrl.searchParams;
            
            if (searchParams.has('badFeedback')) {
                searchParams.delete('badFeedback');
            } else {
                searchParams.set('badFeedback', '1');
            }
            
            // Preserve other filters
            window.location.href = currentUrl.toString();
        }
        
        function applyPaymentFilter(status) {
            const currentUrl = new URL(window.location.href);
            const searchParams = currentUrl.searchParams;
            
            // If the same filter is already active, remove it (toggle behavior)
            if (searchParams.get('paymentFilter') === status) {
                searchParams.delete('paymentFilter');
            } else {
                // Remove any existing payment filter first
                searchParams.delete('paymentFilter');
                // Then set the new one
                searchParams.set('paymentFilter', status);
            }
            
            window.location.href = currentUrl.toString();
        }
        
        function clearAllFilters() {
            const currentUrl = new URL(window.location.href);
            
            // Clear all search parameters including the search input
            currentUrl.search = '';
            
            // Also clear the search input field
            document.querySelector('input[name="search"]').value = '';
            
            window.location.href = currentUrl.toString();
        }
    </script>
  <?php include('includes/power-monitor.php'); ?>
</body>
</html>

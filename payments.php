<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}
include('database.php');

// Optional search query
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
}

// Query completed jobs from ScheduleDiary joined with Clients and (optionally) Payments
// We assume scheduleStatus in ('complete','completed') and invoiceReference = scheduleID.
$query = "SELECT sd.scheduleID, sd.scheduleJobType, sd.scheduleDate, sd.scheduleStartTime, sd.scheduleEndTime, sd.scheduleStatus,
                 c.clientID, c.clientFirstName, c.clientLastName, c.clientEmail,
                 p.paymentID, p.paymentAmount, p.paymentDateTime, p.paymentIsPaid, p.paymentNotes
          FROM ScheduleDiary sd
          JOIN Clients c ON sd.clientID = c.clientID
          LEFT JOIN Payments p ON sd.scheduleID = p.invoiceReference
          WHERE sd.scheduleStatus IN ('complete','completed')";

if ($search !== "") {
    // Filter by client name, job type, or payment notes
    $query .= " AND (c.clientFirstName LIKE '%$search%'
                  OR c.clientLastName LIKE '%$search%'
                  OR sd.scheduleJobType LIKE '%$search%'
                  OR p.paymentNotes LIKE '%$search%')";
}

$query .= " ORDER BY sd.scheduleDate DESC";

$result = mysqli_query($conn, $query);
$records = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $records[] = $row;
    }
}

// Process form submission to update or insert payment records
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['payments']) && is_array($_POST['payments'])) {
        foreach ($_POST['payments'] as $jobID => $data) {
            $paymentAmount = $data['paymentAmount'] ?? "";
            $paymentIsPaid = (isset($data['paymentIsPaid']) && $data['paymentIsPaid'] === "Paid") ? 1 : 0;
            $paymentNotes  = $data['paymentNotes'] ?? "";
            $clientID      = $data['clientID'] ?? 0;
            $paymentDateTime = date("Y-m-d H:i:s"); // Current time on update

            // Check if a payment record exists for this job.
            $check = $conn->prepare("SELECT paymentID FROM Payments WHERE invoiceReference = ?");
            $check->bind_param("i", $jobID);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                // Update existing payment.
                $check->bind_result($paymentID);
                $check->fetch();
                $check->close();
                $upd = $conn->prepare("UPDATE Payments 
                                       SET paymentAmount=?, paymentIsPaid=?, paymentNotes=?, paymentDateTime=? 
                                       WHERE paymentID = ?");
                $upd->bind_param("dsisi", $paymentAmount, $paymentIsPaid, $paymentNotes, $paymentDateTime, $paymentID);
                $upd->execute();
                $upd->close();
            } else {
                $check->close();
                // Insert new payment record.
                $ins = $conn->prepare("INSERT INTO Payments (clientID, invoiceReference, paymentAmount, paymentIsPaid, paymentNotes) 
                                       VALUES (?, ?, ?, ?, ?)");
                $ins->bind_param("iidsi", $clientID, $jobID, $paymentAmount, $paymentIsPaid, $paymentNotes);
                $ins->execute();
                $ins->close();
            }
        }
    }
    // Refresh page
    header("Location: payments.php?search=" . urlencode($search));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payments</title>
  <link rel="stylesheet" href="payments.css">
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
      <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
      <a href="payments.php" class="active">Payments</a>
    </div>
    <div class="nav-right">
      <a href="logout.php">Logout</a>
    </div>
  </nav>
  
  <div class="container">
    <h1>Payments</h1>
    <div class="search-refresh">
      <form method="get" action="payments.php" class="search-form">
        <input type="text" name="search" placeholder="Search payments..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
      </form>
      <button onclick="location.reload()">Refresh</button>
    </div>
    
    <form method="post" action="payments.php?search=<?php echo urlencode($search); ?>">
      <table class="payments-table">
        <thead>
          <tr>
            <th>Job ID</th>
            <th>Job Type</th>
            <th>Client Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Payment Amount</th>
            <th>Paid?</th>
            <th>Payment Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($records)): ?>
            <?php foreach ($records as $rec): 
              $jobID = $rec['scheduleID'];
              $clientID = $rec['clientID'];
              $jobType = htmlspecialchars($rec['scheduleJobType'] ?? '');
              $clientName = htmlspecialchars($rec['clientFirstName'] . " " . $rec['clientLastName']);
              $date = htmlspecialchars($rec['scheduleDate'] ?? '');
              $time = htmlspecialchars(($rec['scheduleStartTime'] ?? '') . " - " . ($rec['scheduleEndTime'] ?? ''));
              $paymentAmount = $rec['paymentAmount'] ?? "";
              $paymentIsPaid = (!empty($rec['paymentIsPaid']) && $rec['paymentIsPaid'] == 1) ? "Paid" : "Unpaid";
              $paymentNotes = $rec['paymentNotes'] ?? "";
            ?>
            <tr>
              <td><?php echo $jobID; ?></td>
              <td><?php echo $jobType; ?></td>
              <td><?php echo $clientName; ?></td>
              <td><?php echo $date; ?></td>
              <td><?php echo $time; ?></td>
              <td>
                <input type="number" step="0.01" name="payments[<?php echo $jobID; ?>][paymentAmount]" 
                       value="<?php echo $paymentAmount; ?>" class="amount-input">
              </td>
              <td>
                <select name="payments[<?php echo $jobID; ?>][paymentIsPaid]">
                  <option value="Unpaid" <?php if ($paymentIsPaid === "Unpaid") echo "selected"; ?>>Unpaid</option>
                  <option value="Paid" <?php if ($paymentIsPaid === "Paid") echo "selected"; ?>>Paid</option>
                </select>
              </td>
              <td>
                <textarea name="payments[<?php echo $jobID; ?>][paymentNotes]" class="notes-input"><?php echo htmlspecialchars($paymentNotes); ?></textarea>
              </td>
              <!-- Hidden field for clientID -->
              <input type="hidden" name="payments[<?php echo $jobID; ?>][clientID]" value="<?php echo $clientID; ?>">
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8">No completed jobs found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <div class="form-actions">
        <button type="submit">Save Payment Changes</button>
      </div>
    </form>
  </div>
  
  <footer>
    <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
  </footer>
</body>
</html>

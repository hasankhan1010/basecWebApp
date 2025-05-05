<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include('database.php');

// SEARCH QUERY: 
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
}

//  QUERY ALL JOBS FROM SCHEDULEDIARY JOINED WITH CLIENTS AND PAYMENTS (OPTIONAL THO)
//  WE NOW INCLUDE ALL JOBS FROM SCHEDULE DIARY, NOT JUST COMPLETED ONES
$query = "SELECT sd.scheduleID, sd.scheduleJobType, sd.scheduleDate, sd.scheduleStartTime, sd.scheduleEndTime, sd.scheduleStatus,
                 c.clientID, c.clientFirstName, c.clientLastName, c.clientEmail,
                 COALESCE(p.paymentID, 0) as paymentID, 
                 COALESCE(p.paymentAmount, 0) as paymentAmount, 
                 p.paymentDateTime, 
                 COALESCE(p.paymentIsPaid, 0) as paymentIsPaid, 
                 p.paymentNotes
          FROM ScheduleDiary sd
          JOIN Clients c ON sd.clientID = c.clientID
          LEFT JOIN Payments p ON sd.scheduleID = p.invoiceReference";

if ($search !== "") {
    // FILTER BY CLIENT NAME, JOB TYPE OR MAYMENT NOTES:::::::::::
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

// PROCESS FORM SUBMISSION TO UPDATE OR INSERT NEW RECORDS 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if we're updating a single payment
    if (isset($_POST['single_payment_id'])) {
        $jobID = $_POST['single_payment_id'];
        $paymentAmount = $_POST['single_payment_amount'] ?? "";
        $paymentIsPaid = (isset($_POST['single_payment_status']) && $_POST['single_payment_status'] === "Paid") ? 1 : 0;
        $paymentNotes = $_POST['single_payment_notes'] ?? "";
        $clientID = $_POST['single_client_id'] ?? 0;
        $paymentDateTime = date("Y-m-d H:i:s"); // CURRENT TIME ON UPDATE - TIMESTAMP
        
        // CHECK IF A PAYMENT RECORD ALREADY EXISTS FOR THIS JOB
        $check = $conn->prepare("SELECT paymentID FROM Payments WHERE invoiceReference = ?");
        $check->bind_param("i", $jobID);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // UPDATE AN EXISTING PAYMENT
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
            // INSERT A NEW PAYMENT RECORD 
            $ins = $conn->prepare("INSERT INTO Payments (clientID, invoiceReference, paymentAmount, paymentIsPaid, paymentNotes) 
                                   VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param("iidsi", $clientID, $jobID, $paymentAmount, $paymentIsPaid, $paymentNotes);
            $ins->execute();
            $ins->close();
        }
        
        // Set success message
        $_SESSION['payment_message'] = "Payment for Job #$jobID has been updated successfully.";
        
        // REFRESH PAGE
        header("Location: payments.php?search=" . urlencode($search));
        exit();
    }
    // Process bulk updates
    else if (isset($_POST['payments']) && is_array($_POST['payments'])) {
        foreach ($_POST['payments'] as $jobID => $data) {
            $paymentAmount = $data['paymentAmount'] ?? "";
            $paymentIsPaid = (isset($data['paymentIsPaid']) && $data['paymentIsPaid'] === "Paid") ? 1 : 0;
            $paymentNotes  = $data['paymentNotes'] ?? "";
            $clientID      = $data['clientID'] ?? 0;
            $paymentDateTime = date("Y-m-d H:i:s"); // CURRENT TIME ON UPDATE - TIMESTAMP

            // CHECK IF A PAYMENT RECORD ALREADY EXISTS FOR THIS JOB
            $check = $conn->prepare("SELECT paymentID FROM Payments WHERE invoiceReference = ?");
            $check->bind_param("i", $jobID);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                // UPDATE AN EXISTING PAYMENT
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
                // INSERT A NEW PAYMENT RECORD 
                $ins = $conn->prepare("INSERT INTO Payments (clientID, invoiceReference, paymentAmount, paymentIsPaid, paymentNotes) 
                                       VALUES (?, ?, ?, ?, ?)");
                $ins->bind_param("iidsi", $clientID, $jobID, $paymentAmount, $paymentIsPaid, $paymentNotes);
                $ins->execute();
                $ins->close();
            }
        }
        
        // Set success message
        $_SESSION['payment_message'] = "All payment changes have been saved successfully.";
        
        // REFRESH PAGE
        header("Location: payments.php?search=" . urlencode($search));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payments</title>
  <link rel="stylesheet" href="payments.css">
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
      <a href="sustainabilityMetrics.php">Sustainability Metrics</a>
      <a href="payments.php" class="active">Payments</a>
      <a href="reminders.php">Reminders</a>
    </div>
    <div class="nav-right">
      <a href="logout.php">Logout</a>
    </div>
  </nav>
  
  <div class="container">
    <h1>Payments</h1>
    <?php if (isset($_SESSION['payment_message'])): ?>
      <div class="alert alert-success">
        <?php 
          echo $_SESSION['payment_message']; 
          unset($_SESSION['payment_message']); 
        ?>
      </div>
    <?php endif; ?>
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
            <th>Client ID</th>
            <th>Client Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Payment Amount</th>
            <th>Paid?</th>
            <th>Payment Notes</th>
            <th>Action</th>
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
              <td><?php echo $clientID; ?></td>
              <td><?php echo $clientName; ?></td>
              <td><?php echo $date; ?></td>
              <td><?php echo $time; ?></td>
              <td>
                <form method="post" action="payments.php?search=<?php echo urlencode($search); ?>" class="single-payment-form">
                  <input type="number" step="0.01" name="single_payment_amount" 
                         value="<?php echo $paymentAmount; ?>" class="amount-input">
              </td>
              <td>
                <div class="payment-status-wrapper">
                  <select name="single_payment_status" class="payment-status-select <?php echo ($paymentIsPaid === "Paid") ? 'status-paid' : 'status-unpaid'; ?>">
                    <option value="Unpaid" <?php if ($paymentIsPaid === "Unpaid") echo "selected"; ?>>Unpaid</option>
                    <option value="Paid" <?php if ($paymentIsPaid === "Paid") echo "selected"; ?>>Paid</option>
                  </select>
                  <span class="payment-status-indicator <?php echo ($paymentIsPaid === "Paid") ? 'paid' : 'unpaid'; ?>"></span>
                </div>
              </td>
              <td>
                <textarea name="single_payment_notes" class="notes-input"><?php echo htmlspecialchars($paymentNotes); ?></textarea>
              </td>
              <td>
                <input type="hidden" name="single_payment_id" value="<?php echo $jobID; ?>">
                <input type="hidden" name="single_client_id" value="<?php echo $clientID; ?>">
                <button type="submit" class="save-row-btn">Save</button>
              </form>
              </td>
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
        <button type="submit">Save All Payment Changes</button>
      </div>
    </form>
  </div>
  
  <footer>
    <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
  </footer>
  <script src="alerts.js"></script>
  <script src="darkmode.js"></script>
  <script>
    // Add event listeners to payment status selects to update the class
    document.addEventListener('DOMContentLoaded', function() {
      const statusSelects = document.querySelectorAll('.payment-status-select');
      
      statusSelects.forEach(select => {
        select.addEventListener('change', function() {
          // Update the select class
          if (this.value === 'Paid') {
            this.classList.remove('status-unpaid');
            this.classList.add('status-paid');
            
            // Update the indicator span
            const indicator = this.nextElementSibling;
            indicator.classList.remove('unpaid');
            indicator.classList.add('paid');
          } else {
            this.classList.remove('status-paid');
            this.classList.add('status-unpaid');
            
            // Update the indicator span
            const indicator = this.nextElementSibling;
            indicator.classList.remove('paid');
            indicator.classList.add('unpaid');
          }
        });
      });
    });
  </script>
</body>
</html>

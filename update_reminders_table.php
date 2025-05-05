<?php
// Script to update the reminders table structure
include 'database.php';

// Add reminderNotes column if it doesn't exist
$checkNotesColumn = $conn->query("SHOW COLUMNS FROM annualreminders LIKE 'reminderNotes'");
if ($checkNotesColumn->num_rows === 0) {
    $conn->query("ALTER TABLE annualreminders ADD COLUMN reminderNotes TEXT AFTER isAnnual");
    echo "Added reminderNotes column.<br>";
}

// Add clientID column if it doesn't exist
$checkClientIDColumn = $conn->query("SHOW COLUMNS FROM annualreminders LIKE 'clientID'");
if ($checkClientIDColumn->num_rows === 0) {
    $conn->query("ALTER TABLE annualreminders ADD COLUMN clientID INT AFTER serviceName");
    echo "Added clientID column.<br>";
}

echo "Database update complete.";
?>

<?php
include('database.php'); 

// Validate job ID
if (!isset($_GET['jobID']) || !is_numeric($_GET['jobID'])) {
    die("Invalid job reference.");
}
$jobID = intval($_GET['jobID']);

// Check if feedback already exists for this job
$stmt = $conn->prepare("SELECT * FROM Feedback WHERE feedbackID = ?");
$stmt->bind_param("i", $jobID);
$stmt->execute();
$result = $stmt->get_result();
$existingFeedback = $result->fetch_assoc();
$stmt->close();

// Handle feedback submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rating = $_POST['rating'] ?? '';
    $comments = $_POST['comments'] ?? '';

    if ($existingFeedback) {
        // Update existing feedback
        $stmt = $conn->prepare("UPDATE Feedback SET feedbackRating = ?, feedbackComments = ?, feedbackRecievedDateTime = NOW() WHERE feedbackID = ?");
        $stmt->bind_param("ssi", $rating, $comments, $jobID);
    } else {
        // Insert new feedback
        $stmt = $conn->prepare("INSERT INTO Feedback (feedbackID, clientID, feedbackRating, feedbackComments, feedbackRecievedDateTime) VALUES (?, (SELECT clientID FROM ScheduleDiary WHERE scheduleID = ?), ?, ?, NOW())");
        $stmt->bind_param("iiss", $jobID, $jobID, $rating, $comments);
    }

    if ($stmt->execute()) {
        $success = "Thank you for your feedback!";
    } else {
        $error = "Error submitting feedback.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Survey</title>
    <link rel="stylesheet" href="feedbackSurvey.css">
    <script>
        function selectRating(value) {
            document.getElementById('ratingInput').value = value;
            let stars = document.querySelectorAll('.star');
            stars.forEach(star => {
                if (parseInt(star.getAttribute('data-value')) <= value) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>BASecurity Feedback Survey</h1>
        
        <?php if (!empty($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php elseif (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php elseif ($existingFeedback): ?>
            <p class="info">You have already provided feedback for this job.</p>
        <?php else: ?>
            <form method="post" action="feedbackSurvey.php?jobID=<?php echo $jobID; ?>">
                <div class="rating">
                    <span class="star" data-value="1" onclick="selectRating(1)">&#9733;</span>
                    <span class="star" data-value="2" onclick="selectRating(2)">&#9733;</span>
                    <span class="star" data-value="3" onclick="selectRating(3)">&#9733;</span>
                    <span class="star" data-value="4" onclick="selectRating(4)">&#9733;</span>
                    <span class="star" data-value="5" onclick="selectRating(5)">&#9733;</span>
                </div>
                <input type="hidden" name="rating" id="ratingInput" required>

                <label for="comments">Additional Comments:</label>
                <textarea id="comments" name="comments" rows="4" placeholder="Write your feedback here..."></textarea>

                <button type="submit">Submit Feedback</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

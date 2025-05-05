/**
 * Feedback popup functionality
 */
document.addEventListener("DOMContentLoaded", function () {
  // Get the modal
  const modal = document.getElementById("feedbackModal");
  const modalContent = document.getElementById("feedbackModalContent");
  const closeBtn = document.getElementsByClassName("close-modal")[0];

  // Function to open modal with feedback details
  window.showFeedbackDetails = function (
    jobID,
    jobType,
    date,
    rating,
    comments
  ) {
    // Set modal content
    document.getElementById("modal-job-type").textContent = jobType;
    document.getElementById("modal-job-date").textContent = date;
    document.getElementById("modal-rating").textContent = rating;
    document.getElementById("modal-comments").textContent =
      comments || "No comments provided";

    // Display star rating visually
    const starsContainer = document.getElementById("modal-stars");
    starsContainer.innerHTML = "";

    const ratingValue = parseInt(rating) || 0;
    for (let i = 1; i <= 5; i++) {
      const star = document.createElement("span");
      star.className = i <= ratingValue ? "star filled" : "star";
      star.innerHTML = "&#9733;";
      starsContainer.appendChild(star);
    }

    // Show the modal
    modal.style.display = "block";
  };

  // Close the modal when clicking the close button
  if (closeBtn) {
    closeBtn.onclick = function () {
      modal.style.display = "none";
    };
  }

  // Close the modal when clicking outside of it
  window.onclick = function (event) {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  };
});

// reminders.js
// Polls for upcoming reminders and shows pop-up alerts at defined thresholds

(function ($) {
  // Poll every second for precision
  const POLL_INTERVAL_MS = 1000;
  // Alert thresholds in seconds
  const THRESHOLDS = [86400, 3600, 60, 10];
  // Track which alerts have been shown (reminderID::threshold)
  const alerted = new Set();

  // Display a modal-style pop-up
  function showPopup(message) {
    if ($("#reminder-popup").length) return;
    const popup = $(
      '<div id="reminder-popup" style="' +
        "position:fixed; top:20%; left:50%; transform:translateX(-50%);" +
        "background:#fff; border:2px solid #FFA500; border-radius:0.5rem;" +
        "padding:1rem 1.5rem; box-shadow:0 4px 12px rgba(0,0,0,0.2);" +
        "z-index:10000; max-width:320px; text-align:center;" +
        '">' +
        `<p style="margin:0 0 1rem; font-size:1rem; color:#333;">${message}</p>` +
        '<button id="reminder-close" class="button" style="' +
        "padding:0.5rem 1rem; background:#FFA500; color:#fff; border:none;" +
        "border-radius:0.25rem; cursor:pointer;" +
        '">OK</button>' +
        "</div>"
    );
    $("body").append(popup);
    popup.find("#reminder-close").on("click", () => popup.remove());
  }

  // Fetch active reminders and check thresholds
  function pollReminders() {
    $.getJSON("reminders.php", { action: "list" }).done(function (items) {
      const now = Date.now();
      items.forEach(function (r) {
        const dueTime = new Date(r.dueDate).getTime();
        const diffSec = Math.floor((dueTime - now) / 1000);
        THRESHOLDS.forEach(function (sec) {
          const key = `${r.reminderID}::${sec}`;
          if (diffSec <= sec && diffSec >= 0 && !alerted.has(key)) {
            let label;
            switch (sec) {
              case 86400:
                label = "1 day";
                break;
              case 3600:
                label = "1 hour";
                break;
              case 60:
                label = "1 minute";
                break;
              case 10:
                label = "10 seconds";
                break;
            }
            showPopup(`Reminder: "${r.serviceName}" is due in ${label}!`);
            alerted.add(key);
          }
        });
      });
    });
  }

  // Initialize polling
  $(function () {
    pollReminders();
    setInterval(pollReminders, POLL_INTERVAL_MS);
  });
})(jQuery);

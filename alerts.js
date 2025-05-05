// alerts.js
// Global reminder pop-ups for any page

(function () {
  // thresholds in seconds - show at exactly 1 day, 1 hour, 1 minute, and 10 seconds
  const THRESHOLDS = [86400, 3600, 60, 10];
  // keep track of shown alerts
  const alerted = new Set();
  
  // Add CSS styles directly to the page to ensure proper display
  const style = document.createElement('style');
  style.textContent = `
    #reminder-popup {
      position: fixed;
      top: 20px;
      right: 20px;
      width: 350px;
      background: #fff;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      z-index: 9999;
      font-family: Arial, sans-serif;
      overflow: hidden;
    }
    .popup-header {
      background: #4a90e2;
      color: white;
      padding: 10px 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .popup-header h3 {
      margin: 0;
      font-size: 16px;
    }
    .popup-close {
      cursor: pointer;
      font-size: 20px;
    }
    .popup-content {
      padding: 15px;
    }
    .popup-time {
      font-weight: bold;
      color: #e74c3c;
      margin-bottom: 10px;
    }
    .popup-service, .popup-client, .popup-date, .popup-type, .popup-notes {
      margin-bottom: 8px;
      font-size: 14px;
    }
    .reminder-type {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 3px;
      font-size: 12px;
    }
    .reminder-type.annual {
      background: #3498db;
      color: white;
    }
    .reminder-type.custom {
      background: #2ecc71;
      color: white;
    }
    .popup-footer {
      padding: 10px 15px;
      background: #f5f5f5;
    }
    .popup-actions {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    .popup-actions.second-row {
      margin-bottom: 0;
    }
    .remind-later-container {
      display: flex;
      width: 100%;
    }
    .remind-later-container select {
      flex: 1;
      margin-right: 5px;
      padding: 5px;
    }
    .button {
      padding: 6px 12px;
      border: none;
      border-radius: 3px;
      cursor: pointer;
      font-size: 13px;
      transition: background 0.3s;
    }
    .btn-complete-popup {
      background: #2ecc71;
      color: white;
    }
    .btn-dismiss {
      background: #95a5a6;
      color: white;
    }
    .btn-remind-later {
      background: #3498db;
      color: white;
      flex: 1;
    }
    .notification {
      padding: 10px 15px;
      margin: 10px 0;
      border-radius: 3px;
      background: #f8f9fa;
      border-left: 4px solid #3498db;
    }
    .notification.success {
      border-left-color: #2ecc71;
    }
  `;
  document.head.appendChild(style);

  // create and show pop-up

  function showPopup(reminder, timeLabel) {
    if (document.getElementById("reminder-popup")) return;

    // Create client info text
    const clientInfo = reminder.clientName ? 
      `<div class="popup-client"><strong>Client:</strong> ${reminder.clientName}</div>` : 
      `<div class="popup-client"><em>No Client</em></div>`;
    
    // Create notes section if available
    const notesSection = reminder.reminderNotes ? 
      `<div class="popup-notes"><strong>Notes:</strong> ${reminder.reminderNotes}</div>` : 
      `<div class="popup-notes"><em>No notes</em></div>`;
    
    // Format the date and time
    const dueDate = new Date(reminder.dueDate);
    const formattedDate = dueDate.toLocaleDateString('en-GB', {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    });
    const formattedTime = dueDate.toLocaleTimeString('en-GB', {
      hour: '2-digit',
      minute: '2-digit'
    });
    
    // Determine reminder type! - pretty neat
    const reminderType = reminder.isAnnual == 1 ? 'Annual' : 'Custom';
    const typeClass = reminder.isAnnual == 1 ? 'annual' : 'custom';

    // container! - so cool
    const popup = document.createElement("div");
    popup.id = "reminder-popup";
    popup.className = "reminder-popup";
    
    // Create the popup content
    popup.innerHTML = `
      <div class="popup-header">
        <h3>Reminder Alert</h3>
        <span class="popup-close" id="close-popup">×</span>
      </div>
      <div class="popup-content">
        <div class="popup-time">Due in ${timeLabel}</div>
        <div class="popup-service"><strong>Service:</strong> ${reminder.serviceName}</div>
        ${clientInfo}
        <div class="popup-date">
          <strong>Due Date:</strong> ${formattedDate} ${formattedTime}
        </div>
        <div class="popup-type">
          <strong>Type:</strong> <span class="reminder-type ${typeClass}">${reminderType}</span>
        </div>
        ${notesSection}
      </div>
      <div class="popup-footer">
        <div class="popup-actions">
          <button class="button btn-complete-popup" id="complete-reminder" data-id="${reminder.reminderID}">
            <i class="fa fa-check"></i> Mark Complete
          </button>
          <button class="button btn-dismiss" id="dismiss-popup">Dismiss</button>
        </div>
        <div class="popup-actions second-row">
          <div class="remind-later-container">
            <select id="remind-later-time">
              <option value="15">15 minutes</option>
              <option value="30">30 minutes</option>
              <option value="60">1 hour</option>
              <option value="240">4 hours</option>
              <option value="1440">1 day</option>
            </select>
            <button class="button btn-remind-later" id="remind-later" data-id="${reminder.reminderID}">
              <i class="fa fa-clock"></i> Remind Later
            </button>
          </div>
        </div>
      </div>
    `;
    
    document.body.appendChild(popup);
    
    // Add event listeners for buttons
    document.getElementById('close-popup').addEventListener('click', () => {
      document.body.removeChild(popup);
    });
    
    document.getElementById('dismiss-popup').addEventListener('click', () => {
      document.body.removeChild(popup);
    });
    
    // Add event listener for Mark as Complete button
    const completeBtn = document.getElementById('complete-reminder');
    if (completeBtn) {
      completeBtn.addEventListener('click', async () => {
        const reminderId = completeBtn.getAttribute('data-id');
        
        try {
          const response = await fetch(`reminders.php?action=complete&id=${reminderId}`, {
            method: 'POST'
          });
          const result = await response.json();
          
          if (result.success) {
            // Show confirmation message
            const confirmDiv = document.createElement('div');
            confirmDiv.className = 'notification success';
            confirmDiv.textContent = `Reminder marked as completed`;
            document.querySelector('.container')?.insertBefore(confirmDiv, document.querySelector('.form-group'));
            
            // Remove notification after 5 seconds
            setTimeout(() => {
              if (document.body.contains(confirmDiv)) {
                confirmDiv.remove();
              }
            }, 5000);
            
            // Close the popup
            document.body.removeChild(popup);
          }
        } catch (error) {
          console.error('Error completing reminder:', error);
        }
      });
    }
    
    // Add event listener for remind later button
    const remindLaterBtn = document.getElementById('remind-later');
    if (remindLaterBtn) {
      remindLaterBtn.addEventListener('click', async () => {
        const minutes = document.getElementById('remind-later-time').value;
        const reminderId = remindLaterBtn.getAttribute('data-id');
        
        try {
          const response = await fetch(`reminders.php?action=reschedule&id=${reminderId}&minutes=${minutes}`);
          const result = await response.json();
          
          if (result.success) {
            // Show confirmation message! - pretty neat
            const confirmDiv = document.createElement('div');
            confirmDiv.className = 'notification';
            confirmDiv.textContent = `Reminder rescheduled for ${new Date(result.newDueDate).toLocaleString()}`;
            document.querySelector('.container')?.insertBefore(confirmDiv, document.querySelector('.form-group'));
            
            // Remove notification after 5 seconds
            setTimeout(() => {
              if (document.body.contains(confirmDiv)) {
                confirmDiv.remove();
              }
            }, 5000);
            
            // Close the popup! - love this part
            document.body.removeChild(popup);
          }
        } catch (error) {
          console.error('Error rescheduling reminder:', error);
        }
      });
    }
  }

  // pull active reminders
  async function fetchReminders() {
    try {
      const res = await fetch("reminders.php?action=list");
      return await res.json();
    } catch {
      return [];
    }
  }

  // polling logic
  async function poll() {
    try {
      const reminders = await fetchReminders();
      const now = Date.now();
      
      reminders.forEach((reminder) => {
        const due = new Date(reminder.dueDate).getTime();
        const diff = Math.floor((due - now) / 1000);
        
        // Skip annual reminders that are less than 364 days away
        if (reminder.isAnnual == 1 && diff < 86400 * 364) {
          return;
        }
        
        THRESHOLDS.forEach((seconds) => {
          const key = `${reminder.reminderID}::${seconds}`;
          
          // Only alert if we're within 5 seconds of the exact threshold
          // and we haven't alerted for this threshold yet
          if (diff <= seconds && diff >= (seconds - 5) && !alerted.has(key)) {
            let timeLabel;
            
            if (seconds === 86400) timeLabel = "1 day";
            else if (seconds === 3600) timeLabel = "1 hour";
            else if (seconds === 60) timeLabel = "1 minute";
            else if (seconds === 10) timeLabel = "10 seconds";
            
            console.log(`Showing reminder popup for ${reminder.serviceName} due in ${timeLabel}`);
            
            // Pass the entire reminder object to showPopup! - omg
            showPopup(reminder, timeLabel);
            alerted.add(key);
          }
        });
      });
    } catch (error) {
      console.error('Error in reminder polling:', error);
    }
  }

  // start polling every second
  setInterval(poll, 1000);
  
  // Initial poll immediately when script loads
  console.log('Reminder alert system initialized');
  poll();
  
  // Create a global variable to indicate the alerts system is active
  window.reminderAlertsActive = true;
})();

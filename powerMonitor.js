/**
 * powerMonitor.js
 * Global power usage monitoring and notification system
 * Displays alerts when power usage exceeds 1000 watts
 */

(function() {
  // Configuration
  const POWER_THRESHOLD = 1000; // Watts
  const CHECK_INTERVAL = 30000; // Check every 30 seconds
  const ALERT_COOLDOWN = 3600000; // Show alert max once per hour (3600000 ms)
  const EXCLUDED_PAGES = [
    '/login.php',
    '/feedback-survey.php'
  ];
  
  // Check if we're on an excluded page
  function isExcludedPage() {
    const currentPath = window.location.pathname;
    return EXCLUDED_PAGES.some(page => currentPath.endsWith(page));
  }
  
  // Fetch the latest power usage data
  async function fetchPowerUsage() {
    try {
      const response = await fetch('sustainabilityMetrics.php?action=fetchData', {
        cache: 'no-store'
      });
      
      if (!response.ok) {
        throw new Error(`Server responded with ${response.status}`);
      }
      
      const data = await response.json();
      
      // Calculate power in watts (Wh * 3600 / seconds)
      // Using minute average and converting to watts
      const powerWatts = data.minuteAvg.energy * 3600;
      
      return {
        power: powerWatts,
        timestamp: new Date()
      };
    } catch (error) {
      console.error('Failed to fetch power usage:', error);
      return null;
    }
  }
  
  // Show high power usage alert
  function showHighPowerAlert(power) {
    // Only show alert if we haven't shown one recently (in the last hour)
    if (!localStorage.getItem('lastPowerAlertTime') || 
        (Date.now() - parseInt(localStorage.getItem('lastPowerAlertTime'))) > ALERT_COOLDOWN) {
      
      // Create alert message
      const alertMessage = `🚨 CRITICAL: Power usage is extremely high (${power.toFixed(1)}W)! Immediate action required to reduce system load.`;
      
      // Create a custom alert
      const alertDiv = document.createElement('div');
      alertDiv.className = 'custom-alert critical';
      alertDiv.innerHTML = `
        <div class="alert-content critical">
          <p>${alertMessage}</p>
          <button id="alertClose">Dismiss</button>
        </div>
      `;
      
      document.body.appendChild(alertDiv);
      
      // Add event listener to close button
      document.getElementById('alertClose').addEventListener('click', () => {
        alertDiv.remove();
      });
      
      // Auto-dismiss after 20 seconds
      setTimeout(() => {
        if (document.body.contains(alertDiv)) {
          alertDiv.remove();
        }
      }, 20000);
      
      // Store last alert time
      localStorage.setItem('lastPowerAlertTime', Date.now().toString());
      
      // Add to reminders history
      try {
        fetch('reminders.php?action=addReminder', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            reminderType: 'sustainability',
            reminderTitle: 'Sustainability Alert: CRITICAL',
            reminderContent: alertMessage,
            reminderSeverity: 'critical',
            reminderDate: new Date().toISOString(),
            isCompleted: 0
          })
        });
      } catch (err) {
        console.error('Error adding reminder to history:', err);
      }
    }
  }
  
  // Check power usage and show alert if needed
  async function checkPowerUsage() {
    // Skip if we're on an excluded page
    if (isExcludedPage()) {
      return;
    }
    
    const powerData = await fetchPowerUsage();
    
    if (powerData && powerData.power >= POWER_THRESHOLD) {
      showHighPowerAlert(powerData.power);
    }
  }
  
  // Initialize on page load
  document.addEventListener('DOMContentLoaded', () => {
    // Skip if we're on an excluded page
    if (isExcludedPage()) {
      return;
    }
    
    // Initial check
    setTimeout(checkPowerUsage, 5000); // Wait 5 seconds after page load
    
    // Set up regular checks
    setInterval(checkPowerUsage, CHECK_INTERVAL);
  });
})();

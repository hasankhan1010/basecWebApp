/**
 * powerUsageMonitor.js
 * Monitors power usage across all pages and creates reminder notifications when usage exceeds 1000 watts
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
  
  // Create a reminder for high power usage
  async function createPowerUsageReminder(power) {
    // Only create reminder if we haven't created one recently
    if (!localStorage.getItem('lastPowerReminderTime') || 
        (Date.now() - parseInt(localStorage.getItem('lastPowerReminderTime'))) > ALERT_COOLDOWN) {
      
      try {
        // Format current date for the reminder
        const now = new Date();
        const formattedDate = now.toISOString();
        
        // Create reminder message
        const reminderTitle = "CRITICAL: High Power Usage Alert";
        const reminderContent = `Power usage has reached ${power.toFixed(1)} watts, which exceeds the threshold of ${POWER_THRESHOLD} watts. Immediate action is required to reduce system load and improve sustainability.`;
        
        // Add reminder to the system
        const response = await fetch('reminders.php?action=addReminder', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            reminderType: 'sustainability',
            reminderTitle: reminderTitle,
            reminderContent: reminderContent,
            reminderSeverity: 'critical',
            reminderDate: formattedDate,
            dueDate: formattedDate, // Due immediately
            isCompleted: 0,
            isAnnual: 0
          })
        });
        
        if (!response.ok) {
          throw new Error('Failed to create reminder');
        }
        
        // Store last reminder time
        localStorage.setItem('lastPowerReminderTime', Date.now().toString());
        
        console.log('Power usage reminder created successfully');
        
        // Return the reminder data for potential use
        return {
          title: reminderTitle,
          content: reminderContent,
          date: formattedDate
        };
      } catch (error) {
        console.error('Error creating power usage reminder:', error);
        return null;
      }
    }
    
    return null;
  }
  
  // Check power usage and create reminder if needed
  async function checkPowerUsage() {
    // Skip if we're on an excluded page
    if (isExcludedPage()) {
      return;
    }
    
    const powerData = await fetchPowerUsage();
    
    if (powerData && powerData.power >= POWER_THRESHOLD) {
      console.log(`High power usage detected: ${powerData.power.toFixed(1)} watts`);
      await createPowerUsageReminder(powerData.power);
    }
  }
  
  // Initialize on page load
  document.addEventListener('DOMContentLoaded', () => {
    // Skip if we're on an excluded page
    if (isExcludedPage()) {
      return;
    }
    
    console.log('Power usage monitoring initialized');
    
    // Initial check after a short delay
    setTimeout(checkPowerUsage, 5000);
    
    // Set up regular checks
    setInterval(checkPowerUsage, CHECK_INTERVAL);
  });
})();

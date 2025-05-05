// sustainabilityMetrics.js
// Fetches and displays minute, hourly, and 7-day averages,
// plus pops an alert if recent energy use is high.
// Enhanced to show sustainability score and more accurate metrics

(function () {
  const FETCH_URL = "sustainabilityMetrics.php?action=fetchData";
  const POLL_INTERVAL = 15_000; // 15 seconds (more responsive)
  const BACKGROUND_POLL_INTERVAL = 60_000; // 1 minute when tab is not active
  const ENERGY_ALERT_THRESHOLD = 1.5; // Wh, adjusted threshold
  const SUSTAINABILITY_THRESHOLD = 70; // Minimum acceptable sustainability score

  // Utility to safely set text
  function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
  }

  // Render minute & hour averages with sustainability score
  function renderAverages(minuteAvg, hourAvg) {
    // Minute averages
    setText("minEnergy", minuteAvg.energy.toFixed(2));
    setText("minCarbon", minuteAvg.carbon.toFixed(2));
    setText("minWater", minuteAvg.water.toFixed(0));
    setText("minPerf", minuteAvg.performance);
    setText("minSustain", minuteAvg.sustainability || "--");
    
    // Apply color coding to sustainability score
    const minSustainEl = document.getElementById("minSustain");
    if (minSustainEl) {
      if (minuteAvg.sustainability >= 80) {
        minSustainEl.className = "score-excellent";
      } else if (minuteAvg.sustainability >= SUSTAINABILITY_THRESHOLD) {
        minSustainEl.className = "score-good";
      } else {
        minSustainEl.className = "score-needs-improvement";
      }
    }

    // Hour averages
    setText("hrEnergy", hourAvg.energy.toFixed(2));
    setText("hrCarbon", hourAvg.carbon.toFixed(2));
    setText("hrWater", hourAvg.water.toFixed(0));
    setText("hrPerf", hourAvg.performance);
    setText("hrSustain", hourAvg.sustainability || "--");
    
    // Apply color coding to hourly sustainability score
    const hrSustainEl = document.getElementById("hrSustain");
    if (hrSustainEl) {
      if (hourAvg.sustainability >= 80) {
        hrSustainEl.className = "score-excellent";
      } else if (hourAvg.sustainability >= SUSTAINABILITY_THRESHOLD) {
        hrSustainEl.className = "score-good";
      } else {
        hrSustainEl.className = "score-needs-improvement";
      }
    }
  }

  // Render 7-day table with sustainability score
  function renderDaily(days) {
    const tbody = document.getElementById("dailyBody");
    if (!tbody) return;
    tbody.innerHTML = "";
    days.forEach((day) => {
      const tr = document.createElement("tr");
      
      // Calculate sustainability score if not provided
      const sustainability = day.sustainability || 
        Math.round((100 - day.energy * 20) * 0.6 + (day.performance * 0.5) * 0.4);
      
      // Add class based on sustainability score
      if (sustainability >= 80) {
        tr.className = "row-excellent";
      } else if (sustainability >= SUSTAINABILITY_THRESHOLD) {
        tr.className = "row-good";
      } else {
        tr.className = "row-needs-improvement";
      }
      
      tr.innerHTML = `
          <td>${day.date}</td>
          <td>${parseFloat(day.energy).toFixed(2)}</td>
          <td>${parseFloat(day.carbon).toFixed(2)}</td>
          <td>${parseFloat(day.water).toFixed(0)}</td>
          <td>${Math.round(day.performance)}</td>
          <td>${sustainability}</td>
        `;
      tbody.appendChild(tr);
    });
  }

  // Check and alert if metrics exceed thresholds
  function checkAlert(minuteAvg, hourAvg, sessionInfo) {
    // Check multiple conditions for alerts
    let alertMessage = null;
    let alertType = 'warning';
    let addToReminders = false;
    
    // Check for extremely high power usage (1000W or more)
    if (minuteAvg.energy * 3600 >= 1000) { // Convert Wh to W
      alertMessage = `🚨 CRITICAL: Power usage is extremely high (${(minuteAvg.energy * 3600).toFixed(1)}W)! Immediate action required.`;
      alertType = 'critical';
      addToReminders = true;
    }
    // Check for long session duration (10+ hours)
    else if (sessionInfo && sessionInfo.sessionHours >= 10) {
      alertMessage = `⚠️ System has been running for ${sessionInfo.sessionHours.toFixed(1)} hours. Consider a restart to optimize resources.`;
      alertType = 'warning';
      addToReminders = true;
    }
    // Check for very high metrics
    else if (hourAvg.energy > ENERGY_ALERT_THRESHOLD * 5) {
      alertMessage = `⚠️ Energy usage is very high over the past hour (${hourAvg.energy.toFixed(2)} Wh). Please review system activity.`;
      alertType = 'warning';
      addToReminders = true;
    }
    // Regular energy usage alert
    else if (minuteAvg.energy > ENERGY_ALERT_THRESHOLD) {
      alertMessage = "⚠️ Energy usage is high! Consider switching to dark mode to save energy.";
    }
    // Sustainability score alert
    else if (minuteAvg.sustainability && minuteAvg.sustainability < SUSTAINABILITY_THRESHOLD) {
      alertMessage = `⚠️ Sustainability score is below target (${minuteAvg.sustainability}/${SUSTAINABILITY_THRESHOLD})! Try reducing resource usage.`;
    }
    
    // Show alert if needed (using a more user-friendly approach)
    if (alertMessage && (!localStorage.getItem('lastAlertTime') || 
        (Date.now() - parseInt(localStorage.getItem('lastAlertTime'))) > 3600000)) { // Max once per hour
      
      // Create a custom alert instead of using the native alert
      const alertDiv = document.createElement('div');
      alertDiv.className = `custom-alert ${alertType}`;
      alertDiv.innerHTML = `
        <div class="alert-content ${alertType}">
          <p>${alertMessage}</p>
          <button id="alertClose">Dismiss</button>
        </div>
      `;
      
      document.body.appendChild(alertDiv);
      
      // Add event listener to close button
      document.getElementById('alertClose').addEventListener('click', () => {
        alertDiv.remove();
      });
      
      // Auto-dismiss after 15 seconds
      setTimeout(() => {
        if (document.body.contains(alertDiv)) {
          alertDiv.remove();
        }
      }, 15000);
      
      // Store last alert time
      localStorage.setItem('lastAlertTime', Date.now().toString());
      
      // Add to reminders history if needed
      if (addToReminders) {
        addMetricReminderToHistory(alertMessage, alertType);
      }
    }
  }
  
  // Add a reminder to the reminders history
  async function addMetricReminderToHistory(message, severity) {
    try {
      const timestamp = new Date().toISOString();
      const response = await fetch('reminders.php?action=addReminder', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          reminderType: 'sustainability',
          reminderTitle: `Sustainability Alert: ${severity === 'critical' ? 'CRITICAL' : 'Warning'}`,
          reminderContent: message,
          reminderSeverity: severity,
          reminderDate: timestamp,
          isCompleted: 0
        })
      });
      
      if (!response.ok) {
        console.error('Failed to add reminder:', await response.text());
      }
    } catch (err) {
      console.error('Error adding reminder to history:', err);
    }
  }

  // Calculate improved averages
  function calculateImprovedAverages(data) {
    // Skip null or undefined values in calculations
    const validValues = arr => arr.filter(val => val !== null && val !== undefined && !isNaN(val));
    
    // Helper for weighted average - more recent values have higher weight
    const weightedAvg = (arr, weights) => {
      if (arr.length === 0) return 0;
      let sum = 0;
      let weightSum = 0;
      
      for (let i = 0; i < arr.length; i++) {
        sum += arr[i] * weights[i];
        weightSum += weights[i];
      }
      
      return sum / weightSum;
    };
    
    // Generate weights - more recent values have higher weights
    const generateWeights = length => {
      const weights = [];
      for (let i = 0; i < length; i++) {
        // Weight increases for more recent values
        weights.push(1 + (i / length));
      }
      return weights;
    };
    
    // Extract arrays of values
    const energyValues = validValues(data.map(item => parseFloat(item.energy)));
    const carbonValues = validValues(data.map(item => parseFloat(item.carbon)));
    const waterValues = validValues(data.map(item => parseFloat(item.water)));
    const perfValues = validValues(data.map(item => parseInt(item.performance)));
    const sustainValues = validValues(data.map(item => parseInt(item.sustainability)));
    
    // Generate weights for each array
    const energyWeights = generateWeights(energyValues.length);
    const carbonWeights = generateWeights(carbonValues.length);
    const waterWeights = generateWeights(waterValues.length);
    const perfWeights = generateWeights(perfValues.length);
    const sustainWeights = generateWeights(sustainValues.length);
    
    // Calculate weighted averages
    return {
      energy: weightedAvg(energyValues, energyWeights),
      carbon: weightedAvg(carbonValues, carbonWeights),
      water: weightedAvg(waterValues, waterWeights),
      performance: Math.round(weightedAvg(perfValues, perfWeights)),
      sustainability: Math.round(weightedAvg(sustainValues, sustainWeights))
    };
  }
  
  // Main fetch + render with error handling and improved averages
  async function updateDashboard() {
    try {
      // Show loading indicator
      const loadingIndicator = document.getElementById('loadingIndicator');
      if (loadingIndicator) loadingIndicator.style.display = 'block';
      
      // Get session info
      const sessionStartTime = localStorage.getItem('sessionStartTime');
      let sessionInfo = null;
      
      if (sessionStartTime) {
        const sessionDuration = Date.now() - parseInt(sessionStartTime);
        const sessionHours = sessionDuration / (1000 * 60 * 60);
        sessionInfo = { sessionHours, sessionDuration };
      } else {
        // Initialize session start time if not set
        localStorage.setItem('sessionStartTime', Date.now().toString());
      }
      
      // Fetch with timeout to prevent hanging
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), 5000);
      
      const resp = await fetch(FETCH_URL, { 
        cache: "no-store",
        signal: controller.signal 
      });
      
      clearTimeout(timeoutId);
      
      if (!resp.ok) {
        throw new Error(`Server responded with ${resp.status}: ${resp.statusText}`);
      }
      
      const json = await resp.json();
      
      // Calculate improved averages
      if (json.dailyAvg && json.dailyAvg.length > 0) {
        // Calculate improved averages from the raw data
        const improvedAvg = calculateImprovedAverages(json.dailyAvg);
        
        // Add improved averages to the response
        json.improvedAvg = improvedAvg;
        
        // Display in the console for debugging
        console.log('Original minute average:', json.minuteAvg);
        console.log('Improved average:', improvedAvg);
        
        // Update the UI with both original and improved averages
        renderAverages(json.minuteAvg, json.improvedAvg);
      } else {
        // Fallback to original averages if no daily data
        renderAverages(json.minuteAvg, json.hourAvg);
      }
      
      // Render daily data
      renderDaily(json.dailyAvg);
      
      // Check alerts with session info
      checkAlert(json.minuteAvg, json.hourAvg, sessionInfo);
      
      // Update usage trend visualization if it exists
      updateTrendVisualization(json.dailyAvg);
      
      // Hide loading indicator
      if (loadingIndicator) loadingIndicator.style.display = 'none';
      
      // Update last refresh time
      setText('lastRefreshTime', new Date().toLocaleTimeString());
      
      // Update session info display
      if (sessionInfo) {
        const sessionInfoEl = document.getElementById('sessionInfo');
        if (sessionInfoEl) {
          sessionInfoEl.textContent = `Session duration: ${sessionInfo.sessionHours.toFixed(1)} hours`;
        }
      }
      
    } catch (err) {
      console.error("Failed to update sustainability metrics:", err);
      
      // Hide loading indicator on error
      const loadingIndicator = document.getElementById('loadingIndicator');
      if (loadingIndicator) loadingIndicator.style.display = 'none';
      
      // Show error message to user
      const errorMsg = document.getElementById('errorMessage');
      if (errorMsg) {
        errorMsg.textContent = `Error updating metrics: ${err.message}`;
        errorMsg.style.display = 'block';
        
        // Hide error after 5 seconds
        setTimeout(() => {
          errorMsg.style.display = 'none';
        }, 5000);
      }
    }
  }
  
  // Format a date string into MM/DD format
  async function formatDate(dateString) {
    const date = new Date(dateString);
    return `${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getDate().toString().padStart(2, '0')}`;
  }

  // Format a number with appropriate units
  function formatNumber(value, unit, decimals = 1) {
    return `${value.toFixed(decimals)} ${unit}`;
  }

  // Create a visualization of the usage trend with proper axis labels
  function updateTrendVisualization(dailyData) {
    const container = document.getElementById('trendVisualization');
    if (!container) return;
    
    // Clear previous content
    container.innerHTML = '';
    
    // Add title
    const title = document.createElement('h3');
    title.className = 'chart-title';
    title.textContent = 'Energy Usage Trend (Past 7 Days)';
    container.appendChild(title);
    
    // Create chart wrapper with axes
    const chartWrapper = document.createElement('div');
    chartWrapper.className = 'chart-wrapper';
    
    // Add Y-axis label and ticks
    const yAxis = document.createElement('div');
    yAxis.className = 'y-axis';
    
    // Y-axis label
    const yAxisLabel = document.createElement('div');
    yAxisLabel.className = 'axis-label y-axis-label';
    yAxisLabel.textContent = 'Energy (Wh)';
    yAxis.appendChild(yAxisLabel);
    
    // Find max value for scaling
    const maxValue = Math.max(...dailyData.map(d => parseFloat(d.energy))) * 1.2; // Add 20% padding
    // If all values are 0, set a default max value
    const effectiveMaxValue = maxValue === 0 || isNaN(maxValue) ? 1.0 : maxValue;
    
    // Create Y-axis ticks
    const tickCount = 6;
    for (let i = 0; i < tickCount; i++) {
      const value = (effectiveMaxValue / (tickCount - 1)) * i;
      const tick = document.createElement('div');
      tick.className = 'y-tick';
      
      const label = document.createElement('span');
      label.textContent = value.toFixed(2);
      tick.appendChild(label);
      
      yAxis.appendChild(tick);
    }
    
    // Create chart container
    const chart = document.createElement('div');
    chart.className = 'trend-chart';
    
    // Render bars
    dailyData.forEach(day => {
      const barContainer = document.createElement('div');
      barContainer.className = 'bar-container';
      
      const bar = document.createElement('div');
      bar.className = 'trend-bar';
      
      // Calculate height percentage based on max value
      const value = parseFloat(day.energy);
      const heightPercent = (value / effectiveMaxValue) * 100;
      bar.style.setProperty('--bar-height', `${heightPercent}%`);
      
      // Add data value as attribute for tooltip
      bar.setAttribute('data-value', `${value.toFixed(2)} Wh`);
      
      // Add date label for X-axis
      const label = document.createElement('div');
      label.className = 'trend-label';
      
      // Format date as Month-Day
      const dateParts = day.date.split('-');
      const formattedDate = `${dateParts[1]}/${dateParts[2]}`;
      label.textContent = formattedDate;
      
      barContainer.appendChild(bar);
      barContainer.appendChild(label);
      chart.appendChild(barContainer);
    });
    
    // X-axis label
    const xAxisLabel = document.createElement('div');
    xAxisLabel.className = 'axis-label x-axis-label';
    xAxisLabel.textContent = 'Date (MM/DD)';
    
    // Assemble the chart
    chartWrapper.appendChild(yAxis);
    chartWrapper.appendChild(chart);
    container.appendChild(chartWrapper);
    container.appendChild(xAxisLabel);
    
    // Add legend
    const legend = document.createElement('div');
    legend.className = 'chart-legend';
    legend.innerHTML = '<span class="legend-item">Daily Energy Consumption</span>';
    container.appendChild(legend);
  }

  // Track page visibility for polling
  let isPageVisible = true;
  document.addEventListener("visibilitychange", () => {
    isPageVisible = document.visibilityState === "visible";
  });
  
  // Initialize with adaptive polling
  document.addEventListener("DOMContentLoaded", () => {
    // Create loading indicator if it doesn't exist
    if (!document.getElementById('loadingIndicator')) {
      const indicator = document.createElement('div');
      indicator.id = 'loadingIndicator';
      indicator.className = 'loading-indicator';
      indicator.innerHTML = 'Updating metrics...';
      indicator.style.display = 'none';
      document.querySelector('.container').appendChild(indicator);
    }
    
    // Create error message container if it doesn't exist
    if (!document.getElementById('errorMessage')) {
      const errorMsg = document.createElement('div');
      errorMsg.id = 'errorMessage';
      errorMsg.className = 'error-message';
      errorMsg.style.display = 'none';
      document.querySelector('.container').appendChild(errorMsg);
    }
    
    // Create last refresh time indicator
    if (!document.getElementById('lastRefreshTime')) {
      const refreshContainer = document.createElement('div');
      refreshContainer.className = 'refresh-info';
      refreshContainer.innerHTML = 'Last updated: <span id="lastRefreshTime">-</span>';
      document.querySelector('.container').appendChild(refreshContainer);
    }
    
    // Initial update
    updateDashboard();
    
    // Set up adaptive polling interval based on page visibility
    function setupPolling() {
      if (isPageVisible) {
        return setInterval(updateDashboard, POLL_INTERVAL);
      } else {
        return setInterval(updateDashboard, BACKGROUND_POLL_INTERVAL);
      }
    }
    
    let intervalId = setupPolling();
    
    // Update polling interval when visibility changes
    document.addEventListener("visibilitychange", () => {
      clearInterval(intervalId);
      intervalId = setupPolling();
      
      // Update immediately when page becomes visible again
      if (isPageVisible) {
        updateDashboard();
      }
    });
  });
})();

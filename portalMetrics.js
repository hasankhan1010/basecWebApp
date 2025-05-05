// portalMetrics.js
// Real-time sustainability: compute, render, and persist metrics every second
// Improved to be more accurate, increase with usage, and continue updating when not on the page

(function () {
  const CONTAINER = ".portal-metrics";
  const POLL_INTERVAL = 1000; // 1 second
  const BACKGROUND_POLL_INTERVAL = 5000; // 5 seconds when tab is not active
  const ENERGY_PER_KB = 0.00015; // Wh per KB transferred (increased for accuracy)
  const ENERGY_PER_PX = 0.000015; // Wh per pixel moved (increased for accuracy)
  const ENERGY_PER_CLICK = 0.0002; // Wh per click
  const ENERGY_PER_KEYPRESS = 0.0001; // Wh per keypress
  const ENERGY_IDLE_BASE = 0.00005; // Base energy consumption per second when idle
  const SESSION_TIME_FACTOR = 0.000002; // Additional energy per second based on session time
  const DARK_MULT = 0.75; // multiplier in dark mode (improved efficiency)
  const LIGHT_MULT = 1.25; // multiplier in light mode (slightly higher)
  const CARBON_PER_WH = 0.0005; // kg CO₂ per Wh
  const WATER_ML_PER_WH = 200; // mL per Wh

  // Metric definitions
  const METRICS = [
    { key: "power", label: "Power (W)" },
    { key: "carbon", label: "Carbon Offset (kg CO₂)" },
    { key: "water", label: "Water Usage (mL)" },
    { key: "performance", label: "Performance (0–100)" },
    { key: "sustainability", label: "Sustainability Score" },
  ];
  const elems = {};
  
  // Session tracking
  const sessionStart = Date.now();
  let sessionDuration = 0;
  let totalClicks = 0;
  let totalKeyPresses = 0;
  let cumulativeEnergy = 0;
  let cumulativeCarbon = 0;
  let cumulativeWater = 0;
  let lastUpdateTime = Date.now();
  let isPageVisible = true;

  // Build metric cards in the DOM
  function buildUI() {
    const root = document.querySelector(CONTAINER);
    if (!root) return;
    root.innerHTML = "";
    const grid = document.createElement("div");
    grid.className = "metrics-grid";
    METRICS.forEach((m) => {
      const card = document.createElement("div");
      card.className = "metric-card";
      card.innerHTML = `
          <div class="metric-label">${m.label}</div>
          <div class="metric-value">--</div>
        `;
      grid.appendChild(card);
      elems[m.key] = card.querySelector(".metric-value");
    });
    root.appendChild(grid);
  }

  // Performance score (0–100) from navigation timing and other factors
  function getPerfScore() {
    // Get basic load time performance
    let perfScore;
    try {
      // Modern performance API
      const navEntries = performance.getEntriesByType("navigation");
      if (navEntries && navEntries.length > 0) {
        const loadTime = navEntries[0].domContentLoadedEventEnd - navEntries[0].startTime;
        perfScore = 100 - (loadTime - 100) / 30;
      } else {
        // Fallback to older API
        const t = performance.timing;
        const dt = t.domContentLoadedEventEnd - t.navigationStart;
        perfScore = 100 - (dt - 100) / 30;
      }
    } catch (e) {
      // Default if performance API fails
      perfScore = 85;
    }
    
    // Adjust based on memory usage if available
    if (performance.memory) {
      const memoryFactor = 1 - (performance.memory.usedJSHeapSize / performance.memory.jsHeapSizeLimit) * 0.2;
      perfScore *= memoryFactor;
    }
    
    // Adjust based on session duration (longer sessions slightly decrease performance)
    const sessionHours = sessionDuration / 3600000;
    const sessionFactor = 1 - Math.min(sessionHours * 0.01, 0.1); // Max 10% reduction
    perfScore *= sessionFactor;
    
    return Math.max(0, Math.min(100, Math.round(perfScore)));
  }

  // Cumulative KB transferred this session
  function getPageKB() {
    const entries = performance.getEntriesByType("resource");
    let bytes = 0;
    entries.forEach((r) => {
      if (r.transferSize) bytes += r.transferSize;
    });
    const nav = performance.getEntriesByType("navigation")[0];
    if (nav && nav.transferSize) bytes += nav.transferSize;
    return bytes / 1024;
  }

  // Track mouse movement
  let lastX = null,
    lastY = null,
    totalPx = 0;
  document.addEventListener("mousemove", (e) => {
    if (lastX !== null) {
      totalPx += Math.hypot(e.clientX - lastX, e.clientY - lastY);
    }
    lastX = e.clientX;
    lastY = e.clientY;
  });

  // Track clicks
  document.addEventListener("click", () => {
    totalClicks++;
  });

  // Track keypresses
  document.addEventListener("keydown", () => {
    totalKeyPresses++;
  });
  
  // Track page visibility
  document.addEventListener("visibilitychange", () => {
    isPageVisible = document.visibilityState === "visible";
    // When page becomes visible again, update metrics immediately
    if (isPageVisible) {
      updateMetrics();
    }
  });

  // State for deltas
  let prevKB = 0,
    prevPx = 0,
    prevClicks = 0,
    prevKeyPresses = 0;
    
  // Calculate sustainability score based on various factors
  function getSustainabilityScore(power, perf) {
    // Base score from inverse of power usage (lower power = higher score)
    const powerScore = Math.max(0, 100 - power * 20);
    
    // Performance contribution (higher performance = higher score)
    const perfContribution = perf * 0.5;
    
    // Session factor - rewards consistent usage
    const sessionMinutes = sessionDuration / 60000;
    const sessionBonus = Math.min(sessionMinutes * 0.5, 10); // Max 10 points bonus
    
    // Calculate final score with weights
    const rawScore = (powerScore * 0.6) + (perfContribution * 0.3) + sessionBonus;
    
    // Ensure score is between 0-100
    return Math.max(0, Math.min(100, Math.round(rawScore)));
  }

  // Main update loop: calculate, render, persist
  async function updateMetrics() {
    // Calculate time since last update
    const now = Date.now();
    const timeDelta = (now - lastUpdateTime) / 1000; // in seconds
    lastUpdateTime = now;
    
    // Update session duration
    sessionDuration = now - sessionStart;
    
    // 1) Compute deltas
    const kb = getPageKB();
    const deltaKB = Math.max(0, kb - prevKB);
    prevKB = kb;

    const px = totalPx;
    const deltaPx = Math.max(0, px - prevPx);
    prevPx = px;
    
    const deltaClicks = totalClicks - prevClicks;
    prevClicks = totalClicks;
    
    const deltaKeyPresses = totalKeyPresses - prevKeyPresses;
    prevKeyPresses = totalKeyPresses;

    // 2) Compute energy in Wh this update period
    // Base energy from user interactions
    let wh = (deltaKB * ENERGY_PER_KB) + 
             (deltaPx * ENERGY_PER_PX) + 
             (deltaClicks * ENERGY_PER_CLICK) + 
             (deltaKeyPresses * ENERGY_PER_KEYPRESS);
    
    // Add idle energy consumption based on time
    const idleEnergy = ENERGY_IDLE_BASE * timeDelta;
    
    // Add session duration factor - energy increases slightly the longer the session runs
    const sessionFactor = SESSION_TIME_FACTOR * (sessionDuration / 1000);
    
    // Total energy for this period
    wh += idleEnergy + (sessionFactor * timeDelta);
    
    // Apply dark/light mode multiplier
    const isDark = document.body.classList.contains("dark-mode");
    wh *= isDark ? DARK_MULT : LIGHT_MULT;

    // 3) Convert to power (W)
    const power = wh * 3600 / timeDelta; // Adjust for time period

    // 4) Carbon offset, with progressive factor
    const carbon = wh * CARBON_PER_WH * (1 + (cumulativeEnergy * 0.00001));

    // 5) Water usage (mL)
    const water = wh * WATER_ML_PER_WH;

    // 6) Performance score - more accurate calculation
    const perf = getPerfScore();
    
    // 7) Calculate sustainability score
    const sustainability = getSustainabilityScore(power, perf);
    
    // 8) Update cumulative values
    cumulativeEnergy += wh;
    cumulativeCarbon += carbon;
    cumulativeWater += water;

    // 9) Render to screen if the container exists
    if (document.querySelector(CONTAINER)) {
      elems.power.textContent = power.toFixed(1);
      elems.carbon.textContent = carbon.toFixed(3);
      elems.water.textContent = water.toFixed(0);
      elems.performance.textContent = perf;
      elems.sustainability.textContent = sustainability;
    }
    
    // Check if power exceeds 1000 watts and show notification
    if (power >= 1000) {
      showHighPowerAlert(power);
    }

    // 10) Persist to database even if not on the page
    try {
      await fetch("portal.php?action=addMetric", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          energyUsageWh: wh,
          carbonOffsetKg: carbon,
          waterUsageMl: water,
          performanceScore: perf,
          sustainabilityScore: sustainability,
          metricNotes: isPageVisible ? "active-session" : "background-session",
        }),
      });
    } catch (err) {
      console.warn("Failed to persist metrics:", err);
    }
  }
  
  // Show high power usage alert
  function showHighPowerAlert(power) {
    console.log(`High power alert triggered: ${power.toFixed(1)}W`);
    
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
    addToReminderHistory(alertMessage, power);
  }
  
  // Add high power alert to reminder history
  function addToReminderHistory(message, power) {
    console.log('Adding high power alert to reminder history');
    
    // Format current date
    const now = new Date();
    const formattedDate = now.toISOString();
    
    // Create reminder data
    const reminderData = {
      reminderType: 'sustainability',
      reminderTitle: 'Sustainability Alert: CRITICAL',
      reminderContent: message,
      reminderSeverity: 'critical',
      reminderDate: formattedDate,
      dueDate: formattedDate,
      isCompleted: 0,
      isAnnual: 0
    };
    
    // Send to server
    fetch('reminders.php?action=addReminder', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(reminderData)
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to add reminder');
      }
      return response.json();
    })
    .then(data => {
      console.log('Successfully added reminder:', data);
    })
    .catch(error => {
      console.error('Error adding reminder to history:', error);
    });
  }

  // Initialize on page load
  document.addEventListener("DOMContentLoaded", () => {
    buildUI();
    updateMetrics();
    
    // Set up adaptive polling interval based on page visibility
    function setupPolling() {
      if (isPageVisible) {
        return setInterval(updateMetrics, POLL_INTERVAL);
      } else {
        return setInterval(updateMetrics, BACKGROUND_POLL_INTERVAL);
      }
    }
    
    let intervalId = setupPolling();
    
    // Update polling interval when visibility changes
    document.addEventListener("visibilitychange", () => {
      clearInterval(intervalId);
      intervalId = setupPolling();
    });
    
    // Recalculate on theme toggle
    document.body.addEventListener("themeChanged", updateMetrics);
    
    // Ensure metrics are sent before page unload
    window.addEventListener("beforeunload", () => {
      // Synchronous fetch to ensure data is sent
      navigator.sendBeacon("portal.php?action=addMetric", JSON.stringify({
        energyUsageWh: cumulativeEnergy / 3600, // Convert to Wh
        carbonOffsetKg: cumulativeCarbon,
        waterUsageMl: cumulativeWater,
        performanceScore: getPerfScore(),
        sustainabilityScore: getSustainabilityScore(cumulativeEnergy * 3600, getPerfScore()),
        metricNotes: "session-end",
      }));
    });
  });
})();

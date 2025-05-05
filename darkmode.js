// darkmode.js
// Handles dark mode functionality across all pages

(function () {
  const STORAGE_KEY = "theme";
  const DARK_CLASS = "dark-mode";
  const TOGGLE_ID = "darkModeToggle";

  // Apply or remove the dark-mode class on <body>
  function applyTheme(isDark) {
    document.body.classList.toggle(DARK_CLASS, isDark);
  }

  document.addEventListener("DOMContentLoaded", () => {
    // Check if the darkmode toggle exists (from included component)
    let toggle = document.getElementById(TOGGLE_ID);
    
    // If toggle doesn't exist, inject it in the nav-left section
    if (!toggle) {
      const navLeft = document.querySelector(".nav-left");
      if (navLeft) {
        // Create and inject the toggle component
        const wrapper = document.createElement("div");
        wrapper.className = "dark-mode-toggle";
        wrapper.innerHTML = `
          <label class="toggle-switch">
            <input type="checkbox" id="${TOGGLE_ID}">
            <span class="slider"></span>
          </label>
          <span class="toggle-label">Dark Mode</span>
        `;
        navLeft.appendChild(wrapper);
        
        // Get the newly created toggle
        toggle = document.getElementById(TOGGLE_ID);
      }
    }

    // Initialize toggle state from localStorage
    if (toggle) {
      const saved = localStorage.getItem(STORAGE_KEY) === "dark";
      applyTheme(saved);
      toggle.checked = saved;

      // Listen for changes
      toggle.addEventListener("change", () => {
        const isDark = toggle.checked;
        applyTheme(isDark);
        localStorage.setItem(STORAGE_KEY, isDark ? "dark" : "light");
      });
    }
    
    // Always check and apply the theme even if toggle doesn't exist
    const isDark = localStorage.getItem(STORAGE_KEY) === "dark";
    applyTheme(isDark);
  });
})();

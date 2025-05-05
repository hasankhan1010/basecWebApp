<div class="dark-mode-toggle">
    <span class="toggle-label">Dark Mode</span>
    <label class="toggle-switch">
        <input type="checkbox" id="darkModeToggle">
        <span class="slider"></span>
    </label>
</div>

<style>
    .dark-mode-toggle {
        display: flex;
        align-items: center;
        margin-left: 20px;
        padding: 5px 10px;
        border-radius: 4px;
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    .dark-mode .dark-mode-toggle {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .nav-left .dark-mode-toggle {
        margin-left: auto;
        margin-right: 15px;
    }
    
    .dark-mode-toggle .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
        margin-left: 8px;
    }
    
    .dark-mode-toggle .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .dark-mode-toggle .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .3s;
        border-radius: 20px;
    }
    
    .dark-mode-toggle .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    
    .dark-mode-toggle input:checked + .slider {
        background-color: #ffa500;
    }
    
    .dark-mode-toggle input:focus + .slider {
        box-shadow: 0 0 1px #ffa500;
    }
    
    .dark-mode-toggle input:checked + .slider:before {
        transform: translateX(20px);
    }
    
    .dark-mode-toggle .toggle-label {
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }
    
    .dark-mode .dark-mode-toggle .toggle-label {
        color: #eee;
    }
</style>

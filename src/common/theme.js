// Theme management
function initializeTheme() {
    // Check if user has a saved preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light') {
        document.documentElement.classList.add('light-mode');
    }
    
    // Create and append the theme toggle button
    const themeBtn = document.createElement('button');
    themeBtn.id = 'theme-toggle';
    themeBtn.className = 'styled-btn theme-btn';
    themeBtn.innerHTML = document.documentElement.classList.contains('light-mode') ? '🌙' : '☀️';
    
    // Add button styles
    const style = document.createElement('style');
    style.textContent = `
        .theme-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 25px;
            font-size: 1.5rem;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--theme-transition);
            background: var(--panel);
            border: 1px solid var(--border);
            box-shadow: var(--elev-soft);
        }
        .theme-btn:hover {
            transform: scale(1.1);
        }
    `;
    document.head.appendChild(style);
    
    // Add click handler
    themeBtn.addEventListener('click', () => {
        document.documentElement.classList.toggle('light-mode');
        themeBtn.innerHTML = document.documentElement.classList.contains('light-mode') ? '🌙' : '☀️';
        localStorage.setItem('theme', 
            document.documentElement.classList.contains('light-mode') ? 'light' : 'dark'
        );
    });
    
    document.body.appendChild(themeBtn);
}

// Initialize theme when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeTheme);
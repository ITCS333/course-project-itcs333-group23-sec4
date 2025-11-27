// Theme management
function initializeTheme() {
    // Check if user has a saved preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
    } else if (savedTheme === 'dark') {
        document.documentElement.removeAttribute('data-theme');
    }

    // Create and append the theme toggle button (fixed fallback)
    if (!document.getElementById('theme-toggle')) {
        const themeBtn = document.createElement('button');
        themeBtn.id = 'theme-toggle';
        themeBtn.className = 'btn btn--rounded theme-btn';
        themeBtn.setAttribute('aria-label', 'Toggle theme');
        // Show sun for dark mode (to switch to light), moon for light mode
        const isLight = document.documentElement.getAttribute('data-theme') === 'light';
        themeBtn.textContent = isLight ? '🌙' : '☀️';

        // Add small scoped styles so pages don't need edits
        const style = document.createElement('style');
        style.textContent = `
            .theme-btn {
                width: 44px;
                height: 44px;
                border-radius: 999px;
                font-size: 1.05rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: transform 0.12s ease, box-shadow 0.12s ease;
                background: var(--surface);
                border: 1px solid var(--border-color);
                box-shadow: var(--shadow-sm);
            }
            .theme-btn:hover { transform: scale(1.05); }
            /* Inline placement inside nav/header */
            .theme-btn-inline { margin-left: 1rem; }
            /* Floating fallback */
            .theme-btn-float { position: fixed; bottom: 20px; right: 20px; z-index: 1000; }
        `;
        document.head.appendChild(style);

        themeBtn.addEventListener('click', () => {
            const isNowLight = document.documentElement.getAttribute('data-theme') !== 'light';
            if (isNowLight) {
                document.documentElement.setAttribute('data-theme', 'light');
                themeBtn.textContent = '🌙';
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.removeAttribute('data-theme');
                themeBtn.textContent = '☀️';
                localStorage.setItem('theme', 'dark');
            }
        });

        // Try to place the toggle inside the site's nav/header if present
        const headerTarget = document.querySelector('nav') || document.querySelector('.index-hero .hero-inner');
        if (headerTarget) {
            themeBtn.classList.add('theme-btn-inline');
            // prefer placing at the end of nav (right side)
            if (headerTarget.querySelector) {
                // create a container if necessary
                let actions = headerTarget.querySelector('.site-actions');
                if (!actions) {
                    actions = document.createElement('div');
                    actions.className = 'site-actions';
                    actions.style.display = 'flex';
                    actions.style.alignItems = 'center';
                    headerTarget.appendChild(actions);
                }
                actions.appendChild(themeBtn);
            } else {
                headerTarget.appendChild(themeBtn);
            }
        } else {
            // fallback: floating button
            themeBtn.classList.add('theme-btn-float');
            document.body.appendChild(themeBtn);
        }
    }
}

// Initialize theme when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeTheme);
const storedTheme = localStorage.getItem('theme');
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
const theme = storedTheme ? storedTheme : (prefersDark ? 'dark' : 'light');
document.documentElement.setAttribute('data-theme', theme);

document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) return;

    const themeIcon = themeToggle.querySelector('i');
    if (!themeIcon) return;

    const themeText = themeToggle.querySelector('.nav-text');

    let currentTheme = document.documentElement.getAttribute('data-theme') || 'light';

    if (currentTheme === 'dark') {
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        themeToggle.title = 'Switch to light mode.';
        if (themeText) {
            themeText.textContent = 'Light Mode';
        }
    }

    themeToggle.addEventListener('click', () => {
        currentTheme = currentTheme === 'light' ? 'dark' : 'light';

        document.documentElement.classList.add('theme-transition');
        document.documentElement.setAttribute('data-theme', currentTheme);
        localStorage.setItem('theme', currentTheme);

        setTimeout(() => {
            document.documentElement.classList.remove('theme-transition');
        }, 300);

        if (currentTheme === 'dark') {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
            themeToggle.title = 'Switch to light mode.';
            if (themeText) {
                themeText.textContent = 'Light Mode';
            }
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            themeToggle.title = 'Switch to dark mode.';
            if (themeText) {
                themeText.textContent = 'Dark Mode';
            }
        }
    });
});

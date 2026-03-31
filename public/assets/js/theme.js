(function () {
    var STORAGE_KEY = 'ui_theme';

    function prefersLight() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches;
    }

    function getTheme() {
        var saved = localStorage.getItem(STORAGE_KEY);
        if (saved === 'light' || saved === 'dark') {
            return saved;
        }
        return prefersLight() ? 'light' : 'dark';
    }

    function setTheme(theme) {
        var isLight = theme === 'light';
        document.body.classList.toggle('theme-light', isLight);
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);
        updateButtons(theme);
    }

    function updateButtons(theme) {
        var buttons = document.querySelectorAll('[data-theme-toggle]');
        var title = theme === 'light' ? 'Usar modo escuro' : 'Usar modo claro';
        buttons.forEach(function (button) {
            button.setAttribute('title', title);
        });
    }

    function toggleTheme() {
        var current = document.documentElement.getAttribute('data-theme') || getTheme();
        setTheme(current === 'light' ? 'dark' : 'light');
    }

    document.addEventListener('DOMContentLoaded', function () {
        setTheme(getTheme());
        document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
            button.addEventListener('click', toggleTheme);
        });
    });
})();

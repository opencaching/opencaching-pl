<?php
/**
 * This chunk is used to manage and load dark mode functionality, including:
 * * - Handling theme preferences (light, dark, auto)
 * * - Persisting the user's theme choice in cookies and localStorage
 * * - Dynamically loading or removing the DarkReader library for dark mode
 *
 * The only thing to load darkmode (this chunk) in template header is to call:
 *
 *     $view->loadDarkmode()
 *
 */

return function () {
    //start of chunk
    ?>

    <!-- darkmode chunk -->
    <script src="/js/libs/@mahozad/theme-switch/theme-switch.min.js"></script>
    <script type="text/javascript">
        function setCookie(name, value, days) {
            const expires = new Date(Date.now() + days * 864e5).toUTCString();
            document.cookie = `${name}=${value}; expires=${expires}; path=/`;
        }

        function getCookie(name) {
            return document.cookie.split('; ').find(row => row.startsWith(name + '='))?.split('=')[1];
        }

        const savedTheme = getCookie('theme');
        if (savedTheme) {
            localStorage.setItem('theme', savedTheme);
            if (savedTheme === 'dark') {
                loadDarkReader();
            } else if (savedTheme === 'auto') {
                const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (isDarkMode) loadDarkReader();
            }
        }

        document.addEventListener("themeToggle", event => {
            currentMode = event.detail.newState;

            setCookie('theme', currentMode, 30);

            if (currentMode === 'dark') {
                loadDarkReader();
            } else if (currentMode === 'light') {
                removeDarkReader();
                removeMask();
            } else if (currentMode === 'auto') {
                const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                removeMask();
                if (isDarkMode) {
                    loadDarkReader();
                } else {
                    removeDarkReader();
                }
            }

        });

        function loadDarkReader() {
            if (!document.getElementById('darkreader-script')) {
                const script = document.createElement('script');
                script.id = 'darkreader-script';
                script.src = "/js/libs/darkreader/darkreader.min.js";
                script.onload = () => DarkReader.enable();
                document.head.appendChild(script);
            }
        }

        function removeDarkReader() {
            const script = document.getElementById('darkreader-script');
            if (script) script.remove();
            if (window.DarkReader) DarkReader.disable();
        }

        function removeMask() {
            const darkModeMask = document.getElementById("dark-mode-mask");
            if (darkModeMask) {
                darkModeMask.remove();
            }
        }

    </script>
    <!-- End of darkmode chunk -->

    <?php
}; //end of chunk

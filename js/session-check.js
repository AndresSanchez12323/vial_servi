// public/js/session-check.js

const checkSession = () => {
    fetch('session.php')
        .then(response => response.text())
        .then(data => {
            if (data.includes('No active session')) {
                window.location.href = 'index.php';
            }
        })
        .catch(error => console.error('Error al validar la sesión:', error));
};

checkSession();

const beforeUnloadHandler = (event) => {
    if (event.target.activeElement?.hasAttribute('data-no-warning')) return;
    navigator.sendBeacon('session.php', new URLSearchParams({ logout: 'true' }));
};

window.addEventListener('beforeunload', beforeUnloadHandler);

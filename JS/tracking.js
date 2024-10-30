let timeout;

function resetTimer() {
    clearTimeout(timeout);
    timeout = setTimeout(logout, 60000); // 30 minutes in milliseconds
}

function logout() {
    window.location.href = 'sessiontimeout.php';
}

// Reset timer on any of these events
window.onload = resetTimer;
window.onmousemove = resetTimer;
window.onmousedown = resetTimer; // catches touchscreen presses
window.ontouchstart = resetTimer;
window.onclick = resetTimer;     // catches touchpad clicks
window.onkeypress = resetTimer;
window.addEventListener('scroll', resetTimer, true); // improved; see comments


function checkSession() {
    fetch('check_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.timeout) {
                window.location.href = 'session_timeout.php';
            }
        })
        .catch(error => console.error('Error:', error));
}

// Check session status every minute (60000 milliseconds)
setInterval(checkSession, 30000);

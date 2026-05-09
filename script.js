function joinClub() {
    window.location.href = "login.php?action=signup";
}

function signIn() {
    window.location.href = "login.php?action=login";
}

function toggleAuth(type) {
    if (type === 'login') {
        document.getElementById('login-box').style.display = 'block';
        document.getElementById('signup-box').style.display = 'none';
    } else {
        document.getElementById('login-box').style.display = 'none';
        document.getElementById('signup-box').style.display = 'block';
    }
}

window.onload = function () {
    // Handle URL param to switch between login/signup on the login page
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'signup') {
        if (document.getElementById('signup-box')) {
            toggleAuth('signup');
        }
    }
}

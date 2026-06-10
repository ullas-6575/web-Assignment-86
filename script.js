function joinClub() {
    window.location.href = "login.php?action=signup";
}

function signIn() {
    window.location.href = "login.php?action=login";
}

/* Toggle between login / signup with a fade-slide animation */
function toggleAuth(type) {
    var loginBox = document.getElementById('login-box');
    var signupBox = document.getElementById('signup-box');
    if (!loginBox || !signupBox) return;

    if (type === 'login') {
        // fade out signup
        signupBox.classList.add('auth-fade-out');
        setTimeout(function () {
            signupBox.style.display = 'none';
            signupBox.classList.remove('auth-fade-out');
            loginBox.style.display = 'block';
            loginBox.classList.add('auth-fade-in');
            setTimeout(function () { loginBox.classList.remove('auth-fade-in'); }, 300);
        }, 200);
    } else {
        // fade out login
        loginBox.classList.add('auth-fade-out');
        setTimeout(function () {
            loginBox.style.display = 'none';
            loginBox.classList.remove('auth-fade-out');
            signupBox.style.display = 'block';
            signupBox.classList.add('auth-fade-in');
            setTimeout(function () { signupBox.classList.remove('auth-fade-in'); }, 300);
        }, 200);
    }
}

/* Show inline message */
function showMessage(elementId, text, isError) {
    var el = document.getElementById(elementId);
    if (!el) return;
    el.textContent = text;
    el.style.color = isError ? '#e74c3c' : '#27ae60';
    el.style.display = 'block';
    el.classList.add('msg-pop');
    setTimeout(function () { el.classList.remove('msg-pop'); }, 400);
}

window.onload = function () {
    /*  URL param: open signup box directly  */
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'signup') {
        var loginBox = document.getElementById('login-box');
        var signupBox = document.getElementById('signup-box');
        if (loginBox) loginBox.style.display = 'none';
        if (signupBox) {
            signupBox.style.display = 'block';
            signupBox.classList.add('auth-fade-in');
            setTimeout(function () { signupBox.classList.remove('auth-fade-in'); }, 300);
        }
    }

    /*  Animate any server-rendered message already visible  */
    ['login-message', 'signup-message'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el && el.style.display === 'block' && el.textContent.trim() !== '') {
            el.classList.add('msg-pop');
            setTimeout(function () { el.classList.remove('msg-pop'); }, 400);
        }
    });
};

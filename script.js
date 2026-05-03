function joinClub() {
    window.location.href = "login.html?action=signup";
}

function signIn() {
    window.location.href = "login.html?action=login";
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

// Show message in the auth form
function showMessage(elementId, text, isError) {
    var el = document.getElementById(elementId);
    if (el) {
        el.textContent = text;
        el.style.color = isError ? '#e74c3c' : '#27ae60';
        el.style.display = 'block';
    }
}

window.onload = function () {
    // Handle URL param to switch between login/signup
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'signup') {
        if (document.getElementById('signup-box')) {
            toggleAuth('signup');
        }
    }

    // --- Sign Up Form Handler ---
    var signupForm = document.getElementById('signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(signupForm);
            var submitBtn = signupForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating...';

            fetch('signup.php', {
                method: 'POST',
                body: formData
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                showMessage('signup-message', data.message, !data.success);
                if (data.success) {
                    signupForm.reset();
                }
            })
            .catch(function () {
                showMessage('signup-message', 'Connection error. Make sure you are running a PHP server.', true);
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Account';
            });
        });
    }

    // --- Login Form Handler ---
    var loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(loginForm);
            var submitBtn = loginForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Signing in...';

            fetch('login_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                showMessage('login-message', data.message, !data.success);
                if (data.success) {
                    localStorage.setItem('loggedInUser', data.fullname);
                    localStorage.setItem('loggedInUsername', data.username);
                }
            })
            .catch(function () {
                showMessage('login-message', 'Connection error. Make sure you are running a PHP server.', true);
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Login';
            });
        });
    }
}

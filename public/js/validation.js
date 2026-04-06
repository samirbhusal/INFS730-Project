/**
 * Project730 — Client-Side Validation
 * ------------------------------------
 * Shared validation helpers used by login and activation pages.
 * Each page calls the init function it needs after this script loads.
 */

/* ── Helpers ──────────────────────────────────── */

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isStrongPassword(password) {
    return password.length >= 6;
}

/* ── Password Visibility Toggle ───────────────── */

function initPasswordToggles() {
    document.querySelectorAll('[data-pw-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var inputId = btn.getAttribute('data-pw-toggle');
            var input   = document.getElementById(inputId);
            var eyeOn   = btn.querySelector('.eye-icon');
            var eyeOff  = btn.querySelector('.eye-off-icon');
            if (!input) return;

            if (input.type === 'password') {
                input.type           = 'text';
                eyeOn.style.display  = '';
                eyeOff.style.display = 'none';
                btn.setAttribute('aria-label', 'Hide password');
            } else {
                input.type           = 'password';
                eyeOn.style.display  = 'none';
                eyeOff.style.display = '';
                btn.setAttribute('aria-label', 'Show password');
            }
        });
    });
}

// Run immediately if DOM is ready, otherwise wait
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPasswordToggles);
} else {
    initPasswordToggles();
}

/* ── Login Page Validation ────────────────────── */

function initLoginValidation() {
    var emailInput    = document.getElementById('email');
    var passwordInput = document.getElementById('password');
    var signInBtn     = document.getElementById('signInBtn');
    var emailError    = document.getElementById('emailError');

    if (!emailInput || !passwordInput || !signInBtn) return;

    function updateButtonState() {
        var emailFilled    = emailInput.value.trim() !== '';
        var passwordFilled = passwordInput.value.trim() !== '';
        signInBtn.disabled = !(emailFilled && passwordFilled);
    }

    emailInput.addEventListener('input', function () {
        var val = emailInput.value.trim();
        if (emailError) {
            emailError.textContent = (val !== '' && !isValidEmail(val))
                ? 'Please enter a valid email address.'
                : '';
        }
        updateButtonState();
    });

    passwordInput.addEventListener('input', updateButtonState);

    var form = document.getElementById('loginForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!isValidEmail(emailInput.value.trim())) {
                if (emailError) emailError.textContent = 'Please enter a valid email address.';
                e.preventDefault();
            }
        });
    }

    window.addEventListener('load', updateButtonState);
}

/* ── Activation Page Validation ───────────────── */

function initActivationValidation() {
    var passwordInput = document.getElementById('password');
    var confirmInput  = document.getElementById('confirm_password');
    var passwordRules = document.getElementById('passwordRules');
    var matchMessage  = document.getElementById('matchMessage');
    var activateBtn   = document.getElementById('activateBtn');

    if (!passwordInput || !confirmInput || !activateBtn) return;

    function updateValidation() {
        var password = passwordInput.value;
        var confirm  = confirmInput.value;
        var strong   = isStrongPassword(password);
        var matches  = password !== '' && confirm !== '' && password === confirm;

        if (passwordRules) {
            if (password === '') {
                passwordRules.textContent = 'Must be at least 6 characters.';
                passwordRules.className   = 'field-note';
            } else if (strong) {
                passwordRules.textContent = 'Password length OK.';
                passwordRules.className   = 'field-note success-text';
            } else {
                passwordRules.textContent = 'Password must be at least 6 characters.';
                passwordRules.className   = 'field-note error-text';
            }
        }

        if (matchMessage) {
            if (confirm === '') {
                matchMessage.textContent = '';
                matchMessage.className   = 'field-note';
            } else if (matches) {
                matchMessage.textContent = 'Passwords match.';
                matchMessage.className   = 'field-note success-text';
            } else {
                matchMessage.textContent = 'Passwords do not match.';
                matchMessage.className   = 'field-note error-text';
            }
        }

        activateBtn.disabled = !(strong && matches);
    }

    passwordInput.addEventListener('input', updateValidation);
    confirmInput.addEventListener('input', updateValidation);
}

/* ── Forgot Password Validation ───────────────── */

function initForgotPasswordValidation() {
    var emailInput = document.getElementById('email');
    var resetBtn   = document.getElementById('resetBtn');
    var emailError = document.getElementById('emailError');

    if (!emailInput || !resetBtn) return;

    function updateButtonState() {
        var val = emailInput.value.trim();
        resetBtn.disabled = !isValidEmail(val);
    }

    emailInput.addEventListener('input', function () {
        var val = emailInput.value.trim();
        if (emailError) {
            emailError.textContent = (val !== '' && !isValidEmail(val))
                ? 'Please enter a valid email address.'
                : '';
        }
        updateButtonState();
    });

    var form = document.getElementById('forgotForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!isValidEmail(emailInput.value.trim())) {
                if (emailError) emailError.textContent = 'Please enter a valid email address.';
                e.preventDefault();
            }
        });
    }
}


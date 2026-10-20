document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.textContent = show ? 'Hide' : 'Show';
            button.setAttribute('aria-pressed', String(show));
            button.setAttribute('aria-label', `${show ? 'Hide' : 'Show'} ${input.id === 'password_confirmation' ? 'confirmed password' : 'password'}`);
        });
    });
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');
    if (password && confirmation) {
        const validateMatch = () => confirmation.setCustomValidity(confirmation.value && confirmation.value !== password.value ? 'Enter the same password in both fields.' : '');
        password.addEventListener('input', validateMatch);
        confirmation.addEventListener('input', validateMatch);
    }
    document.querySelectorAll('[data-submit-form]').forEach(currentForm => {
        currentForm.addEventListener('submit', () => {
            const button = currentForm.querySelector('[type="submit"]');
            if (button) {
                button.disabled = true;
                button.textContent = button.dataset.busyText || 'Please wait…';
            }
        });
    });
    window.addEventListener('pageshow', () => {
        document.querySelectorAll('[data-ready-text]').forEach(button => {
            button.disabled = button.dataset.closed === 'true';
            button.textContent = button.dataset.readyText;
        });
    });
    const activation = document.querySelector('[data-activation-status]');
    if (activation) {
        let checking = false;
        const checkActivation = async () => {
            if (document.hidden || checking) return;
            checking = true;
            try {
                const response = await fetch(activation.dataset.activationStatus, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin', cache: 'no-store'
                });
                if (response.ok && response.headers.get('content-type')?.includes('application/json')) {
                    const status = await response.json();
                    if (status.verified && status.redirect) window.location.replace(status.redirect);
                }
            } catch (_) { /* The manual continue button remains available when offline. */ }
            finally { checking = false; }
        };
        window.setInterval(checkActivation, 10000);
        window.addEventListener('focus', checkActivation);
        document.addEventListener('visibilitychange', checkActivation);
    }
    const errorSummary = document.getElementById('registration-errors');
    if (errorSummary) errorSummary.focus();
});

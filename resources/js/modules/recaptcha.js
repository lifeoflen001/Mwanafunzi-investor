// The provider script and this integration are loaded only when reCAPTCHA is
// enabled. The server-side rule remains authoritative for every form.
const recaptchaConfig = window.mwanafunziRecaptcha;
const recaptchaActionForForm = (form) => {
    const path = new URL(form.action || window.location.href, window.location.origin).pathname;
    if (path.endsWith('/admin/login')) return 'admin_login';
    if (path.endsWith('/login')) return 'login';
    if (path.endsWith('/register')) return 'register';
    if (path.endsWith('/forgot-password') || path.endsWith('/reset-password')) return 'password_reset';
    if (path.endsWith('/contact')) return 'contact';
    if (/\/(blog|journal)\/[^/]+\/comments$/.test(path)) return 'comments';
    if (/\/(financial-academy\/courses|courses)\/[^/]+\/waitlist$/.test(path)) return 'waitlist';
    if (/\/checkout\/(product|course)\/[^/]+$/.test(path)) return 'checkout';
    return null;
};

document.querySelectorAll('form[method="post"]').forEach((form) => {
    const action = recaptchaActionForForm(form);
    if (!action || !recaptchaConfig?.siteKey || form.dataset.recaptchaBound === 'true') return;
    form.dataset.recaptchaBound = 'true';
    let token = form.querySelector('[data-recaptcha-token]');
    if (!token) {
        token = document.createElement('input');
        token.type = 'hidden';
        token.name = 'recaptcha_token';
        token.dataset.recaptchaToken = 'true';
        form.append(token);
    }
    form.addEventListener('submit', (event) => {
        if (form.dataset.recaptchaBypass === 'true') {
            delete form.dataset.recaptchaBypass;
            return;
        }
        if (token.value) return;
        event.preventDefault();
        const status = form.querySelector('[data-recaptcha-status]');
        const submit = event.submitter || form.querySelector('button[type="submit"]');
        submit?.setAttribute('aria-busy', 'true');
        if (status) status.textContent = 'Verifying secure access…';
        const execute = () => window.grecaptcha.execute(recaptchaConfig.siteKey, { action })
            .then((value) => {
                token.value = value;
                form.dataset.recaptchaBypass = 'true';
                form.requestSubmit();
            })
            .catch(() => {
                if (status) status.textContent = 'We could not verify this request. Please try again.';
                submit?.removeAttribute('aria-busy');
            });
        if (window.grecaptcha?.ready) window.grecaptcha.ready(execute);
        else {
            if (status) status.textContent = 'Secure verification is unavailable. Please refresh and try again.';
            submit?.removeAttribute('aria-busy');
        }
    });
});

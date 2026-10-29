// The provider script and this integration are loaded only when reCAPTCHA is
// enabled. The server-side rule remains authoritative for every form.
const recaptchaConfig = () => window.mwanafunziRecaptcha;

// The Google script is loaded asynchronously in the shared layouts. Keep the
// form handler independent from that load order so a quick submit cannot
// reach Laravel with an empty token.
const waitForRecaptcha = (timeout = 15000) => new Promise((resolve, reject) => {
    const startedAt = Date.now();
    const check = () => {
        if (window.grecaptcha?.ready && window.grecaptcha?.execute) {
            resolve(window.grecaptcha);
            return;
        }
        if (Date.now() - startedAt >= timeout) {
            reject(new Error('reCAPTCHA did not become available'));
            return;
        }
        window.setTimeout(check, 100);
    };
    check();
});

const requestToken = (siteKey, action) => waitForRecaptcha().then((grecaptcha) => new Promise((resolve, reject) => {
    grecaptcha.ready(() => {
        Promise.resolve(grecaptcha.execute(siteKey, { action })).then(resolve, reject);
    });
}));
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

const ensureTokenField = (form) => {
    let token = form.querySelector('[data-recaptcha-token]');
    if (!token) {
        token = document.createElement('input');
        token.type = 'hidden';
        token.name = 'recaptcha_token';
        token.dataset.recaptchaToken = 'true';
        form.append(token);
    }
    return token;
};

// Use one delegated capture-phase listener instead of scanning the document
// once. This covers forms rendered after the bundle starts and prevents a
// fast submit from reaching Laravel before the token handler is attached.
document.addEventListener('submit', async (event) => {
    const form = event.target instanceof HTMLFormElement ? event.target : null;
    const config = recaptchaConfig();
    const action = form ? recaptchaActionForForm(form) : null;
    if (!form || !action || !config?.siteKey) return;

    if (form.dataset.recaptchaBypass === 'true') {
        delete form.dataset.recaptchaBypass;
        return;
    }

    event.preventDefault();
    if (form.dataset.recaptchaPending === 'true') return;

    const token = ensureTokenField(form);
    const status = form.querySelector('[data-recaptcha-status]');
    const submit = event.submitter || form.querySelector('button[type="submit"]');
    form.dataset.recaptchaPending = 'true';
    submit?.setAttribute('aria-busy', 'true');
    if (status) status.textContent = 'Verifying secure access…';

    try {
        const value = await requestToken(config.siteKey, action);
        if (!value || typeof value !== 'string') throw new Error('Empty reCAPTCHA token');
        token.value = value;
        form.dataset.recaptchaBypass = 'true';
        form.requestSubmit(submit || undefined);
    } catch {
        if (status) status.textContent = 'We could not verify this request. Please try again.';
    } finally {
        delete form.dataset.recaptchaPending;
        submit?.removeAttribute('aria-busy');
    }
}, true);

import './bootstrap';

const header = document.querySelector('[data-header]');
const menuToggle = document.querySelector('[data-menu-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');
const setHeaderState = () => header?.classList.toggle('scrolled', window.scrollY > 20);
setHeaderState();
window.addEventListener('scroll', setHeaderState, { passive: true });

menuToggle?.addEventListener('click', () => {
    const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!isOpen));
    mobileMenu?.classList.toggle('is-open', !isOpen);
    document.body.classList.toggle('menu-open', !isOpen);
});
mobileMenu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
    menuToggle?.setAttribute('aria-expanded', 'false');
    mobileMenu.classList.remove('is-open');
    document.body.classList.remove('menu-open');
}));

const adminShell = document.querySelector('[data-admin-shell]');
const adminSidebarToggle = document.querySelector('[data-admin-sidebar-toggle]');
const adminSidebar = document.querySelector('[data-admin-sidebar]');
const closeAdminSidebar = () => {
    adminShell?.classList.remove('is-sidebar-open');
    adminSidebarToggle?.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('admin-menu-open');
};
adminSidebarToggle?.addEventListener('click', () => {
    const isOpen = adminShell?.classList.toggle('is-sidebar-open') || false;
    adminSidebarToggle.setAttribute('aria-expanded', String(isOpen));
    document.body.classList.toggle('admin-menu-open', isOpen);
});
document.querySelectorAll('[data-admin-sidebar-close]').forEach((element) => element.addEventListener('click', closeAdminSidebar));
adminSidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeAdminSidebar));
document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeAdminSidebar(); });

const revealItems = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) { entry.target.classList.add('is-visible'); observer.unobserve(entry.target); }
        });
    }, { threshold: 0.12 });
    revealItems.forEach((item) => revealObserver.observe(item));
} else { revealItems.forEach((item) => item.classList.add('is-visible')); }

document.querySelectorAll('a[href^="#"]').forEach((link) => link.addEventListener('click', (event) => {
    const target = document.querySelector(link.getAttribute('href'));
    if (!target) return;
    event.preventDefault();
    target.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
}));

document.querySelectorAll('[data-media-picker]').forEach((picker) => {
    const search = picker.querySelector('.media-picker-search');
    const select = picker.querySelector('[data-media-select]');
    const preview = picker.querySelector('[data-media-preview]');
    const options = [...select.options];
    const updatePreview = () => {
        const option = select.selectedOptions[0];
        preview.replaceChildren();
        if (!option?.dataset.url) { preview.hidden = true; return; }
        const image = document.createElement('img');
        image.src = option.dataset.url;
        image.alt = 'Selected media preview';
        preview.append(image);
        preview.hidden = false;
    };
    search?.addEventListener('input', () => {
        const term = search.value.toLowerCase();
        options.forEach((option) => { option.hidden = option.value !== '' && !option.text.toLowerCase().includes(term); });
    });
    select?.addEventListener('change', updatePreview);
});

const faqTargetType = document.querySelector('[name="target_type"]');
const faqTargetRecord = document.querySelector('[name="target_id"]');
const filterFaqTargets = () => {
    if (!faqTargetType || !faqTargetRecord) return;
    const target = faqTargetType.value;
    [...faqTargetRecord.options].forEach((option) => {
        const visible = option.dataset.faqTarget === target;
        option.hidden = !visible;
        option.disabled = !visible;
    });
    const first = [...faqTargetRecord.options].find((option) => !option.disabled);
    if (first && faqTargetRecord.selectedOptions[0]?.disabled) faqTargetRecord.value = first.value;
};
faqTargetType?.addEventListener('change', filterFaqTargets);
filterFaqTargets();

document.querySelectorAll('form').forEach((form) => form.addEventListener('submit', (event) => {
    const method = form.querySelector('input[name="_method"]')?.value?.toLowerCase();
    const button = form.querySelector('button[type="submit"]')?.textContent?.trim().toLowerCase() || '';
    if (method === 'delete' || /archive|remove|permanently delete/.test(button)) {
        if (!window.confirm('Please confirm this destructive action.')) event.preventDefault();
    }
}));

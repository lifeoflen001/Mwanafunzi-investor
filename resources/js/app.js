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

const adminProfile = document.querySelector('[data-admin-profile]');
const adminProfileToggle = document.querySelector('[data-admin-profile-toggle]');
const adminProfileMenu = document.querySelector('[data-admin-profile-menu]');
const closeAdminProfile = () => {
    if (!adminProfileMenu || !adminProfileToggle) return;
    adminProfileMenu.hidden = true;
    adminProfileToggle.setAttribute('aria-expanded', 'false');
};
adminProfileToggle?.addEventListener('click', () => {
    const isOpen = adminProfileMenu?.hidden === false;
    if (!adminProfileMenu) return;
    adminProfileMenu.hidden = isOpen;
    adminProfileToggle.setAttribute('aria-expanded', String(!isOpen));
});
adminProfile?.addEventListener('click', (event) => event.stopPropagation());
document.addEventListener('click', closeAdminProfile);
document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeAdminProfile(); });

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
    const searchUrl = picker.dataset.mediaSearchUrl;
    let searchTimer;
    let requestController;
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
    const renderResults = (items) => {
        const selected = select.value;
        const selectedUrl = select.selectedOptions[0]?.dataset.url;
        select.replaceChildren(new Option('No image selected', ''));
        items.forEach((item) => {
            const option = new Option(item.label, item.path, false, item.path === selected);
            option.dataset.url = item.url;
            select.append(option);
        });
        if (selected && !items.some((item) => item.path === selected)) {
            const option = new Option(`${selected.split('/').pop()} · current selection`, selected, true, true);
            option.dataset.url = selectedUrl || `${window.location.origin}/storage/${selected.replace(/^\/+/, '')}`;
            select.append(option);
        }
        updatePreview();
    };
    const searchMedia = async () => {
        if (!searchUrl) return;
        requestController?.abort();
        requestController = new AbortController();
        const url = new URL(searchUrl, window.location.origin);
        if (search.value.trim()) url.searchParams.set('q', search.value.trim());
        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: requestController.signal });
            if (!response.ok) return;
            renderResults(await response.json());
        } catch (error) {
            if (error.name !== 'AbortError') console.warn('Media search failed', error);
        }
    };
    search?.addEventListener('input', () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(searchMedia, 250);
    });
    search?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        window.clearTimeout(searchTimer);
        searchMedia();
    });
    select?.addEventListener('change', updatePreview);
});

document.querySelectorAll('[data-rich-editor-wrapper]').forEach((wrapper) => {
    const editor = wrapper.querySelector('[data-rich-editor]');
    const source = wrapper.querySelector('[data-rich-source]');
    if (!editor || !source) return;
    const sync = () => { source.value = editor.innerHTML; };
    editor.addEventListener('input', sync);
    wrapper.querySelectorAll('[data-rich-command]').forEach((button) => button.addEventListener('mousedown', (event) => {
        event.preventDefault();
        editor.focus();
        const command = button.dataset.richCommand;
        if (command === 'createLink') {
            const url = window.prompt('Link URL');
            if (url) document.execCommand('createLink', false, url);
        } else if (command === 'formatBlock') {
            document.execCommand(command, false, `<${button.dataset.richValue}>`);
        } else {
            document.execCommand(command, false);
        }
        sync();
    }));
    editor.closest('form')?.addEventListener('submit', sync);
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

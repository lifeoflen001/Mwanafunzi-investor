import './bootstrap';

const header = document.querySelector('[data-header]');
const menuToggle = document.querySelector('[data-menu-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');
const setHeaderState = () => header?.classList.toggle('scrolled', window.scrollY > 20);
setHeaderState();
window.addEventListener('scroll', setHeaderState, { passive: true });

const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
const focusablesWithin = (container) => [...(container?.querySelectorAll(focusableSelector) || [])].filter((element) => element.getClientRects().length > 0);
let mobileMenuReturnFocus = null;
const closeMobileMenu = (restoreFocus = true) => {
    if (!mobileMenu || !menuToggle) return;
    menuToggle?.setAttribute('aria-expanded', 'false');
    mobileMenu.classList.remove('is-open');
    mobileMenu.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('menu-open');
    if (restoreFocus && mobileMenuReturnFocus?.focus) mobileMenuReturnFocus.focus();
    mobileMenuReturnFocus = null;
};
const openMobileMenu = () => {
    if (!mobileMenu || !menuToggle) return;
    mobileMenuReturnFocus = document.activeElement;
    menuToggle.setAttribute('aria-expanded', 'true');
    mobileMenu.classList.add('is-open');
    mobileMenu.setAttribute('aria-hidden', 'false');
    document.body.classList.add('menu-open');
    focusablesWithin(mobileMenu)[0]?.focus();
};
if (mobileMenu) mobileMenu.setAttribute('aria-hidden', 'true');
menuToggle?.addEventListener('click', () => {
    if (menuToggle.getAttribute('aria-expanded') === 'true') closeMobileMenu();
    else openMobileMenu();
});
mobileMenu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => closeMobileMenu(false)));
document.addEventListener('click', (event) => {
    if (mobileMenu?.classList.contains('is-open') && !mobileMenu.contains(event.target) && !menuToggle?.contains(event.target)) closeMobileMenu();
});

const adminShell = document.querySelector('[data-admin-shell]');
const adminSidebarToggle = document.querySelector('[data-admin-sidebar-toggle]');
const adminSidebar = document.querySelector('[data-admin-sidebar]');
const adminSidebarCollapseKey = 'mwanafunzi-admin-sidebar-collapsed';
const isAdminMobile = () => window.matchMedia('(max-width: 760px)').matches;
const syncAdminSidebarToggle = () => {
    if (!adminShell || !adminSidebarToggle) return;
    const mobileOpen = adminShell.classList.contains('is-sidebar-open');
    const collapsed = adminShell.classList.contains('is-sidebar-collapsed');
    adminSidebarToggle.setAttribute('aria-expanded', String(isAdminMobile() ? mobileOpen : !collapsed));
    adminSidebarToggle.setAttribute('aria-label', isAdminMobile() ? (mobileOpen ? 'Close admin navigation' : 'Open admin navigation') : (collapsed ? 'Expand admin navigation' : 'Collapse admin navigation'));
};
if (adminShell && !isAdminMobile() && window.localStorage.getItem(adminSidebarCollapseKey) === 'true') adminShell.classList.add('is-sidebar-collapsed');
const closeAdminSidebar = (restoreFocus = true) => {
    const wasOpen = adminShell?.classList.contains('is-sidebar-open');
    adminShell?.classList.remove('is-sidebar-open');
    syncAdminSidebarToggle();
    document.body.classList.remove('admin-menu-open');
    if (restoreFocus && wasOpen) adminSidebarToggle?.focus();
};
adminSidebarToggle?.addEventListener('click', () => {
    if (isAdminMobile()) {
        const isOpen = adminShell?.classList.toggle('is-sidebar-open') || false;
        document.body.classList.toggle('admin-menu-open', isOpen);
        syncAdminSidebarToggle();
        if (isOpen) adminSidebar?.querySelector('[data-admin-sidebar-close]')?.focus();
        return;
    }
    const isCollapsed = adminShell?.classList.toggle('is-sidebar-collapsed') || false;
    window.localStorage.setItem(adminSidebarCollapseKey, String(isCollapsed));
    syncAdminSidebarToggle();
});
window.addEventListener('resize', syncAdminSidebarToggle, { passive: true });
syncAdminSidebarToggle();
document.querySelectorAll('[data-admin-sidebar-close]').forEach((element) => element.addEventListener('click', () => closeAdminSidebar()));
adminSidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => closeAdminSidebar(false)));
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        if (mobileMenu?.classList.contains('is-open')) closeMobileMenu();
        closeAdminSidebar();
        return;
    }
    if (event.key !== 'Tab') return;
    const activeContainer = mobileMenu?.classList.contains('is-open') ? mobileMenu : (adminShell?.classList.contains('is-sidebar-open') ? adminSidebar : null);
    if (!activeContainer) return;
    const focusable = focusablesWithin(activeContainer);
    if (!focusable.length) return;
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
});

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
        } else if (command === 'insertTable') {
            document.execCommand('insertHTML', false, '<table><thead><tr><th>Heading</th><th>Heading</th></tr></thead><tbody><tr><td>Value</td><td>Value</td></tr></tbody></table><p><br></p>');
        } else if (command === 'insertImage') {
            const url = window.prompt('Image URL from the Media Library or a trusted https:// source');
            if (!url || !/^(https?:\/\/|\/|#)/i.test(url)) return;
            const alt = window.prompt('Image alt text') || '';
            const escapeAttribute = (value) => value.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            document.execCommand('insertHTML', false, `<img src="${escapeAttribute(url)}" alt="${escapeAttribute(alt)}">`);
        } else if (command === 'formatBlock') {
            document.execCommand(command, false, `<${button.dataset.richValue}>`);
        } else {
            document.execCommand(command, false);
        }
        sync();
    }));
    editor.closest('form')?.addEventListener('submit', sync);
});

document.querySelectorAll('form[data-unsaved-warning]').forEach((form) => {
    let dirty = false;
    form.addEventListener('input', () => { dirty = true; });
    form.addEventListener('change', () => { dirty = true; });
    form.addEventListener('submit', () => { dirty = false; });
    window.addEventListener('beforeunload', (event) => {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });
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

const confirmModal = document.querySelector('[data-confirm-modal]');
const confirmMessage = confirmModal?.querySelector('[data-confirm-message]');
const confirmAccept = confirmModal?.querySelector('[data-confirm-accept]');
const confirmCancel = confirmModal?.querySelector('[data-confirm-cancel]');
let pendingConfirmForm = null;
const closeConfirmModal = () => {
    if (!confirmModal) return;
    confirmModal.hidden = true;
    pendingConfirmForm = null;
};
confirmCancel?.addEventListener('click', closeConfirmModal);
confirmAccept?.addEventListener('click', () => {
    if (!pendingConfirmForm) return;
    const form = pendingConfirmForm;
    form.dataset.confirmed = 'true';
    closeConfirmModal();
    form.requestSubmit();
});
confirmModal?.addEventListener('click', (event) => { if (event.target === confirmModal) closeConfirmModal(); });
document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && confirmModal && !confirmModal.hidden) closeConfirmModal(); });

document.querySelectorAll('form').forEach((form) => form.addEventListener('submit', (event) => {
    if (form.dataset.confirmed === 'true') { delete form.dataset.confirmed; return; }
    const method = form.querySelector('input[name="_method"]')?.value?.toLowerCase();
    const button = form.querySelector('button[type="submit"]')?.textContent?.trim().toLowerCase() || '';
    if (method === 'delete' || /archive|remove|permanently delete/.test(button)) {
        event.preventDefault();
        pendingConfirmForm = form;
        if (confirmMessage) confirmMessage.textContent = form.dataset.confirm || 'Please confirm this destructive action.';
        if (confirmModal) { confirmModal.hidden = false; confirmAccept?.focus(); }
    }
}));

document.querySelectorAll('[data-admin-toast-close]').forEach((button) => button.addEventListener('click', () => {
    button.closest('[data-admin-toast]')?.remove();
}));
document.querySelectorAll('[data-admin-toast]').forEach((toast) => {
    window.setTimeout(() => toast.remove(), 6500);
});

document.querySelectorAll('.admin-portal form.admin-form, .admin-portal form.admin-filter-toolbar, .admin-portal form.admin-inline-form').forEach((form) => form.addEventListener('submit', (event) => {
    if (event.defaultPrevented || form.getAttribute('aria-busy') === 'true') return;
    form.setAttribute('aria-busy', 'true');
    const submitter = event.submitter || form.querySelector('button[type="submit"]');
    if (!submitter || submitter.disabled) return;
    submitter.disabled = true;
    submitter.setAttribute('aria-busy', 'true');
    submitter.dataset.originalContent = submitter.innerHTML;
    submitter.innerHTML = '<span class="admin-button-progress" aria-hidden="true"></span><span>Working…</span>';
}));

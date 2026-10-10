if (document.querySelector('[data-avatar-form]')) import('./modules/avatar-crop.js');
if (document.querySelector('[data-media-picker], [data-rich-editor-wrapper]')) import('./modules/media-editor.js');
if (window.mwanafunziRecaptcha?.siteKey) import('./modules/recaptcha.js');

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
const adminSidebarCollapseKey = adminShell?.classList.contains('account-shell') ? 'mwanafunzi-account-sidebar-collapsed' : 'mwanafunzi-admin-sidebar-collapsed';
const portalSidebarLabel = adminShell?.classList.contains('account-shell') ? 'student' : 'admin';
const isAdminMobile = () => window.matchMedia('(max-width: 760px)').matches;
const syncAdminSidebarToggle = () => {
    if (!adminShell || !adminSidebarToggle) return;
    const mobileOpen = adminShell.classList.contains('is-sidebar-open');
    const collapsed = adminShell.classList.contains('is-sidebar-collapsed');
    adminSidebarToggle.setAttribute('aria-expanded', String(isAdminMobile() ? mobileOpen : !collapsed));
    adminSidebarToggle.setAttribute('aria-label', isAdminMobile() ? (mobileOpen ? `Close ${portalSidebarLabel} navigation` : `Open ${portalSidebarLabel} navigation`) : (collapsed ? `Expand ${portalSidebarLabel} navigation` : `Collapse ${portalSidebarLabel} navigation`));
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
const adminNotificationMenu = document.querySelector('[data-admin-notifications]');
const adminNotificationToggle = document.querySelector('[data-admin-notifications-toggle]');
const adminNotificationPopover = document.querySelector('[data-admin-notifications-menu]');
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
const closeAdminNotifications = () => {
    if (!adminNotificationPopover || !adminNotificationToggle) return;
    adminNotificationPopover.hidden = true;
    adminNotificationToggle.setAttribute('aria-expanded', 'false');
};
adminNotificationToggle?.addEventListener('click', () => {
    if (!adminNotificationPopover) return;
    const isOpen = !adminNotificationPopover.hidden;
    closeAdminProfile();
    adminNotificationPopover.hidden = isOpen;
    adminNotificationToggle.setAttribute('aria-expanded', String(!isOpen));
});
adminNotificationMenu?.addEventListener('click', (event) => event.stopPropagation());
document.addEventListener('click', closeAdminNotifications);
document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeAdminNotifications(); });

const adminFullscreenButton = document.querySelector('[data-admin-fullscreen]');
const syncAdminFullscreen = () => {
    if (!adminFullscreenButton) return;
    const active = Boolean(document.fullscreenElement);
    adminFullscreenButton.setAttribute('aria-label', active ? 'Exit fullscreen' : 'Enter fullscreen');
    adminFullscreenButton.title = active ? 'Exit fullscreen' : 'Enter fullscreen';
    adminFullscreenButton.dataset.fullscreenActive = String(active);
};
adminFullscreenButton?.addEventListener('click', async () => {
    if (!document.fullscreenEnabled) return;
    try {
        if (document.fullscreenElement) await document.exitFullscreen();
        else await document.documentElement.requestFullscreen();
    } catch { /* Fullscreen can be denied by the browser; leave the layout intact. */ }
    syncAdminFullscreen();
});
document.addEventListener('fullscreenchange', syncAdminFullscreen);
syncAdminFullscreen();

const adminSearchForm = document.querySelector('[data-admin-search-form]');
const adminSearchInput = document.querySelector('[data-admin-search-input]');
const adminSearchSuggestions = document.querySelector('[data-admin-search-suggestions]');
const adminSearchClear = document.querySelector('[data-admin-search-clear]');
const adminSearchLoading = document.querySelector('[data-admin-search-loading]');
const adminMobileSearchButton = document.querySelector('[data-admin-mobile-search]');
let adminSearchTimer = null;
let adminSearchAbort = null;
let adminSearchActiveIndex = -1;
const searchOptions = () => [...(adminSearchSuggestions?.querySelectorAll('[role="option"]') || [])];
const closeAdminSearch = () => {
    if (!adminSearchSuggestions || !adminSearchInput) return;
    adminSearchSuggestions.hidden = true;
    adminSearchInput.setAttribute('aria-expanded', 'false');
    adminSearchActiveIndex = -1;
};
const openAdminSearch = () => {
    if (!adminSearchSuggestions || !adminSearchInput) return;
    adminSearchSuggestions.hidden = false;
    adminSearchInput.setAttribute('aria-expanded', 'true');
};
const renderAdminSearchSuggestions = (payload, term) => {
    if (!adminSearchSuggestions) return;
    adminSearchSuggestions.replaceChildren();
    adminSearchActiveIndex = -1;
    if (!payload.total) {
        const empty = document.createElement('p');
        empty.className = 'admin-search-suggestion-empty';
        empty.textContent = term ? `No results for “${term}”.` : 'Start typing to search the desk.';
        adminSearchSuggestions.append(empty);
        openAdminSearch();
        return;
    }
    payload.groups.forEach((group) => {
        const heading = document.createElement('strong');
        heading.className = 'admin-search-suggestion-group';
        heading.textContent = group.type;
        adminSearchSuggestions.append(heading);
        group.items.forEach((item) => {
            const link = document.createElement('a');
            link.href = item.url;
            link.role = 'option';
            link.className = 'admin-search-suggestion-item';
            link.innerHTML = `<span class="admin-search-suggestion-icon" aria-hidden="true">◌</span><span><strong></strong><small></small></span>`;
            link.querySelector('strong').textContent = item.title;
            link.querySelector('small').textContent = item.meta || 'No status';
            adminSearchSuggestions.append(link);
        });
    });
    const footer = document.createElement('a');
    footer.className = 'admin-search-suggestion-footer';
    footer.href = `${adminSearchForm.action}?q=${encodeURIComponent(term)}`;
    footer.textContent = 'View all results →';
    adminSearchSuggestions.append(footer);
    openAdminSearch();
};
const fetchAdminSearchSuggestions = () => {
    if (!adminSearchInput || !adminSearchForm) return;
    const term = adminSearchInput.value.trim();
    if (adminSearchClear) adminSearchClear.hidden = !term;
    window.clearTimeout(adminSearchTimer);
    adminSearchAbort?.abort();
    if (!term) { closeAdminSearch(); return; }
    adminSearchTimer = window.setTimeout(async () => {
        adminSearchLoading?.removeAttribute('hidden');
        adminSearchAbort = new AbortController();
        try {
            const response = await fetch(`${adminSearchForm.dataset.suggestionsUrl}?q=${encodeURIComponent(term)}`, { headers: { Accept: 'application/json' }, signal: adminSearchAbort.signal });
            if (response.ok) renderAdminSearchSuggestions(await response.json(), term);
        } catch (error) { if (error.name !== 'AbortError') closeAdminSearch(); }
        finally { adminSearchLoading?.setAttribute('hidden', 'hidden'); }
    }, 220);
};
adminSearchInput?.addEventListener('input', fetchAdminSearchSuggestions);
adminSearchInput?.addEventListener('focus', () => { if (adminSearchInput.value.trim()) fetchAdminSearchSuggestions(); });
adminSearchInput?.addEventListener('keydown', (event) => {
    const options = searchOptions();
    if (event.key === 'Escape') { closeAdminSearch(); return; }
    if (event.key === 'ArrowDown' && options.length) { event.preventDefault(); adminSearchActiveIndex = Math.min(adminSearchActiveIndex + 1, options.length - 1); options[adminSearchActiveIndex].focus(); }
    if (event.key === 'Enter' && adminSearchActiveIndex >= 0 && options[adminSearchActiveIndex]) { event.preventDefault(); options[adminSearchActiveIndex].click(); }
});
adminSearchSuggestions?.addEventListener('keydown', (event) => {
    const options = searchOptions();
    if (event.key === 'ArrowDown') { event.preventDefault(); adminSearchActiveIndex = Math.min(adminSearchActiveIndex + 1, options.length - 1); options[adminSearchActiveIndex]?.focus(); }
    if (event.key === 'ArrowUp') { event.preventDefault(); adminSearchActiveIndex = Math.max(adminSearchActiveIndex - 1, -1); (options[adminSearchActiveIndex] || adminSearchInput)?.focus(); }
    if (event.key === 'Escape') { closeAdminSearch(); adminSearchInput?.focus(); }
});
adminSearchClear?.addEventListener('click', () => { if (adminSearchInput) { adminSearchInput.value = ''; adminSearchInput.focus(); } closeAdminSearch(); adminSearchClear.hidden = true; });
adminMobileSearchButton?.addEventListener('click', () => { adminShell?.classList.add('is-mobile-search-open'); adminSearchForm?.classList.add('is-mobile-search-open'); adminSearchInput?.focus(); });
document.addEventListener('click', (event) => { if (!event.target.closest('[data-admin-search-form]')) closeAdminSearch(); });
document.addEventListener('keydown', (event) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); adminSearchInput?.focus(); }
});

document.querySelectorAll('[data-password-toggle]').forEach((button) => button.addEventListener('click', () => {
    const input = button.closest('.admin-password-field, .client-password-field, .student-password-field, .workspace-password-field')?.querySelector('input');
    if (!input) return;
    const visible = input.type === 'text';
    input.type = visible ? 'password' : 'text';
    const label = (button.dataset.passwordLabel || 'Show password').replace(/^Show\s+/i, '');
    button.classList.toggle('is-visible', !visible);
    button.setAttribute('aria-label', `${visible ? 'Show' : 'Hide'} ${label}`);
    button.setAttribute('aria-pressed', String(!visible));
}));
document.querySelectorAll('input[name="password_confirmation"]').forEach((confirmation) => {
    const form = confirmation.form;
    const password = form?.querySelector('input[name="password"]');
    if (!form || !password) return;
    const errorId = `${confirmation.id || 'password-confirmation'}-match-error`;
    const syncPasswordMatch = () => {
        const mismatch = confirmation.value.length > 0 && password.value !== confirmation.value;
        confirmation.setAttribute('aria-invalid', mismatch ? 'true' : 'false');
        let error = document.getElementById(errorId);
        if (mismatch && !error) {
            error = document.createElement('small');
            error.id = errorId;
            error.className = 'field-error password-match-error';
            error.textContent = 'Passwords do not match.';
            confirmation.closest('label')?.append(error);
        }
        if (error) error.hidden = !mismatch;
    };
    password.addEventListener('input', syncPasswordMatch);
    confirmation.addEventListener('input', syncPasswordMatch);
    form.addEventListener('submit', (event) => {
        syncPasswordMatch();
        if (confirmation.getAttribute('aria-invalid') === 'true') { event.preventDefault(); confirmation.focus(); }
    });
});

document.querySelectorAll('[data-auth-form]').forEach((form) => form.addEventListener('submit', () => {
    const submit = form.querySelector('[data-auth-submit]');
    const label = submit?.querySelector('[data-auth-submit-label]');
    if (!submit || form.dataset.submitting === 'true') return;
    form.dataset.submitting = 'true';
    submit.disabled = true;
    submit.classList.add('is-submitting');
    submit.setAttribute('aria-busy', 'true');
    if (label) label.textContent = form.querySelector('[name="password_confirmation"]') ? 'Creating account…' : 'Signing in…';
}));

const adminDetailsMenus = [...document.querySelectorAll('.admin-action-menu, .admin-quick-actions')];
const closeAdminDetailsMenus = (except = null) => adminDetailsMenus.forEach((menu) => {
    if (menu !== except) menu.removeAttribute('open');
});
adminDetailsMenus.forEach((menu) => menu.addEventListener('toggle', () => {
    if (menu.open) closeAdminDetailsMenus(menu);
}));
document.addEventListener('click', (event) => {
    if (!event.target.closest('.admin-action-menu, .admin-quick-actions')) closeAdminDetailsMenus();
});
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeAdminDetailsMenus();
});

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
let confirmReturnFocus = null;
const closeConfirmModal = () => {
    if (!confirmModal) return;
    confirmModal.hidden = true;
    pendingConfirmForm = null;
    const returnFocus = confirmReturnFocus;
    confirmReturnFocus = null;
    returnFocus?.focus?.();
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
document.addEventListener('keydown', (event) => {
    if (!confirmModal || confirmModal.hidden) return;
    if (event.key === 'Escape') { closeConfirmModal(); return; }
    if (event.key !== 'Tab') return;
    const focusable = focusablesWithin(confirmModal);
    if (!focusable.length) return;
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
});

document.querySelectorAll('form').forEach((form) => form.addEventListener('submit', (event) => {
    if (form.dataset.confirmed === 'true') { delete form.dataset.confirmed; return; }
    const method = form.querySelector('input[name="_method"]')?.value?.toLowerCase();
    const button = form.querySelector('button[type="submit"]')?.textContent?.trim().toLowerCase() || '';
    if (form.dataset.confirm || method === 'delete' || /archive|remove|permanently delete|revoke|refund|unpublish/.test(button)) {
        event.preventDefault();
        pendingConfirmForm = form;
        if (confirmMessage) confirmMessage.textContent = form.dataset.confirm || 'Please confirm this destructive action.';
        if (confirmModal) { confirmReturnFocus = document.activeElement; confirmModal.hidden = false; confirmAccept?.focus(); }
    }
}));

document.querySelectorAll('[data-admin-toast-close]').forEach((button) => button.addEventListener('click', () => {
    button.closest('[data-admin-toast]')?.remove();
}));
document.querySelectorAll('[data-admin-toast]').forEach((toast) => {
    window.setTimeout(() => toast.remove(), 6500);
});
document.querySelectorAll('[data-feedback-dismiss]').forEach((button) => button.addEventListener('click', () => button.closest('[data-feedback]')?.remove()));
document.querySelectorAll('[data-feedback]').forEach((feedback) => {
    if (!feedback.classList.contains('feedback-error')) window.setTimeout(() => feedback.remove(), 6500);
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

document.querySelectorAll('[data-social-repeater]').forEach((repeater) => {
    const template = document.querySelector('[data-social-template]');
    const add = document.querySelector('[data-add-social]');
    const nextIndex = () => [...repeater.querySelectorAll('[data-social-row]')].reduce((max, row) => {
        const field = row.querySelector('[name*="social_links["]');
        const match = field?.name.match(/social_links\[(\d+)\]/);
        return Math.max(max, match ? Number(match[1]) : -1);
    }, -1) + 1;
    const bindRemove = (row) => row.querySelector('[data-remove-social]')?.addEventListener('click', () => {
        if (repeater.querySelectorAll('[data-social-row]').length > 1) row.remove();
        else row.querySelectorAll('input').forEach((input) => { input.value = ''; });
    });
    repeater.querySelectorAll('[data-social-row]').forEach(bindRemove);
    add?.addEventListener('click', () => {
        if (!template) return;
        const row = template.content.firstElementChild.cloneNode(true);
        row.querySelectorAll('[name]').forEach((field) => { field.name = field.name.replace('__INDEX__', String(nextIndex())); });
        repeater.append(row);
        bindRemove(row);
    });
});

document.querySelectorAll('[data-reaction-form]').forEach((form) => form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const button = form.querySelector('button');
    if (!button || button.disabled) return;
    button.disabled = true;
    try {
        const response = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (!response.ok) throw new Error('Reaction failed');
        const payload = await response.json();
        button.classList.toggle('is-active', payload.active);
        button.setAttribute('aria-pressed', String(payload.active));
        const count = button.querySelector('[data-reaction-count]');
        if (count) count.textContent = payload.count;
        const icon = button.querySelector('[aria-hidden="true"]');
        if (icon) icon.textContent = payload.active ? '♥' : '♡';
    } catch { form.submit(); }
    finally { button.disabled = false; }
}));

document.querySelectorAll('[data-copy-link]').forEach((button) => button.addEventListener('click', async () => {
    const container = button.closest('[data-share-url]');
    const feedback = container?.querySelector('[data-share-feedback]');
    const url = container?.dataset.shareUrl || window.location.href;
    try {
        await navigator.clipboard.writeText(url);
        if (feedback) feedback.textContent = 'Link copied.';
    } catch {
        if (feedback) feedback.textContent = 'Copy unavailable. Select the page address instead.';
    }
    window.setTimeout(() => { if (feedback) feedback.textContent = ''; }, 3500);
}));

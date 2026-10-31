if (document.querySelector('[data-avatar-form]')) import('./modules/avatar-crop.js');
if (document.querySelector('[data-media-picker], [data-rich-editor-wrapper]')) import('./modules/media-editor.js');
// Bind protected forms from the main bundle so the handler is ready even when
// the third-party Google script is still loading. The module remains inert
// when reCAPTCHA is not configured for the current environment.
import './modules/recaptcha.js';

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

const publicAccount = document.querySelector('[data-public-account]');
const publicAccountToggle = document.querySelector('[data-public-account-toggle]');
const publicAccountPopover = document.querySelector('[data-public-account-popover]');
let publicAccountReturnFocus = null;
const closePublicAccount = (restoreFocus = false) => {
    if (!publicAccountPopover || !publicAccountToggle) return;
    publicAccountPopover.hidden = true;
    publicAccountToggle.setAttribute('aria-expanded', 'false');
    if (restoreFocus && publicAccountReturnFocus?.focus) publicAccountReturnFocus.focus();
    publicAccountReturnFocus = null;
};
publicAccountToggle?.addEventListener('click', (event) => {
    event.stopPropagation();
    if (!publicAccountPopover) return;
    const isOpen = !publicAccountPopover.hidden;
    publicAccountPopover.hidden = isOpen;
    publicAccountToggle.setAttribute('aria-expanded', String(!isOpen));
    if (!isOpen) {
        publicAccountReturnFocus = publicAccountToggle;
        publicAccountPopover.querySelector('a, button')?.focus();
    } else {
        publicAccountReturnFocus = null;
    }
});
publicAccount?.addEventListener('click', (event) => event.stopPropagation());
document.addEventListener('click', () => closePublicAccount());
publicAccountToggle?.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        if (publicAccountPopover?.hidden) publicAccountToggle.click();
    }
});
publicAccountPopover?.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        event.preventDefault();
        closePublicAccount(true);
    }
});
document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && publicAccountPopover && !publicAccountPopover.hidden) closePublicAccount(true); });

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
    const label = isAdminMobile() ? (mobileOpen ? `Close ${portalSidebarLabel} navigation` : `Open ${portalSidebarLabel} navigation`) : (collapsed ? `Expand ${portalSidebarLabel} navigation` : `Collapse ${portalSidebarLabel} navigation`);
    adminSidebarToggle.setAttribute('aria-label', label);
    adminSidebarToggle.dataset.sidebarTooltipLabel = label;
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

/* Admin navigation parents use real buttons so route links and expansion stay distinct. */
const adminNavStateKey = 'mwanafunzi-admin-nav-open';
const adminNavToggles = [...(adminSidebar?.querySelectorAll('[data-admin-nav-toggle]') || [])];
let adminNavState = {};
try { adminNavState = JSON.parse(window.localStorage.getItem(adminNavStateKey) || '{}') || {}; } catch { adminNavState = {}; }
const setAdminNavState = (toggle, open, persist = true) => {
    const submenu = document.getElementById(toggle.getAttribute('aria-controls'));
    const parent = toggle.closest('[data-admin-nav-parent]');
    if (!submenu || !parent) return;
    submenu.hidden = !open;
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', `${open ? 'Collapse' : 'Expand'} ${toggle.dataset.navLabel || 'module'} navigation`);
    parent.classList.toggle('is-open', open);
    const flyout = open && !isAdminMobile() && adminShell?.classList.contains('is-sidebar-collapsed');
    if (flyout) {
        const rect = parent.getBoundingClientRect();
        parent.classList.add('is-admin-nav-flyout');
        parent.style.setProperty('--admin-nav-flyout-top', `${Math.max(8, rect.top)}px`);
        parent.style.setProperty('--admin-nav-flyout-left', `${Math.max(8, rect.right + 10)}px`);
    } else {
        parent.classList.remove('is-admin-nav-flyout');
        parent.style.removeProperty('--admin-nav-flyout-top');
        parent.style.removeProperty('--admin-nav-flyout-left');
    }
    if (persist && toggle.closest('[data-nav-key]')) {
        adminNavState[toggle.closest('[data-nav-key]').dataset.navKey] = open;
        try { window.localStorage.setItem(adminNavStateKey, JSON.stringify(adminNavState)); } catch { /* Storage can be unavailable. */ }
    }
};
adminNavToggles.forEach((toggle) => {
    const key = toggle.closest('[data-nav-key]')?.dataset.navKey;
    const routeActive = toggle.dataset.navActive === 'true';
    setAdminNavState(toggle, routeActive || adminNavState[key] === true, false);
    toggle.addEventListener('click', () => {
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';
        setAdminNavState(toggle, !isOpen);
    });
});
const refreshAdminNavFlyouts = () => adminNavToggles.forEach((toggle) => {
    if (toggle.getAttribute('aria-expanded') === 'true') setAdminNavState(toggle, true, false);
});
window.addEventListener('resize', refreshAdminNavFlyouts, { passive: true });
document.addEventListener('scroll', refreshAdminNavFlyouts, true);

/* One body-level tooltip keeps collapsed sidebar labels out of overflow-hidden panels. */
const sidebarTooltip = (() => {
    if (!adminShell || !adminSidebar) return null;
    const tooltip = document.createElement('div');
    tooltip.id = 'portal-sidebar-tooltip';
    tooltip.className = 'portal-sidebar-tooltip';
    tooltip.setAttribute('role', 'tooltip');
    tooltip.setAttribute('aria-hidden', 'true');
    document.body.append(tooltip);

    const targets = [...adminSidebar.querySelectorAll('.portal-nav a, .portal-sidebar-footer a, .portal-signout'), adminSidebarToggle].filter(Boolean);
    let activeTarget = null;
    let showTimer = null;
    let hideTimer = null;

    const labelFor = (target) => {
        if (target.dataset.sidebarTooltipLabel) return target.dataset.sidebarTooltipLabel;
        const clone = target.cloneNode(true);
        clone.querySelectorAll('[aria-hidden="true"]').forEach((element) => element.remove());
        return clone.textContent.replace(/\s+/g, ' ').trim();
    };
    const canShow = (target) => !isAdminMobile() && adminShell.classList.contains('is-sidebar-collapsed') && Boolean(target?.getClientRects().length);
    const position = () => {
        if (!activeTarget || tooltip.hidden) return;
        const targetRect = activeTarget.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        if (!targetRect.width && !targetRect.height) return;
        const fitsRight = targetRect.right + 12 + tooltipRect.width <= window.innerWidth - 8;
        const left = fitsRight ? targetRect.right + 12 : Math.max(8, targetRect.left - tooltipRect.width - 12);
        const top = Math.min(Math.max(8, targetRect.top + (targetRect.height - tooltipRect.height) / 2), Math.max(8, window.innerHeight - tooltipRect.height - 8));
        tooltip.style.left = `${Math.round(left)}px`;
        tooltip.style.top = `${Math.round(top)}px`;
    };
    const hide = (immediate = false) => {
        window.clearTimeout(showTimer);
        window.clearTimeout(hideTimer);
        activeTarget?.removeAttribute('aria-describedby');
        activeTarget = null;
        tooltip.classList.remove('is-visible');
        tooltip.setAttribute('aria-hidden', 'true');
        if (immediate) tooltip.hidden = true;
        else hideTimer = window.setTimeout(() => { tooltip.hidden = true; }, 160);
    };
    const show = (target) => {
        window.clearTimeout(hideTimer);
        window.clearTimeout(showTimer);
        if (!canShow(target)) { hide(true); return; }
        if (activeTarget && activeTarget !== target) activeTarget.removeAttribute('aria-describedby');
        activeTarget = target;
        tooltip.textContent = labelFor(target);
        tooltip.hidden = false;
        tooltip.setAttribute('aria-hidden', 'false');
        target.setAttribute('aria-describedby', tooltip.id);
        position();
        showTimer = window.setTimeout(() => tooltip.classList.add('is-visible'), 120);
    };

    targets.forEach((target) => {
        if (!target.dataset.sidebarTooltipLabel) target.dataset.sidebarTooltipLabel = labelFor(target);
        if (!target.hasAttribute('aria-label')) target.setAttribute('aria-label', target.dataset.sidebarTooltipLabel);
        target.addEventListener('pointerenter', () => show(target));
        target.addEventListener('pointerleave', () => hide());
        target.addEventListener('focusin', () => show(target));
        target.addEventListener('focusout', () => hide());
    });
    window.addEventListener('resize', () => { if (activeTarget) { if (canShow(activeTarget)) position(); else hide(true); } }, { passive: true });
    document.addEventListener('scroll', () => { if (activeTarget) { if (canShow(activeTarget)) position(); else hide(true); } }, true);
    return { hide };
})();
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        if (mobileMenu?.classList.contains('is-open')) closeMobileMenu();
        sidebarTooltip?.hide(true);
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
const adminSearchLoadingPanel = document.querySelector('[data-admin-search-loading-panel]');
const adminSearchStatus = document.querySelector('[data-admin-search-status]');
const adminMobileSearchButton = document.querySelector('[data-admin-mobile-search]');
let adminSearchTimer = null;
let adminSearchAbort = null;
let adminSearchActiveIndex = -1;
const searchOptions = () => [...(adminSearchSuggestions?.querySelectorAll('[role="option"]') || [])];
const closeAdminSearch = () => {
    if (!adminSearchSuggestions || !adminSearchInput) return;
    adminSearchSuggestions.hidden = true;
    adminSearchInput.setAttribute('aria-expanded', 'false');
    adminSearchLoadingPanel?.setAttribute('hidden', 'hidden');
    adminSearchSuggestions.removeAttribute('aria-busy');
    adminSearchActiveIndex = -1;
};
const openAdminSearch = () => {
    if (!adminSearchSuggestions || !adminSearchInput) return;
    adminSearchSuggestions.hidden = false;
    adminSearchInput.setAttribute('aria-expanded', 'true');
};
const renderAdminSearchSuggestions = (payload, term) => {
    if (!adminSearchSuggestions) return;
    adminSearchLoadingPanel?.setAttribute('hidden', 'hidden');
    adminSearchSuggestions.removeAttribute('aria-busy');
    if (adminSearchStatus) adminSearchStatus.textContent = 'Search suggestions updated.';
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
        const requestController = new AbortController();
        adminSearchAbort = requestController;
        adminSearchSuggestions?.setAttribute('aria-busy', 'true');
        adminSearchSuggestions && (adminSearchSuggestions.hidden = true);
        adminSearchLoadingPanel?.removeAttribute('hidden');
        if (adminSearchStatus) adminSearchStatus.textContent = 'Loading search suggestions.';
        try {
            const response = await fetch(`${adminSearchForm.dataset.suggestionsUrl}?q=${encodeURIComponent(term)}`, { headers: { Accept: 'application/json' }, signal: requestController.signal });
            if (!response.ok) throw new Error(`Search suggestions returned ${response.status}`);
            renderAdminSearchSuggestions(await response.json(), term);
        } catch (error) {
            if (error.name !== 'AbortError') {
                if (adminSearchStatus) adminSearchStatus.textContent = 'Search suggestions are unavailable.';
                closeAdminSearch();
            }
        } finally {
            if (adminSearchAbort === requestController) {
                adminSearchLoading?.setAttribute('hidden', 'hidden');
                adminSearchLoadingPanel?.setAttribute('hidden', 'hidden');
                adminSearchSuggestions?.removeAttribute('aria-busy');
            }
        }
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

document.querySelectorAll('[data-auth-showcase]').forEach((showcase) => {
    const panels = [...showcase.querySelectorAll('[data-auth-slide-panel]')];
    const dots = [...showcase.querySelectorAll('[data-auth-slide]')];
    if (panels.length < 2 || dots.length !== panels.length) return;

    const reducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
    let activeIndex = panels.findIndex((panel) => panel.classList.contains('is-active'));
    let timer = null;

    const showSlide = (index) => {
        activeIndex = (index + panels.length) % panels.length;
        panels.forEach((panel, panelIndex) => {
            const active = panelIndex === activeIndex;
            panel.classList.toggle('is-active', active);
            panel.setAttribute('aria-hidden', String(!active));
        });
        dots.forEach((dot, dotIndex) => {
            const active = dotIndex === activeIndex;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-selected', String(active));
        });
    };

    const stop = () => { if (timer) { window.clearInterval(timer); timer = null; } };
    const start = () => {
        if (reducedMotion || timer) return;
        timer = window.setInterval(() => showSlide(activeIndex + 1), 6000);
    };

    dots.forEach((dot, index) => dot.addEventListener('click', () => { showSlide(index); stop(); start(); }));
    showcase.addEventListener('mouseenter', stop);
    showcase.addEventListener('mouseleave', start);
    showcase.addEventListener('focusin', stop);
    showcase.addEventListener('focusout', (event) => { if (!showcase.contains(event.relatedTarget)) start(); });
    showSlide(activeIndex < 0 ? 0 : activeIndex);
    start();
});

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

document.querySelectorAll('[data-newsletter-form]').forEach((form) => form.addEventListener('submit', () => {
    const submit = form.querySelector('[data-newsletter-submit]');
    const label = form.querySelector('[data-newsletter-submit-label]');
    if (!submit || form.dataset.submitting === 'true') return;
    form.dataset.submitting = 'true';
    submit.disabled = true;
    submit.setAttribute('aria-busy', 'true');
    if (label) label.textContent = 'Subscribing…';
}));

const cookieRoot = document.querySelector('[data-cookie-consent]');
if (cookieRoot) {
    const cookieConfig = (() => {
        try { return JSON.parse(decodeURIComponent(escape(window.atob(cookieRoot.dataset.cookieConfig || '')))); } catch { return {}; }
    })();
    const cookieName = 'mwanafunzi_cookie_consent';
    const readCookie = (name) => document.cookie.split('; ').find((part) => part.startsWith(`${name}=`))?.slice(name.length + 1) || null;
    const readConsent = () => {
        const value = readCookie(cookieName);
        if (!value) return null;
        try {
            const state = JSON.parse(decodeURIComponent(value));
            return state.version === cookieConfig.version && state.essential === true ? state : null;
        } catch { return null; }
    };
    const writeConsent = (state) => {
        const maxAge = Math.max(1, Number(cookieConfig.lifetime || 9)) * 30 * 24 * 60 * 60;
        document.cookie = `${cookieName}=${encodeURIComponent(JSON.stringify({ essential: true, preferences: Boolean(state.preferences), analytics: Boolean(state.analytics), marketing: Boolean(state.marketing), external_media: Boolean(state.external_media), version: cookieConfig.version }))}; max-age=${maxAge}; path=/; samesite=lax${window.location.protocol === 'https:' ? '; secure' : ''}`;
        window.dispatchEvent(new CustomEvent('mwanafunzi:cookie-consent', { detail: { ...state, essential: true, version: cookieConfig.version } }));
    };
    const modal = cookieRoot.querySelector('[data-cookie-modal]');
    const banner = cookieRoot.querySelector('[data-cookie-banner]');
    const toggles = [...cookieRoot.querySelectorAll('[data-cookie-toggle]')];
    let cookieReturnFocus = null;
    const optionalState = () => Object.fromEntries(toggles.map((toggle) => [toggle.dataset.cookieToggle, toggle.checked]));
    const syncToggles = (state) => toggles.forEach((toggle) => { toggle.checked = Boolean(state?.[toggle.dataset.cookieToggle]); });
    const loadConsentAwareScripts = (state) => document.querySelectorAll('script[type="text/plain"][data-cookie-category]').forEach((placeholder) => {
        if (placeholder.dataset.loaded === 'true' || !state?.[placeholder.dataset.cookieCategory]) return;
        const script = document.createElement('script');
        [...placeholder.attributes].filter((attribute) => !['type', 'data-cookie-category'].includes(attribute.name)).forEach((attribute) => script.setAttribute(attribute.name, attribute.value));
        script.textContent = placeholder.textContent;
        placeholder.replaceWith(script);
    });
    const close = () => { modal.hidden = true; if (readConsent()) cookieRoot.hidden = true; else { banner.hidden = false; cookieRoot.hidden = false; } cookieReturnFocus?.focus?.(); cookieReturnFocus = null; };
    const openPreferences = () => { cookieReturnFocus = document.activeElement; syncToggles(readConsent() || {}); banner.hidden = true; modal.hidden = false; cookieRoot.hidden = false; modal.querySelector('button, input')?.focus(); };
    const apply = (state) => { writeConsent(state); modal.hidden = true; banner.hidden = false; cookieRoot.hidden = true; loadConsentAwareScripts(state); cookieReturnFocus?.focus?.(); cookieReturnFocus = null; };
    cookieRoot.querySelectorAll('[data-cookie-action]').forEach((button) => button.addEventListener('click', () => {
        const action = button.dataset.cookieAction;
        if (action === 'accept') apply({ preferences: true, analytics: true, marketing: true, external_media: cookieConfig.externalMediaEnabled === true });
        else if (action === 'reject') apply({ preferences: false, analytics: false, marketing: false, external_media: false });
        else if (action === 'save') apply(optionalState());
        else if (action === 'manage') openPreferences();
        else if (action === 'close') close();
    }));
    document.querySelectorAll('[data-cookie-preferences]').forEach((link) => link.addEventListener('click', (event) => { event.preventDefault(); openPreferences(); }));
    document.addEventListener('keydown', (event) => {
        if (modal.hidden) return;
        if (event.key === 'Escape') { event.preventDefault(); close(); return; }
        if (event.key !== 'Tab') return;
        const focusable = focusablesWithin(modal);
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });
    const consent = readConsent();
    if (consent) { cookieRoot.hidden = true; loadConsentAwareScripts(consent); }
    else { cookieRoot.hidden = false; banner.hidden = false; modal.hidden = true; }
    window.mwanafunziCookieConsent = { current: readConsent, open: openPreferences, has: (category) => Boolean(readConsent()?.[category]) };
}

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

/* Keep first-time MFA secrets out of the main form until the administrator asks to view them. */
document.querySelectorAll('[data-mfa-setup-open]').forEach((trigger) => {
    const dialog = document.getElementById(trigger.dataset.mfaSetupOpen);
    if (!dialog) return;
    let returnFocus = null;
    const close = () => {
        if (typeof dialog.close === 'function' && dialog.open) dialog.close();
        else { dialog.removeAttribute('open'); dialog.classList.remove('is-open'); }
        returnFocus?.focus?.();
        returnFocus = null;
    };
    trigger.addEventListener('click', () => {
        returnFocus = trigger;
        if (typeof dialog.showModal === 'function') dialog.showModal();
        else { dialog.setAttribute('open', ''); dialog.classList.add('is-open'); }
        dialog.querySelector('[data-mfa-setup-close]')?.focus();
    });
    dialog.querySelectorAll('[data-mfa-setup-close]').forEach((button) => button.addEventListener('click', close));
    dialog.addEventListener('click', (event) => { if (event.target === dialog) close(); });
    dialog.addEventListener('cancel', (event) => { event.preventDefault(); close(); });
    dialog.addEventListener('close', () => {
        if (returnFocus) { returnFocus.focus?.(); returnFocus = null; }
    });
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

// Mark only the controls that failed validation. The message stays in the
// existing inline error or summary; fields receive a red border without a fill.
document.querySelectorAll('[data-validation-summary]').forEach((summary) => {
    let fields = [];
    try { fields = JSON.parse(summary.dataset.validationFields || '[]'); } catch { fields = []; }
    if (!fields.length) return;
    const scope = summary.closest('form, .auth-panel, .portal-content, main') || document;
    const normalise = (name) => name.replace(/\[([^\]]+)\]/g, '.$1');
    const invalid = new Set(fields.map((field) => normalise(String(field))));
    scope.querySelectorAll('input[name], select[name], textarea[name]').forEach((control) => {
        if (control.type === 'hidden' || !invalid.has(normalise(control.name))) return;
        control.classList.add('has-validation-error');
        control.setAttribute('aria-invalid', 'true');
    });
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
    const bindRemove = (row) => row.querySelector('[data-remove-social]')?.addEventListener('click', () => row.remove());
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

document.querySelectorAll('[data-comment-reaction-form]').forEach((form) => form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const button = form.querySelector('button');
    if (!button || button.disabled) return;
    button.disabled = true;
    try {
        const response = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (!response.ok) throw new Error('Comment reaction failed');
        const payload = await response.json();
        const group = form.closest('[data-comment-reaction-group]');
        group?.querySelectorAll('[data-comment-reaction-form]').forEach((reactionForm) => {
            const type = reactionForm.querySelector('input[name="type"]')?.value;
            const reactionButton = reactionForm.querySelector('button');
            if (!reactionButton || !type) return;
            const active = payload.user_type === type;
            reactionButton.classList.toggle('is-active', active);
            reactionButton.setAttribute('aria-pressed', String(active));
            reactionButton.setAttribute('aria-label', active ? `Remove thumbs ${type}` : `Thumbs ${type} this comment`);
            reactionButton.title = active ? `Remove thumbs ${type}` : `Thumbs ${type}`;
            const count = reactionButton.querySelector(`[data-comment-reaction-count="${type}"]`);
            if (count) count.textContent = payload[`${type}_count`];
        });
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

document.querySelectorAll('[data-gallery-viewer]').forEach((viewer) => {
    let items = [];
    try { items = JSON.parse(viewer.querySelector('[data-gallery-items-json]')?.textContent || '[]'); } catch { items = []; }
    const triggers = [...document.querySelectorAll('[data-gallery-trigger]')];
    const mediaStage = viewer.querySelector('[data-gallery-media]');
    const count = viewer.querySelector('[data-gallery-count]');
    const caption = viewer.querySelector('[data-gallery-caption]');
    const previous = viewer.querySelector('[data-gallery-prev]');
    const next = viewer.querySelector('[data-gallery-next]');
    const closeButton = viewer.querySelector('[data-gallery-close]');
    let activeIndex = 0;
    let returnFocus = null;
    let touchStartX = null;
    if (!mediaStage || !items.length) return;

    const normaliseIndex = (index) => (index + items.length) % items.length;
    const preloadAdjacent = () => [normaliseIndex(activeIndex - 1), normaliseIndex(activeIndex + 1)].forEach((index) => {
        const item = items[index];
        if (item?.type === 'image' && item.fullUrl) { const image = new Image(); image.src = item.fullUrl; }
    });
    const render = (index) => {
        activeIndex = normaliseIndex(index);
        const item = items[activeIndex];
        mediaStage.replaceChildren();
        if (item.type === 'video') {
            const video = document.createElement('video');
            video.controls = true;
            video.preload = 'metadata';
            video.setAttribute('aria-label', item.alt || 'Project video');
            if (item.poster) video.poster = item.poster;
            const source = document.createElement('source');
            source.src = item.fullUrl || item.url;
            source.type = item.mime || 'video/mp4';
            video.append(source);
            mediaStage.append(video);
        } else {
            const image = document.createElement('img');
            image.src = item.fullUrl || item.url;
            if (item.srcset) image.srcset = item.srcset;
            image.sizes = 'min(100vw, 1200px)';
            image.width = item.width || '';
            image.height = item.height || '';
            image.alt = item.alt || '';
            mediaStage.append(image);
        }
        count.textContent = `${activeIndex + 1} / ${items.length}`;
        caption.textContent = item.caption || item.alt || '';
        preloadAdjacent();
    };
    const open = (index, trigger) => {
        returnFocus = trigger || document.activeElement;
        render(index);
        viewer.hidden = false;
        document.body.classList.add('studio-gallery-open');
        closeButton?.focus();
    };
    const close = () => {
        viewer.hidden = true;
        mediaStage.replaceChildren();
        document.body.classList.remove('studio-gallery-open');
        returnFocus?.focus?.();
        returnFocus = null;
    };
    const move = (step) => render(activeIndex + step);
    triggers.forEach((trigger) => trigger.addEventListener('click', () => open(Number(trigger.dataset.galleryIndex || 0), trigger)));
    previous?.addEventListener('click', () => move(-1));
    next?.addEventListener('click', () => move(1));
    closeButton?.addEventListener('click', close);
    viewer.addEventListener('click', (event) => { if (event.target === viewer) close(); });
    viewer.addEventListener('touchstart', (event) => { touchStartX = event.changedTouches[0]?.clientX ?? null; }, { passive: true });
    viewer.addEventListener('touchend', (event) => {
        if (touchStartX === null) return;
        const delta = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
        if (Math.abs(delta) > 45) move(delta < 0 ? 1 : -1);
        touchStartX = null;
    }, { passive: true });
    viewer.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') { event.preventDefault(); close(); return; }
        if (event.key === 'ArrowLeft') { event.preventDefault(); move(-1); return; }
        if (event.key === 'ArrowRight') { event.preventDefault(); move(1); return; }
        if (event.key !== 'Tab') return;
        const focusable = focusablesWithin(viewer);
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });
});

const journalViewGrid = document.querySelector('.editorial-article-grid');
const journalViewButtons = [...document.querySelectorAll('[data-journal-view]')];
const journalViewInput = document.querySelector('[data-journal-view-input]');
if (journalViewGrid && journalViewButtons.length) {
    const journalViewKey = 'mwanafunzi-journal-view';
    const applyJournalView = (view, persist = true) => {
        const selectedView = view === 'list' ? 'list' : 'grid';
        journalViewGrid.classList.toggle('is-list-view', selectedView === 'list');
        journalViewButtons.forEach((button) => {
            const selected = button.dataset.journalView === selectedView;
            button.classList.toggle('is-active', selected);
            button.setAttribute('aria-pressed', String(selected));
        });
        if (journalViewInput) journalViewInput.value = selectedView;
        if (persist) {
            try { window.localStorage.setItem(journalViewKey, selectedView); } catch { /* Storage can be unavailable. */ }
        }
    };
    const queryView = new URLSearchParams(window.location.search).get('view');
    let savedJournalView = queryView === 'list' || queryView === 'grid' ? queryView : 'grid';
    if (!queryView) {
        try { savedJournalView = window.localStorage.getItem(journalViewKey) || 'grid'; } catch { /* Storage can be unavailable. */ }
    }
    applyJournalView(savedJournalView, false);
    journalViewButtons.forEach((button) => button.addEventListener('click', () => applyJournalView(button.dataset.journalView)));
}

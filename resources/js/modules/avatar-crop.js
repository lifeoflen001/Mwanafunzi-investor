const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
const focusablesWithin = (container) => [...(container?.querySelectorAll(focusableSelector) || [])].filter((element) => element.getClientRects().length > 0);
const avatarForm = document.querySelector('[data-avatar-form]');
const avatarInput = document.querySelector('[data-avatar-input]');
const avatarCropModal = document.querySelector('[data-avatar-crop-modal]');
const avatarCropCanvas = document.querySelector('[data-avatar-crop-canvas]');
const avatarCropZoom = document.querySelector('[data-avatar-crop-zoom]');
const avatarCropApply = document.querySelector('[data-avatar-crop-apply]');
const avatarCropCancelButtons = document.querySelectorAll('[data-avatar-crop-cancel]');
const avatarCropStatus = document.querySelector('[data-avatar-crop-status]');
const avatarCropHelp = avatarCropModal?.querySelector('.admin-avatar-crop-help');
let avatarCropImage = null;
let avatarCropObjectUrl = null;
let avatarCropOffset = { x: 0, y: 0 };
let avatarCropDragging = false;
let avatarCropLastPoint = null;
let avatarCropReturnFocus = null;

const drawAvatarCrop = () => {
    if (!avatarCropCanvas || !avatarCropImage) return;
    const context = avatarCropCanvas.getContext('2d');
    const width = avatarCropImage.naturalWidth;
    const height = avatarCropImage.naturalHeight;
    const zoom = Number(avatarCropZoom?.value || 1);
    const cropSize = Math.min(width, height) / zoom;
    const maxX = (width - cropSize) / 2;
    const maxY = (height - cropSize) / 2;
    avatarCropOffset.x = Math.max(-maxX, Math.min(maxX, avatarCropOffset.x));
    avatarCropOffset.y = Math.max(-maxY, Math.min(maxY, avatarCropOffset.y));
    const sourceX = (width - cropSize) / 2 + avatarCropOffset.x;
    const sourceY = (height - cropSize) / 2 + avatarCropOffset.y;
    context.clearRect(0, 0, avatarCropCanvas.width, avatarCropCanvas.height);
    context.fillStyle = '#202521';
    context.fillRect(0, 0, avatarCropCanvas.width, avatarCropCanvas.height);
    context.drawImage(avatarCropImage, sourceX, sourceY, cropSize, cropSize, 0, 0, avatarCropCanvas.width, avatarCropCanvas.height);
};

const closeAvatarCrop = (clearFile = true) => {
    if (!avatarCropModal) return;
    avatarCropModal.hidden = true;
    avatarCropImage = null;
    if (avatarCropObjectUrl) URL.revokeObjectURL(avatarCropObjectUrl);
    avatarCropObjectUrl = null;
    if (avatarCropZoom) avatarCropZoom.value = '1';
    avatarCropOffset = { x: 0, y: 0 };
    if (clearFile && avatarInput) avatarInput.value = '';
    const returnFocus = avatarCropReturnFocus;
    avatarCropReturnFocus = null;
    returnFocus?.focus?.();
};

const openAvatarCrop = (file) => {
    if (!avatarCropModal || !avatarCropCanvas || !file) return;
    if (!['image/jpeg', 'image/png', 'image/webp', 'image/avif'].includes(file.type) || file.size > 4 * 1024 * 1024) {
        if (avatarCropHelp) avatarCropHelp.textContent = 'Choose a JPG, PNG, WebP or AVIF image up to 4 MB.';
        if (avatarInput) avatarInput.value = '';
        return;
    }
    avatarCropReturnFocus = document.activeElement;
    avatarCropObjectUrl = URL.createObjectURL(file);
    avatarCropImage = new Image();
    avatarCropImage.onload = () => {
        avatarCropOffset = { x: 0, y: 0 };
        if (avatarCropZoom) avatarCropZoom.value = '1';
        drawAvatarCrop();
        avatarCropModal.hidden = false;
        avatarCropApply?.focus();
    };
    avatarCropImage.onerror = () => closeAvatarCrop();
    avatarCropImage.src = avatarCropObjectUrl;
};

avatarInput?.addEventListener('change', () => openAvatarCrop(avatarInput.files?.[0]));
avatarCropZoom?.addEventListener('input', drawAvatarCrop);
avatarCropCanvas?.addEventListener('pointerdown', (event) => {
    avatarCropDragging = true;
    avatarCropLastPoint = { x: event.clientX, y: event.clientY };
    avatarCropCanvas.setPointerCapture(event.pointerId);
});
avatarCropCanvas?.addEventListener('pointermove', (event) => {
    if (!avatarCropDragging || !avatarCropImage) return;
    const zoom = Number(avatarCropZoom?.value || 1);
    const cropSize = Math.min(avatarCropImage.naturalWidth, avatarCropImage.naturalHeight) / zoom;
    const scale = cropSize / avatarCropCanvas.width;
    avatarCropOffset.x -= (event.clientX - avatarCropLastPoint.x) * scale;
    avatarCropOffset.y -= (event.clientY - avatarCropLastPoint.y) * scale;
    avatarCropLastPoint = { x: event.clientX, y: event.clientY };
    drawAvatarCrop();
});
avatarCropCanvas?.addEventListener('pointerup', () => { avatarCropDragging = false; avatarCropLastPoint = null; });
avatarCropCanvas?.addEventListener('pointercancel', () => { avatarCropDragging = false; avatarCropLastPoint = null; });
avatarCropCancelButtons.forEach((button) => button.addEventListener('click', () => closeAvatarCrop()));
avatarCropApply?.addEventListener('click', () => {
    if (!avatarCropCanvas || !avatarInput || !avatarCropImage) return;
    avatarCropCanvas.toBlob((blob) => {
        if (!blob || typeof DataTransfer === 'undefined') return;
        const transfer = new DataTransfer();
        transfer.items.add(new File([blob], 'profile-cropped.png', { type: 'image/png', lastModified: Date.now() }));
        avatarInput.files = transfer.files;
        avatarInput.dataset.cropped = 'true';
        if (avatarCropStatus) avatarCropStatus.textContent = 'Cropped photo ready to upload.';
        closeAvatarCrop(false);
    }, 'image/png', .92);
});
document.addEventListener('keydown', (event) => {
    if (!avatarCropModal || avatarCropModal.hidden) return;
    if (event.key === 'Escape') { closeAvatarCrop(); return; }
    if (event.key !== 'Tab') return;
    const focusable = focusablesWithin(avatarCropModal);
    if (!focusable.length) return;
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
});

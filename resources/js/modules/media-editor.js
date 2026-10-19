document.querySelectorAll('[data-media-picker]').forEach((picker) => {
    const search = picker.querySelector('.media-picker-search');
    const select = picker.querySelector('[data-media-select]');
    const preview = picker.querySelector('[data-media-preview]');
    const loading = picker.querySelector('[data-media-loading]');
    const status = picker.querySelector('[data-media-status]');
    const searchUrl = picker.dataset.mediaSearchUrl;
    let searchTimer;
    let requestController;
    const updatePreview = () => {
        const option = select.selectedOptions[0];
        preview.replaceChildren();
        if (!option?.dataset.url) { preview.hidden = true; return; }
        if (option.dataset.mime?.startsWith('video/')) {
            const video = document.createElement('video');
            video.src = option.dataset.url;
            video.controls = true;
            video.preload = 'metadata';
            video.setAttribute('aria-label', 'Selected media preview');
            preview.append(video);
        } else {
            const image = document.createElement('img');
            image.src = option.dataset.url;
            image.alt = 'Selected media preview';
            image.loading = 'lazy';
            preview.append(image);
        }
        preview.hidden = false;
    };
    const renderResults = (items) => {
        const selected = select.value;
        const selectedUrl = select.selectedOptions[0]?.dataset.url;
        select.replaceChildren(new Option('No media selected', ''));
        items.forEach((item) => {
            const option = new Option(`${item.label}${item.status ? ` · ${item.status}` : ''}`, item.path, false, item.path === selected);
            option.dataset.url = item.url;
            option.dataset.mime = item.mime_type || '';
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
        const currentController = new AbortController();
        requestController = currentController;
        const url = new URL(searchUrl, window.location.origin);
        if (search.value.trim()) url.searchParams.set('q', search.value.trim());
        picker.setAttribute('aria-busy', 'true');
        loading?.removeAttribute('hidden');
        if (status) status.textContent = 'Loading media results.';
        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: currentController.signal });
            if (!response.ok) throw new Error(`Media search returned ${response.status}`);
            renderResults(await response.json());
            if (status) status.textContent = 'Media results updated.';
        } catch (error) {
            if (error.name !== 'AbortError') {
                if (status) status.textContent = 'Media results are unavailable.';
                console.warn('Media search failed', error);
            }
        } finally {
            if (requestController === currentController) {
                picker.removeAttribute('aria-busy');
                loading?.setAttribute('hidden', 'hidden');
            }
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

    const uploadInput = picker.querySelector('[data-media-upload-input]');
    const uploadButton = picker.querySelector('[data-media-upload-button]');
    const uploadProgress = picker.querySelector('[data-media-upload-progress]');
    const uploadProgressBar = picker.querySelector('[data-media-upload-progress-bar]');
    const uploadProgressLabel = picker.querySelector('[data-media-upload-progress-label]');
    const uploadUrl = picker.dataset.mediaUploadAction;

    if (uploadInput && uploadButton && uploadUrl) {
        const defaultButtonLabel = uploadButton.textContent;
        const setProgress = (percent) => {
            const value = Math.max(0, Math.min(100, Math.round(percent)));
            uploadProgress?.removeAttribute('hidden');
            if (uploadProgressBar) uploadProgressBar.style.width = `${value}%`;
            if (uploadProgressLabel) uploadProgressLabel.textContent = `${value}%`;
            uploadButton.textContent = `Uploading ${value}%`;
        };

        uploadButton.addEventListener('click', () => uploadInput.click());
        uploadInput.addEventListener('change', () => {
            const file = uploadInput.files?.[0];
            if (!file) return;

            uploadButton.disabled = true;
            uploadInput.disabled = true;
            if (status) status.textContent = `Uploading ${file.name}.`;
            setProgress(0);

            const formData = new FormData();
            formData.append('file', file);
            const csrf = picker.closest('form')?.querySelector('input[name="_token"]')?.value;
            if (csrf) formData.append('_token', csrf);

            const request = new XMLHttpRequest();
            request.open('POST', uploadUrl);
            request.setRequestHeader('Accept', 'application/json');
            request.upload.addEventListener('progress', (event) => {
                if (event.lengthComputable) setProgress((event.loaded / event.total) * 100);
            });
            request.addEventListener('load', () => {
                let payload = null;
                try { payload = JSON.parse(request.responseText); } catch (error) { /* handled below */ }
                if (request.status < 200 || request.status >= 300 || !payload?.path) {
                    const message = payload?.message || Object.values(payload?.errors || {}).flat()[0] || 'The media could not be uploaded.';
                    if (status) status.textContent = message;
                    uploadButton.textContent = defaultButtonLabel;
                    uploadProgress?.setAttribute('hidden', 'hidden');
                    uploadButton.disabled = false;
                    uploadInput.disabled = false;
                    uploadInput.value = '';
                    return;
                }

                const existing = [...select.options].find((option) => option.value === payload.path);
                existing?.remove();
                const option = new Option(`${payload.filename} · uploaded`, payload.path, true, true);
                option.dataset.url = payload.url;
                option.dataset.mime = payload.mime_type || file.type || '';
                select.append(option);
                select.value = payload.path;
                updatePreview();
                setProgress(100);
                uploadButton.textContent = 'Uploaded';
                if (status) status.textContent = `${payload.filename} uploaded and selected.`;
                uploadButton.disabled = false;
                uploadInput.disabled = false;
                uploadInput.value = '';
            });
            request.addEventListener('error', () => {
                if (status) status.textContent = 'The media could not be uploaded. Check the connection and try again.';
                uploadButton.textContent = defaultButtonLabel;
                uploadProgress?.setAttribute('hidden', 'hidden');
                uploadButton.disabled = false;
                uploadInput.disabled = false;
                uploadInput.value = '';
            });
            request.send(formData);
        });
    }
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

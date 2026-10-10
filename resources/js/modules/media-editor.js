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
        image.loading = 'lazy';
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

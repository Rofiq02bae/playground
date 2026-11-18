import Sortable from 'sortablejs';

// Block editor untuk form artikel admin: susun isi artikel dari block
// (heading, paragraf, foto, list, kutipan) dengan drag & drop.
// Hasilnya diserialisasi ke JSON pada input hidden #blocks-json.
const list = document.getElementById('block-list');

if (list) {
    const hidden = document.getElementById('blocks-json');
    const hint = document.getElementById('block-empty-hint');
    const form = document.getElementById('article-form');

    const LABELS = {
        heading: 'Judul',
        paragraph: 'Paragraf',
        image: 'Foto',
        list: 'List',
        quote: 'Kutipan',
    };

    const inputCls =
        'w-full rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 bg-white';

    function el(html) {
        const template = document.createElement('template');
        template.innerHTML = html.trim();
        return template.content.firstElementChild;
    }

    function esc(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;');
    }

    function bodyFor(block) {
        switch (block.type) {
            case 'heading':
                return `
                    <div class="flex gap-2">
                        <select class="b-level w-28 shrink-0 rounded-lg border border-stone-300 px-2 py-2 text-sm outline-none focus:border-emerald-600 bg-white">
                            <option value="2" ${block.level !== 3 ? 'selected' : ''}>Besar (H2)</option>
                            <option value="3" ${block.level === 3 ? 'selected' : ''}>Kecil (H3)</option>
                        </select>
                        <input type="text" class="b-text ${inputCls}" placeholder="Tulis sub-judul..." value="${esc(block.text)}">
                    </div>`;
            case 'paragraph':
                return `<textarea class="b-text ${inputCls}" rows="4" placeholder="Tulis paragraf...">${esc(block.text)}</textarea>`;
            case 'image': {
                const src = block.src || '';
                const preview = src
                    ? `<img class="b-preview mb-2 aspect-[16/9] w-full rounded-lg object-cover" src="${esc(src.startsWith('http') || src.startsWith('/') ? src : '/storage/' + src)}" alt="">`
                    : '<img class="b-preview mb-2 hidden aspect-[16/9] w-full rounded-lg object-cover" alt="">';
                return `
                    ${preview}
                    <input type="hidden" class="b-src" value="${esc(src)}">
                    <input type="file" accept="image/*" class="b-file w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-800 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white">
                    <input type="text" class="b-url ${inputCls} mt-2" placeholder="...atau tempel URL gambar" value="${esc(src.startsWith('http') ? src : '')}">
                    <input type="text" class="b-caption ${inputCls} mt-2" placeholder="Keterangan foto (opsional)" value="${esc(block.caption)}">`;
            }
            case 'list':
                return `
                    <select class="b-ordered mb-2 w-44 rounded-lg border border-stone-300 px-2 py-2 text-sm outline-none focus:border-emerald-600 bg-white">
                        <option value="0" ${!block.ordered ? 'selected' : ''}>Bullet (•)</option>
                        <option value="1" ${block.ordered ? 'selected' : ''}>Nomor (1.)</option>
                    </select>
                    <textarea class="b-items ${inputCls}" rows="4" placeholder="Satu item per baris...">${esc((block.items || []).join('\n'))}</textarea>`;
            case 'quote':
                return `
                    <textarea class="b-text ${inputCls}" rows="3" placeholder="Tulis kutipan...">${esc(block.text)}</textarea>
                    <input type="text" class="b-source ${inputCls} mt-2" placeholder="Sumber kutipan (opsional)" value="${esc(block.source)}">`;
            default:
                return '';
        }
    }

    function addBlock(block) {
        const item = el(`
            <div class="block-item rounded-xl border border-stone-200 bg-stone-50 p-3" data-type="${block.type}">
                <div class="mb-2.5 flex items-center gap-2">
                    <button type="button" class="drag-handle cursor-grab rounded-md px-1.5 py-1 text-stone-400 hover:bg-stone-200 hover:text-stone-600" title="Seret untuk memindah" aria-label="Seret untuk memindah">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>
                    </button>
                    <span class="text-xs font-bold uppercase tracking-wide text-stone-500">${LABELS[block.type] || block.type}</span>
                    <span class="ml-auto flex items-center gap-1">
                        <button type="button" class="b-up rounded-md px-2 py-1 text-sm text-stone-500 hover:bg-stone-200" title="Naikkan" aria-label="Naikkan">&uarr;</button>
                        <button type="button" class="b-down rounded-md px-2 py-1 text-sm text-stone-500 hover:bg-stone-200" title="Turunkan" aria-label="Turunkan">&darr;</button>
                        <button type="button" class="b-del rounded-md px-2 py-1 text-sm font-medium text-red-500 hover:bg-red-50" title="Hapus block" aria-label="Hapus block">&times;</button>
                    </span>
                </div>
                <div class="block-body">${bodyFor(block)}</div>
            </div>`);

        item.querySelector('.b-del').addEventListener('click', () => {
            item.remove();
            refreshHint();
        });
        item.querySelector('.b-up').addEventListener('click', () => {
            const prev = item.previousElementSibling;
            if (prev) list.insertBefore(item, prev);
        });
        item.querySelector('.b-down').addEventListener('click', () => {
            const next = item.nextElementSibling;
            if (next) list.insertBefore(next, item);
        });

        const fileInput = item.querySelector('.b-file');
        if (fileInput) {
            fileInput.addEventListener('change', () => {
                const file = fileInput.files[0];
                const preview = item.querySelector('.b-preview');
                if (file && preview) {
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('hidden');
                }
            });
        }

        list.appendChild(item);
        refreshHint();
        return item;
    }

    function refreshHint() {
        if (hint) hint.classList.toggle('hidden', list.children.length > 0);
    }

    function serialize() {
        const blocks = [];
        list.querySelectorAll('.block-item').forEach((item, index) => {
            const type = item.dataset.type;
            const val = (selector) => item.querySelector(selector)?.value ?? '';

            if (type === 'heading') {
                blocks.push({ type, level: parseInt(val('.b-level'), 10) || 2, text: val('.b-text') });
            } else if (type === 'paragraph') {
                blocks.push({ type, text: val('.b-text') });
            } else if (type === 'image') {
                const url = val('.b-url').trim();
                blocks.push({ type, src: url !== '' ? url : val('.b-src'), caption: val('.b-caption') });
                const fileInput = item.querySelector('.b-file');
                if (fileInput) fileInput.name = `block_images[${index}]`;
            } else if (type === 'list') {
                blocks.push({
                    type,
                    ordered: val('.b-ordered') === '1',
                    items: val('.b-items').split('\n'),
                });
            } else if (type === 'quote') {
                blocks.push({ type, text: val('.b-text'), source: val('.b-source') });
            }
        });
        return blocks;
    }

    new Sortable(list, { handle: '.drag-handle', animation: 150, ghostClass: 'opacity-40' });

    document.querySelectorAll('[data-add-block]').forEach((button) => {
        button.addEventListener('click', () => {
            const item = addBlock({ type: button.dataset.addBlock });
            item.scrollIntoView({ behavior: 'smooth', block: 'center' });
            item.querySelector('input, textarea, select')?.focus();
        });
    });

    let initial = [];
    try {
        initial = JSON.parse(document.getElementById('blocks-initial')?.textContent || '[]');
    } catch {
        initial = [];
    }
    if (Array.isArray(initial) && initial.length > 0) {
        initial.forEach(addBlock);
    } else {
        addBlock({ type: 'paragraph', text: '' });
    }

    form?.addEventListener('submit', () => {
        hidden.value = JSON.stringify(serialize());
    });
}

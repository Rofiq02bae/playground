import Sortable from 'sortablejs';

// Block editor ala Gutenberg untuk form artikel admin.
// - Canvas editor ditata mirip tampilan artikel asli (judul besar, paragraf
//   lega) dan diedit langsung di tempat (contenteditable + rich text).
// - Tiap block: seret lewat handle, naik/turun, duplikat, hapus, tambah
//   block baru di bawahnya lewat menu sisip (+).
// - Hasil diserialisasi ke JSON pada input hidden #blocks-json.
const list = document.getElementById('block-list');

if (list) {
    const hidden = document.getElementById('blocks-json');
    const form = document.getElementById('article-form');

    const TYPES = [
        ['heading', 'Judul (sub-bab)'],
        ['paragraph', 'Paragraf'],
        ['image', 'Foto'],
        ['list', 'List'],
        ['quote', 'Kutipan'],
        ['code', 'Kode'],
        ['video', 'Video (YouTube/Vimeo)'],
        ['separator', 'Garis pemisah'],
    ];
    const LABELS = Object.fromEntries(TYPES);

    const fieldCls =
        'w-full rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100';
    const smallFieldCls =
        'rounded-lg border border-stone-200 bg-white px-2 py-1 text-xs outline-none focus:border-emerald-600';

    function el(html) {
        const t = document.createElement('template');
        t.innerHTML = html.trim();
        return t.content.firstElementChild;
    }

    function esc(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;');
    }

    // ---------- Rich text toolbar (mengambang di atas seleksi) ----------
    const toolbar = el(`
        <div id="rich-toolbar" class="fixed z-50 hidden items-center gap-1 rounded-lg bg-stone-900 p-1 shadow-lg">
            <button type="button" data-cmd="bold" class="rounded px-2 py-1 text-xs font-bold text-white hover:bg-stone-700" title="Tebal">B</button>
            <button type="button" data-cmd="italic" class="rounded px-2 py-1 text-xs italic text-white hover:bg-stone-700" title="Miring">I</button>
            <button type="button" data-cmd="underline" class="rounded px-2 py-1 text-xs underline text-white hover:bg-stone-700" title="Garis bawah">U</button>
            <button type="button" data-cmd="link" class="rounded px-2 py-1 text-xs text-white hover:bg-stone-700" title="Sisipkan link">Link</button>
            <button type="button" data-cmd="unlink" class="rounded px-2 py-1 text-xs text-white hover:bg-stone-700" title="Hapus link">&times; Link</button>
        </div>`);
    document.body.appendChild(toolbar);

    let savedRange = null;

    function currentEditable() {
        const sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return null;
        const node = sel.anchorNode;
        const host = (node.nodeType === 1 ? node : node.parentElement)?.closest?.('.ff-editable');
        return host || null;
    }

    document.addEventListener('selectionchange', () => {
        const sel = window.getSelection();
        const host = currentEditable();
        if (!host || !sel || sel.isCollapsed) {
            toolbar.classList.add('hidden');
            toolbar.classList.remove('flex');
            return;
        }
        savedRange = sel.getRangeAt(0).cloneRange();
        const rect = sel.getRangeAt(0).getBoundingClientRect();
        toolbar.style.left = Math.max(8, rect.left + rect.width / 2 - 90) + 'px';
        toolbar.style.top = rect.top - 44 + 'px';
        toolbar.classList.remove('hidden');
        toolbar.classList.add('flex');
    });

    toolbar.addEventListener('mousedown', (event) => {
        event.preventDefault();
        const cmd = event.target.closest('[data-cmd]')?.dataset.cmd;
        if (!cmd || !savedRange) return;
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(savedRange);
        if (cmd === 'link') {
            const url = window.prompt('Alamat link (https://...)');
            if (url) document.execCommand('createLink', false, url);
        } else if (cmd === 'unlink') {
            document.execCommand('unlink');
        } else {
            document.execCommand(cmd);
        }
        savedRange = sel.rangeCount ? sel.getRangeAt(0).cloneRange() : null;
    });

    // ---------- Isi tiap tipe block ----------
    function bodyFor(block) {
        switch (block.type) {
            case 'heading':
                return `
                    <div class="mb-1 flex items-center gap-2">
                        <select class="b-level ${smallFieldCls}">
                            <option value="2" ${block.level !== 3 ? 'selected' : ''}>H2 — besar</option>
                            <option value="3" ${block.level === 3 ? 'selected' : ''}>H3 — kecil</option>
                        </select>
                    </div>
                    <div class="ff-editable b-text text-[1.55rem] font-bold leading-snug text-stone-900 outline-none" contenteditable="true" data-placeholder="Tulis sub-judul...">${block.text || ''}</div>`;
            case 'paragraph':
                return `<div class="ff-editable b-text text-[1.05rem] leading-[1.85] text-stone-700 outline-none" contenteditable="true" data-placeholder="Tulis paragraf... (sorot teks untuk bold / italic / link)">${block.text || ''}</div>`;
            case 'image': {
                const src = block.src || '';
                const resolved = src ? (src.startsWith('http') || src.startsWith('/') ? src : '/storage/' + src) : '';
                return `
                    ${resolved ? `<img class="b-preview mb-2 w-full rounded-xl" src="${esc(resolved)}" alt="">` : '<img class="b-preview mb-2 hidden w-full rounded-xl" alt="">'}
                    <input type="hidden" class="b-src" value="${esc(src)}">
                    <div class="flex flex-wrap items-center gap-2">
                        <input type="file" accept="image/*" class="b-file text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-800 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white">
                        <select class="b-size ${smallFieldCls}">
                            <option value="normal" ${block.size !== 'full' ? 'selected' : ''}>Lebar normal</option>
                            <option value="full" ${block.size === 'full' ? 'selected' : ''}>Lebar penuh</option>
                        </select>
                    </div>
                    <input type="text" class="b-url ${fieldCls} mt-2" placeholder="...atau tempel URL gambar" value="${esc(src.startsWith('http') ? src : '')}">
                    <input type="text" class="b-caption ${fieldCls} mt-2" placeholder="Keterangan foto (opsional)" value="${esc(block.caption)}">`;
            }
            case 'list':
                return `
                    <select class="b-ordered mb-2 ${smallFieldCls}">
                        <option value="0" ${!block.ordered ? 'selected' : ''}>Bullet (•)</option>
                        <option value="1" ${block.ordered ? 'selected' : ''}>Nomor (1.)</option>
                    </select>
                    <textarea class="b-items ${fieldCls} font-[inherit]" rows="4" placeholder="Satu item per baris...">${esc((block.items || []).join('\n'))}</textarea>`;
            case 'quote':
                return `
                    <div class="border-l-4 border-emerald-600 bg-stone-50 py-1 pl-4">
                        <div class="ff-editable b-text text-lg italic leading-relaxed text-stone-600 outline-none" contenteditable="true" data-placeholder="Tulis kutipan...">${block.text || ''}</div>
                        <input type="text" class="b-source mt-2 w-full bg-transparent text-sm font-medium text-emerald-800 outline-none" placeholder="— Sumber kutipan (opsional)" value="${esc(block.source)}">
                    </div>`;
            case 'code':
                return `
                    <input type="text" class="b-language ${smallFieldCls} mb-2" placeholder="Bahasa (mis. php, javascript)" value="${esc(block.language)}">
                    <textarea class="b-text w-full rounded-lg bg-stone-900 px-4 py-3 font-mono text-[13px] leading-relaxed text-emerald-100 outline-none" rows="6" placeholder="Tempel kode di sini..." spellcheck="false">${esc(block.text)}</textarea>`;
            case 'video':
                return `
                    <input type="url" class="b-url ${fieldCls}" placeholder="Tempel link YouTube / Vimeo..." value="${esc(block.url || block.embed || '')}">
                    <input type="text" class="b-caption ${fieldCls} mt-2" placeholder="Keterangan video (opsional)" value="${esc(block.caption)}">
                    <p class="mt-1.5 text-xs text-stone-400">Contoh: https://www.youtube.com/watch?v=xxxx atau https://youtu.be/xxxx</p>`;
            case 'separator':
                return `<div class="py-1"><hr class="border-t-2 border-stone-200"></div>`;
            default:
                return '';
        }
    }

    function readBlock(item) {
        const type = item.dataset.type;
        const val = (s) => item.querySelector(s)?.value ?? '';
        const html = (s) => item.querySelector(s)?.innerHTML ?? '';
        switch (type) {
            case 'heading':
                return { type, level: parseInt(val('.b-level'), 10) || 2, text: html('.b-text') };
            case 'paragraph':
                return { type, text: html('.b-text') };
            case 'image': {
                const url = val('.b-url').trim();
                return { type, src: url !== '' ? url : val('.b-src'), caption: val('.b-caption'), size: val('.b-size') };
            }
            case 'list':
                return { type, ordered: val('.b-ordered') === '1', items: val('.b-items').split('\n') };
            case 'quote':
                return { type, text: html('.b-text'), source: val('.b-source') };
            case 'code':
                return { type, text: val('.b-text'), language: val('.b-language') };
            case 'video':
                return { type, url: val('.b-url'), caption: val('.b-caption') };
            case 'separator':
                return { type };
            default:
                return { type };
        }
    }

    function addBlock(block, afterItem = null) {
        const item = el(`
            <div class="ff-block group relative rounded-xl px-3 py-2 transition hover:bg-stone-50 focus-within:bg-stone-50" data-type="${block.type}">
                <div class="mb-1 flex items-center gap-1 opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                    <button type="button" class="drag-handle cursor-grab rounded px-1.5 py-0.5 text-stone-400 hover:bg-stone-200" title="Seret untuk memindah" aria-label="Seret untuk memindah">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>
                    </button>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">${LABELS[block.type] || block.type}</span>
                    <span class="relative ml-auto flex items-center gap-0.5">
                        <button type="button" class="b-add rounded px-2 py-0.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-50" title="Sisipkan block di bawah" aria-label="Sisipkan block di bawah">+</button>
                        <button type="button" class="b-up rounded px-1.5 py-0.5 text-sm text-stone-500 hover:bg-stone-200" title="Naikkan" aria-label="Naikkan">&uarr;</button>
                        <button type="button" class="b-down rounded px-1.5 py-0.5 text-sm text-stone-500 hover:bg-stone-200" title="Turunkan" aria-label="Turunkan">&darr;</button>
                        <button type="button" class="b-dup rounded px-2 py-0.5 text-xs font-medium text-stone-500 hover:bg-stone-200" title="Duplikat block">Duplikat</button>
                        <button type="button" class="b-del rounded px-1.5 py-0.5 text-sm font-medium text-red-500 hover:bg-red-50" title="Hapus block" aria-label="Hapus block">&times;</button>
                        <span class="b-menu absolute right-0 top-full z-40 mt-1 hidden w-52 rounded-xl border border-stone-200 bg-white p-1.5 shadow-lg"></span>
                    </span>
                </div>
                <div class="block-body">${bodyFor(block)}</div>
            </div>`);

        const menu = item.querySelector('.b-menu');
        TYPES.forEach(([type, label]) => {
            const btn = el(`<button type="button" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-stone-700 hover:bg-emerald-50 hover:text-emerald-900">${label}</button>`);
            btn.addEventListener('click', () => {
                menu.classList.add('hidden');
                const created = addBlock({ type }, item);
                created.querySelector('.ff-editable, input, textarea')?.focus();
            });
            menu.appendChild(btn);
        });

        item.querySelector('.b-add').addEventListener('click', () => {
            document.querySelectorAll('.b-menu').forEach((m) => { if (m !== menu) m.classList.add('hidden'); });
            menu.classList.toggle('hidden');
        });
        item.querySelector('.b-del').addEventListener('click', () => item.remove());
        item.querySelector('.b-dup').addEventListener('click', () => addBlock(readBlock(item), item));
        item.querySelector('.b-up').addEventListener('click', () => {
            const prev = item.previousElementSibling;
            if (prev) list.insertBefore(item, prev);
        });
        item.querySelector('.b-down').addEventListener('click', () => {
            const next = item.nextElementSibling;
            if (next) list.insertBefore(next, item);
        });

        item.querySelectorAll('.ff-editable').forEach((editable) => {
            editable.addEventListener('paste', (event) => {
                event.preventDefault();
                const text = (event.clipboardData || window.clipboardData).getData('text/plain');
                document.execCommand('insertText', false, text);
            });
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

        if (afterItem) {
            afterItem.after(item);
        } else {
            list.appendChild(item);
        }
        return item;
    }

    function serialize() {
        const blocks = [];
        list.querySelectorAll('.ff-block').forEach((item, index) => {
            blocks.push(readBlock(item));
            const fileInput = item.querySelector('.b-file');
            if (fileInput) fileInput.name = `block_images[${index}]`;
        });
        return blocks;
    }

    new Sortable(list, { handle: '.drag-handle', animation: 150, ghostClass: 'opacity-40' });

    document.querySelectorAll('[data-add-block]').forEach((button) => {
        button.addEventListener('click', () => {
            const item = addBlock({ type: button.dataset.addBlock });
            item.scrollIntoView({ behavior: 'smooth', block: 'center' });
            item.querySelector('.ff-editable, input, textarea')?.focus();
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.b-menu') && !event.target.closest('.b-add')) {
            document.querySelectorAll('.b-menu').forEach((m) => m.classList.add('hidden'));
        }
    });

    let initial = [];
    try {
        initial = JSON.parse(document.getElementById('blocks-initial')?.textContent || '[]');
    } catch {
        initial = [];
    }
    if (Array.isArray(initial) && initial.length > 0) {
        initial.forEach((block) => addBlock(block));
    } else {
        addBlock({ type: 'paragraph', text: '' });
    }

    form?.addEventListener('submit', () => {
        hidden.value = JSON.stringify(serialize());
    });
}

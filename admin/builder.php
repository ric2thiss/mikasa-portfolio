<?php include 'includes/header.php'; ?>
<style>
.builder-grid { display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem; }
.builder-item {
    background: var(--dark-2); border: 1px solid var(--gray-light); padding: 1rem; border-radius: 6px;
    display: flex; justify-content: space-between; align-items: center; cursor: grab;
}
.builder-item:active { cursor: grabbing; }
.builder-item.hidden-section { opacity: 0.5; border-left: 4px solid #ff5050; }
.builder-item.visible-section { border-left: 4px solid #50c878; }
.drag-handle { color: var(--gray); margin-right: 1rem; cursor: grab; font-size: 1.2rem; }
.b-left { display: flex; align-items: center; }
.b-title { font-weight: bold; color: var(--light); }
.b-type { font-size: 0.7rem; color: var(--gray); text-transform: uppercase; margin-left: 0.5rem; letter-spacing: 1px; }

.page-item {
    background: var(--dark-2); border: 1px solid var(--gray-light); padding: 1rem; border-radius: 6px;
    display: flex; justify-content: space-between; align-items: center;
}

.ql-container { background: var(--dark-2); color: var(--light); font-family: var(--font-body); font-size: 0.9rem; border-color: var(--gray-light) !important; min-height: 150px; }
.ql-toolbar { background: #2a2a2a; border-color: var(--gray-light) !important; border-top-left-radius: 4px; border-top-right-radius: 4px; }
.ql-toolbar .ql-stroke { stroke: #ccc !important; }
.ql-toolbar .ql-fill { fill: #ccc !important; }
.ql-toolbar .ql-picker-label { color: #ccc !important; }
.ql-toolbar .ql-picker-options { background: #2a2a2a !important; border-color: var(--gray-light) !important; }
.ql-toolbar .ql-picker-item { color: #ccc !important; }
.ql-toolbar button:hover .ql-stroke { stroke: #fff !important; }
.ql-toolbar button:hover .ql-fill { fill: #fff !important; }
.ql-toolbar button.ql-active .ql-stroke { stroke: #50c878 !important; }
.ql-toolbar button.ql-active .ql-fill { fill: #50c878 !important; }
.ql-container { border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; }
.ql-editor { min-height: 120px; }

.builder-modal {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.8); z-index: 9999;
    display: none; align-items: center; justify-content: center;
}
.builder-modal.show { display: flex; }
.builder-modal-content {
    background: var(--dark-2); padding: 2rem; border-radius: 8px;
    width: 90%; max-width: 500px; border: 1px solid var(--gray-light);
}
.builder-modal-content.large { max-width: 800px; }
</style>

<div class="admin-card" id="pages-view">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>Website Pages</h2>
        <button class="add-btn" style="width:auto;margin:0" onclick="showNewPageModal()">+ Add New Page</button>
    </div>
    
    <div class="builder-grid" id="pages-grid" style="margin-bottom: 2rem;">
        <!-- populated by js -->
    </div>
</div>

<div class="admin-card hidden" id="sections-view">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2 id="sections-title">Page Sections</h2>
        <div style="display:flex;gap:1rem;">
            <button class="save-btn" style="width:auto;margin:0" onclick="addSection()">+ Add Text Section</button>
            <button class="delete-btn" style="width:auto;margin:0" onclick="backToPages()">Back to Pages</button>
        </div>
    </div>
    <p style="color:var(--gray);font-size:0.9rem;margin-top:0.5rem;">Drag and drop to reorder sections. Toggle visibility.</p>
    
    <div class="builder-grid" id="builder-grid">
        <!-- populated by js -->
    </div>
    
    <button class="save-btn" onclick="saveBuilder()" style="margin-top: 2rem;">Save Layout</button>
</div>

<!-- Modal for New Page -->
<div id="page-modal" class="builder-modal">
    <div class="builder-modal-content">
        <h3>New Page</h3>
        <div class="form-group" style="margin-top:1rem;">
            <label>Page Title</label>
            <input type="text" id="new_page_title" class="login-input" placeholder="e.g. About Us">
        </div>
        <div style="display:flex;gap:1rem;margin-top:1rem;">
            <button class="save-btn" onclick="submitNewPage()">Create</button>
            <button class="delete-btn" onclick="document.getElementById('page-modal').classList.remove('show')">Cancel</button>
        </div>
    </div>
</div>

<!-- Modal for New Section -->
<div id="section-modal" class="builder-modal">
    <div class="builder-modal-content">
        <h3>New Text Section</h3>
        <div class="form-group" style="margin-top:1rem;">
            <label>Section Title</label>
            <input type="text" id="new_sec_title" class="login-input" placeholder="e.g. My Custom Section">
        </div>
        <div style="display:flex;gap:1rem;margin-top:1rem;">
            <button class="save-btn" onclick="submitNewSection()">Create</button>
            <button class="delete-btn" onclick="document.getElementById('section-modal').classList.remove('show')">Cancel</button>
        </div>
    </div>
</div>

<!-- Modal for Edit Section Content -->
<div id="edit-modal" class="builder-modal">
    <div class="builder-modal-content large">
        <h3>Edit Content</h3>
        <input type="hidden" id="edit_sec_id">
        <div class="form-group" style="margin-top:1rem;">
            <div id="editor-custom" style="background:var(--dark-3);color:var(--light);border:1px solid var(--gray-light);min-height:250px;"></div>
        </div>
        <div style="display:flex;gap:1rem;margin-top:1rem;">
            <button class="save-btn" onclick="saveCustomSection()">Save Content</button>
            <button class="delete-btn" onclick="document.getElementById('edit-modal').classList.remove('show')">Cancel</button>
        </div>
    </div>
</div>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
let pages = [];
let sections = [];
let currentPageId = 1;
let quillCustom;

document.addEventListener('DOMContentLoaded', () => {
    quillCustom = new Quill('#editor-custom', { 
        theme: 'snow', 
        modules: { toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'image', 'video'],
            ['clean']
        ]} 
    });
    loadPages();
});

async function loadPages() {
    const res = await fetch(`${API}?action=get_pages`);
    const json = await res.json();
    pages = json.data || [];
    renderPages();
}

function renderPages() {
    const grid = document.getElementById('pages-grid');
    grid.innerHTML = '';
    pages.forEach(p => {
        grid.innerHTML += `
        <div class="page-item" data-id="${p.id}">
            <div class="b-left">
                <span class="b-title">${esc(p.title)}</span>
                <span class="b-type">(/${esc(p.slug)})</span>
            </div>
            <div class="b-right">
                <a href="../page.php?slug=${esc(p.slug)}" target="_blank" class="item-save-btn" style="margin-right:0.5rem;text-decoration:none;display:inline-block">View Page</a>
                <button class="save-btn" style="margin-right:0.5rem;width:auto;display:inline-block;padding:0.4rem 1rem" onclick="editPageLayout(${p.id}, '${esc(p.title)}')">Edit Layout</button>
                ${p.slug !== 'home' ? `<button class="delete-btn" style="width:auto;display:inline-block;padding:0.4rem 1rem" onclick="deletePage(${p.id})">Delete</button>` : ''}
            </div>
        </div>`;
    });
}

function showNewPageModal() {
    document.getElementById('new_page_title').value = '';
    document.getElementById('page-modal').classList.add('show');
}

async function submitNewPage() {
    const title = document.getElementById('new_page_title').value;
    if (!title) return alert('Enter title');
    const form = new FormData();
    form.append('action', 'add_page');
    form.append('title', title);
    await fetch(API, { method: 'POST', body: form });
    document.getElementById('page-modal').classList.remove('show');
    loadPages();
    showAlert('Page created successfully!');
}

async function deletePage(id) {
    if (!confirm('Are you sure you want to delete this page and all its sections?')) return;
    const form = new FormData();
    form.append('action', 'delete_page');
    form.append('id', id);
    await fetch(API, { method: 'POST', body: form });
    loadPages();
    showAlert('Page deleted!');
}

async function editPageLayout(id, title) {
    currentPageId = id;
    document.getElementById('pages-view').classList.add('hidden');
    document.getElementById('sections-view').classList.remove('hidden');
    document.getElementById('sections-title').innerText = `Sections for: ${title}`;
    await loadBuilder();
}

function backToPages() {
    document.getElementById('sections-view').classList.add('hidden');
    document.getElementById('pages-view').classList.remove('hidden');
}

async function loadBuilder() {
    const res = await fetch(`${API}?action=get_sections&page_id=${currentPageId}`);
    const json = await res.json();
    sections = json.data || [];
    renderBuilder();
}

function renderBuilder() {
    const grid = document.getElementById('builder-grid');
    grid.innerHTML = '';
    
    if (sections.length === 0) {
        grid.innerHTML = '<p style="color:var(--gray)">No sections yet. Add a new section!</p>';
        return;
    }
    
    sections.forEach(s => {
        const vis = s.is_visible == 1;
        grid.innerHTML += `
        <div class="builder-item ${vis ? 'visible-section' : 'hidden-section'}" data-id="${s.id}">
            <div class="b-left">
                <span class="drag-handle">☰</span>
                <span class="b-title">${esc(s.title)}</span>
                <span class="b-type">(${esc(s.section_type)})</span>
            </div>
            <div class="b-right">
                <button class="item-save-btn" onclick="toggleVisibility(${s.id}, ${vis ? 0 : 1})" style="background:${vis ? 'var(--dark-3)' : '#50c878'};border:1px solid ${vis ? 'var(--gray-light)' : '#50c878'}">
                    ${vis ? 'Hide' : 'Show'}
                </button>
                ${s.section_type === 'richtext' ? `<button class="item-save-btn" onclick="editSection(${s.id})" style="margin-right:0.5rem">Edit</button><button class="delete-btn" onclick="deleteSection(${s.id})">Delete</button>` : ''}
            </div>
        </div>`;
    });
    
    new Sortable(grid, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'sortable-ghost'
    });
}

async function saveBuilder() {
    const items = document.querySelectorAll('.builder-item');
    const order = [];
    items.forEach((item, index) => {
        order.push({ id: item.dataset.id, sort_order: index + 1 });
    });
    
    const form = new FormData();
    form.append('action', 'save_section_order');
    form.append('order', JSON.stringify(order));
    await fetch(API, { method: 'POST', body: form });
    showAlert('Layout saved!');
}

async function toggleVisibility(id, state) {
    const form = new FormData();
    form.append('action', 'toggle_section_vis');
    form.append('id', id);
    form.append('state', state);
    await fetch(API, { method: 'POST', body: form });
    loadBuilder();
}

function addSection() {
    document.getElementById('new_sec_title').value = '';
    document.getElementById('section-modal').classList.add('show');
}

async function submitNewSection() {
    const title = document.getElementById('new_sec_title').value;
    if (!title) return alert('Enter title');
    const form = new FormData();
    form.append('action', 'add_custom_section');
    form.append('title', title);
    form.append('page_id', currentPageId);
    await fetch(API, { method: 'POST', body: form });
    document.getElementById('section-modal').classList.remove('show');
    loadBuilder();
    showAlert('Section added!');
}

async function deleteSection(id) {
    if (!confirm('Delete this section?')) return;
    const form = new FormData();
    form.append('action', 'delete_section');
    form.append('id', id);
    await fetch(API, { method: 'POST', body: form });
    loadBuilder();
    showAlert('Section deleted!');
}

async function editSection(id) {
    const sec = sections.find(s => s.id == id);
    document.getElementById('edit_sec_id').value = id;
    let content = {};
    try { content = JSON.parse(sec.content || '{}'); } catch(e){}
    quillCustom.root.innerHTML = content.html || '';
    document.getElementById('edit-modal').classList.add('show');
}

async function saveCustomSection() {
    const id = document.getElementById('edit_sec_id').value;
    const html = quillCustom.root.innerHTML;
    const form = new FormData();
    form.append('action', 'save_custom_section');
    form.append('id', id);
    form.append('html', html);
    await fetch(API, { method: 'POST', body: form });
    document.getElementById('edit-modal').classList.remove('show');
    showAlert('Content saved!');
    loadBuilder();
}
</script>
<?php include 'includes/footer.php'; ?>

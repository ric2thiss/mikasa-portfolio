document.addEventListener('DOMContentLoaded', () => {
    loadSettings(['portfolio_title', 'portfolio_desc']);
    loadPortfolio();
});

async function loadPortfolio() {
    const res = await fetch(`${API}?action=get_portfolio`);
    const json = await res.json();
    const list = document.getElementById('portfolio-list');
    list.innerHTML = '';
    (json.data || []).forEach(p => {
        const selCarousel = p.display_type === 'carousel' ? 'selected' : '';
        const selGrid = p.display_type === 'grid' ? 'selected' : '';
        list.innerHTML += `<div class="crud-item" data-id="${p.id}">
            <img src="../${esc(p.image_path)}" class="img-preview" alt="">
            <div class="form-group"><label>Category</label><input type="text" value="${esc(p.category_name)}" class="p-name"></div>
            <div class="form-group"><label>Tag</label><input type="text" value="${esc(p.category_tag)}" class="p-tag"></div>
            <div class="form-group"><label>Display Mode</label>
                <select class="p-type" style="width:100%;padding:0.5rem;background:var(--dark-3);border:1px solid var(--gray-light);color:var(--light);border-radius:4px">
                    <option value="carousel" ${selCarousel}>Carousel</option>
                    <option value="grid" ${selGrid}>Masonry Grid</option>
                </select>
            </div>
            <div class="form-group" style="max-width:70px"><label>Order</label><input type="number" value="${p.sort_order}" class="p-order"></div>
            <div class="form-group"><label>Image</label><input type="file" accept="image/*" class="p-file"></div>
            <div class="crud-actions" style="display:flex;flex-direction:column;gap:0.5rem;justify-content:center">
                <button class="item-save-btn" onclick="savePortfolio(this)">Save</button>
                <a href="gallery_manage.php?portfolio_id=${p.id}" class="add-btn" style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center;padding:0.5rem;font-size:0.75rem;margin:0;text-align:center">Manage Gallery</a>
                <button class="delete-btn" onclick="deletePortfolio(${p.id})">Delete</button>
            </div></div>`;
    });
}

function addPortfolioRow() {
    const list = document.getElementById('portfolio-list');
    list.innerHTML += `<div class="crud-item" data-id="">
        <div class="form-group"><label>Category</label><input type="text" class="p-name"></div>
        <div class="form-group"><label>Tag</label><input type="text" class="p-tag"></div>
        <div class="form-group"><label>Display Mode</label>
            <select class="p-type" style="width:100%;padding:0.5rem;background:var(--dark-3);border:1px solid var(--gray-light);color:var(--light);border-radius:4px">
                <option value="carousel">Carousel</option>
                <option value="grid">Masonry Grid</option>
            </select>
        </div>
        <div class="form-group" style="max-width:70px"><label>Order</label><input type="number" class="p-order" value="0"></div>
        <div class="form-group"><label>Image</label><input type="file" accept="image/*" class="p-file"></div>
        <div class="crud-actions"><button class="item-save-btn" onclick="savePortfolio(this)">Save</button></div></div>`;
}

async function savePortfolio(btn) {
    const item = btn.closest('.crud-item');
    const form = new FormData();
    form.append('action', 'save_portfolio');
    if (item.dataset.id) form.append('id', item.dataset.id);
    form.append('category_name', item.querySelector('.p-name').value);
    form.append('category_tag', item.querySelector('.p-tag').value);
    form.append('display_type', item.querySelector('.p-type').value);
    form.append('sort_order', item.querySelector('.p-order').value);
    const fileInput = item.querySelector('.p-file');
    if (fileInput && fileInput.files[0]) {
        form.append('image', fileInput.files[0]);
    } else {
        const img = item.querySelector('.img-preview');
        form.append('existing_image', img ? img.src.split('/mikasa/')[1] || '' : '');
    }
    const res = await fetch(API, { method: 'POST', body: form });
    const json = await res.json();
    showAlert(json.success ? 'Portfolio saved!' : 'Error.', json.success ? 'success' : 'error');
    loadPortfolio();
}

async function deletePortfolio(id) {
    if (!confirm('Delete this category?')) return;
    const form = new FormData();
    form.append('action', 'delete_portfolio');
    form.append('id', id);
    await fetch(API, { method: 'POST', body: form });
    showAlert('Portfolio item deleted.');
    loadPortfolio();
}
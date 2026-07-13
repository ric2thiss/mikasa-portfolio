document.addEventListener('DOMContentLoaded', loadSocials);

async function loadSocials() {
    const res = await fetch(`${API}?action=get_socials`);
    const json = await res.json();
    const list = document.getElementById('socials-list');
    list.innerHTML = '';
    (json.data || []).forEach(s => {
        list.innerHTML += `<div class="crud-item" data-id="${s.id}">
            <div class="form-group"><label>Platform</label><input type="text" value="${esc(s.platform_name)}" class="so-name"></div>
            <div class="form-group"><label>URL</label><input type="text" value="${esc(s.url)}" class="so-url"></div>
            <div class="form-group" style="max-width:70px"><label>Order</label><input type="number" value="${s.sort_order}" class="so-order"></div>
            <div class="crud-actions">
                <button class="item-save-btn" onclick="saveSocial(this)">Save</button>
                <button class="delete-btn" onclick="deleteSocial(${s.id})">Delete</button>
            </div></div>`;
    });
}

function addSocialRow() {
    const list = document.getElementById('socials-list');
    list.innerHTML += `<div class="crud-item" data-id="">
        <div class="form-group"><label>Platform</label><input type="text" class="so-name" placeholder="e.g. TikTok"></div>
        <div class="form-group"><label>URL</label><input type="text" class="so-url" placeholder="https://..."></div>
        <div class="form-group" style="max-width:70px"><label>Order</label><input type="number" class="so-order" value="0"></div>
        <div class="crud-actions"><button class="item-save-btn" onclick="saveSocial(this)">Save</button></div></div>`;
}

async function saveSocial(btn) {
    const item = btn.closest('.crud-item');
    const form = new FormData();
    form.append('action', 'save_social');
    if (item.dataset.id) form.append('id', item.dataset.id);
    form.append('platform_name', item.querySelector('.so-name').value);
    form.append('url', item.querySelector('.so-url').value);
    form.append('sort_order', item.querySelector('.so-order').value);
    const res = await fetch(API, { method: 'POST', body: form });
    const json = await res.json();
    showAlert(json.success ? 'Social link saved!' : 'Error.', json.success ? 'success' : 'error');
    loadSocials();
}

async function deleteSocial(id) {
    if (!confirm('Delete this link?')) return;
    const form = new FormData();
    form.append('action', 'delete_social');
    form.append('id', id);
    await fetch(API, { method: 'POST', body: form });
    showAlert('Link deleted.');
    loadSocials();
}
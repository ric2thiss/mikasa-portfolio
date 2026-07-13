document.addEventListener('DOMContentLoaded', () => {
    loadSettings(['services_title', 'services_desc']);
    loadServices();
});

async function loadServices() {
    const res = await fetch(`${API}?action=get_services`);
    const json = await res.json();
    const list = document.getElementById('services-list');
    list.innerHTML = '';
    (json.data || []).forEach(s => {
        list.innerHTML += `<div class="crud-item" data-id="${s.id}">
            <div class="form-group" style="max-width:70px"><label>#</label><input type="text" value="${esc(s.num)}" class="sv-num"></div>
            <div class="form-group"><label>Name</label><input type="text" value="${esc(s.name)}" class="sv-name"></div>
            <div class="form-group"><label>Description</label><input type="text" value="${esc(s.description)}" class="sv-desc"></div>
            <div class="form-group" style="max-width:70px"><label>Order</label><input type="number" value="${s.sort_order}" class="sv-order"></div>
            <div class="crud-actions">
                <button class="item-save-btn" onclick="saveService(this)">Save</button>
                <button class="delete-btn" onclick="deleteService(${s.id})">Delete</button>
            </div></div>`;
    });
}

function addServiceRow() {
    const list = document.getElementById('services-list');
    list.innerHTML += `<div class="crud-item" data-id="">
        <div class="form-group" style="max-width:70px"><label>#</label><input type="text" class="sv-num" placeholder="06"></div>
        <div class="form-group"><label>Name</label><input type="text" class="sv-name"></div>
        <div class="form-group"><label>Description</label><input type="text" class="sv-desc"></div>
        <div class="form-group" style="max-width:70px"><label>Order</label><input type="number" class="sv-order" value="0"></div>
        <div class="crud-actions"><button class="item-save-btn" onclick="saveService(this)">Save</button></div></div>`;
}

async function saveService(btn) {
    const item = btn.closest('.crud-item');
    const form = new FormData();
    form.append('action', 'save_service');
    if (item.dataset.id) form.append('id', item.dataset.id);
    form.append('num', item.querySelector('.sv-num').value);
    form.append('name', item.querySelector('.sv-name').value);
    form.append('description', item.querySelector('.sv-desc').value);
    form.append('sort_order', item.querySelector('.sv-order').value);
    const res = await fetch(API, { method: 'POST', body: form });
    const json = await res.json();
    showAlert(json.success ? 'Service saved!' : 'Error.', json.success ? 'success' : 'error');
    loadServices();
}

async function deleteService(id) {
    if (!confirm('Delete this service?')) return;
    const form = new FormData();
    form.append('action', 'delete_service');
    form.append('id', id);
    await fetch(API, { method: 'POST', body: form });
    showAlert('Service deleted.');
    loadServices();
}
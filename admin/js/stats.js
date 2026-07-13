document.addEventListener('DOMContentLoaded', loadStats);

async function loadStats() {
    const res = await fetch(`${API}?action=get_stats`);
    const json = await res.json();
    const list = document.getElementById('stats-list');
    list.innerHTML = '';
    (json.data || []).forEach(s => {
        list.innerHTML += `<div class="crud-item" data-id="${s.id}">
            <div class="form-group"><label>Number</label><input type="text" value="${esc(s.stat_num)}" class="s-num"></div>
            <div class="form-group"><label>Label</label><input type="text" value="${esc(s.stat_label)}" class="s-label"></div>
            <div class="form-group" style="max-width:80px"><label>Order</label><input type="number" value="${s.sort_order}" class="s-order"></div>
            <div class="crud-actions">
                <button class="item-save-btn" onclick="saveStat(this)">Save</button>
                <button class="delete-btn" onclick="deleteStat(${s.id})">Delete</button>
            </div></div>`;
    });
}

function addStatRow() {
    const list = document.getElementById('stats-list');
    list.innerHTML += `<div class="crud-item" data-id="">
        <div class="form-group"><label>Number</label><input type="text" class="s-num" placeholder="e.g. 10+"></div>
        <div class="form-group"><label>Label</label><input type="text" class="s-label" placeholder="e.g. Years"></div>
        <div class="form-group" style="max-width:80px"><label>Order</label><input type="number" class="s-order" value="0"></div>
        <div class="crud-actions"><button class="item-save-btn" onclick="saveStat(this)">Save</button></div></div>`;
}

async function saveStat(btn) {
    const item = btn.closest('.crud-item');
    const form = new FormData();
    form.append('action', 'save_stat');
    if (item.dataset.id) form.append('id', item.dataset.id);
    form.append('stat_num', item.querySelector('.s-num').value);
    form.append('stat_label', item.querySelector('.s-label').value);
    form.append('sort_order', item.querySelector('.s-order').value);
    const res = await fetch(API, { method: 'POST', body: form });
    const json = await res.json();
    showAlert(json.success ? 'Stat saved!' : 'Error.', json.success ? 'success' : 'error');
    loadStats();
}

async function deleteStat(id) {
    if (!confirm('Delete this stat?')) return;
    const form = new FormData();
    form.append('action', 'delete_stat');
    form.append('id', id);
    await fetch(API, { method: 'POST', body: form });
    showAlert('Stat deleted.');
    loadStats();
}
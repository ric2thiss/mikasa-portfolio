var API = 'api.php';

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
});

function initMobileMenu() {
    const btn = document.getElementById('admin-hamburger');
    const sidebar = document.getElementById('sidebar');
    if (btn && sidebar) {
        btn.addEventListener('click', () => sidebar.classList.toggle('open'));
    }
}

function showAlert(msg, type = 'success') {
    const el = document.getElementById('admin-alert');
    el.textContent = msg;
    el.className = 'admin-alert ' + type;
    setTimeout(() => el.classList.add('hidden'), 3000);
}

function esc(str) {
    const div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
}

async function loadSettings(keys) {
    const res = await fetch(`${API}?action=get_settings`);
    const json = await res.json();
    if (!json.success) return;
    const d = json.data;
    keys.forEach(f => {
        const el = document.getElementById(f);
        if (el && d[f] !== undefined) el.value = d[f];
    });
    const prev = document.getElementById('about_image_preview');
    if (prev && d['about_image']) prev.src = '../' + d['about_image'];
}

async function saveSettings(keys) {
    const fields = {};
    keys.forEach(k => {
        const el = document.getElementById(k);
        if (el) fields[k] = el.value;
    });
    const form = new FormData();
    form.append('action', 'update_settings');
    form.append('fields', JSON.stringify(fields));
    const res = await fetch(API, { method: 'POST', body: form });
    const json = await res.json();
    showAlert(json.success ? 'Settings saved!' : 'Error saving.', json.success ? 'success' : 'error');
}
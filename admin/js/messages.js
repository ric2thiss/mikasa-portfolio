document.addEventListener('DOMContentLoaded', loadMessages);

async function loadMessages() {
    const res = await fetch(`${API}?action=get_messages`);
    const json = await res.json();
    const list = document.getElementById('messages-list');
    if (!list) return;
    list.innerHTML = '';
    const msgs = json.data || [];
    if (msgs.length === 0) {
        list.innerHTML = '<p style="color:var(--gray);font-size:0.9rem;">No messages yet.</p>';
        return;
    }
    msgs.forEach(m => {
        const read = m.is_read == 1;
        list.innerHTML += `<div class="crud-item" style="flex-direction:column;align-items:stretch;${read ? 'opacity:0.6' : ''}">
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem">
                <strong style="color:var(--light)">${esc(m.name)}</strong>
                <span style="font-size:0.7rem;color:var(--gray)">${m.created_at}</span>
            </div>
            <div style="font-size:0.8rem;color:var(--gray);margin-bottom:0.3rem">${esc(m.email)} — ${esc(m.subject)}</div>
            <p style="font-size:0.85rem;color:var(--light-2);margin-bottom:0.8rem;line-height:1.6">${esc(m.message)}</p>
            <div class="crud-actions">
                ${!read ? `<button class="item-save-btn" onclick="markRead(${m.id})">Mark Read</button>` : ''}
                <button class="delete-btn" onclick="deleteMessage(${m.id})">Delete</button>
            </div></div>`;
    });
}

async function markRead(id) {
    const form = new FormData();
    form.append('action', 'mark_read');
    form.append('id', id);
    await fetch(API, { method: 'POST', body: form });
    loadMessages();
}

async function deleteMessage(id) {
    if (!confirm('Delete this message?')) return;
    const form = new FormData();
    form.append('action', 'delete_message');
    form.append('id', id);
    await fetch(API, { method: 'POST', body: form });
    showAlert('Message deleted.');
    loadMessages();
}
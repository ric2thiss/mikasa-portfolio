const API = 'api.php';

document.addEventListener('DOMContentLoaded', () => {
    loadGallery();

    const form = document.getElementById('add-gallery-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('action', 'add_gallery_image');

            const res = await fetch(API, {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            if (json.success) {
                showAlert('Image uploaded successfully!', 'success');
                form.reset();
                loadGallery();
            } else {
                showAlert(json.error || 'Upload failed.', 'error');
            }
        });
    }
});

function esc(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

async function loadGallery() {
    const res = await fetch(`${API}?action=get_gallery_images&portfolio_id=${portfolioId}`);
    const json = await res.json();
    const list = document.getElementById('gallery-list');
    list.innerHTML = '';
    
    if (!json.data || json.data.length === 0) {
        list.innerHTML = '<p style="color:var(--gray); grid-column: span 12; padding: 2rem 0;">No images in this gallery yet. Upload one above!</p>';
        return;
    }

    json.data.forEach(img => {
        list.innerHTML += `<div class="crud-item" data-id="${img.id}">
            <img src="../${esc(img.image_path)}" class="img-preview" alt="" style="width:100px;height:100px;object-fit:cover;border-radius:4px">
            <div class="form-group" style="flex:1"><label>Caption</label><input type="text" value="${esc(img.caption)}" class="img-caption" readonly></div>
            <div class="form-group" style="max-width:70px"><label>Order</label><input type="number" value="${img.sort_order}" class="img-order" onchange="updateImageOrder(${img.id}, this.value)"></div>
            <div class="crud-actions">
                <button class="delete-btn" onclick="deleteImage(${img.id})">Delete</button>
            </div>
        </div>`;
    });
}

async function deleteImage(id) {
    if (!confirm('Delete this image from gallery?')) return;
    const formData = new FormData();
    formData.append('action', 'delete_gallery_image');
    formData.append('id', id);

    const res = await fetch(API, {
        method: 'POST',
        body: formData
    });
    const json = await res.json();
    if (json.success) {
        showAlert('Image deleted.', 'success');
        loadGallery();
    } else {
        showAlert('Error deleting image.', 'error');
    }
}

async function updateImageOrder(id, order) {
    const list = document.getElementById('gallery-list');
    const items = list.querySelectorAll('.crud-item');
    const orderData = [];
    items.forEach(item => {
        const itemId = parseInt(item.dataset.id);
        const itemOrder = itemId === id ? parseInt(order) : parseInt(item.querySelector('.img-order').value);
        orderData.push({ id: itemId, sort_order: itemOrder });
    });

    // Sort orderData by sort_order
    orderData.sort((a, b) => a.sort_order - b.sort_order);
    
    // Normalize sort orders 1, 2, 3...
    orderData.forEach((item, index) => {
        item.sort_order = index + 1;
    });

    const formData = new FormData();
    formData.append('action', 'save_gallery_order');
    formData.append('order', JSON.stringify(orderData));

    await fetch(API, {
        method: 'POST',
        body: formData
    });
    loadGallery();
}

function showAlert(msg, type = 'success') {
    const alert = document.getElementById('admin-alert');
    if (!alert) return;
    alert.textContent = msg;
    alert.className = `admin-alert ${type}`;
    alert.classList.remove('hidden');
    setTimeout(() => alert.classList.add('hidden'), 3000);
}

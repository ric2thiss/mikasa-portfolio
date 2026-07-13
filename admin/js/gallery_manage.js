var API = 'api.php';

document.addEventListener('DOMContentLoaded', () => {
    loadGallery();

    const form = document.getElementById('add-gallery-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            
            const fileInput = form.querySelector('input[type="file"]');
            const files = fileInput.files;
            
            if (files.length === 0) {
                btn.disabled = false;
                return;
            }

            const portfolioId = form.querySelector('input[name="portfolio_id"]').value;
            const caption = form.querySelector('input[name="caption"]').value;

            let indicator = document.getElementById('upload-indicator');
            if (!indicator) {
                indicator = document.createElement('span');
                indicator.id = 'upload-indicator';
                indicator.style.marginLeft = '1rem';
                indicator.style.color = '#50c878';
                indicator.style.fontWeight = 'bold';
                btn.parentNode.insertBefore(indicator, btn.nextSibling);
            }

            let successCount = 0;
            let errorMessages = [];

            // Upload files one by one to bypass server post_max_size and max_file_uploads limits
            for (let i = 0; i < files.length; i++) {
                btn.textContent = `Uploading...`;
                indicator.textContent = ` ${i + 1} of ${files.length} images...`;
                
                const fd = new FormData();
                fd.append('action', 'add_gallery_image');
                fd.append('portfolio_id', portfolioId);
                fd.append('caption', caption);
                fd.append('images[]', files[i]);

                try {
                    const res = await fetch(API, { method: 'POST', body: fd });
                    const json = await res.json();
                    if (json.success) {
                        successCount++;
                        if (json.errors && json.errors.length > 0) {
                            errorMessages.push(...json.errors);
                        }
                    } else {
                        errorMessages.push(`File ${files[i].name}: ${json.error || 'Failed'}`);
                    }
                } catch (err) {
                    errorMessages.push(`File ${files[i].name} failed to upload.`);
                }
            }

            btn.textContent = 'Upload Images';
            btn.disabled = false;
            indicator.textContent = '';
            form.reset();
            loadGallery();

            if (errorMessages.length > 0) {
                showAlert(`${successCount} uploaded, but errors occurred: ` + errorMessages.join(', '), 'error');
            } else {
                showAlert(`Successfully uploaded ${successCount} images!`, 'success');
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

let quill1, quill2;
document.addEventListener('DOMContentLoaded', async () => {
    // Initialize Quill editors
    const toolbarOptions = [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link', 'image', 'video'],
        ['clean']
    ];
    quill1 = new Quill('#editor-desc1', { theme: 'snow', modules: { toolbar: toolbarOptions } });
    quill2 = new Quill('#editor-desc2', { theme: 'snow', modules: { toolbar: toolbarOptions } });
    
    // We override the default loadSettings callback slightly to populate Quill
    const res = await fetch(`${API}?action=get_settings`);
    const json = await res.json();
    if (json.success) {
        const d = json.data;
        const keys = ['about_label','about_heading','about_quote'];
        keys.forEach(f => {
            const el = document.getElementById(f);
            if (el && d[f] !== undefined) el.value = d[f];
        });
        if (d['about_desc_1']) quill1.root.innerHTML = d['about_desc_1'];
        if (d['about_desc_2']) quill2.root.innerHTML = d['about_desc_2'];
        const prev = document.getElementById('about_image_preview');
        if (prev && d['about_image']) prev.src = '../' + d['about_image'];
    }
});

// Override the global saveSettings button call for this page to sync Quill data
const originalSave = window.saveSettings;
window.saveSettings = async function(keys) {
    document.getElementById('about_desc_1').value = quill1.root.innerHTML;
    document.getElementById('about_desc_2').value = quill2.root.innerHTML;
    originalSave(keys);
};

async function uploadAboutImage() {
    const fileInput = document.getElementById('about_image_file');
    if (!fileInput.files[0]) return showAlert('Select an image first.', 'error');
    const form = new FormData();
    form.append('action', 'upload_about_image');
    form.append('image', fileInput.files[0]);
    const res = await fetch(API, { method: 'POST', body: form });
    const json = await res.json();
    if (json.success) {
        document.getElementById('about_image_preview').src = '../' + json.path;
        showAlert('Image uploaded!');
    }
}
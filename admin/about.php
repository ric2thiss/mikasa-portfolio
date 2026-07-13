<?php include 'includes/header.php'; ?>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
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
</style>
<div class="admin-card">
    <h2>About Section</h2>
    <div class="form-grid">
        <div class="form-group"><label>Label</label><input type="text" id="about_label"></div>
        <div class="form-group full"><label>Heading (supports &lt;em&gt; tags)</label><input type="text" id="about_heading"></div>
        <div class="form-group full">
            <label>Description 1</label>
            <div id="editor-desc1"></div>
            <textarea id="about_desc_1" style="display:none;"></textarea>
        </div>
        <div class="form-group full">
            <label>Description 2</label>
            <div id="editor-desc2"></div>
            <textarea id="about_desc_2" style="display:none;"></textarea>
        </div>
        <div class="form-group full"><label>Quote</label><textarea id="about_quote" rows="2"></textarea></div>
        <div class="form-group full">
            <label>About Image</label>
            <div class="image-upload-wrap">
                <img id="about_image_preview" src="" alt="Preview" class="img-preview">
                <input type="file" id="about_image_file" accept="image/*">
                <button class="upload-btn" onclick="uploadAboutImage()">Upload</button>
            </div>
        </div>
    </div>
    <button class="save-btn" onclick="saveSettings(['about_label','about_heading','about_desc_1','about_desc_2','about_quote'])">Save About</button>
</div>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<?php include 'includes/footer.php'; ?>
<?php
$currentPage = 'seo';
include 'includes/header.php'; 
?>
<div class="admin-card">
    <h2>SEO Settings</h2>
    <p style="color:var(--gray);margin-bottom:2rem;">Manage the search engine optimization settings for your main website.</p>
    
    <div class="form-grid">
        <div class="form-group full">
            <label>Meta Title</label>
            <input type="text" id="seo_title" placeholder="e.g. Mikasa Fine Arts Photography | Dubai">
        </div>
        <div class="form-group full">
            <label>Meta Description</label>
            <textarea id="seo_description" rows="3" placeholder="Enter a brief description of your site for search engines" style="width:100%;padding:1rem;background:var(--dark-3);border:1px solid var(--gray-light);color:var(--light);border-radius:4px;resize:vertical;"></textarea>
        </div>
        <div class="form-group full">
            <label>Meta Keywords (comma separated)</label>
            <input type="text" id="seo_keywords" placeholder="photography, fashion, portraits, dubai">
        </div>
    </div>
    
    <button class="save-btn" onclick="saveSettings(['seo_title', 'seo_description', 'seo_keywords'])" style="margin-top: 1.5rem;">Save SEO Settings</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadSettings(['seo_title', 'seo_description', 'seo_keywords']);
});
</script>

<?php include 'includes/footer.php'; ?>

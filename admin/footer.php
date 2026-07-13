<?php include 'includes/header.php'; ?>
<div class="admin-card">
    <h2>Footer</h2>
    <div class="form-grid">
        <div class="form-group"><label>Brand Name</label><input type="text" id="footer_brand"></div>
        <div class="form-group"><label>Copyright Text</label><input type="text" id="footer_copy"></div>
    </div>
    <button class="save-btn" onclick="saveSettings(['footer_brand','footer_copy'])">Save Footer</button>
</div>
<?php include 'includes/footer.php'; ?>
<?php include 'includes/header.php'; ?>
<div class="admin-card">
    <h2>CTA / Contact</h2>
    <div class="form-grid">
        <div class="form-group"><label>Ghost Text</label><input type="text" id="cta_ghost"></div>
        <div class="form-group full"><label>Heading (supports &lt;br&gt;)</label><input type="text" id="cta_heading"></div>
        <div class="form-group full"><label>Description</label><textarea id="cta_desc" rows="2"></textarea></div>
        <div class="form-group"><label>Email</label><input type="text" id="cta_email"></div>
    </div>
    <button class="save-btn" onclick="saveSettings(['cta_ghost','cta_heading','cta_desc','cta_email'])">Save CTA</button>
</div>
<?php include 'includes/footer.php'; ?>
<?php include 'includes/header.php'; ?>
<div class="admin-card">
    <h2>Testimonial</h2>
    <div class="form-grid">
        <div class="form-group"><label>Label</label><input type="text" id="testimonial_label"></div>
        <div class="form-group full"><label>Quote</label><textarea id="testimonial_quote" rows="3"></textarea></div>
        <div class="form-group"><label>Author</label><input type="text" id="testimonial_author"></div>
    </div>
    <button class="save-btn" onclick="saveSettings(['testimonial_label','testimonial_quote','testimonial_author'])">Save Testimonial</button>
</div>
<?php include 'includes/footer.php'; ?>
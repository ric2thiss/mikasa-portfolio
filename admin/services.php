<?php include 'includes/header.php'; ?>
<div class="admin-card">
    <h2>Services</h2>
    <div class="form-grid">
        <div class="form-group"><label>Section Title</label><input type="text" id="services_title"></div>
        <div class="form-group full"><label>Section Description</label><textarea id="services_desc" rows="2"></textarea></div>
    </div>
    <button class="save-btn" onclick="saveSettings(['services_title','services_desc'])" style="margin-bottom:2rem">Save Header</button>
    <h3>Service Items</h3>
    <div id="services-list" class="crud-list"></div>
    <button class="add-btn" onclick="addServiceRow()">+ Add Service</button>
</div>
<?php include 'includes/footer.php'; ?>
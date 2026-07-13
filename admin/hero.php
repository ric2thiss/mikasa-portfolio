<?php include 'includes/header.php'; ?>
<div class="admin-card">
    <h2>Hero Section</h2>
    <div class="form-grid">
        <div class="form-group"><label>Ghost Text Line 1</label><input type="text" id="hero_ghost_1"></div>
        <div class="form-group"><label>Ghost Text Line 2</label><input type="text" id="hero_ghost_2"></div>
        <div class="form-group"><label>Eyebrow Text</label><input type="text" id="hero_eyebrow"></div>
        <div class="form-group"><label>Name Line 1</label><input type="text" id="hero_name_1"></div>
        <div class="form-group"><label>Name Line 2</label><input type="text" id="hero_name_2"></div>
        <div class="form-group full"><label>Tags (comma separated)</label><input type="text" id="hero_tags"></div>
    </div>
    <button class="save-btn" onclick="saveSettings(['hero_ghost_1','hero_ghost_2','hero_eyebrow','hero_name_1','hero_name_2','hero_tags'])">Save Hero</button>
</div>
<?php include 'includes/footer.php'; ?>
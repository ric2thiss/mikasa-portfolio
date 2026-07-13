<?php include 'includes/header.php'; ?>
<div class="admin-card">
    <h2>Portfolio Categories</h2>
    <div class="form-grid">
        <div class="form-group"><label>Section Title</label><input type="text" id="portfolio_title"></div>
        <div class="form-group"><label>Section Subtitle</label><input type="text" id="portfolio_desc"></div>
    </div>
    <button class="save-btn" onclick="saveSettings(['portfolio_title','portfolio_desc'])" style="margin-bottom:2rem">Save Header</button>
    <h3>Categories</h3>
    <div id="portfolio-list" class="crud-list"></div>
    <button class="add-btn" onclick="addPortfolioRow()">+ Add Category</button>
</div>
<?php include 'includes/footer.php'; ?>
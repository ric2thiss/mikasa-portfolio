<?php
include 'includes/header.php';
$portfolioId = $_GET['portfolio_id'] ?? 0;
if (!$portfolioId) {
    header('Location: portfolio.php');
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM portfolio WHERE id = ?");
$stmt->execute([$portfolioId]);
$portfolio = $stmt->fetch();
if (!$portfolio) {
    header('Location: portfolio.php');
    exit;
}
?>
<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem">
        <h2>Manage Gallery for: <?= htmlspecialchars($portfolio['category_name']) ?></h2>
        <a href="portfolio.php" style="color:var(--light);text-decoration:none;font-size:0.9rem;border:1px solid var(--gray-light);padding:0.4rem 0.8rem;border-radius:4px">← Back to Portfolio</a>
    </div>
    
    <div style="margin-bottom: 2.5rem; padding: 1.5rem; background: var(--dark-2); border-radius: 6px; border: 1px solid var(--gray-light)">
        <h3 style="margin-top:0">Upload Image to Gallery</h3>
        <form id="add-gallery-form" style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: flex-end;">
            <input type="hidden" name="portfolio_id" value="<?= $portfolioId ?>">
            <div class="form-group" style="flex: 1; min-width: 200px; margin: 0">
                <label>Caption (optional)</label>
                <input type="text" name="caption" placeholder="Enter image caption" style="width:100%;padding:0.6rem;background:var(--dark-3);border:1px solid var(--gray-light);color:var(--light);border-radius:4px">
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px; margin: 0">
                <label>Image Files (Select multiple) *</label>
                <input type="file" name="images[]" accept="image/*" multiple required style="width:100%;padding:0.4rem;background:var(--dark-3);border:1px solid var(--gray-light);color:var(--light);border-radius:4px">
            </div>
            <button type="submit" class="save-btn" style="height: 38px; margin: 0; padding: 0 1.5rem">Upload Images</button>
        </form>
    </div>

    <h3>Gallery Images</h3>
    <div id="gallery-list" class="crud-list"></div>
</div>

<script>
const portfolioId = <?= $portfolioId ?>;
</script>
<script src="js/gallery_manage.js"></script>
<?php include 'includes/footer.php'; ?>

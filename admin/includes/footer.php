    </div>
    <script src="js/admin.js"></script>
    <?php if (file_exists("js/{$currentPage}.js")): ?>
        <script src="js/<?= $currentPage ?>.js"></script>
    <?php endif; ?>
</body>
</html>
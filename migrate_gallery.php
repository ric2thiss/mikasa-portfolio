<?php
require_once 'config.php';

// Add slug column to portfolio if not exists
try {
    $pdo->exec("ALTER TABLE portfolio ADD COLUMN slug VARCHAR(100) DEFAULT NULL AFTER category_tag");
    echo "Added slug column.\n";
} catch (Exception $e) {
    echo "slug column may already exist: " . $e->getMessage() . "\n";
}

// Add display_type column to portfolio if not exists  
try {
    $pdo->exec("ALTER TABLE portfolio ADD COLUMN display_type ENUM('carousel','grid') DEFAULT 'carousel' AFTER slug");
    echo "Added display_type column.\n";
} catch (Exception $e) {
    echo "display_type column may already exist: " . $e->getMessage() . "\n";
}

// Create gallery_images table
$pdo->exec("CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (portfolio_id) REFERENCES portfolio(id) ON DELETE CASCADE
)");
echo "Created gallery_images table.\n";

// Update slugs for existing portfolio items
$items = $pdo->query("SELECT id, category_name FROM portfolio")->fetchAll();
foreach ($items as $item) {
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($item['category_name'])));
    $slug = trim($slug, '-');
    $pdo->prepare("UPDATE portfolio SET slug = ? WHERE id = ?")->execute([$slug, $item['id']]);
    echo "Set slug for '{$item['category_name']}' -> '$slug'\n";
}

echo "\nMigration complete!\n";

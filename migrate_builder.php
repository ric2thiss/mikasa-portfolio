<?php
require_once 'config.php';

// Create pages table
$pdo->exec("CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    status VARCHAR(20) DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Create sections table
$pdo->exec("CREATE TABLE IF NOT EXISTS sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    section_type VARCHAR(50) NOT NULL,
    title VARCHAR(100) DEFAULT '',
    content JSON,
    sort_order INT DEFAULT 0,
    is_visible TINYINT(1) DEFAULT 1,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
)");

// Insert default Home page if not exists
$stmt = $pdo->query("SELECT id FROM pages WHERE slug='home'");
if (!$stmt->fetch()) {
    $pdo->exec("INSERT INTO pages (title, slug) VALUES ('Home', 'home')");
    $page_id = $pdo->lastInsertId();

    function getS($pdo, $key) {
        $st = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key=?");
        $st->execute([$key]);
        return $st->fetchColumn();
    }

    $insertStmt = $pdo->prepare("INSERT INTO sections (page_id, section_type, title, content, sort_order) VALUES (?, ?, ?, ?, ?)");

    $heroContent = json_encode([
        'ghost_1' => getS($pdo, 'hero_ghost_1'),
        'ghost_2' => getS($pdo, 'hero_ghost_2'),
        'eyebrow' => getS($pdo, 'hero_eyebrow'),
        'name_1' => getS($pdo, 'hero_name_1'),
        'name_2' => getS($pdo, 'hero_name_2'),
        'tags' => getS($pdo, 'hero_tags')
    ]);
    $insertStmt->execute([$page_id, 'hero', 'Hero Area', $heroContent, 1]);

    $aboutContent = json_encode([
        'label' => getS($pdo, 'about_label'),
        'heading' => getS($pdo, 'about_heading'),
        'desc_1' => getS($pdo, 'about_desc_1'),
        'desc_2' => getS($pdo, 'about_desc_2'),
        'quote' => getS($pdo, 'about_quote'),
        'image' => getS($pdo, 'about_image')
    ]);
    $insertStmt->execute([$page_id, 'about', 'About Me', $aboutContent, 2]);

    $insertStmt->execute([$page_id, 'stats', 'Statistics', '{}', 3]);

    $servicesContent = json_encode([
        'title' => getS($pdo, 'services_title'),
        'desc' => getS($pdo, 'services_desc')
    ]);
    $insertStmt->execute([$page_id, 'services', 'My Services', $servicesContent, 4]);

    $portContent = json_encode([
        'title' => getS($pdo, 'portfolio_title'),
        'desc' => getS($pdo, 'portfolio_desc')
    ]);
    $insertStmt->execute([$page_id, 'portfolio', 'Portfolio Grid', $portContent, 5]);

    $testContent = json_encode([
        'label' => getS($pdo, 'testimonial_label'),
        'quote' => getS($pdo, 'testimonial_quote'),
        'author' => getS($pdo, 'testimonial_author')
    ]);
    $insertStmt->execute([$page_id, 'testimonial', 'Testimonial', $testContent, 6]);

    $ctaContent = json_encode([
        'ghost' => getS($pdo, 'cta_ghost'),
        'heading' => getS($pdo, 'cta_heading'),
        'desc' => getS($pdo, 'cta_desc'),
        'email' => getS($pdo, 'cta_email')
    ]);
    $insertStmt->execute([$page_id, 'cta', 'Call to Action', $ctaContent, 7]);
    
    $richContent = json_encode([
        'html' => '<p>This is a custom rich text block. You can edit this using the WYSIWYG editor.</p>'
    ]);
    $insertStmt->execute([$page_id, 'richtext', 'Custom Content', $richContent, 8]);
}
echo "Migration successful.";

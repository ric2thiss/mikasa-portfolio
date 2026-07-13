<?php
require_once 'config.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: index.php');
    exit;
}

// If slug is home, redirect to index
if ($slug === 'home') {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
$stmt->execute([$slug]);
$page = $stmt->fetch();

if (!$page || $page['status'] !== 'published') {
    header('HTTP/1.0 404 Not Found');
    echo "<h1>404 Not Found</h1>";
    exit;
}

$s = getAllSettings($pdo);
$socials = $pdo->query("SELECT * FROM social_links ORDER BY sort_order")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['title']) ?> — Mikasa Fine Arts</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .custom-page-main { padding: 8rem 5vw 4rem; min-height: 80vh; }
        .richtext-section { margin-bottom: 3rem; max-width: 900px; margin-left: auto; margin-right: auto; line-height: 1.8; font-size: 1.1rem; }
        .richtext-section h1, .richtext-section h2, .richtext-section h3 { font-family: var(--font-heading); margin-bottom: 1rem; color: var(--light); }
        .richtext-section p { margin-bottom: 1.5rem; color: var(--gray); }
        body.light-theme .richtext-section h1, body.light-theme .richtext-section h2, body.light-theme .richtext-section h3 { color: var(--light); }
        body.light-theme .richtext-section p { color: var(--gray); }
    </style>
</head>
<body>
    <script>if(localStorage.getItem('theme')==='light')document.body.classList.add('light-theme');</script>
    
    <!-- Navigation -->
    <nav>
        <a href="index.php" class="logo">Mikasa<em>Fine Arts</em></a>
        <ul class="nav-links">
            <li><a href="index.php#about">About</a></li>
            <li><a href="index.php#services">Services</a></li>
            <li><a href="index.php#portfolio">Portfolio</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="menu-toggle" id="mobile-menu-btn"><span></span><span></span></div>
    </nav>
    <div class="mobile-menu" id="mobile-menu">
        <ul class="mobile-nav-links">
            <li><a href="index.php#about">About</a></li>
            <li><a href="index.php#services">Services</a></li>
            <li><a href="index.php#portfolio">Portfolio</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </div>

    <main class="custom-page-main">
        <h1 style="text-align:center; font-family: var(--font-heading); font-size: 3rem; margin-bottom: 4rem; text-transform: uppercase; letter-spacing: 2px;">
            <?= htmlspecialchars($page['title']) ?>
        </h1>
        
        <?php
        $secStmt = $pdo->prepare("SELECT * FROM sections WHERE page_id=? AND is_visible=1 ORDER BY sort_order");
        $secStmt->execute([$page['id']]);
        $sections = $secStmt->fetchAll();
        
        foreach ($sections as $sec) {
            if ($sec['section_type'] === 'richtext') {
                $content = json_decode($sec['content'], true);
                echo '<div class="richtext-section">';
                echo $content['html'] ?? '';
                echo '</div>';
            }
        }
        ?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-inner">
            <div class="footer-left">
                <span class="footer-brand"><?= htmlspecialchars($s['footer_brand'] ?? 'Mikasa Fine Arts') ?></span>
                <span class="footer-copy"><?= htmlspecialchars($s['footer_copy'] ?? '') ?></span>
            </div>
            <ul class="footer-links">
                <?php foreach ($socials as $social): ?>
                    <li><a href="<?= htmlspecialchars($social['url']) ?>" target="_blank"><?= htmlspecialchars($social['platform_name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </footer>

    <!-- Theme Toggle -->
    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
        <div class="theme-toggle-knob">
            <svg class="icon-moon" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            <svg class="icon-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
        </div>
    </button>

    <script src="assets/js/main.js"></script>
</body>
</html>

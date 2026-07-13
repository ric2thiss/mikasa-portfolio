<?php require_once 'config.php';

$slug = $_GET['cat'] ?? '';
if (!$slug) {
    $slug = basename($_SERVER['PHP_SELF'], '.php');
}
if (!$slug || $slug === 'gallery') { header('Location: index.php#portfolio'); exit; }

$stmt = $pdo->prepare("SELECT * FROM portfolio WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$category = $stmt->fetch();
if (!$category) { header('Location: index.php#portfolio'); exit; }

$s = getAllSettings($pdo);
$socials = $pdo->query("SELECT * FROM social_links ORDER BY sort_order")->fetchAll();

// Fetch gallery images
$imgStmt = $pdo->prepare("SELECT * FROM gallery_images WHERE portfolio_id = ? ORDER BY sort_order");
$imgStmt->execute([$category['id']]);
$images = $imgStmt->fetchAll();

// If no gallery images yet, use the category cover image as a placeholder
if (empty($images)) {
    $images = [['id' => 0, 'image_path' => $category['image_path'], 'caption' => $category['category_name']]];
}

$displayType = $category['display_type'] ?? 'carousel';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['category_name']) ?> — Mikasa Fine Arts Photography</title>
    <meta name="description" content="<?= htmlspecialchars($category['category_tag']) ?> — Mikasa Fine Arts Photography Gallery">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/gallery.css">
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

    <main class="gallery-page">
        <!-- Gallery Header -->
        <section class="gallery-header">
            <a href="index.php#portfolio" class="gallery-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12" />
                    <polyline points="12 19 5 12 12 5" />
                </svg>
                Back to Portfolio
            </a>
            <h1><?= htmlspecialchars($category['category_name']) ?></h1>
            <p class="gallery-tag"><?= htmlspecialchars($category['category_tag']) ?></p>
        </section>

        <?php if ($displayType === 'carousel'): ?>
        <!-- ===== CAROUSEL DISPLAY ===== -->
        <section class="gallery-carousel" id="gallery-carousel">
            <div class="carousel-stage">
                <button class="carousel-btn carousel-prev" id="carousel-prev" aria-label="Previous image">
                    <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <div class="carousel-track-wrapper">
                    <div class="carousel-track" id="carousel-track">
                        <?php foreach ($images as $i => $img): ?>
                        <div class="carousel-slide <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
                            <img src="<?= htmlspecialchars($img['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($img['caption'] ?? $category['category_name']) ?>"
                                 loading="<?= $i < 3 ? 'eager' : 'lazy' ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="carousel-btn carousel-next" id="carousel-next" aria-label="Next image">
                    <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
            <!-- Thumbnail Strip -->
            <div class="carousel-thumbnails" id="carousel-thumbnails">
                <?php foreach ($images as $i => $img): ?>
                <button class="thumb <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
                    <img src="<?= htmlspecialchars($img['image_path']) ?>" 
                         alt="Thumbnail <?= $i + 1 ?>">
                </button>
                <?php endforeach; ?>
            </div>
        </section>

        <?php else: ?>
        <!-- ===== MASONRY / RANDOM GRID DISPLAY ===== -->
        <section class="gallery-grid-masonry" id="gallery-grid">
            <?php foreach ($images as $i => $img): ?>
            <div class="masonry-item" data-index="<?= $i ?>">
                <img src="<?= htmlspecialchars($img['image_path']) ?>" 
                     alt="<?= htmlspecialchars($img['caption'] ?? $category['category_name']) ?>"
                     loading="lazy">
            </div>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <!-- ===== FULLSCREEN LIGHTBOX ===== -->
        <div class="lightbox" id="lightbox">
            <button class="lightbox-close" id="lightbox-close" aria-label="Close">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <button class="lightbox-nav lightbox-prev" id="lightbox-prev" aria-label="Previous">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="lightbox-image-wrap">
                <img id="lightbox-img" src="" alt="Fullscreen view">
            </div>
            <button class="lightbox-nav lightbox-next" id="lightbox-next" aria-label="Next">
                <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
            <div class="lightbox-counter" id="lightbox-counter"></div>
        </div>
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
    <script src="assets/js/gallery.js"></script>
</body>
</html>

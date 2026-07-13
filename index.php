<?php require_once 'config.php';

// Fetch all data
$s = getAllSettings($pdo);
$stats = $pdo->query("SELECT * FROM stats ORDER BY sort_order")->fetchAll();
$services = $pdo->query("SELECT * FROM services ORDER BY sort_order")->fetchAll();
$portfolios = $pdo->query("SELECT * FROM portfolio ORDER BY sort_order")->fetchAll();
$socials = $pdo->query("SELECT * FROM social_links ORDER BY sort_order")->fetchAll();

// Hero tags as array
$heroTags = array_filter(array_map('trim', explode(',', $s['hero_tags'] ?? '')));
?>
<!DOCTYPE html>
<html lang="en" class="snap-scroll">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikasa Fine Arts Photography | Dubai</title>
    <meta name="description"
        content="Mikasa — Freelance Fine Arts Photographer in Dubai. Fashion, Portraits, Weddings, Events & Graphic Design.">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <script>if(localStorage.getItem('theme')==='light')document.body.classList.add('light-theme');</script>

    <!-- Loader -->
    <div class="loader" id="loader">
        <div class="loader-text" id="loader-text"></div>
    </div>

    <!-- Navigation -->
    <nav>
        <a href="#" class="logo">Mikasa<em>Fine Arts</em></a>
        <ul class="nav-links">
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#portfolio">Portfolio</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="menu-toggle" id="mobile-menu-btn">
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu" id="mobile-menu">
        <ul class="mobile-nav-links">
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#portfolio">Portfolio</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <?php
        $dbSections = $pdo->query("SELECT * FROM sections WHERE page_id=1 AND is_visible=1 ORDER BY sort_order")->fetchAll();
        foreach ($dbSections as $sec):
            $type = $sec['section_type'];
            if ($type === 'hero'):
                ?>
                <!-- Hero -->
                <div class="hero-wrapper" id="home">
                    <section class="hero">
                        <div class="ghost-text"><?= htmlspecialchars($s['hero_ghost_1'] ?? 'MIKASA') ?></div>
                        <div class="ghost-text-2"><?= htmlspecialchars($s['hero_ghost_2'] ?? 'PHOTOGRAPHY') ?></div>
                        <div class="hero-main">
                            <p class="hero-eyebrow"><?= htmlspecialchars($s['hero_eyebrow'] ?? '') ?></p>
                            <h1 class="hero-name">
                                <span class="line"><?= htmlspecialchars($s['hero_name_1'] ?? 'Mikasa Fine Arts') ?></span>
                                <span class="line"><?= htmlspecialchars($s['hero_name_2'] ?? 'Photography') ?></span>
                            </h1>
                            <div class="hero-tags">
                                <?php foreach ($heroTags as $tag): ?>
                                    <span><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="scroll-cue">
                            <span>Scroll</span>
                            <div class="line"></div>
                        </div>
                    </section>
                </div>

                <!-- Marquee -->
                <div class="marquee">
                    <div class="marquee-inner">
                        <?php foreach ($heroTags as $tag): ?>
                            <span><?= htmlspecialchars($tag) ?><span class="dot"></span></span>
                        <?php endforeach; ?>
                        <?php foreach ($heroTags as $tag): ?>
                            <span><?= htmlspecialchars($tag) ?><span class="dot"></span></span>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php elseif ($type === 'about'): ?>
                <!-- About -->
                <section class="about snap-section" id="about">
                    <div class="about-left">
                        <p class="about-label reveal"><?= htmlspecialchars($s['about_label'] ?? 'About') ?></p>
                        <h2 class="about-heading reveal reveal-delay-1"><?= $s['about_heading'] ?? '' ?></h2>
                        <div class="about-description reveal reveal-delay-2"><?= $s['about_desc_1'] ?? '' ?></div>
                        <div class="about-description reveal reveal-delay-3"><?= $s['about_desc_2'] ?? '' ?></div>
                        <blockquote class="about-quote reveal reveal-delay-4">"<?= htmlspecialchars($s['about_quote'] ?? '') ?>"
                        </blockquote>
                    </div>
                    <div class="about-right">
                        <div class="about-image-wrap reveal">
                            <img src="<?= htmlspecialchars($s['about_image'] ?? '') ?>" alt="Mikasa — Fine Arts Photographer">
                        </div>
                        <div class="about-stats reveal">
                            <?php foreach ($stats as $stat): ?>
                                <div class="stat-item">
                                    <span class="stat-num"><?= htmlspecialchars($stat['stat_num']) ?></span>
                                    <span class="stat-label"><?= htmlspecialchars($stat['stat_label']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

            <?php elseif ($type === 'services'): ?>
                <!-- Services -->
                <section class="services snap-section" id="services">
                    <div class="services-header reveal">
                        <h2><?= htmlspecialchars($s['services_title'] ?? 'What I Do') ?></h2>
                        <p><?= htmlspecialchars($s['services_desc'] ?? '') ?></p>
                    </div>
                    <div class="services-list">
                        <?php foreach ($services as $svc): ?>
                            <div class="service-row reveal">
                                <span class="service-num"><?= htmlspecialchars($svc['num']) ?></span>
                                <span class="service-name"><?= htmlspecialchars($svc['name']) ?></span>
                                <span class="service-desc"><?= htmlspecialchars($svc['description']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

            <?php elseif ($type === 'portfolio'): ?>
                <!-- Portfolio Categories -->
                <section class="portfolio snap-section" id="portfolio">
                    <div class="portfolio-header">
                        <h2 class="reveal"><?= htmlspecialchars($s['portfolio_title'] ?? 'Art Works') ?></h2>
                        <p class="reveal reveal-delay-1"><?= htmlspecialchars($s['portfolio_desc'] ?? '') ?></p>
                    </div>
                    <div class="portfolio-grid">
                        <?php foreach ($portfolios as $i => $p):
                            $delay = $i % 3;
                            $delayClass = $delay > 0 ? " reveal-delay-{$delay}" : '';
                            $slug = htmlspecialchars($p['slug'] ?? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($p['category_name']))));
                            ?>
                            <a href="<?= $slug ?>.php" class="portfolio-item reveal<?= $delayClass ?>">
                                <img src="<?= htmlspecialchars($p['image_path']) ?>"
                                    alt="<?= htmlspecialchars($p['category_name']) ?> Photography">
                                <div class="portfolio-overlay">
                                    <div class="cat-arrow"><svg viewBox="0 0 24 24">
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                            <polyline points="12 5 19 12 12 19" />
                                        </svg></div>
                                    <span class="cat-name"><?= htmlspecialchars($p['category_name']) ?></span>
                                    <span class="cat-tag"><?= htmlspecialchars($p['category_tag']) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

            <?php elseif ($type === 'testimonial'): ?>
                <!-- Testimonials -->
                <section class="testimonials snap-section">
                    <div class="testimonials-inner reveal">
                        <p class="testimonials-label"><?= htmlspecialchars($s['testimonial_label'] ?? 'Client Words') ?></p>
                        <blockquote class="testimonial-quote">"<?= htmlspecialchars($s['testimonial_quote'] ?? '') ?>"
                        </blockquote>
                        <p class="testimonial-author"><?= htmlspecialchars($s['testimonial_author'] ?? '') ?></p>
                    </div>
                </section>

            <?php elseif ($type === 'cta'): ?>
                <!-- CTA -->
                <section class="cta snap-section" id="contact">
                    <div class="cta-ghost"><?= htmlspecialchars($s['cta_ghost'] ?? 'CONTACT') ?></div>
                    <div class="cta-content reveal">
                        <h2><?= $s['cta_heading'] ?? '' ?></h2>
                        <p><?= htmlspecialchars($s['cta_desc'] ?? '') ?></p>
                        <a href="mailto:<?= htmlspecialchars($s['cta_email'] ?? '') ?>" class="cta-btn">
                            Get In Touch
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </a>
                    </div>
                </section>

            <?php elseif ($type === 'richtext'): ?>
                <!-- Custom Rich Text Block -->
                <!-- <section class="richtext" style="padding: 10rem 5vw; max-width: 900px; margin: 0 auto; color: var(--light);">
            <div class="reveal">
                <h2 style="font-size: 2.5rem; margin-bottom: 2rem; font-family: var(--font-heading); text-transform: uppercase; letter-spacing: 2px;">
                    <?= htmlspecialchars($sec['title']) ?>
                </h2>
                <div style="font-size: 1rem; line-height: 1.8; color: var(--gray);">
                    <?php
                    $contentData = json_decode($sec['content'], true);
                    echo $contentData['html'] ?? '';
                    ?>
                </div>
            </div>
        </section> -->

            <?php endif; endforeach; ?>

        <!-- Footer -->
        <footer>
            <div class="footer-inner">
                <div class="footer-left">
                    <span class="footer-brand"><?= htmlspecialchars($s['footer_brand'] ?? 'Mikasa Fine Arts') ?></span>
                    <span class="footer-copy"><?= htmlspecialchars($s['footer_copy'] ?? '') ?></span>
                </div>
                <ul class="footer-links">
                    <?php foreach ($socials as $social): ?>
                        <li><a href="<?= htmlspecialchars($social['url']) ?>"
                                target="_blank"><?= htmlspecialchars($social['platform_name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </footer>

    </main>

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
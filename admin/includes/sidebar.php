<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <span class="sb-name">Mikasa</span>
        <span class="sb-tag">CMS Dashboard</span>
    </div>
    <ul class="sidebar-nav">
        <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="builder.php" class="<?= basename($_SERVER['PHP_SELF']) == 'builder.php' ? 'active' : '' ?>">Page Builder</a></li>
        <li><a href="hero.php" class="<?= basename($_SERVER['PHP_SELF']) == 'hero.php' ? 'active' : '' ?>">Hero</a></li>
        <li><a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">About</a></li>
        <li><a href="stats.php" class="<?= basename($_SERVER['PHP_SELF']) == 'stats.php' ? 'active' : '' ?>">Stats</a></li>
        <li><a href="services.php" class="<?= basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : '' ?>">Services</a></li>
        <li><a href="portfolio.php" class="<?= basename($_SERVER['PHP_SELF']) == 'portfolio.php' ? 'active' : '' ?>">Portfolio</a></li>
        <li><a href="testimonial.php" class="<?= basename($_SERVER['PHP_SELF']) == 'testimonial.php' ? 'active' : '' ?>">Testimonial</a></li>
        <li><a href="cta.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cta.php' ? 'active' : '' ?>">CTA / Contact</a></li>
        <li><a href="seo.php" class="<?= basename($_SERVER['PHP_SELF']) == 'seo.php' ? 'active' : '' ?>">SEO Settings</a></li>
        <li><a href="social.php" class="<?= basename($_SERVER['PHP_SELF']) == 'social.php' ? 'active' : '' ?>">Social Links</a></li>
        <li><a href="footer.php" class="<?= basename($_SERVER['PHP_SELF']) == 'footer.php' ? 'active' : '' ?>">Footer</a></li>
        <li><a href="messages.php" class="<?= basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : '' ?>">Messages</a></li>
    </ul>
    <div class="sidebar-footer">
        <a href="../index.php" target="_blank" class="view-site-btn">View Site ↗</a>
        <a href="dashboard.php?logout=1" class="view-site-btn" style="margin-top:0.5rem;color:#ff5050;border-color:rgba(255,80,80,0.3)">Logout</a>
    </div>
</aside>
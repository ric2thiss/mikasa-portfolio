<?php require_once 'config.php';
$s = getAllSettings($pdo);
$socials = $pdo->query("SELECT * FROM social_links ORDER BY sort_order")->fetchAll();
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $email && $message) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = true;
    } else {
        $error = 'Please fill in all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact — Mikasa Fine Arts Photography</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .contact-page { padding: 10rem 5vw 6rem; max-width: 900px; margin: 0 auto; }
        .contact-page h1 { font-size: clamp(2.5rem, 5vw, 4rem); margin-bottom: 1rem; color: var(--light); }
        .contact-page .sub { color: var(--gray); font-size: 1rem; margin-bottom: 3rem; }
        .contact-form { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .contact-form .full { grid-column: span 2; }
        .contact-form label { display: block; font-size: 0.7rem; letter-spacing: 2px; text-transform: uppercase; color: var(--gray); margin-bottom: 0.4rem; }
        .contact-form input, .contact-form textarea {
            width: 100%; padding: 0.85rem 1rem; background: var(--dark-2); border: 1px solid var(--gray-light);
            border-radius: 4px; color: var(--light); font-family: var(--font-body); font-size: 0.9rem; resize: vertical;
        }
        .contact-form input:focus, .contact-form textarea:focus { outline: none; border-color: var(--gray); }
        .contact-submit {
            grid-column: span 2; padding: 1rem 3rem; background: var(--light); color: var(--dark); border: none;
            font-family: var(--font-body); font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase;
            cursor: pointer; border-radius: 4px; transition: background 0.3s ease; margin-top: 0.5rem;
        }
        .contact-submit:hover { background: var(--light-2); }
        .msg-success { background: rgba(80,200,120,0.1); color: #50c878; border: 1px solid rgba(80,200,120,0.2); padding: 1rem; border-radius: 6px; margin-bottom: 2rem; }
        .msg-error { background: rgba(255,80,80,0.1); color: #ff5050; border: 1px solid rgba(255,80,80,0.2); padding: 1rem; border-radius: 6px; margin-bottom: 2rem; }
        @media (max-width: 768px) {
            .contact-form { grid-template-columns: 1fr; }
            .contact-form .full, .contact-submit { grid-column: span 1; }
        }
    </style>
</head>
<body>
    <script>if(localStorage.getItem('theme')==='light')document.body.classList.add('light-theme');</script>
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

    <main class="contact-page">
        <h1>Get In Touch</h1>
        <p class="sub"><?= htmlspecialchars($s['cta_desc'] ?? 'Available for freelance projects worldwide.') ?></p>

        <?php if ($success): ?>
            <div class="msg-success">Your message has been sent successfully! I'll get back to you soon.</div>
        <?php elseif ($error): ?>
            <div class="msg-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" class="contact-form">
            <div><label>Name *</label><input type="text" name="name" required></div>
            <div><label>Email *</label><input type="email" name="email" required></div>
            <div class="full"><label>Subject</label><input type="text" name="subject"></div>
            <div class="full"><label>Message *</label><textarea name="message" rows="6" required></textarea></div>
            <button type="submit" class="contact-submit">Send Message</button>
        </form>
        <?php endif; ?>
    </main>

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

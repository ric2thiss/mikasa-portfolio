document.addEventListener('DOMContentLoaded', () => {

    /* ===== LOADER ===== */
    const loader = document.getElementById('loader');
    const loaderText = document.getElementById('loader-text');
    
    if (loader && loaderText) {
        const brandName = 'MIKASA';
        // Animate each letter
        brandName.split('').forEach((char, i) => {
            const span = document.createElement('span');
            span.textContent = char;
            span.style.animationDelay = `${i * 0.1}s`;
            loaderText.appendChild(span);
        });

        // Hide loader after animation
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 2200);
    }

    /* ===== SCROLL REVEAL ===== */
    const revealElements = document.querySelectorAll('.reveal');

    function checkReveal() {
        const windowHeight = window.innerHeight;
        revealElements.forEach(el => {
            const top = el.getBoundingClientRect().top;
            if (top < windowHeight - 80) {
                el.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', checkReveal, { passive: true });
    checkReveal();

    /* ===== SMOOTH ANCHOR SCROLL ===== */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.getAttribute('href');
            if (id === '#') return;
            const target = document.querySelector(id);
            if (target) {
                const navHeight = document.querySelector('nav').offsetHeight;
                window.scrollTo({ top: target.offsetTop - navHeight, behavior: 'smooth' });
            }
        });
    });

    /* ===== COUNTER ANIMATION FOR STATS ===== */
    const stats = document.querySelectorAll('.stat-num');
    let statsDone = false;

    function animateStats() {
        if (statsDone) return;
        const statsSection = document.querySelector('.about-stats');
        if (!statsSection) return;
        const top = statsSection.getBoundingClientRect().top;
        if (top < window.innerHeight - 50) {
            statsDone = true;
            stats.forEach(stat => {
                const raw = stat.textContent;
                const suffix = raw.replace(/[0-9]/g, '');
                const target = parseInt(raw);
                let current = 0;
                const increment = Math.max(1, Math.floor(target / 40));
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    stat.textContent = current + suffix;
                }, 30);
            });
        }
    }

    window.addEventListener('scroll', animateStats, { passive: true });
    animateStats();

    /* ===== MOBILE MENU TOGGLE ===== */
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-links a');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenuBtn.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            
            // Prevent body scrolling when menu is open
            if (mobileMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });

        // Close menu when a link is clicked
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenuBtn.classList.remove('active');
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }
    /* ===== THEME TOGGLE ===== */
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-theme');
        }

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('light-theme');
            const isLight = document.body.classList.contains('light-theme');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
        });
    } else {
        // Apply theme anyway if saved, for pages without toggle button (or with it loaded later)
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-theme');
        }
    }
});

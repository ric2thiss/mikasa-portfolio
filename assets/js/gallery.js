document.addEventListener('DOMContentLoaded', () => {

    /* ===== CAROUSEL ===== */
    const track = document.getElementById('carousel-track');
    const thumbsWrap = document.getElementById('carousel-thumbnails');

    if (track) {
        const slides = track.querySelectorAll('.carousel-slide');
        const thumbs = thumbsWrap ? thumbsWrap.querySelectorAll('.thumb') : [];
        const prevBtn = document.getElementById('carousel-prev');
        const nextBtn = document.getElementById('carousel-next');
        let current = 0;
        const total = slides.length;

        function updateCarousel(index) {
            slides.forEach((s, i) => {
                s.classList.remove('active', 'prev', 'next');
                if (i === index) s.classList.add('active');
                else if (i === (index - 1 + total) % total) s.classList.add('prev');
                else if (i === (index + 1) % total) s.classList.add('next');
            });
            thumbs.forEach((t, i) => {
                t.classList.toggle('active', i === index);
            });
            // Scroll active thumb into view
            if (thumbs[index]) {
                thumbs[index].scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }
            current = index;
        }

        if (prevBtn) prevBtn.addEventListener('click', () => updateCarousel((current - 1 + total) % total));
        if (nextBtn) nextBtn.addEventListener('click', () => updateCarousel((current + 1) % total));

        thumbs.forEach(t => {
            t.addEventListener('click', () => updateCarousel(parseInt(t.dataset.index)));
        });

        // Swipe support
        let touchStartX = 0;
        let touchEndX = 0;
        track.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
        track.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) updateCarousel((current + 1) % total);
                else updateCarousel((current - 1 + total) % total);
            }
        }, { passive: true });

        // Keyboard
        document.addEventListener('keydown', e => {
            if (document.getElementById('lightbox').classList.contains('active')) return;
            if (e.key === 'ArrowLeft') updateCarousel((current - 1 + total) % total);
            if (e.key === 'ArrowRight') updateCarousel((current + 1) % total);
        });

        // Click image to open lightbox
        slides.forEach(s => {
            s.querySelector('img').addEventListener('click', () => openLightbox(parseInt(s.dataset.index)));
        });

        updateCarousel(0);
    }

    /* ===== MASONRY GRID — click to open lightbox ===== */
    const masonryItems = document.querySelectorAll('.masonry-item');
    masonryItems.forEach(item => {
        item.addEventListener('click', () => openLightbox(parseInt(item.dataset.index)));
    });

    /* ===== LIGHTBOX ===== */
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCounter = document.getElementById('lightbox-counter');
    const lightboxClose = document.getElementById('lightbox-close');
    const lightboxPrev = document.getElementById('lightbox-prev');
    const lightboxNext = document.getElementById('lightbox-next');

    // Gather all image sources
    const allImages = [];
    document.querySelectorAll('.carousel-slide img, .masonry-item img').forEach(img => {
        allImages.push(img.src);
    });

    let lbIndex = 0;

    function openLightbox(index) {
        if (!lightbox || allImages.length === 0) return;
        lbIndex = index;
        lightboxImg.src = allImages[lbIndex];
        lightboxCounter.textContent = `${lbIndex + 1} / ${allImages.length}`;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    function lightboxGo(dir) {
        lbIndex = (lbIndex + dir + allImages.length) % allImages.length;
        lightboxImg.style.opacity = 0;
        setTimeout(() => {
            lightboxImg.src = allImages[lbIndex];
            lightboxImg.style.opacity = 1;
            lightboxCounter.textContent = `${lbIndex + 1} / ${allImages.length}`;
        }, 200);
    }

    if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    if (lightboxPrev) lightboxPrev.addEventListener('click', () => lightboxGo(-1));
    if (lightboxNext) lightboxNext.addEventListener('click', () => lightboxGo(1));

    // Click backdrop to close
    if (lightbox) {
        lightbox.addEventListener('click', e => {
            if (e.target === lightbox || e.target.classList.contains('lightbox-image-wrap')) closeLightbox();
        });
    }

    // Keyboard for lightbox
    document.addEventListener('keydown', e => {
        if (!lightbox || !lightbox.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') lightboxGo(-1);
        if (e.key === 'ArrowRight') lightboxGo(1);
    });

    // Swipe in lightbox
    if (lightbox) {
        let lbTouchStartX = 0;
        lightbox.addEventListener('touchstart', e => { lbTouchStartX = e.changedTouches[0].screenX; }, { passive: true });
        lightbox.addEventListener('touchend', e => {
            const diff = lbTouchStartX - e.changedTouches[0].screenX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) lightboxGo(1);
                else lightboxGo(-1);
            }
        }, { passive: true });
    }

    // Expose openLightbox globally for carousel
    window.openLightbox = openLightbox;
});

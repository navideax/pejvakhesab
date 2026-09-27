/* ============================================================
   Pejvak Hesab — Software Slider (Vanilla JS)
   اضافه شده بدون تغییر در هیچ‌کدام از کارت‌های نرم‌افزار
   ============================================================ */
(function () {
    'use strict';

    const CONFIG = window.PH_SLIDER_CFG || {
        perView: { desktop: 3, tablet: 2, mobile: 1 },
        gap: 20,
        autoplay: false,
        autoplayDelay: 5000,
        loop: false,
        showDots: true,
        showArrows: true,
    };

    function getPerView() {
        const w = window.innerWidth;
        if (w >= 1080) return CONFIG.perView.desktop;
        if (w >= 680) return CONFIG.perView.tablet;
        return CONFIG.perView.mobile;
    }

    function buildSlider(wrap) {
        const track = wrap.querySelector('[data-slider-track]');
        const prevBtn = wrap.querySelector('[data-slider-prev]');
        const nextBtn = wrap.querySelector('[data-slider-next]');
        const dotsBox = wrap.querySelector('[data-slider-dots]');
        if (!track) return;

        const slides = Array.from(track.children);
        if (slides.length === 0) return;

        // اگر تعداد اسلایدها کمتر یا برابر تعداد قابل نمایش بود، اسلایدر غیرفعال
        let perView = getPerView();
        if (slides.length <= perView) {
            wrap.classList.add('is-disabled');
            if (prevBtn) prevBtn.style.display = 'none';
            if (nextBtn) nextBtn.style.display = 'none';
            if (dotsBox) dotsBox.style.display = 'none';
            return;
        }

        wrap.classList.remove('is-disabled');
        let currentIndex = 0;
        let autoplayTimer = null;

        // تعداد پیج‌ها = (تعداد اسلایدها) - (perView) + 1  (اسکرول پیوسته)
        function totalPages() {
            return Math.max(1, slides.length - perView + 1);
        }

        function slideWidth() {
            return track.clientWidth / perView;
        }

        function updateTrack() {
            const offset = -(currentIndex * (slideWidth() + CONFIG.gap));
            track.style.transform = `translateX(${offset}px)`;
            // اگر RTL بود، جهت برعکس
            if (document.dir === 'rtl' || document.documentElement.dir === 'rtl') {
                track.style.transform = `translateX(${-offset}px)`;
            }
            updateDots();
            updateArrows();
        }

        function updateDots() {
            if (!dotsBox || !CONFIG.showDots) return;
            const total = totalPages();
            if (dotsBox.children.length !== total) {
                dotsBox.innerHTML = '';
                for (let i = 0; i < total; i++) {
                    const b = document.createElement('button');
                    b.className = 'slider-dot';
                    b.setAttribute('aria-label', `اسلاید ${i + 1}`);
                    b.addEventListener('click', () => goTo(i));
                    dotsBox.appendChild(b);
                }
            }
            Array.from(dotsBox.children).forEach((d, i) =>
                d.classList.toggle('active', i === currentIndex)
            );
        }

        function updateArrows() {
            if (!prevBtn || !nextBtn) return;
            const rtl = document.dir === 'rtl' || document.documentElement.dir === 'rtl';
            prevBtn.disabled = !CONFIG.loop && currentIndex === 0;
            nextBtn.disabled = !CONFIG.loop && currentIndex >= totalPages() - 1;
            // در RTL، جهت دکمه‌ها برعکس می‌شه
            if (rtl) {
                prevBtn.style.opacity = nextBtn.disabled ? '0.4' : '1';
                nextBtn.style.opacity = prevBtn.disabled ? '0.4' : '1';
            }
        }

        function goTo(index) {
            const total = totalPages();
            if (CONFIG.loop) {
                if (index < 0) index = total - 1;
                if (index >= total) index = 0;
            } else {
                if (index < 0) index = 0;
                if (index >= total) index = total - 1;
            }
            currentIndex = index;
            updateTrack();
        }

        function next() {
            const total = totalPages();
            if (currentIndex < total - 1) goTo(currentIndex + 1);
            else if (CONFIG.loop) goTo(0);
        }

        function prev() {
            if (currentIndex > 0) goTo(currentIndex - 1);
            else if (CONFIG.loop) goTo(totalPages() - 1);
        }

        if (prevBtn) prevBtn.addEventListener('click', () => { stopAuto(); prev(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { stopAuto(); next(); });

        // Swipe / drag پشتیبانی
        let startX = 0, isDown = false;
        track.addEventListener('pointerdown', e => {
            isDown = true;
            startX = e.clientX;
            track.style.transition = 'none';
        });
        track.addEventListener('pointerup', e => {
            if (!isDown) return;
            isDown = false;
            track.style.transition = '';
            const diff = e.clientX - startX;
            const threshold = 50;
            if (Math.abs(diff) > threshold) {
                if (diff < 0) next(); else prev();
            }
        });
        track.addEventListener('pointercancel', () => {
            isDown = false;
            track.style.transition = '';
        });

        // Autoplay
        function startAuto() {
            if (!CONFIG.autoplay) return;
            stopAuto();
            autoplayTimer = setInterval(next, CONFIG.autoplayDelay);
        }
        function stopAuto() {
            if (autoplayTimer) { clearInterval(autoplayTimer); autoplayTimer = null; }
        }
        wrap.addEventListener('mouseenter', stopAuto);
        wrap.addEventListener('mouseleave', startAuto);
        startAuto();

        // Resize handler (debounced)
        let rt;
        window.addEventListener('resize', () => {
            clearTimeout(rt);
            rt = setTimeout(() => {
                const newPerView = getPerView();
                if (newPerView !== perView) {
                    perView = newPerView;
                    currentIndex = 0;
                }
                if (slides.length <= perView) {
                    wrap.classList.add('is-disabled');
                    if (prevBtn) prevBtn.style.display = 'none';
                    if (nextBtn) nextBtn.style.display = 'none';
                    if (dotsBox) dotsBox.style.display = 'none';
                    track.style.transform = '';
                    return;
                }
                wrap.classList.remove('is-disabled');
                if (prevBtn) prevBtn.style.display = '';
                if (nextBtn) nextBtn.style.display = '';
                if (dotsBox) dotsBox.style.display = '';
                updateTrack();
            }, 150);
        });

        // Keyboard navigation
        wrap.setAttribute('tabindex', '0');
        wrap.addEventListener('keydown', e => {
            const rtl = document.dir === 'rtl' || document.documentElement.dir === 'rtl';
            if (e.key === 'ArrowLeft') { rtl ? next() : prev(); }
            if (e.key === 'ArrowRight') { rtl ? prev() : next(); }
        });

        // Init
        requestAnimationFrame(() => {
            // منتظر می‌مونیم تا کارت‌ها رندر بشن
            setTimeout(updateTrack, 50);
        });
    }

    function initAll() {
        document.querySelectorAll('[data-slider]').forEach(buildSlider);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // MutationObserver: چون main.js با AJAX کارت‌ها رو می‌سازه
    if (window.MutationObserver) {
        let mo;
        const obs = new MutationObserver(() => {
            clearTimeout(mo);
            mo = setTimeout(() => {
                document.querySelectorAll('[data-slider]:not(.is-ready)').forEach(w => {
                    buildSlider(w);
                    w.classList.add('is-ready');
                });
            }, 100);
        });
        obs.observe(document.body, { childList: true, subtree: true });
    }
})();
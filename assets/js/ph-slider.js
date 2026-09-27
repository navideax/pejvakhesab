/* ============================================================
   Pejvak Hesab — Slider JS v2.1.0
   scrollBy ساده بدون محاسبه index + اتصال کامل به تنظیمات پنل:
   perView (دسکتاپ/تبلت/موبایل)، autoplay + تأخیر، loop، dots، arrows
   (خوانده‌شده از window.PH_SLIDER_CFG که functions.php تزریق می‌کند)
   ============================================================ */
(function () {
  'use strict';

  function getCfg() {
    var c = window.PH_SLIDER_CFG || {};
    var pv = c.perView || {};
    function clamp(v, d) {
      v = parseInt(v, 10);
      if (isNaN(v)) v = d;
      return Math.min(6, Math.max(1, v));
    }
    return {
      perView: {
        desktop: clamp(pv.desktop, 3),
        tablet: clamp(pv.tablet, 2),
        mobile: clamp(pv.mobile, 1)
      },
      autoplay: !!c.autoplay,
      autoplayDelay: Math.max(2000, parseInt(c.autoplayDelay, 10) || 5000),
      loop: !!c.loop,
      dots: c.dots !== false,
      arrows: c.arrows !== false
    };
  }

  function getRTL() {
    return document.documentElement.getAttribute('dir') === 'rtl' ||
           getComputedStyle(document.body).direction === 'rtl';
  }

  function initSlider(root) {
    if (root.classList.contains('is-ready')) return;
    /* محافظ همگام — چون is-ready داخل rAF ست می‌شود، از init دوبله جلوگیری می‌کند */
    if (root.dataset.phSliderInit) return;
    root.dataset.phSliderInit = '1';

    var track = root.querySelector('[data-slider-track]');
    var prevBtn = root.querySelector('[data-slider-prev]');
    var nextBtn = root.querySelector('[data-slider-next]');
    var dotsBox = root.querySelector('[data-slider-dots]');

    if (!track) {
      root.classList.add('is-ready');
      return;
    }

    /* اگر track هنوز خالی است، منتظر رندر داینامیک main.js می‌مانیم
       (MutationObserver دوباره initAll را صدا می‌زند) */
    var slides = track.querySelectorAll(':scope > *');
    if (slides.length === 0) return;

    var cfg = getCfg();
    var rtl = getRTL();

    /* ---------- اعمال perView از تنظیمات (CSS var — سازگار با slider.css) ---------- */
    function applyPerView() {
      var w = window.innerWidth;
      var pv = w >= 1080 ? cfg.perView.desktop : (w >= 680 ? cfg.perView.tablet : cfg.perView.mobile);
      root.style.setProperty('--ph-per-view', pv);
    }

    /* ---------- دکمه‌ها و نقطه‌ها طبق تنظیمات ---------- */
    if (!cfg.arrows) {
      if (prevBtn) prevBtn.style.display = 'none';
      if (nextBtn) nextBtn.style.display = 'none';
    }
    if (!cfg.dots && dotsBox) dotsBox.style.display = 'none';

    /* ---------- محاسبه فاصله یک اسلاید ---------- */
    function getStep() {
      var first = track.querySelector(':scope > *');
      if (!first) return 0;
      var w = first.getBoundingClientRect().width;
      var gapStr = getComputedStyle(track).columnGap ||
                   getComputedStyle(track).gap ||
                   '0px';
      var gap = parseFloat(gapStr) || 0;
      return w + gap;
    }

    /* ---------- چک کردن نیاز به اسلایدر ---------- */
    function needsSlider() {
      return track.scrollWidth > track.clientWidth + 4;
    }

    /* ---------- موقعیت فعلی (0 تا max) ---------- */
    function getScrollLeft() {
      return rtl ? Math.abs(track.scrollLeft) : track.scrollLeft;
    }

    function getMaxScroll() {
      return Math.max(0, track.scrollWidth - track.clientWidth);
    }

    function atStart() {
      return getScrollLeft() < 4;
    }

    function atEnd() {
      return getScrollLeft() > getMaxScroll() - 4;
    }

    /* ---------- به‌روزرسانی دکمه‌ها ---------- */
    function updateButtons() {
      if (cfg.loop) {
        if (prevBtn) prevBtn.disabled = false;
        if (nextBtn) nextBtn.disabled = false;
        return;
      }
      var sl = getScrollLeft();
      var max = getMaxScroll();

      if (prevBtn) prevBtn.disabled = sl < 4;
      if (nextBtn) nextBtn.disabled = sl > max - 4;
    }

    /* ---------- به‌روزرسانی نقطه‌ها ---------- */
    function updateDots() {
      if (!dotsBox || !dotsBox.children.length) return;
      var step = getStep();
      if (step < 1) return;
      var idx = Math.round(getScrollLeft() / step);
      var dots = dotsBox.children;
      for (var i = 0; i < dots.length; i++) {
        dots[i].classList.toggle('active', i === idx);
      }
    }

    /* ---------- ساخت نقطه‌ها ---------- */
    function buildDots() {
      if (!dotsBox || !cfg.dots) return;
      dotsBox.innerHTML = '';
      var step = getStep();
      if (step < 1) return;
      var maxScroll = getMaxScroll();
      if (maxScroll < 4) return;
      var count = Math.ceil(maxScroll / step) + 1;
      for (var i = 0; i < count; i++) {
        (function (idx) {
          var b = document.createElement('button');
          b.type = 'button';
          b.className = 'ph-slider__dot';
          b.setAttribute('aria-label', 'اسلاید ' + (idx + 1));
          b.addEventListener('click', function () {
            var stepVal = getStep();
            var target = idx * stepVal;
            track.scrollTo({
              left: rtl ? -target : target,
              behavior: 'smooth'
            });
          });
          dotsBox.appendChild(b);
        })(i);
      }
    }

    /* ---------- دکمه‌ها (با پشتیبانی Loop) ---------- */
    function goNext() {
      var step = getStep();
      if (step < 1) return;
      if (cfg.loop && atEnd()) {
        track.scrollTo({ left: 0, behavior: 'smooth' });
        return;
      }
      var delta = rtl ? -step : step;
      track.scrollBy({ left: delta, behavior: 'smooth' });
    }

    function goPrev() {
      var step = getStep();
      if (step < 1) return;
      if (cfg.loop && atStart()) {
        var max = getMaxScroll();
        track.scrollTo({ left: rtl ? -max : max, behavior: 'smooth' });
        return;
      }
      var delta = rtl ? step : -step;
      track.scrollBy({ left: delta, behavior: 'smooth' });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function (e) {
        e.preventDefault();
        goNext();
      });
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', function (e) {
        e.preventDefault();
        goPrev();
      });
    }

    /* ---------- پخش خودکار (با توقف روی هاور/فوکوس/لمس) ---------- */
    var autoTimer = null;
    function startAuto() {
      if (!cfg.autoplay || autoTimer) return;
      if (root.classList.contains('is-disabled')) return;
      if (getMaxScroll() < 4) return;
      autoTimer = setInterval(function () {
        if (!document.hidden) goNext();
      }, cfg.autoplayDelay);
    }
    function stopAuto() {
      if (autoTimer) {
        clearInterval(autoTimer);
        autoTimer = null;
      }
    }
    root.addEventListener('mouseenter', stopAuto);
    root.addEventListener('mouseleave', startAuto);
    root.addEventListener('focusin', stopAuto);
    root.addEventListener('focusout', startAuto);
    root.addEventListener('touchstart', stopAuto, { passive: true });
    document.addEventListener('visibilitychange', function () {
      if (document.hidden) stopAuto();
      else startAuto();
    });

    /* ---------- سینک با اسکرول دستی ---------- */
    var rafId = null;
    track.addEventListener('scroll', function () {
      if (rafId) return;
      rafId = requestAnimationFrame(function () {
        rafId = null;
        updateButtons();
        updateDots();
      });
    }, { passive: true });

    /* ---------- چک کردن وضعیت ---------- */
    function refresh() {
      applyPerView();
      // صبر کن تا layout آماده بشه
      requestAnimationFrame(function () {
        if (!needsSlider()) {
          // کارت‌ها کم هستن → اسلایدر غیرفعال
          root.classList.add('is-disabled');
          stopAuto();
        } else {
          root.classList.remove('is-disabled');
          buildDots();
          updateButtons();
          updateDots();
          startAuto();
        }
        root.classList.add('is-ready');
      });
    }

    /* ---------- resize ---------- */
    var resizeTimer = null;
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(refresh, 200);
    });

    /* ---------- اجرا ---------- */
    refresh();
  }

  function initAll() {
    var sliders = document.querySelectorAll('[data-slider]');
    for (var i = 0; i < sliders.length; i++) {
      initSlider(sliders[i]);
    }
  }

  // شروع
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  // رندر داینامیک (main.js کارت‌ها رو می‌سازه)
  if (window.MutationObserver) {
    var moTimer = null;
    var mo = new MutationObserver(function () {
      clearTimeout(moTimer);
      moTimer = setTimeout(initAll, 150);
    });
    mo.observe(document.body, { childList: true, subtree: true });
  }

  // در دسترس بودن برای فراخوانی دستی
  window.PHSliderInit = initAll;
})();

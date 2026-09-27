/* ============================================================
   Pejvak Hesab — Slider JS v2.0.0
   راه‌حل قطعی: scrollBy ساده بدون محاسبه index
   ============================================================ */
(function () {
  'use strict';

  function getRTL() {
    return document.documentElement.getAttribute('dir') === 'rtl' ||
           getComputedStyle(document.body).direction === 'rtl';
  }

  function initSlider(root) {
    if (root.classList.contains('is-ready')) return;

    var track = root.querySelector('[data-slider-track]');
    var prevBtn = root.querySelector('[data-slider-prev]');
    var nextBtn = root.querySelector('[data-slider-next]');
    var dotsBox = root.querySelector('[data-slider-dots]');

    if (!track) {
      root.classList.add('is-ready');
      return;
    }

    var slides = track.querySelectorAll(':scope > *');
    if (slides.length === 0) {
      root.classList.add('is-ready');
      return;
    }

    var rtl = getRTL();

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

    /* ---------- به‌روزرسانی دکمه‌ها ---------- */
    function updateButtons() {
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
      if (!dotsBox) return;
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

    /* ---------- دکمه‌ها ---------- */
    function goNext() {
      var step = getStep();
      if (step < 1) return;
      var delta = rtl ? -step : step;
      track.scrollBy({ left: delta, behavior: 'smooth' });
    }

    function goPrev() {
      var step = getStep();
      if (step < 1) return;
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

    /* ---------- چک کردن وضعیت اول ---------- */
    function checkState() {
      // صبر کن تا layout آماده بشه
      requestAnimationFrame(function () {
        if (!needsSlider()) {
          // کارت‌ها کم هستن → اسلایدر غیرفعال
          root.classList.add('is-disabled');
        } else {
          root.classList.remove('is-disabled');
          buildDots();
          updateButtons();
          updateDots();
        }
        root.classList.add('is-ready');
      });
    }

    /* ---------- resize ---------- */
    var resizeTimer = null;
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        // محاسبه مجدد
        if (!needsSlider()) {
          root.classList.add('is-disabled');
        } else {
          root.classList.remove('is-disabled');
          buildDots();
          updateButtons();
          updateDots();
        }
      }, 200);
    });

    /* ---------- اجرا ---------- */
    checkState();
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

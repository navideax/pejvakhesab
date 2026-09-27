/* Pejvak Hesab — Slider config loader
   تنظیمات از window.PH_SLIDER_CFG (functions.php تزریق می‌کنه)
   این فایل فقط برای اطمینان از بارگذاری درست config هست
*/
(function () {
  if (typeof window.PH_SLIDER_CFG === 'undefined') {
    window.PH_SLIDER_CFG = {
      perView: { desktop: 3, tablet: 2, mobile: 1 },
      gap: 20,
      autoplay: false,
      autoplayDelay: 5000,
      loop: false,
      dots: true,
      arrows: true
    };
  }
})();

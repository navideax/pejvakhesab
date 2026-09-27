<?php
/**
 * Template Name: راهکار صنفی
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="sw-hero" id="solHero"></div>
<section class="section"><div class="container">
  <div class="section-head center"><div><span class="eyebrow"><span class="dot"></span>مسیر راه‌اندازی</span><h2 class="section-title">از مشاوره تا بهره‌برداری در ۴ قدم</h2></div></div>
  <div class="svc-grid c4" id="solSteps"></div>
</div></section>
<section class="section section-soft"><div class="container">
  <div class="section-head"><div><span class="eyebrow amber"><span class="dot"></span>تجهیزات پیشنهادی</span><h2 class="section-title">پکیج پیشنهادی این صنف</h2></div><a class="link-more" href="<?php echo esc_url(ph_url('shop')); ?>">مشاهده همه <span data-icon="arrowLeft"></span></a></div>
  <?php ph_slider_open('solProds', 'p-grid c3'); ?>
</div></section>
<section class="section"><div class="container">
  <div class="final-cta"><h2><?php echo esc_html(ph_opt('ph_sol_cta_t', 'راه‌اندازی صنف شما، تخصص ماست')); ?></h2><p><?php echo esc_html(ph_opt('ph_sol_cta_s', 'همین امروز مشاوره بگیرید و ترکیب بهینه نرم‌افزار و تجهیزات را بشناسید.')); ?></p>
  <div class="row"><button class="btn btn-accent btn-lg" data-consult><span data-icon="headset"></span>دریافت مشاوره رایگان</button><a class="btn btn-outline-white btn-lg" href="<?php echo esc_url(home_url('/')); ?>#wizard"><span data-icon="spark"></span>پیشنهادگر نرم‌افزار</a></div></div>
</div></section>
</main>
<?php get_footer();

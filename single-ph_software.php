<?php
/**
 * Single Software
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="sw-hero" id="swHero"></div>
<section class="section"><div class="container">
  <div class="section-head center"><div><span class="eyebrow"><span class="dot"></span>ویژگی‌ها</span><h2 class="section-title">چرا این نرم‌افزار؟</h2></div></div>
  <div class="feat-grid" id="swFeats"></div>
</div></section>
<section class="section section-soft"><div class="container">
  <div class="section-head center"><div><span class="eyebrow amber"><span class="dot"></span>کاربرد</span><h2 class="section-title">مناسب چه کسب‌وکارهایی است؟</h2></div></div>
  <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap" id="swFor"></div>
</div></section>
<section class="section"><div class="container">
  <div class="section-head center"><div><span class="eyebrow"><span class="dot"></span>ماژول‌ها</span><h2 class="section-title">ماژول‌های یکپارچه</h2><p class="section-sub">همه بخش‌ها در یک سیستم؛ بدون نیاز به نرم‌افزار جدا</p></div></div>
  <div class="feat-grid" id="swModules"></div>
</div></section>
<section class="section section-soft"><div class="container">
  <div class="section-head center"><div><span class="eyebrow amber"><span class="dot"></span>نسخه‌ها و قیمت</span><h2 class="section-title">پلن مناسب خود را انتخاب کنید</h2><p class="section-sub">لایسنس دائم + آموزش + پشتیبانی؛ بدون هزینه پنهان</p></div></div>
  <div class="plan-grid" id="swPlans"></div>
</div></section>
<section class="section"><div class="container">
  <div class="section-head center"><div><span class="eyebrow"><span class="dot"></span>زیرساخت</span><h2 class="section-title">سیستم مورد نیاز</h2></div></div>
  <div class="req-box" id="swReq"></div>
</div></section>
<section class="section section-soft"><div class="container" style="max-width:860px">
  <div class="section-head center"><div><h2 class="section-title">سوالات متداول</h2></div></div>
  <div class="accordion" id="swFaq"></div>
</div></section>
<section class="section"><div class="container">
  <div class="section-head"><div><h2 class="section-title">سایر نرم‌افزارها</h2></div><a class="link-more" href="<?php echo esc_url(ph_url('compare')); ?>">مقایسه نرم‌افزارها <span data-icon="arrowLeft"></span></a></div>
  <?php ph_slider_open('swOthers', 'soft-grid c3'); ?>
</div></section>
<section class="section" style="padding-top:0"><div class="container">
  <div class="final-cta"><h2><?php echo esc_html(ph_opt('ph_sw_cta_t', 'هنوز مطمئن نیستید؟ دمو ببینید')); ?></h2><p><?php echo esc_html(ph_opt('ph_sw_cta_s', 'جلسه معرفی آنلاین رایگان؛ نرم‌افزار را با سناریوی واقعی کسب‌وکار خودتان ببینید.')); ?></p>
  <div class="row"><button class="btn btn-accent btn-lg" data-consult><span data-icon="eye"></span>درخواست دمو رایگان</button><a class="btn btn-outline-white btn-lg" href="<?php echo esc_url(home_url('/')); ?>#wizard"><span data-icon="spark"></span>راهنمای انتخاب نرم‌افزار</a></div></div>
</div></section>
</main>
<?php get_footer();

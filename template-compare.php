<?php
/**
 * Template Name: مقایسه
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="page-hero"><div class="container"><div class="breadcrumb-lite"><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div><h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_excerpt() ?: 'تا ۳ محصول یا نرم‌افزار را کنار هم ببینید و بهترین تصمیم را بگیرید'); ?></p></div></div>
<div class="section"><div class="container">
  <div id="cmpEmpty" class="empty" style="display:none;background:#fff;border:1px solid var(--line);border-radius:20px"><span data-icon="compare" style="font-size:64px;color:#C2D2D8"></span><h3>لیست مقایسه خالی است</h3><p>از دکمه مقایسه روی کارت محصولات استفاده کنید.</p><div style="display:flex;gap:10px;justify-content:center;margin-top:16px;flex-wrap:wrap"><a class="btn btn-primary" href="<?php echo esc_url(ph_url('shop')); ?>">مشاهده فروشگاه</a><a class="btn btn-outline" href="<?php echo esc_url(home_url('/')); ?>#software">نرم‌افزارها</a></div></div>
  <div id="cmpBox"></div>
</div></div>
</main>
<?php get_footer();

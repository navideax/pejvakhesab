<?php
/**
 * Template Name: سبد خرید
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<nav class="breadcrumb"><div class="container"><ol><li><a href="<?php echo home_url('/'); ?>">خانه</a></li><li><span aria-current="page">سبد خرید</span></li></ol></div></nav>
<div class="section" style="padding-top:40px"><div class="container">
  <div class="section-head"><div><h1 class="section-title"><?php the_title(); ?></h1><p class="section-sub">کالاها را بازبینی کنید؛ کد تخفیف <b class="num" dir="ltr"><?php echo esc_html(ph_opt('ph_coupon_code', 'PEJVAK10')); ?></b> یعنی <?php echo esc_html(strtr((string) (int) ph_opt('ph_coupon_off', 10), '0123456789', '۰۱۲۳۴۵۶۷۸۹')); ?>٪ تخفیف 🎁</p></div></div>
  <div class="cart-layout" id="cartWrap"></div>
</div></div>
</main>
<?php get_footer();

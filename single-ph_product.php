<?php
/**
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<nav class="breadcrumb"><div class="container"><ol><li><a href="<?php echo home_url('/'); ?>">خانه</a></li><li><a href="<?php echo ph_url('shop'); ?>">فروشگاه</a></li><li><span aria-current="page" id="pdCrumb">محصول</span></li></ol></div></nav>
<div class="section" style="padding-top:40px"><div class="container"><div id="pdWrap"></div></div></div>
<section class="section section-soft" style="padding-top:56px"><div class="container">
<div class="section-head"><div><h2 class="section-title">محصولات مرتبط</h2><p class="section-sub">تکمیل‌کننده این خرید برای فروشگاه شما</p></div><a class="link-more" href="<?php echo ph_url('shop'); ?>">مشاهده همه <span data-icon="arrowLeft"></span></a></div>
<div class="p-grid c4" id="relGrid"></div></div></section>
</main>
<div class="sticky-buy" id="stickyBuy"></div>
<?php get_footer();

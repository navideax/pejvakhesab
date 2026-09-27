<?php
/**
 * Template Name: تجهیزات فروشگاهی
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="page-hero"><div class="container"><div class="breadcrumb-lite"><a href="<?php echo home_url('/'); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div><h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_excerpt() ?: 'از صندوق تا بارکدخوان؛ تجهیزات تست‌شده، سازگار با نرم‌افزار شما و با گارانتی شرکتی'); ?></p></div></div>
<section class="section"><div class="container">
  <div class="section-head"><div><span class="eyebrow"><span class="dot"></span>دسته‌بندی تجهیزات</span><h2 class="section-title">بر اساس نیاز انتخاب کنید</h2></div></div>
  <div class="cat-grid" id="">

        <?php $ph_terms = get_terms(['taxonomy' => 'ph_cat', 'hide_empty' => false, 'number' => 10000000]); ?>
        <?php if ($ph_terms && !is_wp_error($ph_terms)) : foreach ($ph_terms as $pt) : ?>
            <a class="cat-card reveal" href="<?php echo esc_url(ph_url('product-cat')); ?><?php echo esc_attr($pt->slug); ?>"><span class="cat-ic"><span data-icon="<?php echo esc_attr(ph_cat_icon($pt->slug)); ?>"></span></span><div><h3><?php echo esc_html($pt->name); ?></h3><p><?php echo esc_html($pt->description ?: 'تجهیزات تخصصی فروشگاهی'); ?></p></div><span class="go"><span data-icon="arrowLeft"></span></span></a>
        <?php endforeach; endif; ?>
    </div>
</div></section>
<section class="section section-soft"><div class="container">
  <div class="section-head"><div><span class="eyebrow amber"><span class="dot"></span>پیشنهاد کارشناسان</span><h2 class="section-title">پرفروش‌ترین تجهیزات</h2></div><a class="link-more" href="<?php echo ph_url('shop'); ?>">مشاهده همه <span data-icon="arrowLeft"></span></a></div>
  <div class="p-grid c3" id="hwGrid"></div>
</div></section>
<section class="section"><div class="container ready-grid">
  <div class="reveal"><span class="eyebrow"><span class="dot"></span>خدمات تجهیزات</span><h2 class="section-title">خرید تجهیزات، فقط شروع ماجراست</h2>
    <ul class="check-list"><?php $ph_hc = ph_pairs(ph_opt('ph_hw_check', '')); if (!$ph_hc) $ph_hc = [['بررسی سازگاری رایگان', 'قبل از خرید، سازگاری با نرم‌افزار و سیستم شما چک می‌شود.'], ['تست قبل از ارسال', 'هر دستگاه قبل از ارسال روشن، تست و پیکربندی می‌شود.'], ['نصب و راه‌اندازی', 'نصب حضوری یا ریموت با آموزش کامل کار با دستگاه.'], ['گارانتی و قطعات', 'گارانتی شرکتی ۱۲ تا ۱۸ ماهه + تأمین قطعات.']]; foreach ($ph_hc as [$ht, $hd]) echo '<li><span data-icon="checkCircle"></span><span><b>' . esc_html($ht) . ':</b> ' . esc_html($hd) . '</span></li>'; ?></ul>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px"><a class="btn btn-primary" href="<?php echo ph_url('services'); ?>">مشاهده خدمات</a><button class="btn btn-outline" data-consult>استعلام سازگاری</button></div></div>
  <div class="ready-visual reveal"><img src="<?php echo PH_URI; ?>/assets/img/img-ready-system.jpg" alt="خدمات تجهیزات فروشگاهی" loading="lazy"></div>
</div></section>
</main>
<?php get_footer();

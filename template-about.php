<?php
/**
 * Template Name: درباره ما
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="page-hero"><div class="container"><div class="breadcrumb-lite"><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div><h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_excerpt() ?: 'Pejvak Hesab — شریک تخصصی کسب‌وکارهای فروشگاهی در مسیر هوشمندسازی'); ?></p></div></div>
<section class="section"><div class="container about-grid">
  <div class="about-visual"><img src="<?php echo PH_URI; ?>/assets/img/img-supermarket.jpg" alt="فروشگاه مدرن" loading="lazy"><div class="exp-badge"><b class="num"><?php echo esc_html(ph_opt('ph_about_exp', '+۱۰')); ?></b><span><?php echo esc_html(ph_opt('ph_about_exp_label', 'سال تجربه صنفی')); ?></span></div></div>
  <div><span class="eyebrow"><span class="dot"></span>داستان ما</span><h2 class="section-title">تخصص ما، آرامش خیال شماست</h2>
  <div style="margin-top:14px"><?php echo wpautop(esc_html(ph_opt('ph_about_story', 'پژواک حساب با تمرکز بر دو حوزه به‌هم‌پیوسته شکل گرفت: نرم‌افزارهای حسابداری و تجهیزات فروشگاهی. ما باور داریم فروشگاه مدرن، به یک راهکار یکپارچه نیاز دارد؛ نه چند تکه جدا از هم. به همین دلیل، از مشاوره و انتخاب تا تأمین، نصب، آموزش و پشتیبانی را یکجا ارائه می‌کنیم.'))); ?></div>
  <ul class="check-list"><?php $ph_ak = ph_lines(ph_opt('ph_about_checks', '')); if (!$ph_ak) $ph_ak = ['تخصص همزمان در نرم‌افزار و سخت‌افزار فروشگاهی', 'راهکار آماده برای ۸ صنف مختلف', 'ضمانت اصالت کالا و گارانتی شرکتی', 'پشتیبانی فنی واقعی توسط کارشناس متخصص']; foreach ($ph_ak as $k) echo '<li><span data-icon="checkCircle"></span>' . esc_html($k) . '</li>'; ?></ul>
  <div style="display:flex;gap:12px;flex-wrap:wrap"><a class="btn btn-primary" href="<?php echo esc_url(ph_url('services')); ?>">خدمات ما</a><a class="btn btn-outline" href="<?php echo esc_url(ph_url('contact')); ?>">تماس با ما</a></div></div>
</div></section>
<section class="section" style="padding-top:0"><div class="container"><div class="counter-band"><?php $ph_at = wp_count_terms('ph_cat'); $ph_ac = [[(int) wp_count_posts('ph_software')->publish, 'نرم‌افزار تخصصی'], [is_wp_error($ph_at) ? 0 : (int) $ph_at, 'دسته تجهیزات'], [(int) wp_count_posts('ph_solution')->publish, 'راهکار صنفی آماده'], [(int) wp_count_posts('ph_service')->publish, 'خدمت تخصصی یکپارچه']]; foreach ($ph_ac as [$n, $l]) echo '<div><b class="num">' . esc_html(strtr((string) $n, '0123456789', '۰۱۲۳۴۵۶۷۸۹')) . '</b><span>' . esc_html($l) . '</span></div>'; ?></div></div></section>
<section class="section section-soft"><div class="container">
  <div class="section-head center"><div><span class="eyebrow"><span class="dot"></span>ارزش‌های ما</span><h2 class="section-title">اصولی که به آن پایبندیم</h2></div></div>
  <div class="why-grid"><?php $ph_av = array_map(function ($l) { return array_pad(explode('|', $l, 3), 3, ''); }, ph_lines(ph_opt('ph_about_values', ''))); if (!$ph_av) $ph_av = [['shield', 'صداقت در پیشنهاد', 'اگر محصولی به درد شما نخورد، صریح می‌گوییم؛ حتی به قیمت از دست دادن فروش.'], ['wrench', 'تخصص واقعی', 'تیم فنی ما خودش نصب می‌کند، آموزش می‌دهد و پشتیبانی می‌کند.'], ['headset', 'همراهی بلندمدت', 'رابطه ما با تحویل کالا تمام نمی‌شود؛ تازه شروع می‌شود.']]; foreach ($ph_av as [$vi, $vt, $vd]) echo '<div class="why-card"><span class="ic"><span data-icon="' . esc_attr($vi ?: 'shield') . '"></span></span><h3>' . esc_html($vt) . '</h3><p>' . esc_html($vd) . '</p></div>'; ?></div>
</div></section>
</main>
<?php get_footer();

<?php
/**
 * Template Name: خدمات
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="page-hero"><div class="container"><div class="breadcrumb-lite"><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div><h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_excerpt() ?: 'از انتخاب تا بهره‌برداری و پشتیبانی بلندمدت؛ یک همراه واقعی برای کسب‌وکار شما'); ?></p></div></div>
<section class="section"><div class="container">
<div class="svc-grid">
  <?php $ph_svcs = get_posts(['post_type' => 'ph_service', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']); ?>
  <?php foreach ($ph_svcs as $si => $svc) : $ph_btn = get_post_meta($svc->ID, '_ph_btn', true) ?: 'consult'; $ph_bt = get_post_meta($svc->ID, '_ph_btn_text', true) ?: 'درخواست مشاوره'; ?>
  <div class="svc-card"><span class="step-n"><?php echo esc_html(strtr((string) ($si + 1), '0123456789', '۰۱۲۳۴۵۶۷۸۹')); ?></span><span class="ic"><span data-icon="<?php echo esc_attr(get_post_meta($svc->ID, '_ph_icon', true) ?: 'checkCircle'); ?>"></span></span><h3><?php echo esc_html(get_the_title($svc)); ?></h3><p><?php echo esc_html($svc->post_content); ?></p><?php if ($ph_btn === 'consult') : ?><button class="link-more" data-consult<?php $ph_subj = get_post_meta($svc->ID, '_ph_subject', true); if ($ph_subj) echo ' data-subject="' . esc_attr($ph_subj) . '"'; ?>><?php echo esc_html($ph_bt); ?> <span data-icon="arrowLeft"></span></button><?php else : ?><a class="link-more" href="<?php echo esc_url(preg_match('#^https?://#', $ph_btn) ? $ph_btn : ph_url($ph_btn)); ?>"><?php echo esc_html($ph_bt); ?> <span data-icon="arrowLeft"></span></a><?php endif; ?></div>
  <?php endforeach; ?>
</div></div>
</div></section>
<section class="section" style="padding-top:0"><div class="container"><div class="final-cta"><h2><?php echo esc_html(ph_opt('ph_svc_cta_t', 'به کدام خدمت نیاز دارید؟')); ?></h2><p><?php echo esc_html(ph_opt('ph_svc_cta_s', 'درخواست خود را ثبت کنید تا کارشناسان ما با شما تماس بگیرند.')); ?></p><div class="row"><button class="btn btn-accent btn-lg" data-consult><span data-icon="headset"></span>ثبت درخواست خدمت</button><a class="btn btn-outline-white btn-lg" href="<?php echo esc_url(ph_url('contact')); ?>">تماس با ما</a></div></div></div></section>
</main>
<?php get_footer();

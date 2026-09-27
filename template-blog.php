<?php
/**
 * Template Name: مجله
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="page-hero"><div class="container"><div class="breadcrumb-lite"><a href="<?php echo home_url('/'); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div><h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_excerpt() ?: 'آموزش، راهنمای خرید و مقایسه تخصصی نرم‌افزار و تجهیزات فروشگاهی'); ?></p></div></div>
<div class="section"><div class="container">
  <div class="shop-cat-chips" id="blogChips"><a href="<?php echo esc_url(ph_url('blog')); ?>" data-bcat="" class="active">همه مطالب</a><?php $ph_bc = get_categories(['hide_empty' => false]); foreach ($ph_bc as $c) echo '<a href="' . esc_url(get_category_link($c)) . '" data-bcat="' . esc_attr($c->name) . '">' . esc_html($c->name) . '</a>'; ?></div>
  <div class="post-grid" id="blogGrid"></div>
</div></div>
</main>
<?php get_footer();

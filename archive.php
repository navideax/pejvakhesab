<?php
defined('ABSPATH') || exit;
get_header();
?>
<div class="page-hero"><div class="container">
  <div class="breadcrumb-lite"><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a><span>/</span><span><?php the_archive_title(); ?></span></div>
  <h1><?php the_archive_title(); ?></h1>
  <p><?php echo esc_html(wp_strip_all_tags(get_the_archive_description()) ?: 'آرشیو مطالب پژواک حساب'); ?></p>
</div></div>
<div class="section"><div class="container">
  <?php if (have_posts()) : ?>
  <div class="post-grid"><?php while (have_posts()) : the_post(); get_template_part('template-parts/post-card'); endwhile; ?></div>
  <?php $links = paginate_links(['type' => 'array', 'prev_text' => '‹', 'next_text' => '›']); if ($links) echo '<div class="ph-pagination">' . implode('', $links) . '</div>'; ?>
  <?php else : ?>
  <div class="empty"><span data-icon="book"></span><h3>مطلبی یافت نشد</h3><p>در این بخش هنوز مطلبی منتشر نشده است.</p></div>
  <?php endif; ?>
</div></div>
<?php get_footer(); ?>

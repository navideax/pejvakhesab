<?php
defined('ABSPATH') || exit;
$cats = get_the_category();
$cat = $cats ? $cats[0]->name : 'مجله';
?>
<article class="post-card reveal in">
  <a class="post-thumb" href="<?php the_permalink(); ?>">
    <?php if (has_post_thumbnail()) : the_post_thumbnail('ph-card'); else : ?>
    <img src="<?php echo PH_URI; ?>/assets/img/hero-pos.jpg" alt="<?php the_title_attribute(); ?>">
    <?php endif; ?>
    <span class="post-cat"><?php echo esc_html($cat); ?></span>
  </a>
  <div class="post-body">
    <a href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3></a>
    <p><?php echo esc_html(get_the_excerpt()); ?></p>
    <div class="post-meta">
      <span><span data-icon="cal"></span><?php echo esc_html(ph_fa_date(get_post_time('U'))); ?></span>
      <span><span data-icon="clock"></span><?php echo esc_html(ph_read_time(get_the_ID())); ?></span>
      <a class="link-more" style="margin-inline-start:auto" href="<?php the_permalink(); ?>">مطالعه <span data-icon="arrowLeft"></span></a>
    </div>
  </div>
</article>

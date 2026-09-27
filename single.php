<?php
/** Single blog post — server rendered article layout */
defined('ABSPATH') || exit;
get_header();
while (have_posts()) : the_post();
    $cats = get_the_category();
    $cat = $cats ? $cats[0]->name : 'مجله';
?>
<nav class="breadcrumb"><div class="container"><ol>
  <li><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a></li>
  <li><a href="<?php echo esc_url(ph_url('blog')); ?>">مجله</a></li>
  <li><span aria-current="page"><?php the_title(); ?></span></li>
</ol></div></nav>
<div class="section" style="padding-top:40px"><div class="container article-layout">
  <div>
    <article class="article">
      <?php if (has_post_thumbnail()) : the_post_thumbnail('ph-cover', ['class' => 'article-cover']); else : ?>
      <img class="article-cover" src="<?php echo PH_URI; ?>/assets/img/hero-pos.jpg" alt="<?php the_title_attribute(); ?>">
      <?php endif; ?>
      <div class="article-body">
        <span class="badge badge-brand"><?php echo esc_html($cat); ?></span>
        <h1><?php the_title(); ?></h1>
        <div class="article-meta">
          <span><span data-icon="user"></span><?php the_author(); ?></span>
          <span><span data-icon="cal"></span><?php echo esc_html(get_post_meta(get_the_ID(), '_ph_date', true) ?: ph_fa_date(get_post_time('U'))); ?></span>
          <span><span data-icon="clock"></span>زمان مطالعه: <?php echo esc_html(ph_read_time(get_the_ID())); ?></span>
          <span><span data-icon="message"></span><?php $ph_cn = (int) get_comments_number(); echo $ph_cn === 0 ? 'بدون دیدگاه' : ($ph_cn === 1 ? '۱ دیدگاه' : esc_html(strtr((string) $ph_cn, '0123456789', '۰۱۲۳۴۵۶۷۸۹')) . ' دیدگاه'); ?></span>
        </div>
        <div class="prose"><?php the_content(); ?></div>
        <?php $tags = get_the_tags(); if ($tags) : ?>
        <div class="article-tags"><?php foreach ($tags as $t) echo '<a href="' . esc_url(get_tag_link($t)) . '">' . esc_html($t->name) . '</a>'; ?></div>
        <?php endif; ?>
      </div>
    </article>
    <?php comments_template(); ?>
  </div>
  <aside>
    <div class="side-cta"><h4>نیاز به مشاوره دارید؟</h4><p>کارشناسان ما رایگان راهنمایی‌تان می‌کنند.</p><button class="btn btn-accent btn-block" data-consult>مشاوره رایگان</button></div>
    <div class="side-box" style="margin-top:20px"><h4>مطالب پربازدید</h4>
      <?php foreach (get_posts(['numberposts' => 4, 'post__not_in' => [get_the_ID()]]) as $rp) : ?>
      <a class="side-post" href="<?php echo esc_url(get_permalink($rp)); ?>">
        <?php echo get_the_post_thumbnail($rp, 'thumbnail') ?: '<img src="' . PH_URI . '/assets/img/hero-pos.jpg" alt="">'; ?>
        <div><b><?php echo esc_html(get_the_title($rp)); ?></b><span><?php echo esc_html(ph_fa_date($rp->post_date)); ?></span></div>
      </a>
      <?php endforeach; ?>
    </div>
  </aside>
</div>
<?php
$rel = new WP_Query(['posts_per_page' => 3, 'post__not_in' => [get_the_ID()], 'cat' => $cats ? $cats[0]->term_id : 0]);
if ($rel->have_posts()) : ?>
<div class="container" style="margin-top:48px">
  <div class="section-head"><div><h2 class="section-title">مطالب مرتبط</h2></div><a class="link-more" href="<?php echo esc_url(ph_url('blog')); ?>">همه مقالات <span data-icon="arrowLeft"></span></a></div>
  <div class="post-grid"><?php while ($rel->have_posts()) : $rel->the_post(); get_template_part('template-parts/post-card'); endwhile; wp_reset_postdata(); ?></div>
</div>
<?php endif; ?>
</div>
<?php endwhile; get_footer(); ?>

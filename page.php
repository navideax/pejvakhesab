<?php
/** Generic page */
defined('ABSPATH') || exit;
get_header();
while (have_posts()) : the_post();
?>
<div class="page-hero"><div class="container">
  <div class="breadcrumb-lite"><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div>
  <h1><?php the_title(); ?></h1>
</div></div>
<div class="section"><div class="container" style="max-width:900px">
  <div class="co-box"><div class="prose"><?php the_content(); ?></div></div>
</div></div>
<?php endwhile; get_footer(); ?>

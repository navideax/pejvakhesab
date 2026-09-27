<?php
defined('ABSPATH') || exit;
get_header();
?>
<div class="page-hero"><div class="container">
  <div class="breadcrumb-lite"><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a><span>/</span><span>جستجو</span></div>
  <h1>نتایج جستجو برای «<?php echo esc_html(get_search_query()); ?>»</h1>
  <p>در محصولات، نرم‌افزارها و مقالات جستجو شد</p>
</div></div>
<div class="section"><div class="container">
  <form class="search-box" style="max-width:640px;margin-bottom:28px" method="get" action="<?php echo esc_url(home_url('/')); ?>">
    <input class="input" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="جستجوی مجدد...">
    <button class="btn btn-primary" type="submit"><span data-icon="search"></span>جستجو</button>
  </form>
  <?php if (have_posts()) : ?>
  <div class="post-grid"><?php while (have_posts()) : the_post(); get_template_part('template-parts/post-card'); endwhile; ?></div>
  <?php $links = paginate_links(['type' => 'array', 'prev_text' => '‹', 'next_text' => '›']); if ($links) echo '<div class="ph-pagination">' . implode('', $links) . '</div>'; ?>
  <?php else : ?>
  <div class="empty"><span data-icon="search"></span><h3>نتیجه‌ای یافت نشد</h3><p>عبارت دیگری را امتحان کنید یا از <a href="<?php echo esc_url(ph_url('shop')); ?>" style="color:var(--brand-strong);font-weight:700">فروشگاه</a> دیدن کنید.</p></div>
  <?php endif; ?>
</div></div>
<?php get_footer(); ?>

<?php
defined('ABSPATH') || exit;
get_header();
?>
<div class="section"><div class="container">
  <div class="empty" style="background:#fff;border:1px solid var(--line);border-radius:22px;padding:80px 24px">
    <div class="num" style="font-size:72px;font-weight:900;color:var(--brand-tint);line-height:1">۴۰۴</div>
    <h3 style="margin:12px 0 8px">صفحه مورد نظر یافت نشد</h3>
    <p>ممکن است آدرس اشتباه باشد یا صفحه حذف شده باشد.</p>
    <div style="display:flex;gap:10px;justify-content:center;margin-top:20px;flex-wrap:wrap">
      <a class="btn btn-primary" href="<?php echo esc_url(home_url('/')); ?>">بازگشت به خانه</a>
      <a class="btn btn-outline" href="<?php echo esc_url(ph_url('shop')); ?>">فروشگاه</a>
    </div>
  </div>
</div></div>
<?php get_footer(); ?>

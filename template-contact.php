<?php
/**
 * Template Name: تماس
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="page-hero"><div class="container"><div class="breadcrumb-lite"><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div><h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_excerpt() ?: 'برای مشاوره، خرید و پشتیبانی در کنار شما هستیم'); ?></p></div></div>
<div class="section"><div class="container">
<div class="contact-grid">
  <div class="contact-info">
    <div class="c-info-card"><span class="ic"><span data-icon="phone"></span></span><div><b>تلفن فروش و مشاوره</b><a class="dir num" href="tel:<?php echo esc_attr(ph_phone_tel()); ?>"><?php echo esc_html(ph_phone()); ?></a><p><?php echo esc_html(ph_hours()); ?></p></div></div>
    <div class="c-info-card"><span class="ic"><span data-icon="headset"></span></span><div><?php $ph_sp = array_pad(explode('|', ph_opt('ph_contact_support', ''), 3), 3, ''); if (!$ph_sp[0]) $ph_sp = ['پشتیبانی فنی', 'تلفنی و ریموت؛ پاسخگویی در ساعات کاری', 'قراردادهای سازمانی: پشتیبانی ویژه']; ?><b><?php echo esc_html($ph_sp[0]); ?></b><p><?php echo esc_html($ph_sp[1]); if ($ph_sp[2]) echo '<br>' . esc_html($ph_sp[2]); ?></p></div></div>
    <div class="c-info-card"><span class="ic"><span data-icon="mail"></span></span><div><b>ایمیل</b><p dir="ltr" style="text-align:right"><?php echo esc_html(ph_email()); ?></p></div></div>
    <div class="c-info-card"><span class="ic"><span data-icon="pin"></span></span><div><b>آدرس</b><p><?php echo esc_html(ph_address()); ?></p></div></div>
    <div class="c-info-card"><span class="ic"><span data-icon="clock"></span></span><div><b>ساعات کاری</b><p><?php echo esc_html(ph_hours()); ?> · جمعه‌ها تعطیل</p></div></div>
  </div>
  <div class="contact-form-box"><h2 style="font-size:18px;margin-bottom:6px"><?php echo esc_html(ph_opt('ph_contact_form_t', 'ارسال پیام')); ?></h2><p style="font-size:13.5px;color:var(--muted);margin-bottom:22px"><?php echo esc_html(ph_opt('ph_contact_form_s', 'فرم زیر را تکمیل کنید؛ حداکثر ظرف یک روز کاری پاسخ می‌دهیم.')); ?></p>
    <form id="cForm"><div class="form-row"><div class="field"><label>نام و نام خانوادگی <span class="req">*</span></label><input class="input" required placeholder="نام شما"></div><div class="field"><label>شماره موبایل <span class="req">*</span></label><input class="input num" required inputmode="tel" placeholder="۰۹۱۲۰۰۰۰۰۰۰"></div></div>
    <div class="field"><label>موضوع</label><select class="input"><option>مشاوره خرید</option><option>پشتیبانی فنی</option><option>درخواست نصب</option><option>همکاری تجاری</option><option>سایر</option></select></div>
    <div class="field"><label>متن پیام <span class="req">*</span></label><textarea class="input" required placeholder="پیام خود را بنویسید..."></textarea></div>
    <button class="btn btn-primary btn-lg"><span data-icon="send"></span>ارسال پیام</button></form></div>
</div>
<?php $ph_map = ph_opt('ph_map_embed', ''); if ($ph_map) : ?><div class="map-embed" style="border-radius:20px;overflow:hidden"><?php echo $ph_map; ?></div><?php else : ?><div class="map-ph"><span data-icon="pin"></span><b>نقشه و مسیریابی</b><span style="font-size:13px"><?php echo esc_html(ph_address()); ?></span><?php $ph_mu = ph_opt('ph_map_url', ''); if ($ph_mu && $ph_mu !== '#') : ?><a class="btn btn-dark btn-sm" style="margin-top:8px" href="<?php echo esc_url($ph_mu); ?>" target="_blank" rel="noopener">مسیریابی در نقشه</a><?php endif; ?></div><?php endif; ?>
</div></div>
</main>
<?php get_footer();

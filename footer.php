<?php
/**
 * Footer. The #wpFooter guard tells assets/js/components.js to skip its static footer render.
 */
defined('ABSPATH') || exit;
?>
<div id="site-footer"><div id="wpFooter">
  <footer class="site-footer"><div class="container">
    <div class="footer-main">
      <div class="footer-brand">
        <?php if (!dynamic_sidebar('footer-1')) : ?>
        <a class="logo" href="<?php echo esc_url(home_url('/')); ?>">
          <svg class="logo-mark" viewBox="0 0 48 48" fill="none" aria-hidden="true"><rect x="1" y="1" width="46" height="46" rx="13" fill="#07242D"/><path d="M14 34 26 12h6L20 34h-6Z" fill="#0FA3BC"/><path d="M23 34 35 12h6L29 34h-6Z" fill="#F0B429"/></svg>
          <span class="logo-text"><span class="logo-fa"><span class="t">پژواک</span> <span class="a">حساب</span></span><span class="logo-en">Pejvak Hesab</span></span>
        </a>
        <p><?php echo esc_html(ph_opt('ph_footer_about', 'مجموعه تخصصی ارائه‌دهنده نرم‌افزارهای حسابداری، تجهیزات فروشگاهی و راهکارهای یکپارچه فروش.')); ?></p>
        <ul class="footer-contact">
          <li><span data-icon="phone"></span><span>تلفن مشاوره و فروش: <strong class="num"><?php echo esc_html(ph_phone()); ?></strong></span></li>
          <li><span data-icon="mail"></span><span dir="ltr"><?php echo esc_html(ph_email()); ?></span></li>
          <li><span data-icon="pin"></span><span><?php echo esc_html(ph_address()); ?></span></li>
        </ul>
        <?php $ph_soc = []; foreach ([['instagram', 'اینستاگرام'], ['telegram', 'تلگرام'], ['whatsapp', 'واتساپ'], ['linkedin', 'لینکدین']] as $sn) { $su = trim((string) ph_opt('ph_social_' . $sn[0], '')); if ($su !== '' && $su !== '#') $ph_soc[] = [$su, $sn[1], $sn[0]]; } ?>
        <?php if ($ph_soc) : ?><div class="socials"><?php foreach ($ph_soc as $sc) : ?><a href="<?php echo esc_url($sc[0]); ?>" aria-label="<?php echo esc_attr($sc[1]); ?>" target="_blank" rel="noopener"><span data-icon="<?php echo esc_attr($sc[2]); ?>"></span></a><?php endforeach; ?></div><?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="footer-col"><h4><?php echo esc_html(ph_opt('ph_foot_t2', 'دسترسی سریع')); ?></h4>
        <?php if (!dynamic_sidebar('footer-2')) wp_nav_menu(['theme_location' => 'footer-shop', 'container' => false, 'fallback_cb' => 'ph_fb_shop']); ?>
      </div>
      <div class="footer-col"><h4><?php echo esc_html(ph_opt('ph_foot_t3', 'نرم‌افزارها')); ?></h4>
        <?php if (!dynamic_sidebar('footer-3')) wp_nav_menu(['theme_location' => 'footer-soft', 'container' => false, 'fallback_cb' => 'ph_fb_soft']); ?>
      </div>
      <div class="footer-col">
        <?php if (!dynamic_sidebar('footer-4')) : ?>
        <h4><?php echo esc_html(ph_opt('ph_foot_nl_t', 'عضویت در خبرنامه')); ?></h4>
        <p style="font-size:13px"><?php echo esc_html(ph_opt('ph_foot_nl_s', 'آموزش‌ها، تخفیف‌ها و راهنمای خرید تجهیزات را دریافت کنید.')); ?></p>
        <form class="newsletter" id="nlForm"><input class="input" type="email" placeholder="ایمیل شما" required aria-label="ایمیل"><button class="btn btn-accent btn-sm" type="submit">عضویت</button></form>
        <div class="trust-row">
          <?php $ph_chips = ph_pairs(ph_opt('ph_foot_chips', '')); if (!$ph_chips) $ph_chips = [['shield', 'ضمانت اصالت کالا'], ['truck', 'ارسال به سراسر کشور'], ['headset', 'پشتیبانی تخصصی']]; ?>
          <?php foreach ($ph_chips as [$ci, $ct]) : ?><span class="trust-chip"><span data-icon="<?php echo esc_attr($ci ?: 'shield'); ?>"></span><?php echo esc_html($ct); ?></span><?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="footer-bottom">
      <span><?php echo esc_html(str_replace('{year}', ph_fa_year(), ph_opt('ph_foot_copy', '© {year} پژواک حساب — تمامی حقوق محفوظ است.'))); ?><?php $ph_tu = ph_legal_url('ph_terms_url', 'terms'); $ph_pu = ph_legal_url('ph_privacy_url', 'privacy'); if ($ph_tu) : ?> <a href="<?php echo esc_url($ph_tu); ?>" style="text-decoration:underline">قوانین و مقررات</a><?php endif; if ($ph_pu) : ?> · <a href="<?php echo esc_url($ph_pu); ?>" style="text-decoration:underline">حریم خصوصی</a><?php endif; ?> </span>
      <span class="en">© Pejvak Hesab — Retail Technology Solutions</span>
    </div>
  </div></footer>
</div></div>
<?php wp_footer(); ?>
</body>
</html>

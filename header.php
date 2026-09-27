<?php
/**
 * Header: topbar + sticky header + mega menus.
 * The #wpHeader guard tells assets/js/components.js to skip its static header render.
 */
defined('ABSPATH') || exit;
$dp = ph_data_page();
$act = function ($id) use ($dp) {
    $map = ['home' => ['home'], 'software' => ['software'], 'hardware' => ['shop', 'product', 'hardware'], 'solutions' => ['solution'], 'services' => ['services'], 'about' => ['about'], 'contact' => ['contact']];
    return in_array($dp, $map[$id] ?? [], true) ? 'active' : '';
};
$softs = get_posts(['post_type' => 'ph_software', 'numberposts' => 4, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']);
$soft_icons = ['chart', 'shirt', 'briefcase', 'coffee'];
?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-page="<?php echo esc_attr($dp); ?>">
<?php wp_body_open(); ?>
<div id="site-header"><div id="wpHeader">
  <div class="topbar"><div class="container">
    <div class="topbar-right">
      <a class="topbar-item" href="tel:<?php echo esc_attr(ph_phone_tel()); ?>"><span data-icon="phone"></span><strong class="num"><?php echo esc_html(ph_phone()); ?></strong></a>
      <span class="topbar-sep hide-m"></span>
      <span class="topbar-item hide-m"><span data-icon="clock"></span><?php echo esc_html(ph_hours()); ?></span>
    </div>
    <div class="topbar-left">
      <a class="topbar-item hide-m" href="<?php echo esc_url(ph_url('account')); ?>">پیگیری سفارش</a>
      <a class="topbar-item hide-m" href="<?php echo esc_url(ph_url('faq')); ?>">سوالات متداول</a>
      <a class="topbar-item" href="<?php echo esc_url(ph_url('blog')); ?>">مجله پژواک</a>
    </div>
  </div></div>
  <header class="site-header" id="mainHeader"><div class="container header-inner" style="position:relative">
    <a class="logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="پژواک حساب">
      <?php $ph_logo_id = (int) get_theme_mod('custom_logo'); if ($ph_logo_id) : echo wp_get_attachment_image($ph_logo_id, 'full', false, ['class' => 'logo-img']); else : ?>
      <svg class="logo-mark" viewBox="0 0 48 48" fill="none" aria-hidden="true"><rect x="1" y="1" width="46" height="46" rx="13" fill="#07242D"/><path d="M14 34 26 12h6L20 34h-6Z" fill="#0FA3BC"/><path d="M23 34 35 12h6L29 34h-6Z" fill="#F0B429"/></svg>
      <?php endif; ?>
      <span class="logo-text"><span class="logo-fa"><span class="t">پژواک</span> <span class="a">حساب</span></span><span class="logo-en">Pejvak Hesab</span></span>
    </a>
    <?php if (has_nav_menu('primary')) : ?>
      <?php wp_nav_menu(['theme_location' => 'primary', 'container' => 'nav', 'container_class' => 'main-nav', 'menu_class' => 'main-nav', 'link_before' => '', 'fallback_cb' => false]); ?>
    <?php else : ?>
    <nav class="main-nav" aria-label="ناوبری اصلی">
      <div class="has-mega" style="position:relative"><a class="nav-link <?php echo $act('home'); ?>" href="<?php echo esc_url(home_url('/')); ?>">صفحه اصلی</a></div>
      <div class="has-mega" style="position:static"><a class="nav-link <?php echo $act('software'); ?>" href="<?php echo esc_url(ph_url('shop')); ?>?group=software">نرم‌افزار حسابداری<span data-icon="chevDown"></span></a>
        <div class="mega"><div class="container mega-inner">
          <div class="mega-col"><h4>نرم‌افزارها</h4><ul>
            <?php if ($softs) : foreach ($softs as $i => $s) : ?>
            <li><a href="<?php echo esc_url(get_permalink($s)); ?>"><span data-icon="<?php echo esc_attr(get_post_meta($s->ID, '_ph_icon', true) ?: $soft_icons[$i % 4]); ?>"></span><span><?php echo esc_html(get_the_title($s)); ?><small><?php echo esc_html(get_post_meta($s->ID, '_ph_tag', true)); ?></small></span></a></li>
            <?php endforeach; else : ?>
            <li><a href="<?php echo esc_url(ph_url('shop')); ?>"><span data-icon="chart"></span><span>نرم‌افزارها</span></a></li>
            <?php endif; ?>
          </ul></div>
          <div class="mega-col"><h4>راهنمای انتخاب</h4><ul>
            <li><a href="<?php echo esc_url(ph_url('compare')); ?>"><span data-icon="compare"></span><span>مقایسه نرم‌افزارها</span></a></li>
            <li><a href="<?php echo esc_url(home_url('/#wizard')); ?>"><span data-icon="spark"></span><span>کدام نرم‌افزار برای من مناسب است؟</span></a></li>
            <li><a href="<?php echo esc_url(ph_url('blog')); ?>"><span data-icon="book"></span><span>راهنمای خرید نرم‌افزار</span></a></li>
            <li><a href="<?php echo esc_url(ph_url('faq')); ?>"><span data-icon="message"></span><span>سوالات متداول</span></a></li>
          </ul></div>
          <div class="mega-col"><h4>بر اساس کسب‌وکار</h4><ul>
            <?php $ph_msols = get_posts(['post_type' => 'ph_solution', 'numberposts' => 4, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']); ?>
            <?php if ($ph_msols) : foreach ($ph_msols as $ms) : ?>
            <li><a href="<?php echo esc_url(ph_url('solution')); ?>?biz=<?php echo esc_attr($ms->post_name); ?>"><span data-icon="<?php echo esc_attr(get_post_meta($ms->ID, '_ph_icon', true) ?: 'store'); ?>"></span><span><?php echo esc_html(get_the_title($ms)); ?></span></a></li>
            <?php endforeach; else : ?>
            <li><a href="<?php echo esc_url(home_url('/#solutions')); ?>"><span data-icon="grid"></span><span>همه راهکارها</span></a></li>
            <?php endif; ?>
          </ul></div>
          <div class="mega-banner"><h5><?php echo esc_html(ph_opt('ph_mega_sw_t', 'در انتخاب نرم‌افزار مطمئن نیستید؟')); ?></h5><p><?php echo esc_html(ph_opt('ph_mega_sw_s', 'کارشناسان پژواک حساب رایگان راهنمایی‌تان می‌کنند.')); ?></p><button class="btn btn-accent btn-sm" data-consult>مشاوره رایگان</button></div>
        </div></div>
      </div>
      <div class="has-mega" style="position:static"><a class="nav-link <?php echo $act('hardware'); ?>" href="<?php echo esc_url(ph_url('hardware')); ?>">تجهیزات فروشگاهی<span data-icon="chevDown"></span></a>
        <div class="mega"><div class="container mega-inner">
          <div class="mega-col"><h4>دسته‌بندی تجهیزات</h4><ul>
            <?php $ph_mterms = get_terms(['taxonomy' => 'ph_cat', 'hide_empty' => false]); ?>
            <?php if ($ph_mterms && !is_wp_error($ph_mterms)) : foreach ($ph_mterms as $mt) : ?>
            <li><a href="<?php echo esc_url(ph_url('shop')); ?>?pcat=<?php echo esc_attr($mt->slug); ?>"><span data-icon="<?php echo esc_attr(ph_cat_icon($mt->slug)); ?>"></span><span><?php echo esc_html($mt->name); ?></span></a></li>
            <?php endforeach; endif; ?>
            <li><a href="<?php echo esc_url(ph_url('shop')); ?>"><span data-icon="grid"></span><span>همه تجهیزات</span></a></li>
          </ul></div>
          <div class="mega-col"><h4>خدمات تجهیزات</h4><ul>
            <li><a href="<?php echo esc_url(ph_url('services')); ?>"><span data-icon="wrench"></span><span>نصب و راه‌اندازی</span></a></li>
            <li><a href="<?php echo esc_url(ph_url('services')); ?>"><span data-icon="shield"></span><span>گارانتی و خدمات پس از فروش</span></a></li>
            <li><a href="<?php echo esc_url(ph_url('compare')); ?>"><span data-icon="compare"></span><span>مقایسه تجهیزات</span></a></li>
          </ul></div>
          <div class="mega-banner"><h5><?php echo esc_html(ph_opt('ph_mega_hw_t', 'سیستم آماده فروشگاهی')); ?></h5><p><?php echo esc_html(ph_opt('ph_mega_hw_s', 'فقط وصل کنید و شروع کنید؛ با نصب و آموزش رایگان.')); ?></p><a class="btn btn-accent btn-sm" href="<?php echo esc_url(ph_url('shop')); ?>?pcat=ready-systems">مشاهده سیستم‌ها</a></div>
        </div></div>
      </div>
      <div class="has-mega" style="position:static"><a class="nav-link <?php echo $act('solutions'); ?>" href="<?php echo esc_url(home_url('/#solutions')); ?>">راهکارهای فروشگاهی<span data-icon="chevDown"></span></a>
        <div class="mega"><div class="container mega-inner">
          <?php $ph_asols = get_posts(['post_type' => 'ph_solution', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']); ?>
          <?php if ($ph_asols) : $ph_half = max(1, (int) ceil(count($ph_asols) / 2)); foreach (array_chunk($ph_asols, $ph_half) as $ci => $chunk) : ?>
          <div class="mega-col"><h4><?php echo $ci === 0 ? 'راهکارهای صنفی' : 'سایر صنوف'; ?></h4><ul>
            <?php foreach ($chunk as $as) : ?>
            <li><a href="<?php echo esc_url(ph_url('solution')); ?>?biz=<?php echo esc_attr($as->post_name); ?>"><span data-icon="<?php echo esc_attr(get_post_meta($as->ID, '_ph_icon', true) ?: 'store'); ?>"></span><span><?php echo esc_html(get_the_title($as)); ?></span></a></li>
            <?php endforeach; ?>
          </ul></div>
          <?php endforeach; else : ?>
          <div class="mega-col"><h4>راهکارهای صنفی</h4><ul><li><a href="<?php echo esc_url(home_url('/#solutions')); ?>"><span data-icon="grid"></span><span>همه راهکارها</span></a></li></ul></div>
          <?php endif; ?>
          <div class="mega-col"><h4>چرا راهکار آماده؟</h4><ul>
            <li><a href="<?php echo esc_url(ph_url('services')); ?>"><span data-icon="users"></span><span>مشاوره قبل از خرید</span></a></li>
            <li><a href="<?php echo esc_url(ph_url('services')); ?>"><span data-icon="wrench"></span><span>نصب، آموزش و پشتیبانی</span></a></li>
            <li><a href="<?php echo esc_url(ph_url('about')); ?>"><span data-icon="award"></span><span>چرا پژواک حساب؟</span></a></li>
          </ul></div>
          <div class="mega-banner"><h5><?php echo esc_html(ph_opt('ph_mega_sol_t', 'راهکار مناسب کسب‌وکار شما؟')); ?></h5><p><?php echo esc_html(ph_opt('ph_mega_sol_s', 'در ۲ دقیقه، نرم‌افزار مناسب خود را پیدا کنید.')); ?></p><a class="btn btn-accent btn-sm" href="<?php echo esc_url(home_url('/#wizard')); ?>">شروع راهنمای انتخاب</a></div>
        </div></div>
      </div>
      <div class="has-mega" style="position:relative"><a class="nav-link <?php echo $act('services'); ?>" href="<?php echo esc_url(ph_url('services')); ?>">خدمات</a></div>
      <div class="has-mega" style="position:relative"><a class="nav-link <?php echo $act('about'); ?>" href="<?php echo esc_url(ph_url('about')); ?>">درباره ما</a></div>
      <div class="has-mega" style="position:relative"><a class="nav-link <?php echo $act('contact'); ?>" href="<?php echo esc_url(ph_url('contact')); ?>">تماس با ما</a></div>
    </nav>
    <?php endif; ?>
    <div class="header-actions">
      <button class="icon-btn" id="btnSearch" aria-label="جستجو"><span data-icon="search"></span></button>
      <a class="icon-btn hide-m" href="<?php echo esc_url(ph_url('compare')); ?>" aria-label="مقایسه"><span data-icon="compare"></span><span class="count" id="cmpCount" style="display:none">۰</span></a>
      <a class="icon-btn" href="<?php echo esc_url(ph_url('account')); ?>" aria-label="حساب کاربری"><span data-icon="user"></span></a>
      <button class="icon-btn" id="btnCart" aria-label="سبد خرید"><span data-icon="cart"></span><span class="count" id="cartCount" style="display:none">۰</span></button>
      <button class="btn btn-accent btn-sm header-cta" data-consult><span data-icon="headset"></span><span class="cta-txt">مشاوره رایگان</span></button>
      <button class="icon-btn hamburger" id="btnMenu" aria-label="منو"><span data-icon="menu"></span></button>
    </div>
  </div></header>
</div></div>

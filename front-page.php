<?php
/**
 * Pejvak Hesab Theme
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<!-- HERO -->
<section class="hero"><div class="hero-bg"></div><div class="hero-grid-bg"></div>
<div class="container hero-inner">
  <div class="hero-copy">
    <span class="eyebrow"><span class="dot"></span><?php echo esc_html(ph_opt('ph_hero_eyebrow', 'نرم‌افزار حسابداری · تجهیزات فروشگاهی · راهکار یکپارچه')); ?></span>
    <h1 class="hero-title"><?php echo wp_kses_post(ph_opt('ph_hero_title', 'راهکارهای هوشمند برای <span class="hl">حسابداری</span> و <span class="hl">فروشگاه</span> شما')); ?></h1>
    <p class="hero-sub"><?php echo esc_html(ph_opt('ph_hero_sub', 'از نرم‌افزار حسابداری تا تجهیزات فروشگاهی؛ هر آنچه برای مدیریت حرفه‌ای کسب‌وکار خود نیاز دارید، در پژواک حساب.')); ?></p>
    <div class="hero-cta">
      <a class="btn btn-primary btn-lg" href="<?php echo esc_url(ph_url('shop')); ?>"><span data-icon="package"></span>مشاهده محصولات</a>
      <button class="btn btn-accent btn-lg" data-consult><span data-icon="headset"></span>دریافت مشاوره رایگان</button>
    </div>
    <ul class="hero-points"><?php $ph_hp = ph_lines(ph_opt('ph_hero_points', '')); if (!$ph_hp) $ph_hp = ['ضمانت اصالت کالا', 'نصب و آموزش', 'پشتیبانی تخصصی']; foreach ($ph_hp as $hp) echo '<li><span data-icon="checkCircle"></span>' . esc_html($hp) . '</li>'; ?></ul>
    <div class="hero-stats"><?php
$ph_sl = ph_lines(ph_opt('ph_hero_stat_labels', ''));
if (count($ph_sl) < 3) $ph_sl = ['نرم‌افزار تخصصی', 'دسته تجهیزات', 'راهکار صنفی'];
$ph_tc = wp_count_terms('ph_cat');
$ph_sc = [(int) wp_count_posts('ph_software')->publish, is_wp_error($ph_tc) ? 0 : (int) $ph_tc, (int) wp_count_posts('ph_solution')->publish];
foreach ([0, 1, 2] as $i) echo '<div><b class="num" data-count="' . $ph_sc[$i] . '">۰</b><span>' . esc_html($ph_sl[$i]) . '</span></div>';
?></div>
  </div>
  <div class="hero-visual">
    <div class="hero-frame"><?php $ph_hi = ph_opt('ph_hero_image', ''); ?><img src="<?php echo esc_url($ph_hi ?: PH_URI . '/assets/img/hero-pos.jpg'); ?>" alt="صندوق فروشگاهی و نرم‌افزار حسابداری پژواک حساب" fetchpriority="high"></div>
    <div class="float-card fc-1"><span class="ic"><span data-icon="check"></span></span><div><b><?php echo esc_html(ph_opt('ph_float_1t', 'فاکتور #۱۲۴۸ صادر شد')); ?></b><span><?php echo esc_html(ph_opt('ph_float_1s', 'نرم‌افزار باران · صندوق ۱')); ?></span></div></div>
    <div class="float-card fc-2"><span class="ic"><span data-icon="chart"></span></span><div><b class="num"><?php echo esc_html(ph_opt('ph_float_2t', '٪۱۸ رشد فروش ماه')); ?></b><span><?php echo esc_html(ph_opt('ph_float_2s', 'گزارش مدیریتی لحظه‌ای')); ?></span></div></div>
    <div class="float-card fc-3"><span class="ic"><span data-icon="printer"></span></span><div><b><?php echo esc_html(ph_opt('ph_float_3t', 'فیش‌پرینتر متصل است')); ?></b><span><?php echo esc_html(ph_opt('ph_float_3s', 'آماده چاپ فاکتور')); ?></span></div></div>
  </div>
</div></section>
<!-- TRUST BAR -->
<div class="trustbar"><div class="container trustbar-inner">
  <span class="trustbar-title"><?php echo esc_html(ph_opt('ph_trust_title', 'راهکار تخصصی کسب‌وکارهای فروشگاهی')); ?></span>
  <?php $ph_tr = ph_pairs(ph_opt('ph_trust_items', '')); if (!$ph_tr) $ph_tr = [['users', 'مشاوره تخصصی'], ['shield', 'ضمانت اصالت کالا'], ['headset', 'پشتیبانی فنی'], ['wrench', 'نصب و راه‌اندازی'], ['truck', 'ارسال سریع'], ['award', 'خدمات پس از فروش']]; foreach ($ph_tr as [$ti, $tt]) echo '<span class="trust-item"><span class="ic"><span data-icon="' . esc_attr($ti ?: 'check') . '"></span></span>' . esc_html($tt) . '</span>'; ?>
</div></div>
<!-- CATEGORIES -->
<section class="section"><div class="container">
  <div class="section-head center reveal"><div><span class="eyebrow"><span class="dot"></span><?php echo esc_html(ph_sec('cats', 'eye', 'دسته‌بندی محصولات')); ?></span><h2 class="section-title"><?php echo esc_html(ph_sec('cats', 'title', 'هر چیزی که برای مدیریت فروشگاه نیاز دارید')); ?></h2><p class="section-sub"><?php echo esc_html(ph_sec('cats', 'sub', 'از نرم‌افزار تا سخت‌افزار؛ یک تأمین‌کننده مطمئن برای همه نیازهای فروشگاهی شما')); ?></p></div></div>
  <div class="cat-grid">
    <a class="cat-card reveal" href="#software"><span class="cat-ic"><span data-icon="chart"></span></span><div><h3>نرم‌افزارهای حسابداری</h3><p>برای مدیریت فروش، خرید، انبار، مشتریان و امور مالی</p></div><span class="go"><span data-icon="arrowLeft"></span></span></a>
    <?php $ph_terms = get_terms(['taxonomy' => 'ph_cat', 'hide_empty' => false, 'number' => 4]); ?>
    <?php if ($ph_terms && !is_wp_error($ph_terms)) : foreach ($ph_terms as $pt) : ?>
    <a class="cat-card reveal" href="<?php echo esc_url(ph_url('product-cat')); ?><?php echo esc_attr($pt->slug); ?>"><span class="cat-ic"><span data-icon="<?php echo esc_attr(ph_cat_icon($pt->slug)); ?>"></span></span><div><h3><?php echo esc_html($pt->name); ?></h3><p><?php echo esc_html($pt->description ?: 'تجهیزات تخصصی فروشگاهی'); ?></p></div><span class="go"><span data-icon="arrowLeft"></span></span></a>
    <?php endforeach; endif; ?>
    <a class="cat-card reveal" href="<?php echo esc_url(ph_url('hardware')); ?>"><span class="cat-ic"><span data-icon="archive"></span></span><div><h3>تجهیزات فروشگاهی</h3><p>کشوی پول، نمایشگر مشتری و تجهیزات جانبی</p></div><span class="go"><span data-icon="arrowLeft"></span></span></a>
  </div>
</div></section>
<!-- SOFTWARE (SLIDER) -->
<section class="section soft-sec" id="software"><div class="container">
  <div class="section-head reveal"><div><span class="eyebrow"><span class="dot"></span><?php echo esc_html(ph_sec('soft', 'eye', 'نرم‌افزارهای حسابداری')); ?></span><h2 class="section-title"><?php echo esc_html(ph_sec('soft', 'title', 'نرم‌افزار حسابداری مناسب کسب‌وکار شما')); ?></h2><p class="section-sub"><?php echo esc_html(ph_sec('soft', 'sub', 'راهکارهای تخصصی برای اصناف مختلف؛ با دمو، آموزش و پشتیبانی واقعی')); ?></p></div><a class="link-more" style="color:var(--accent-bright)" href="<?php echo esc_url(ph_url('compare')); ?>">مقایسه نرم‌افزارها <span data-icon="arrowLeft"></span></a></div>
  <?php ph_slider_open('softGrid', 'soft-grid', 'ph-slider--dark'); ?>
</div></section>
<!-- WIZARD -->
<section class="section" id="wizard"><div class="container">
  <div class="section-head center reveal"><div><span class="eyebrow"><span class="dot"></span><?php echo esc_html(ph_sec('wiz', 'eye', 'راهنمای هوشمند انتخاب')); ?></span><h2 class="section-title"><?php echo esc_html(ph_sec('wiz', 'title', 'کدام نرم‌افزار برای من مناسب است؟')); ?></h2><p class="section-sub"><?php echo esc_html(ph_sec('wiz', 'sub', 'به ۴ سؤال کوتاه پاسخ دهید تا مناسب‌ترین نرم‌افزار را پیشنهاد دهیم')); ?></p></div></div>
  <div class="wizard reveal" id="wizard">
    <div class="wizard-side"><h3><?php echo esc_html(ph_opt('ph_wiz_title', 'پیشنهادگر نرم‌افزار')); ?></h3><p><?php echo esc_html(ph_opt('ph_wiz_sub', 'بر اساس نوع کسب‌وکار، تعداد کاربران و نیازهای شما، بهترین گزینه را معرفی می‌کنیم.')); ?></p>
      <div class="wsteps"><?php $ph_ws = ph_lines(ph_opt('ph_wiz_steps', '')); if (count($ph_ws) < 4) $ph_ws = ['نوع کسب‌وکار', 'تعداد کاربران', 'حجم فروش', 'انبارداری']; $ph_fad = ['۱', '۲', '۳', '۴']; foreach (array_slice($ph_ws, 0, 4) as $wi => $wt) echo '<div class="wstep' . ($wi === 0 ? ' active' : '') . '"><span class="n">' . $ph_fad[$wi] . '</span>' . esc_html($wt) . '</div>'; ?></div></div>
    <div class="wizard-body">
      <div class="wpane active" data-wpane="0"><h4><?php echo esc_html(ph_opt('ph_wiz_q1t', 'نوع کسب‌وکار شما چیست؟')); ?></h4><p><?php echo esc_html(ph_opt('ph_wiz_q1s', 'یکی از گزینه‌ها را انتخاب کنید')); ?></p>
        <div class="pick-grid"><?php $ph_wz = get_posts(['post_type' => 'ph_solution', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']); if ($ph_wz) foreach ($ph_wz as $wz) echo '<button class="pick" data-pick="biz" data-val="' . esc_attr($wz->post_name) . '"><span data-icon="' . esc_attr(get_post_meta($wz->ID, '_ph_icon', true) ?: 'store') . '"></span>' . esc_html(get_the_title($wz)) . '</button>'; ?><button class="pick" data-pick="biz" data-val="other"><span data-icon="grid"></span>سایر</button></div></div>
      <div class="wpane" data-wpane="1"><h4><?php echo esc_html(ph_opt('ph_wiz_q2t', 'چند کاربر همزمان نیاز دارید؟')); ?></h4><p><?php echo esc_html(ph_opt('ph_wiz_q2s', 'تعداد صندوق و کاربران سیستم')); ?></p>
        <div class="pick-row"><button class="pick-chip" data-pick="users" data-val="1">۱ کاربر</button><button class="pick-chip" data-pick="users" data-val="2-3">۲ تا ۳ کاربر</button><button class="pick-chip" data-pick="users" data-val="4-10">۴ تا ۱۰ کاربر</button><button class="pick-chip" data-pick="users" data-val="10+">بیش از ۱۰ کاربر</button></div></div>
      <div class="wpane" data-wpane="2"><h4><?php echo esc_html(ph_opt('ph_wiz_q3t', 'حجم فروش روزانه شما چقدر است؟')); ?></h4><p><?php echo esc_html(ph_opt('ph_wiz_q3s', 'میانگین تعداد فاکتور در روز')); ?></p>
        <div class="pick-row"><button class="pick-chip" data-pick="volume" data-val="low">کم (تا ۵۰ فاکتور)</button><button class="pick-chip" data-pick="volume" data-val="mid">متوسط (۵۰ تا ۲۰۰)</button><button class="pick-chip" data-pick="volume" data-val="high">زیاد (بیش از ۲۰۰)</button></div></div>
      <div class="wpane" data-wpane="3"><h4><?php echo esc_html(ph_opt('ph_wiz_q4t', 'آیا به انبارداری نیاز دارید؟')); ?></h4><p><?php echo esc_html(ph_opt('ph_wiz_q4s', 'مدیریت موجودی، چندانبار و انبارگردانی')); ?></p>
        <div class="pick-row"><button class="pick-chip" data-pick="warehouse" data-val="yes">بله، انبار دارم</button><button class="pick-chip" data-pick="warehouse" data-val="multi">بله، چند انبار / چند شعبه</button><button class="pick-chip" data-pick="warehouse" data-val="no">خیر</button></div></div>
      <div class="wpane" data-wpane="4"><div class="wizard-result"><div class="ok"><span data-icon="check"></span></div><h4><?php echo esc_html(ph_opt('ph_wiz_done_t', 'پیشنهاد ما برای شما آماده شد')); ?></h4><p style="font-size:13.5px;color:var(--muted)"><?php echo esc_html(ph_opt('ph_wiz_done_s', 'بر اساس پاسخ‌های شما، این نرم‌افزار بهترین گزینه است:')); ?></p><div class="rec-box" id="wRecBox"></div><div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap"><button class="btn btn-accent" data-consult><span data-icon="headset"></span>دریافت مشاوره و دمو</button><button class="btn btn-ghost" id="wReset">شروع مجدد</button></div></div></div>
      <div class="wizard-nav"><button class="btn btn-ghost" id="wBack">مرحله قبل</button><button class="btn btn-primary" id="wNext">ادامه</button></div>
    </div>
  </div>
</div></section>
<!-- HARDWARE -->
<section class="section section-soft"><div class="container">
  <div class="section-head reveal"><div><span class="eyebrow"><span class="dot"></span><?php echo esc_html(ph_sec('hw', 'eye', 'تجهیزات فروشگاهی')); ?></span><h2 class="section-title"><?php echo esc_html(ph_sec('hw', 'title', 'تجهیزات حرفه‌ای برای فروش سریع‌تر')); ?></h2><p class="section-sub"><?php echo esc_html(ph_sec('hw', 'sub', 'تجهیزات اصلی با ضمانت شرکتی، تست‌شده و سازگار با نرم‌افزار شما')); ?></p></div><a class="link-more" href="<?php echo esc_url(ph_url('shop')); ?>">مشاهده همه محصولات <span data-icon="arrowLeft"></span></a></div>
  <div class="p-grid c4" id="hwRail"></div>
</div></section>
<!-- READY SYSTEMS -->
<section class="section"><div class="container ready-grid">
  <div class="ready-visual reveal"><img src="<?php echo PH_URI; ?>/assets/img/img-ready-system.jpg" alt="سیستم آماده فروشگاهی" loading="lazy"><?php $ph_rm = ph_ready_min(); if ($ph_rm) : ?><div class="ready-price-tag"><span><?php echo esc_html(ph_opt('ph_ready_price_label', 'شروع قیمت سیستم‌های آماده')); ?></span><b class="num"><?php echo esc_html(strtr(number_format($ph_rm), '0123456789,', '۰۱۲۳۴۵۶۷۸۹٬')); ?> تومان</b></div><?php endif; ?></div>
  <div class="reveal"><span class="eyebrow amber"><span class="dot"></span><?php echo esc_html(ph_sec('ready', 'eye', 'سیستم‌های آماده')); ?></span><h2 class="section-title"><?php echo esc_html(ph_sec('ready', 'title', 'سیستم فروشگاهی آماده؛ فقط وصل کنید و شروع کنید')); ?></h2><p class="section-sub"><?php echo esc_html(ph_sec('ready', 'sub', 'یک پکیج کامل، تست‌شده و آماده‌به‌کار؛ بدون دردسر سازگاری و نصب')); ?></p>
    <ul class="ready-list"><?php $ph_rl = ph_pairs(ph_opt('ph_ready_list', '')); if (!$ph_rl) $ph_rl = [['cpu', 'کیس یا Mini PC'], ['monitor', 'مانیتور لمسی'], ['scan', 'بارکدخوان'], ['printer', 'فیش پرینتر'], ['archive', 'کشوی پول'], ['chart', 'نرم‌افزار حسابداری']]; foreach ($ph_rl as [$ri, $rt]) echo '<li><span data-icon="' . esc_attr($ri ?: 'check') . '"></span>' . esc_html($rt) . '</li>'; ?></ul>
    <div style="display:flex;gap:12px;flex-wrap:wrap"><a class="btn btn-primary btn-lg" href="<?php echo esc_url(ph_url('shop')); ?>?pcat=ready-systems">مشاهده سیستم‌های آماده</a><button class="btn btn-outline btn-lg" data-consult>مشاوره خرید سیستم</button></div></div>
</div></section>
<?php $ph_sols = get_posts(['post_type' => 'ph_solution', 'numberposts' => 8, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']); if ($ph_sols) : ?>
<!-- SOLUTIONS -->
<section class="section section-soft" id="solutions"><div class="container">
  <div class="section-head center reveal"><div><span class="eyebrow"><span class="dot"></span><?php echo esc_html(ph_sec('sol', 'eye', 'راهکار صنفی')); ?></span><h2 class="section-title"><?php echo esc_html(ph_sec('sol', 'title', 'راهکار مناسب هر کسب‌وکار')); ?></h2><p class="section-sub"><?php echo esc_html(ph_sec('sol', 'sub', 'برای صنف خود، ترکیب آماده نرم‌افزار و تجهیزات را ببینید')); ?></p></div></div>
  <div class="biz-grid">
    <?php foreach ($ph_sols as $sol) : $simg = get_the_post_thumbnail_url($sol, 'large'); ?>
    <a class="biz-card reveal<?php echo $simg ? '' : ' icon-only'; ?>" href="<?php echo esc_url(ph_url('solution')); ?>?biz=<?php echo esc_attr($sol->post_name); ?>"><?php if ($simg) : ?><img src="<?php echo esc_url($simg); ?>" alt="<?php echo esc_attr(get_the_title($sol)); ?>" loading="lazy"><?php endif; ?><span class="b-ic"><span data-icon="<?php echo esc_attr(get_post_meta($sol->ID, '_ph_icon', true) ?: 'store'); ?>"></span></span><div><h3><?php echo esc_html(get_the_title($sol)); ?></h3><p><?php echo esc_html(get_post_meta($sol->ID, '_ph_sub', true) ?: get_the_excerpt($sol)); ?></p></div><span class="arr"><span data-icon="arrowLeft"></span></span></a>
    <?php endforeach; ?>
  </div>
</div></section>
<?php endif; ?>
<?php $ph_feats = get_posts(['post_type' => 'ph_feature', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']); if ($ph_feats) : ?>
<!-- WHY -->
<section class="section"><div class="container">
  <div class="section-head center reveal"><div><span class="eyebrow"><span class="dot"></span><?php echo esc_html(ph_sec('why', 'eye', 'مزیت رقابتی')); ?></span><h2 class="section-title"><?php echo esc_html(ph_sec('why', 'title', 'چرا پژواک حساب؟')); ?></h2><p class="section-sub"><?php echo esc_html(ph_sec('why', 'sub', 'ما فقط فروشنده نیستیم؛ شریک راه‌اندازی و رشد فروشگاه شما هستیم')); ?></p></div></div>
  <div class="why-grid">
    <?php foreach ($ph_feats as $ft) : ?>
    <div class="why-card reveal"><span class="ic"><span data-icon="<?php echo esc_attr(get_post_meta($ft->ID, '_ph_icon', true) ?: 'checkCircle'); ?>"></span></span><h3><?php echo esc_html(get_the_title($ft)); ?></h3><p><?php echo esc_html($ft->post_content); ?></p></div>
    <?php endforeach; ?>
  </div>
</div></section>
<?php endif; ?>
<?php $ph_brands = get_posts(['post_type' => 'ph_brand', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']); if ($ph_brands) : ?>
<!-- BRANDS -->
<section class="section" style="padding-top:0"><div class="container">
  <div class="section-head center reveal"><div><h2 class="section-title" style="font-size:1.3rem"><?php echo esc_html(ph_sec('brands', 'title', 'همکاران نرم‌افزاری و سخت‌افزاری ما')); ?></h2></div></div>
  <div class="brand-wall reveal">
    <?php foreach ($ph_brands as $br) : ?>
    <span class="brand-logo"><?php echo esc_html(get_the_title($br)); ?><small><?php echo esc_html(get_post_meta($br->ID, '_ph_latin', true)); ?></small></span>
    <?php endforeach; ?>
  </div>
</div></section>
<?php endif; ?>
<!-- COMPARE -->
<section class="section" style="padding-top:0"><div class="container">
  <div class="compare-band reveal"><div><h2><?php echo esc_html(ph_sec('cmp', 'title', 'محصولات را با هم مقایسه کنید')); ?></h2><p><?php echo esc_html(ph_sec('cmp', 'sub', 'دو یا سه محصول را انتخاب کنید و مشخصات فنی، قیمت و امکانات را کنار هم ببینید.')); ?></p></div>
  <div><div class="compare-picks"><select id="cmp1" aria-label="محصول اول"></select><select id="cmp2" aria-label="محصول دوم"></select><select id="cmp3" aria-label="محصول سوم"></select></div><button class="btn btn-accent" id="cmpGo" style="margin-top:14px"><span data-icon="compare"></span>مقایسه حالا</button></div></div>
</div></section>
<?php $ph_testis = get_posts(['post_type' => 'ph_testimonial', 'numberposts' => 6, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']); if ($ph_testis) : ?>
<!-- TESTIMONIALS -->
<section class="section section-soft"><div class="container">
  <div class="section-head center reveal"><div><span class="eyebrow"><span class="dot"></span><?php echo esc_html(ph_sec('testi', 'eye', 'نظر مشتریان')); ?></span><h2 class="section-title"><?php echo esc_html(ph_sec('testi', 'title', 'کسب‌وکارهایی که به پژواک حساب اعتماد کرده‌اند')); ?></h2></div></div>
  <div class="testi-grid">
    <?php foreach ($ph_testis as $ts) : $tr = (int) get_post_meta($ts->ID, '_ph_rating', true) ?: 5; ?>
    <div class="testi reveal"><div class="q">"</div><p><?php echo esc_html($ts->post_content); ?></p><div class="testi-who"><span class="testi-ava"><?php echo esc_html(mb_substr(get_the_title($ts), 0, 1)); ?></span><div><b><?php echo esc_html(get_the_title($ts)); ?></b><span><?php echo esc_html(trim(get_post_meta($ts->ID, '_ph_business', true) . ' · ' . get_post_meta($ts->ID, '_ph_city', true), ' ·')); ?></span></div><span class="badge badge-accent num"><?php echo esc_html(strtr((string) $tr, '12345', '۱۲۳۴۵')); ?> از ۵</span></div></div>
    <?php endforeach; ?>
  </div>
</div></section>
<?php endif; ?>
<!-- BLOG -->
<section class="section"><div class="container">
  <div class="section-head reveal"><div><span class="eyebrow"><span class="dot"></span><?php echo esc_html(ph_sec('blog', 'eye', 'مجله پژواک حساب')); ?></span><h2 class="section-title"><?php echo esc_html(ph_sec('blog', 'title', 'آموزش و راهنمای خرید')); ?></h2><p class="section-sub"><?php echo esc_html(ph_sec('blog', 'sub', 'مطالب کاربردی برای انتخاب درست نرم‌افزار و تجهیزات')); ?></p></div><a class="link-more" href="<?php echo esc_url(ph_url('blog')); ?>">همه مقالات <span data-icon="arrowLeft"></span></a></div>
  <div class="post-grid" id="postRail"></div>
</div></section>
<!-- FINAL CTA -->
<section class="section" style="padding-top:0"><div class="container">
  <div class="final-cta reveal"><h2><?php echo esc_html(ph_opt('ph_cta_title', 'برای کسب‌وکار شما چه راهکاری مناسب است؟')); ?></h2><p><?php echo esc_html(ph_opt('ph_cta_sub', 'اگر برای انتخاب نرم‌افزار یا تجهیزات فروشگاهی مطمئن نیستید، کارشناسان پژواک حساب آماده راهنمایی شما هستند.')); ?></p>
  <div class="row"><button class="btn btn-accent btn-lg" data-consult><span data-icon="headset"></span>دریافت مشاوره رایگان</button><a class="btn btn-outline-white btn-lg" href="<?php echo esc_url(ph_url('contact')); ?>"><span data-icon="phone"></span>تماس با کارشناسان</a></div>
  <div class="final-contact"><a href="tel:<?php echo esc_attr(ph_phone_tel()); ?>"><span data-icon="phone"></span><span class="num"><?php echo esc_html(ph_phone()); ?></span></a><a href="<?php echo esc_url(ph_url('contact')); ?>"><span data-icon="pin"></span><?php echo esc_html(ph_opt('ph_address_short', 'اردبیل، میدان مادر')); ?></a></div></div>
</div></section>
</main>
<?php get_footer();

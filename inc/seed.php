<?php
/**
 * Demo content seeder: pages, products, software, posts, menu.
 * Runs on theme activation + manual button in admin notice.
 */
defined('ABSPATH') || exit;

function ph_seed_data() {
    static $d = null;
    if ($d === null) {
        $f = PH_DIR . '/inc/seed-data.json';
        $d = file_exists($f) ? json_decode(file_get_contents($f), true) : [];
    }
    return $d ?: [];
}

function ph_attach_theme_image($rel) {
    $found = get_posts(['post_type' => 'attachment', 'meta_key' => '_ph_seed_file', 'meta_value' => $rel, 'numberposts' => 1, 'post_status' => 'any']);
    if ($found) return $found[0]->ID;
    $src = PH_DIR . '/' . ltrim($rel, '/');
    if (!file_exists($src)) return 0;
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $up = wp_upload_dir();
    $filename = wp_unique_filename($up['path'], basename($src));
    if (!copy($src, $up['path'] . '/' . $filename)) return 0;
    $id = wp_insert_attachment(['post_mime_type' => 'image/jpeg', 'post_title' => sanitize_file_name($filename), 'post_status' => 'inherit'], $up['path'] . '/' . $filename);
    if (!$id) return 0;
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $up['path'] . '/' . $filename));
    update_post_meta($id, '_ph_seed_file', $rel);
    return $id;
}

function ph_by_slug($post_type, $slug) {
    $q = get_posts(['post_type' => $post_type, 'name' => $slug, 'numberposts' => 1, 'post_status' => 'any']);
    return $q ? $q[0] : null;
}

function ph_post_body($excerpt) {
    return $excerpt . "\n\n<h3>۱. نیازسنجی دقیق قبل از خرید</h3>\n<p>اولین قدم، شناخت دقیق نیاز کسب‌وکار شماست: حجم فروش روزانه، تعداد کاربران، نیاز به انبارداری و اتصال به تجهیزات. انتخابی که بدون نیازسنجی انجام شود، معمولاً یا پرهزینه است یا ناکارآمد.</p>\n<h3>۲. معیارهای مهم انتخاب</h3>\n<ul>\n<li>سرعت و پایداری در ساعات اوج فروش</li>\n<li>سازگاری با تجهیزات فروشگاهی موجود</li>\n<li>گزارش‌های مدیریتی کاربردی و لحظه‌ای</li>\n<li>آموزش، پشتیبانی و خدمات پس از فروش واقعی</li>\n</ul>\n<h3>۳. اشتباهات رایج خریداران</h3>\n<p>خرید بر اساس قیمتِ صرف، نادیده گرفتن هزینه‌های پنهان مثل آموزش و پشتیبانی، و عدم تست عملیاتی قبل از خرید، سه اشتباه پرتکرار است. پیشنهاد ما همیشه تست دمو و مشاوره با کارشناس است.</p>\n<h3>جمع‌بندی</h3>\n<p>اگر برای انتخاب گزینه مناسب مطمئن نیستید، کارشناسان پژواک حساب آماده‌اند رایگان راهنمایی‌تان کنند.</p>";
}

function ph_seed_all() {
    $d = ph_seed_data();
    if (!$d) return;
    /* pages */
    $pages = [
        'shop' => ['فروشگاه', 'template-shop.php'], 'compare' => ['مقایسه', 'template-compare.php'],
        'cart' => ['سبد خرید', 'template-cart.php'], 'checkout' => ['تسویه حساب', 'template-checkout.php'],
        'account' => ['حساب کاربری', 'template-account.php'], 'services' => ['خدمات', 'template-services.php'],
        'about' => ['درباره ما', 'template-about.php'], 'contact' => ['تماس با ما', 'template-contact.php'],
        'blog' => ['مجله', 'template-blog.php'], 'faq' => ['سوالات متداول', 'template-faq.php'],
        'hardware' => ['تجهیزات فروشگاهی', 'template-hardware.php'], 'solution' => ['راهکارها', 'template-solution.php'],
    ];
    foreach ($pages as $slug => [$title, $tpl]) {
        if (ph_by_slug('page', $slug)) continue;
        $id = wp_insert_post(['post_type' => 'page', 'post_title' => $title, 'post_name' => $slug, 'post_status' => 'publish', 'post_content' => '']);
        if ($id && !is_wp_error($id)) update_post_meta($id, '_wp_page_template', $tpl);
    }
    /* page subtitles (editable via page excerpt) */
    $subs = [
        'shop' => 'تجهیزات اصلی با ضمانت شرکتی، تست سازگاری با نرم‌افزار شما و پشتیبانی واقعی',
        'compare' => 'تا ۳ محصول یا نرم‌افزار را کنار هم ببینید و بهترین تصمیم را بگیرید',
        'account' => 'سفارش‌ها، علاقه‌مندی‌ها و اطلاعات خود را مدیریت کنید',
        'services' => 'از انتخاب تا بهره‌برداری و پشتیبانی بلندمدت؛ یک همراه واقعی برای کسب‌وکار شما',
        'about' => 'Pejvak Hesab — شریک تخصصی کسب‌وکارهای فروشگاهی در مسیر هوشمندسازی',
        'contact' => 'برای مشاوره، خرید و پشتیبانی در کنار شما هستیم',
        'blog' => 'آموزش، راهنمای خرید و مقایسه تخصصی نرم‌افزار و تجهیزات فروشگاهی',
        'faq' => 'پاسخ سریع به پرتکرارترین سؤالات مشتریان',
        'hardware' => 'از صندوق تا بارکدخوان؛ تجهیزات تست‌شده، سازگار با نرم‌افزار شما و با گارانتی شرکتی',
    ];
    foreach ($subs as $slug => $sub) {
        $pg = ph_by_slug('page', $slug);
        if ($pg && !$pg->post_excerpt) wp_update_post(['ID' => $pg->ID, 'post_excerpt' => $sub]);
    }
    /* product categories */
    foreach (($d['cats'] ?? []) as $c) {
        if (($c['id'] ?? '') === 'software') continue;
        $ex = term_exists($c['id'], 'ph_cat');
        if (!$ex) $ex = wp_insert_term($c['name'], 'ph_cat', ['slug' => $c['id'], 'description' => $c['desc'] ?? '']);
        if (!is_wp_error($ex)) {
            $tid = is_array($ex) ? $ex['term_id'] : $ex;
            if (!get_term_meta($tid, '_ph_icon', true) && !empty($c['icon'])) update_term_meta($tid, '_ph_icon', $c['icon']);
        }
    }
    /* products */
    foreach (($d['products'] ?? []) as $pr) {
        if (ph_by_slug('ph_product', $pr['id'])) continue;
        $id = wp_insert_post(['post_type' => 'ph_product', 'post_title' => $pr['name'], 'post_name' => $pr['id'], 'post_status' => 'publish', 'post_content' => $pr['short'], 'post_excerpt' => $pr['short'], 'comment_status' => 'open']);
        if (!$id || is_wp_error($id)) continue;
        wp_set_object_terms($id, $pr['cat'], 'ph_cat');
        update_post_meta($id, '_ph_price', $pr['price']);
        update_post_meta($id, '_ph_old', $pr['old'] ?? '');
        update_post_meta($id, '_ph_brand', $pr['brand']);
        update_post_meta($id, '_ph_code', $pr['code']);
        update_post_meta($id, '_ph_badge', $pr['badge'] ?? '');
        update_post_meta($id, '_ph_rating', $pr['rating']);
        update_post_meta($id, '_ph_reviews', $pr['reviews']);
        update_post_meta($id, '_ph_stock', $pr['stock']);
        update_post_meta($id, '_ph_short', $pr['short']);
        update_post_meta($id, '_ph_features', implode("\n", $pr['features'] ?? []));
        update_post_meta($id, '_ph_specs', implode("\n", array_map(function ($s) { return $s[0] . '|' . $s[1]; }, $pr['specs'] ?? [])));
        update_post_meta($id, '_ph_img', $pr['img']);
        $att = ph_attach_theme_image($pr['img']);
        if ($att) set_post_thumbnail($id, $att);
    }
    /* software */
    $sw_icons = ['chart', 'shirt', 'briefcase', 'coffee'];
    $swi = 0;
    foreach (($d['software'] ?? []) as $sw) {
        if (ph_by_slug('ph_software', $sw['id'])) continue;
        $id = wp_insert_post(['post_type' => 'ph_software', 'post_title' => $sw['name'], 'post_name' => $sw['id'], 'post_status' => 'publish', 'post_content' => $sw['desc'], 'post_excerpt' => $sw['short']]);
        if (!$id || is_wp_error($id)) continue;
        $pair = function ($arr) { return implode("\n", array_map(function ($x) { return $x[0] . '|' . $x[1]; }, $arr)); };
        update_post_meta($id, '_ph_tag', $sw['tag']);
        update_post_meta($id, '_ph_mono', $sw['mono']);
        update_post_meta($id, '_ph_color', $sw['color']);
        update_post_meta($id, '_ph_icon', $sw_icons[$swi++ % 4]);
        update_post_meta($id, '_ph_badge', $sw['badge'] ?? '');
        update_post_meta($id, '_ph_rating', $sw['rating']);
        update_post_meta($id, '_ph_reviews', $sw['reviews']);
        update_post_meta($id, '_ph_price', $sw['price']);
        update_post_meta($id, '_ph_old', $sw['old'] ?? '');
        update_post_meta($id, '_ph_short', $sw['short']);
        update_post_meta($id, '_ph_forwho', implode("\n", $sw['forWho'] ?? []));
        update_post_meta($id, '_ph_features', $pair($sw['features'] ?? []));
        update_post_meta($id, '_ph_modules', implode("\n", $sw['modules'] ?? []));
        update_post_meta($id, '_ph_req_min', $pair($sw['req'][0] ?? []));
        update_post_meta($id, '_ph_req_srv', $pair($sw['req'][1] ?? []));
        update_post_meta($id, '_ph_plans', json_encode($sw['plans'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
    /* blog categories + posts */
    $cat_ids = [];
    foreach (array_unique(array_column($d['posts'] ?? [], 'cat')) as $cn) {
        $t = term_exists($cn, 'category');
        if (!$t) $t = wp_insert_term($cn, 'category');
        if (!is_wp_error($t)) $cat_ids[$cn] = is_array($t) ? $t['term_id'] : $t;
    }
    foreach (($d['posts'] ?? []) as $po) {
        if (ph_by_slug('post', $po['id'])) continue;
        $id = wp_insert_post([
            'post_title' => $po['title'], 'post_name' => $po['id'], 'post_status' => 'publish',
            'post_content' => ph_post_body($po['excerpt']), 'post_excerpt' => $po['excerpt'],
            'post_category' => isset($cat_ids[$po['cat']]) ? [$cat_ids[$po['cat']]] : [],
        ]);
        if (!$id || is_wp_error($id)) continue;
        update_post_meta($id, '_ph_date', $po['date']);
        update_post_meta($id, '_ph_read', $po['read']);
        $att = ph_attach_theme_image($po['img']);
        if ($att) set_post_thumbnail($id, $att);
    }
    /* primary menu (created unassigned; default mega nav renders until user assigns it) */
    if (!wp_get_nav_menu_object('منوی اصلی')) {
        $menu_id = wp_create_nav_menu('منوی اصلی');
        if ($menu_id && !is_wp_error($menu_id)) {
            $items = [['خانه', home_url('/')], ['فروشگاه', ph_url('shop')], ['تجهیزات فروشگاهی', ph_url('hardware')], ['خدمات', ph_url('services')], ['مجله', ph_url('blog')], ['درباره ما', ph_url('about')], ['تماس با ما', ph_url('contact')]];
            foreach ($items as [$t, $u]) {
                wp_update_nav_menu_item($menu_id, 0, ['menu-item-title' => $t, 'menu-item-url' => $u, 'menu-item-status' => 'publish', 'menu-item-type' => 'custom']);
            }
        }
    }
    /* FAQ categories + faqs */
    $faq_cats = [];
    foreach (array_unique(array_column($d['faqs'] ?? [], 'c')) as $cn) {
        $t = term_exists($cn, 'ph_faq_cat');
        if (!$t) $t = wp_insert_term($cn, 'ph_faq_cat');
        if (!is_wp_error($t)) $faq_cats[$cn] = is_array($t) ? $t['term_id'] : $t;
    }
    $fi = 0;
    foreach (($d['faqs'] ?? []) as $fq) {
        $slug = 'faq-' . (++$fi);
        if (ph_by_slug('ph_faq', $slug)) continue;
        $id = wp_insert_post(['post_type' => 'ph_faq', 'post_title' => $fq['q'], 'post_name' => $slug, 'post_status' => 'publish', 'post_content' => $fq['a'], 'menu_order' => $fi]);
        if ($id && !is_wp_error($id) && isset($faq_cats[$fq['c']])) wp_set_object_terms($id, [(int) $faq_cats[$fq['c']]], 'ph_faq_cat');
    }
    /* testimonials */
    $testis = [
        ['مدیریت سوپرمارکت', 'از مشاوره اول تا نصب و آموزش، همه‌چیز حرفه‌ای بود. صندوق فروشگاه ما حالا دو برابر سریع‌تر کار می‌کند.', 'سوپرمارکت', 'تهران', 5],
        ['مدیر فروشگاه پوشاک', 'برای سه شعبه پوشاک از زعفران استفاده می‌کنیم. همگام‌سازی موجودی و گزارش شعب واقعاً کار ما را راحت کرد.', 'پوشاک زنجیره‌ای', 'اصفهان', 5],
        ['مدیر کافه', 'سیستم آماده کافه را گرفتیم؛ همان روز اول بدون هیچ مشکلی شروع به کار کردیم. پشتیبانی‌شان عالی است.', 'کافه', 'شیراز', 5],
    ];
    $ti = 0;
    foreach ($testis as [$t, $c, $b, $city, $r]) {
        $slug = 'testimonial-' . (++$ti);
        if (ph_by_slug('ph_testimonial', $slug)) continue;
        $id = wp_insert_post(['post_type' => 'ph_testimonial', 'post_title' => $t, 'post_name' => $slug, 'post_status' => 'publish', 'post_content' => $c, 'menu_order' => $ti]);
        if ($id && !is_wp_error($id)) { update_post_meta($id, '_ph_business', $b); update_post_meta($id, '_ph_city', $city); update_post_meta($id, '_ph_rating', $r); }
    }
    /* brands */
    $bi = 0;
    foreach (($d['brands'] ?? []) as $br) {
        $slug = 'brand-' . (++$bi);
        if (ph_by_slug('ph_brand', $slug)) continue;
        $id = wp_insert_post(['post_type' => 'ph_brand', 'post_title' => $br['name'], 'post_name' => $slug, 'post_status' => 'publish', 'menu_order' => $bi]);
        if ($id && !is_wp_error($id)) update_post_meta($id, '_ph_latin', $br['en']);
    }
    /* solutions */
    $solutions = [
        ['supermarket', 'سوپرمارکت و هایپرمارکت', 'store', 'assets/img/img-supermarket.jpg', 'صندوق سریع + انبار + ترازو', 'سرعت در صندوق، مدیریت هزاران کالا و کنترل انبار؛ راهکار کامل سوپرمارکت‌ها.', 'صدور فاکتور زیر ۱۰ ثانیه|اتصال ترازو و بارکدخوان|انبار چندگانه و کسری خودکار|باشگاه مشتریان و تخفیف', 'baran', 'pos-aio-p15|receipt-xp80|scan-hh120'],
        ['clothing', 'فروشگاه پوشاک', 'shirt', 'assets/img/img-supermarket.jpg', 'سایز/رنگ + شعب + تخفیف', 'ماتریس سایز و رنگ، مدیریت شعب و کمپین‌های فصلی برای بوتیک‌ها و برندها.', 'تعریف سایز/رنگ|مدیریت چندشعبه|تخفیف فصلی و کوپن|تسویه صندوق چندشیفته', 'zafaran', 'pos-dual-d17|label-dt420|scan-wl200'],
        ['mobile', 'فروشگاه موبایل', 'mobile', 'assets/img/img-pos-allinone.jpg', 'سریال + اقساط + گارانتی', 'سریال‌دار کردن کالا، گارانتی، اقساط و خدمات پس از فروش تخصصی موبایل.', 'ردیابی سریال کالا|فروش اقساطی|مدیریت گارانتی و مرجوعی|چاپ فاکتور رسمی', 'baran', 'pos-aio-p15|label-dt420|drawer-m5'],
        ['home', 'لوازم خانگی', 'home', 'assets/img/img-ready-system.jpg', 'اقساط + ارسال + نصب', 'فروش حجیم، ارسال و نصب، اقساط و حسابداری دقیق برای فروشگاه‌های بزرگ.', 'فاکتور و پیش‌فاکتور|مدیریت ارسال و نصب|فروش اقساطی و چک|انبار حجیم', 'pejvak', 'ready-market-pro|receipt-xp80|display-vfd220'],
        ['restaurant', 'رستوران و کافه', 'coffee', 'assets/img/img-restaurant.jpg', 'میز + آشپزخانه + پیک', 'مدیریت میز، آشپزخانه، پیک و منوی دیجیتال؛ همه‌چیز برای فروش بیشتر.', 'چیدمان سالن و میز|ارسال سفارش به آشپزخانه|منوی دیجیتال QR|مدیریت پیک', 'pos-suite', 'ready-cafe-lite|receipt-bt58|drawer-m5'],
        ['chain', 'فروشگاه زنجیره‌ای', 'layers', 'assets/img/img-supermarket.jpg', 'مدیریت متمرکز شعب', 'مدیریت متمرکز شعب، قیمت‌گذاری یکپارچه و داشبورد مقایسه عملکرد.', 'همگام‌سازی لحظه‌ای شعب|قیمت‌گذاری متمرکز|انتقال بین شعب|داشبورد مدیریتی', 'zafaran', 'pos-dual-d17|scan-wl200|label-ind300'],
        ['wholesale', 'عمده‌فروشی', 'package', 'assets/img/img-ready-system.jpg', 'اعتبار + چک + خزانه', 'فاکتور حجیم، اعتبار مشتریان، چک و خزانه‌داری برای عمده‌فروشان.', 'فاکتور سریع حجیم|سقف اعتبار مشتری|مدیریت چک و تسویه|انبار چندگانه', 'pejvak', 'ready-market-pro|scan-wl200|label-ind300'],
        ['service', 'خدماتی', 'briefcase', 'assets/img/img-restaurant.jpg', 'قرارداد + دریافتی + سود', 'صدور فاکتور خدماتی، قراردادها، دریافتی‌ها و حسابداری تمیز.', 'فاکتور خدماتی و قرارداد|یادآوری سررسید|مدیریت دریافتی|گزارش سود خدمات', 'pejvak', 'pos-aio-p15|receipt-xp80|drawer-m5'],
    ];
    $si = 0;
    foreach ($solutions as [$key, $t, $icon, $img, $sub, $desc, $needs, $soft, $prods]) {
        if (ph_by_slug('ph_solution', $key)) continue;
        $id = wp_insert_post(['post_type' => 'ph_solution', 'post_title' => $t, 'post_name' => $key, 'post_status' => 'publish', 'post_content' => $desc, 'post_excerpt' => $sub, 'menu_order' => ++$si]);
        if (!$id || is_wp_error($id)) continue;
        update_post_meta($id, '_ph_icon', $icon);
        update_post_meta($id, '_ph_sub', $sub);
        update_post_meta($id, '_ph_needs', str_replace('|', "\n", $needs));
        update_post_meta($id, '_ph_soft', $soft);
        update_post_meta($id, '_ph_prods', str_replace('|', "\n", $prods));
        $att = ph_attach_theme_image($img);
        if ($att) set_post_thumbnail($id, $att);
    }
    /* services */
    $services = [
        ['مشاوره قبل از خرید', 'users', 'نیازسنجی رایگان کسب‌وکار شما و پیشنهاد بهینه‌ترین ترکیب نرم‌افزار و تجهیزات؛ صادانه و بدون هزینه.', 'درخواست مشاوره', 'consult', ''],
        ['تأمین و آماده‌سازی', 'checkCircle', 'تأمین کالای اصلی با ضمانت، پیکربندی، نصب نرم‌افزار و تست کامل سازگاری قبل از تحویل.', 'مشاهده فروشگاه', 'shop', ''],
        ['نصب و راه‌اندازی', 'wrench', 'نصب حضوری در تهران و شهرستان‌ها یا راه‌اندازی ریموت؛ ورود اطلاعات پایه و تحویل آماده‌به‌کار.', 'درخواست نصب', 'consult', 'نصب و راه‌اندازی'],
        ['آموزش تخصصی', 'book', 'آموزش کامل صندوقدار، انباردار و مدیر + ویدیوهای آموزشی و دفترچه راهنمای فارسی.', 'مطالب آموزشی', 'blog', ''],
        ['پشتیبانی فنی', 'headset', 'پشتیبانی تلفنی و ریموت توسط کارشناس فنی؛ رفع اشکال سریع در ساعات کاری و قراردادهای ویژه.', 'تماس با پشتیبانی', 'contact', ''],
        ['خدمات پس از فروش', 'shield', 'گارانتی شرکتی، تأمین قطعات، تعمیرات تخصصی و به‌روزرسانی نرم‌افزار در بلندمدت.', 'سوالات متداول', 'faq', ''],
    ];
    $vi = 0;
    foreach ($services as [$t, $icon, $desc, $bt, $btn, $subj]) {
        $slug = 'service-' . (++$vi);
        if (ph_by_slug('ph_service', $slug)) continue;
        $id = wp_insert_post(['post_type' => 'ph_service', 'post_title' => $t, 'post_name' => $slug, 'post_status' => 'publish', 'post_content' => $desc, 'menu_order' => $vi]);
        if (!$id || is_wp_error($id)) continue;
        update_post_meta($id, '_ph_icon', $icon);
        update_post_meta($id, '_ph_btn_text', $bt);
        update_post_meta($id, '_ph_btn', $btn);
        update_post_meta($id, '_ph_subject', $subj);
    }
    /* features */
    $features = [
        ['مشاوره قبل از خرید', 'users', 'نیازسنجی دقیق و پیشنهاد صادانه؛ حتی اگر به نفع ما نباشد.'],
        ['انتخاب دقیق تجهیزات', 'checkCircle', 'سازگاری کامل نرم‌افزار و سخت‌افزار، تست‌شده قبل از ارسال.'],
        ['نصب و راه‌اندازی', 'wrench', 'نصب در محل یا ریموت، ورود اطلاعات پایه و تحویل آماده‌به‌کار.'],
        ['آموزش کار با سیستم', 'book', 'آموزش کامل صندوقدار و مدیر + ویدیوهای آموزشی دائمی.'],
        ['پشتیبانی تخصصی', 'headset', 'پاسخگویی سریع تلفنی و ریموت توسط کارشناس فنی واقعی.'],
        ['خدمات پس از فروش', 'shield', 'گارانتی شرکتی، تأمین قطعات و همراهی بلندمدت.'],
    ];
    $fi2 = 0;
    foreach ($features as [$t, $icon, $desc]) {
        $slug = 'feature-' . (++$fi2);
        if (ph_by_slug('ph_feature', $slug)) continue;
        $id = wp_insert_post(['post_type' => 'ph_feature', 'post_title' => $t, 'post_name' => $slug, 'post_status' => 'publish', 'post_content' => $desc, 'menu_order' => $fi2]);
        if ($id && !is_wp_error($id)) update_post_meta($id, '_ph_icon', $icon);
    }
    delete_transient('ph_seed_cache');
}

add_action('after_switch_theme', function () { ph_seed_all(); update_option('ph_seed_ver', PH_SEED_VER); flush_rewrite_rules(); });
add_action('admin_init', function () { if (get_option('ph_seed_ver') !== PH_SEED_VER) { ph_seed_all(); update_option('ph_seed_ver', PH_SEED_VER); } });
add_action('admin_post_ph_seed', function () {
    if (!current_user_can('manage_options') || !check_admin_referer('ph_seed')) wp_die('دسترسی غیرمجاز');
    ph_seed_all();
    flush_rewrite_rules();
    wp_redirect(admin_url('edit.php?post_type=ph_product'));
    exit;
});
add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) return;
    if (get_posts(['post_type' => 'ph_product', 'numberposts' => 1])) return;
    $url = wp_nonce_url(admin_url('admin-post.php?action=ph_seed'), 'ph_seed');
    echo '<div class="notice notice-info"><p>قالب پژواک حساب فعال است. <a href="' . esc_url($url) . '"><strong>ایجاد محتوای اولیه</strong> (برگه‌ها، محصولات، نرم‌افزارها و مقالات)</a></p></div>';
});

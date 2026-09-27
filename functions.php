<?php
/**
 * Pejvak Hesab Theme — functions.php
 * Design & Development: OneCode | https://onecode.ir
 */
defined('ABSPATH') || exit;

define('PH_VER', '1.3.0');
define('PH_SEED_VER', '1.1');
define('PH_URI', get_template_directory_uri());
define('PH_DIR', get_template_directory());

require_once PH_DIR . '/inc/cpts.php';
require_once PH_DIR . '/inc/meta.php';
require_once PH_DIR . '/inc/customizer.php';
require_once PH_DIR . '/inc/settings-panel.php';
require_once PH_DIR . '/inc/seed.php';
require_once PH_DIR . '/inc/woocommerce.php';
require_once PH_DIR . '/inc/sms.php';
require_once PH_DIR . '/inc/zarinpal.php';
require_once PH_DIR . '/inc/ajax.php';
require_once PH_DIR . '/inc/admin.php';

/* ---------------- setup ---------------- */
add_action('after_setup_theme', function () {
    load_theme_textdomain('pejvak-hesab', PH_DIR . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', ['flex-width' => true, 'flex-height' => true, 'width' => 200, 'height' => 60]);
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
    add_image_size('ph-card', 640, 480, true);
    add_post_type_support('page', 'excerpt');
    add_image_size('ph-cover', 1200, 600, true);
    register_nav_menus([
        'primary' => 'منوی اصلی',
        'footer-shop' => 'فوتر: دسترسی سریع',
        'footer-soft' => 'فوتر: نرم‌افزارها',
    ]);
});

add_action('widgets_init', function () {
    $areas = ['footer-1' => 'فوتر ستون ۱ (معرفی)', 'footer-2' => 'فوتر ستون ۲', 'footer-3' => 'فوتر ستون ۳', 'footer-4' => 'فوتر ستون ۴ (خبرنامه)'];
    foreach ($areas as $id => $name) {
        register_sidebar(['id' => $id, 'name' => $name, 'before_widget' => '<div class="widget">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
    }
});

/* ---------------- contact options ---------------- */
function ph_opt($key, $default = '') { return get_theme_mod($key, $default); }
function ph_fa_date($date = null, $pattern = 'yyyy/MM/dd') {
    if ($date === null || $date === '' || $date === 'now') $ts = time();
    elseif (is_numeric($date)) $ts = (int) $date;
    else $ts = strtotime($date);
    if (!$ts) $ts = time();
    if (class_exists('IntlDateFormatter')) {
        try {
            $f = new IntlDateFormatter('fa_IR@calendar=persian', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, $pattern);
            $s = $f->format($ts);
            if ($s) return $s;
        } catch (Exception $e) {}
    }
    return date('Y/m/d', $ts);
}
function ph_phone() { return ph_opt('ph_phone_display', '۰۹۱۲ ۰۲۴ ۱۱۲۰'); }
function ph_phone_tel() { return ph_opt('ph_phone_tel', '+989120241120'); }
function ph_email() { return ph_opt('ph_email', 'info@pejvakhesab.ir'); }
function ph_address() { return ph_opt('ph_address', 'اردبیل، بزرگراه شهید سلیمانی، میدان مادر، دفتر مرکزی پژواک حساب'); }
function ph_hours() { return ph_opt('ph_hours', 'شنبه تا پنجشنبه ۹ تا ۱۸'); }

/* ---------------- page urls ---------------- */
function ph_url($key) {
    $map = [
        'shop' => 'shop', 'compare' => 'compare', 'cart' => 'cart', 'checkout' => 'checkout',
        'account' => 'account', 'services' => 'services', 'about' => 'about', 'contact' => 'contact',
        'blog' => 'blog', 'faq' => 'faq', 'hardware' => 'hardware', 'solution' => 'solution', 'category' => 'shop',
    ];
    $slug = $map[$key] ?? $key;
    $page = get_page_by_path($slug);
    $url = $page ? get_permalink($page) : home_url('/' . $slug . '/');
    return apply_filters('ph_page_url', $url, $key);
}

/* ---------------- JS data-page router ---------------- */
function ph_data_page() {
    if (is_front_page()) return 'home';
    if (is_singular('ph_product')) return 'product';
    if (is_singular('ph_software')) return 'software';
    if (is_post_type_archive('ph_product') || is_tax('ph_cat')) return 'shop';
    if (is_page()) {
        $map = [
            'template-shop.php' => 'shop', 'template-compare.php' => 'compare', 'template-cart.php' => 'cart',
            'template-checkout.php' => 'checkout', 'template-account.php' => 'account', 'template-contact.php' => 'contact',
            'template-faq.php' => 'faq', 'template-blog.php' => 'blog', 'template-hardware.php' => 'hardware',
            'template-solution.php' => 'solution', 'template-services.php' => 'services', 'template-about.php' => 'about',
        ];
        return $map[get_page_template_slug()] ?? '';
    }
    return '';
}

/* ---------------- meta parsers ---------------- */
function ph_lines($v) { return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) $v)))); }
function ph_pairs($v) {
    $out = [];
    foreach (ph_lines($v) as $line) {
        $p = explode('|', $line, 2);
        $out[] = [trim($p[0]), trim($p[1] ?? '')];
    }
    return $out;
}

/* ---------------- JS bridge: links (cached — ۳ کوئری سنگین را فقط هر ۱۲ ساعت اجرا می‌کند) ---------------- */
function ph_build_links() {
    $cached = get_transient('ph_links_cache');
    if (is_array($cached)) return $cached;
    $L = [
        'home' => home_url('/'), 'shop' => ph_url('shop'), 'compare' => ph_url('compare'),
        'cart' => ph_url('cart'), 'checkout' => ph_url('checkout'), 'account' => ph_url('account'),
        'services' => ph_url('services'), 'about' => ph_url('about'), 'contact' => ph_url('contact'),
        'blog' => ph_url('blog'), 'faq' => ph_url('faq'), 'hardware' => ph_url('hardware'),
        'solution' => ph_url('solution'), 'product' => [], 'software' => [], 'article' => [],
    ];
    $ids = get_posts(['post_type' => 'ph_product', 'numberposts' => -1, 'post_status' => 'publish', 'fields' => 'ids']);
    foreach ($ids as $id) $L['product'][get_post_field('post_name', $id)] = get_permalink($id);
    $ids = get_posts(['post_type' => 'ph_software', 'numberposts' => -1, 'post_status' => 'publish', 'fields' => 'ids']);
    foreach ($ids as $id) $L['software'][get_post_field('post_name', $id)] = get_permalink($id);
    $ids = get_posts(['post_type' => 'post', 'numberposts' => 60, 'post_status' => 'publish', 'fields' => 'ids']);
    foreach ($ids as $id) $L['article'][get_post_field('post_name', $id)] = get_permalink($id);
    return $L;
}

/* ---------------- JS bridge: catalog seed from WP content ---------------- */
function ph_read_time($post = null) {
    $post = $post ? get_post($post) : get_post();
    $words = $post ? str_word_count(strip_tags($post->post_content)) : 0;
    return max(1, (int) ceil($words / 200));
}
function ph_build_seed() {
    $cached = get_transient('ph_seed_cache');
    if (is_array($cached)) return $cached;
    $seed = ['site' => ['phone' => ph_phone(), 'phoneDir' => ph_phone_tel(), 'email' => ph_email(), 'address' => ph_address(), 'hours' => ph_hours()]];
    $prods = get_posts(['post_type' => 'ph_product', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']);
    if ($prods) {
        $seed['products'] = array_map(function ($p) {
            $img = get_the_post_thumbnail_url($p, 'large') ?: PH_URI . '/' . ltrim(get_post_meta($p->ID, '_ph_img', true) ?: 'assets/img/img-pos-allinone.jpg', '/');
            $terms = get_the_terms($p, 'ph_cat');
            return [
                'id' => $p->post_name, 'name' => get_the_title($p),
                'brand' => get_post_meta($p->ID, '_ph_brand', true) ?: 'پژواک حساب',
                'cat' => ($terms && !is_wp_error($terms)) ? $terms[0]->slug : 'pos-systems',
                'price' => (int) get_post_meta($p->ID, '_ph_price', true),
                'old' => (int) get_post_meta($p->ID, '_ph_old', true) ?: null,
                'rating' => (float) get_post_meta($p->ID, '_ph_rating', true) ?: 4.5,
                'reviews' => (int) get_post_meta($p->ID, '_ph_reviews', true),
                'badge' => get_post_meta($p->ID, '_ph_badge', true) ?: null,
                'stock' => get_post_meta($p->ID, '_ph_stock', true) ?: 'in',
                'code' => get_post_meta($p->ID, '_ph_code', true),
                'img' => $img, 'short' => get_post_meta($p->ID, '_ph_short', true) ?: get_the_excerpt($p),
                'features' => ph_lines(get_post_meta($p->ID, '_ph_features', true)),
                'specs' => ph_pairs(get_post_meta($p->ID, '_ph_specs', true)),
                'gallery' => ph_gallery_urls($p, $img),
            ];
        }, $prods);
    }
    $softs = get_posts(['post_type' => 'ph_software', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']);
    if ($softs) {
        $seed['software'] = array_map(function ($p) {
            return [
                'id' => $p->post_name, 'name' => get_the_title($p),
                'tag' => get_post_meta($p->ID, '_ph_tag', true),
                'mono' => get_post_meta($p->ID, '_ph_mono', true) ?: mb_substr(get_the_title($p), 0, 1),
                'color' => get_post_meta($p->ID, '_ph_color', true) ?: '#00798C',
                'badge' => get_post_meta($p->ID, '_ph_badge', true) ?: null,
                'rating' => (float) get_post_meta($p->ID, '_ph_rating', true) ?: 4.5,
                'reviews' => (int) get_post_meta($p->ID, '_ph_reviews', true),
                'price' => (int) get_post_meta($p->ID, '_ph_price', true),
                'old' => (int) get_post_meta($p->ID, '_ph_old', true) ?: null,
                'short' => get_post_meta($p->ID, '_ph_short', true) ?: get_the_excerpt($p),
                'desc' => $p->post_content ?: get_the_excerpt($p),
                'forWho' => ph_lines(get_post_meta($p->ID, '_ph_forwho', true)),
                'features' => ph_pairs(get_post_meta($p->ID, '_ph_features', true)),
                'modules' => ph_lines(get_post_meta($p->ID, '_ph_modules', true)),
                'req' => [ph_pairs(get_post_meta($p->ID, '_ph_req_min', true)), ph_pairs(get_post_meta($p->ID, '_ph_req_srv', true))],
                'plans' => json_decode(get_post_meta($p->ID, '_ph_plans', true), true) ?: [],
            ];
        }, $softs);
    }
    $posts = get_posts(['post_type' => 'post', 'numberposts' => 24, 'post_status' => 'publish']);
    if ($posts) {
        $fallbacks = ['hero-pos.jpg', 'img-pos-allinone.jpg', 'img-receipt-printer.jpg', 'img-label-printer.jpg', 'img-barcode-scanner.jpg', 'img-supermarket.jpg'];
        $seed['posts'] = array_map(function ($p, $i) use ($fallbacks) {
            $cats = get_the_category($p->ID);
            return [
                'id' => $p->post_name, 'title' => get_the_title($p),
                'cat' => $cats ? $cats[0]->name : 'مجله',
                'excerpt' => get_the_excerpt($p),
                'date' => get_post_meta($p->ID, '_ph_date', true) ?: ph_fa_date($p->post_date),
                'read' => ph_read_time($p),
                'img' => get_the_post_thumbnail_url($p, 'large') ?: PH_URI . '/assets/img/' . $fallbacks[$i % count($fallbacks)],
            ];
        }, $posts, array_keys($posts));
    }
    $fqs = get_posts(['post_type' => 'ph_faq', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']);
    if ($fqs) {
        $seed['faqs'] = array_map(function ($p) {
            $terms = get_the_terms($p, 'ph_faq_cat');
            return ['c' => ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'عمومی', 'q' => get_the_title($p), 'a' => $p->post_content];
        }, $fqs);
    }
    $sols = get_posts(['post_type' => 'ph_solution', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']);
    if ($sols) {
        $seed['solutions'] = [];
        foreach ($sols as $sp) {
            $seed['solutions'][$sp->post_name] = [
                'n' => get_the_title($sp),
                'i' => get_post_meta($sp->ID, '_ph_icon', true) ?: 'store',
                'img' => get_the_post_thumbnail_url($sp, 'large') ?: PH_URI . '/assets/img/img-supermarket.jpg',
                'd' => $sp->post_content,
                'needs' => ph_lines(get_post_meta($sp->ID, '_ph_needs', true)),
                'soft' => get_post_meta($sp->ID, '_ph_soft', true) ?: 'baran',
                'prods' => ph_lines(get_post_meta($sp->ID, '_ph_prods', true)),
            ];
        }
    }
    $pterms = get_terms(['taxonomy' => 'ph_cat', 'hide_empty' => false]);
    if ($pterms && !is_wp_error($pterms)) {
        $seed['cats'] = array_map(function ($t) { return ['id' => $t->slug, 'name' => $t->name, 'desc' => $t->description, 'icon' => ph_cat_icon($t->slug)]; }, $pterms);
        $seed['cats'][] = ['id' => 'software', 'name' => 'نرم‌افزارهای حسابداری', 'desc' => 'مدیریت فروش، انبار و امور مالی', 'icon' => 'chart'];
    }
    set_transient('ph_seed_cache', $seed, 12 * HOUR_IN_SECONDS);
    return $seed;
}
add_action('save_post', function () { delete_transient('ph_seed_cache'); delete_transient('ph_links_cache'); });
add_action('deleted_post', function () { delete_transient('ph_seed_cache'); delete_transient('ph_links_cache'); });
add_action('customize_save_after', function () { delete_transient('ph_seed_cache'); delete_transient('ph_links_cache'); });
/* تغییر دسته‌بندی‌ها هم کش seed را باطل می‌کند */
foreach (['created_term', 'edited_term', 'delete_term'] as $ph_term_hook) {
    add_action($ph_term_hook, function () { delete_transient('ph_seed_cache'); });
}

/* ---------------- assets ---------------- */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('ph-base', PH_URI . '/assets/css/base.css', [], PH_VER);
    wp_enqueue_style('ph-home', PH_URI . '/assets/css/home.css', ['ph-base'], PH_VER);
    wp_enqueue_style('ph-shop', PH_URI . '/assets/css/shop.css', ['ph-base'], PH_VER);
    wp_enqueue_style('ph-slider', PH_URI . '/assets/css/slider.css', ['ph-shop'], PH_VER);
    wp_enqueue_style('ph-style', get_stylesheet_uri(), ['ph-slider'], PH_VER);

    wp_enqueue_script('lucide', PH_URI . '/assets/js/lucide.min.js', [], '1.44.0', true);
    wp_enqueue_script('ph-data', PH_URI . '/assets/js/data.js', ['lucide'], PH_VER, true);
    wp_enqueue_script('ph-components', PH_URI . '/assets/js/components.js', ['ph-data'], PH_VER, true);
    wp_enqueue_script('ph-main', PH_URI . '/assets/js/main.js', ['ph-components'], PH_VER, true);
    wp_enqueue_script('ph-slider-boot', PH_URI . '/assets/js/ph-slider-boot.js', ['ph-main'], PH_VER, true);
    wp_enqueue_script('ph-slider', PH_URI . '/assets/js/ph-slider.js', ['ph-slider-boot'], PH_VER, true);

    wp_add_inline_script('ph-data', 'window.PH_WP=' . wp_json_encode([
        'themeUrl' => PH_URI, 'home' => home_url('/'),
        'urls' => ['shop' => ph_url('shop')],
        'links' => ph_build_links(),
        'pid' => (is_singular() ? get_the_ID() : 0),
        'ajax' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ph_ajax'),
        'rest' => esc_url_raw(rest_url()),
        'ship' => ph_ship_config(),
        'coupon' => ['code' => ph_opt('ph_coupon_code', 'PEJVAK10'), 'off' => (int) ph_opt('ph_coupon_off', 10)],
        'warranty' => ph_opt('ph_warranty', '۱۲ تا ۱۸ ماه + پشتیبانی'),
        'solSteps' => ph_pairs(ph_opt('ph_sol_steps', '')),
        'swDash' => ph_pairs(ph_opt('ph_sw_dash', '')),
        'searchTags' => ph_lines(ph_opt('ph_search_tags', '')),
        'user' => ['logged' => is_user_logged_in(), 'name' => is_user_logged_in() ? wp_get_current_user()->display_name : '', 'logout' => wp_logout_url(home_url('/'))],
    ]) . ';', 'before');

    /* slider config */
    $ph_clamp = function ($v, $d) { $v = (int) $v; return ($v >= 1 && $v <= 6) ? $v : $d; };
    wp_add_inline_script('ph-slider-boot', 'window.PH_SLIDER_CFG=' . wp_json_encode([
        'perView' => [
            'desktop' => $ph_clamp(ph_opt('ph_slider_per_desktop', 3), 3),
            'tablet' => $ph_clamp(ph_opt('ph_slider_per_tablet', 2), 2),
            'mobile' => $ph_clamp(ph_opt('ph_slider_per_mobile', 1), 1),
        ],
        'gap' => 20,
        'autoplay' => ph_opt('ph_slider_autoplay', '') === '1',
        'autoplayDelay' => max(2000, (int) ph_opt('ph_slider_delay', 5000)),
        'loop' => ph_opt('ph_slider_loop', '') === '1',
        'dots' => ph_opt('ph_slider_dots', '1') === '1',
        'arrows' => true,
    ]) . ';', 'before');

    wp_add_inline_script('ph-data', 'window.PH_SEED=' . wp_json_encode(ph_build_seed()) . ';', 'before');
    if (is_singular(['ph_product', 'ph_software'])) {
        wp_add_inline_script('ph-data', 'window.PH_SINGLE_ID=' . wp_json_encode(get_post_field('post_name')) . ';', 'before');
    }
    if (is_tax('ph_cat')) {
        wp_add_inline_script('ph-data', 'window.PH_PRECAT=' . wp_json_encode(get_queried_object()->slug) . ';', 'before');
    }
    if (is_singular() && comments_open() && get_option('thread_comments')) wp_enqueue_script('comment-reply');
});

/* ---------------- SEO basics ---------------- */
add_action('wp_head', function () {
    if (is_singular()) {
        $d = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 25);
    } else {
        $d = get_bloginfo('description') ?: 'پژواک حساب؛ نرم‌افزارهای حسابداری و تجهیزات فروشگاهی';
    }
    echo '<meta name="description" content="' . esc_attr($d) . '">' . "\n";
}, 5);

/* ---------------- content tweaks ---------------- */
add_filter('excerpt_length', function () { return 25; });
add_filter('excerpt_more', function () { return '…'; });

/* ---------------- nav fallbacks ---------------- */
function ph_fb_shop() {
    $items = [['درباره ما', 'about'], ['تماس با ما', 'contact'], ['فروشگاه', 'shop'], ['تجهیزات فروشگاهی', 'hardware'], ['خدمات', 'services'], ['مجله', 'blog'], ['سوالات متداول', 'faq']];
    echo '<ul>';
    foreach ($items as [$t, $k]) echo '<li><a href="' . esc_url(ph_url($k)) . '">' . esc_html($t) . '</a></li>';
    echo '</ul>';
}
function ph_fb_soft() {
    $softs = get_posts(['post_type' => 'ph_software', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC']);
    echo '<ul>';
    if ($softs) foreach ($softs as $s) echo '<li><a href="' . esc_url(get_permalink($s)) . '">' . esc_html(get_the_title($s)) . '</a></li>';
    echo '<li><a href="' . esc_url(ph_url('compare')) . '">مقایسه نرم‌افزارها</a></li>';
    echo '<li><a href="' . esc_url(home_url('/#wizard')) . '">راهنمای انتخاب نرم‌افزار</a></li></ul>';
}

/* ---------------- category icon map ---------------- */
function ph_cat_icon($slug) {
    $t = get_term_by('slug', $slug, 'ph_cat');
    if ($t && ($ic = get_term_meta($t->term_id, '_ph_icon', true))) return $ic;
    $map = ['pos-systems' => 'monitor', 'ready-systems' => 'package', 'receipt-printers' => 'printer', 'label-printers' => 'tag', 'scanners' => 'scan', 'cash-drawers' => 'archive', 'customer-displays' => 'display', 'software' => 'chart'];
    return $map[$slug] ?? 'grid';
}

/* ---------------- shop helpers ---------------- */
function ph_ship_config() {
    return ['std' => (int) ph_opt('ph_ship_std_p', 350000), 'exp' => (int) ph_opt('ph_ship_exp_p', 550000), 'freeOver' => (int) ph_opt('ph_ship_freeover', 50000000)];
}
function ph_gallery_urls($p, $featured) {
    $out = [$featured];
    foreach (ph_lines(get_post_meta($p->ID, '_ph_gallery', true)) as $line) {
        if (ctype_digit($line)) { $u = wp_get_attachment_url((int) $line); if ($u) $out[] = $u; }
        elseif (preg_match('#^(https?://|/)#', $line)) $out[] = $line;
    }
    return array_values(array_unique($out));
}
function ph_fa_year() {
    if (class_exists('IntlDateFormatter')) {
        try { return (new IntlDateFormatter('fa_IR@calendar=persian', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'yyyy'))->format(time()); } catch (Exception $e) {}
    }
    return (string) ((int) date('Y') - 621);
}
function ph_legal_url($key, $slug) {
    $u = ph_opt($key, '');
    if ($u) return $u;
    $p = get_page_by_path($slug);
    return $p ? get_permalink($p) : '';
}
function ph_ready_min() {
    $t = get_term_by('slug', 'ready-systems', 'ph_cat');
    if (!$t) return 0;
    $ids = get_posts(['post_type' => 'ph_product', 'numberposts' => -1, 'post_status' => 'publish', 'tax_query' => [['taxonomy' => 'ph_cat', 'field' => 'term_id', 'terms' => $t->term_id]], 'fields' => 'ids']);
    $min = 0;
    foreach ($ids as $id) { $pr = (int) get_post_meta($id, '_ph_price', true); if ($pr > 0 && (!$min || $pr < $min)) $min = $pr; }
    return $min;
}
function ph_sec($id, $part, $default = '') { return ph_opt("ph_sec_{$id}_{$part}", $default); }

/* ---------------- slider helpers ---------------- */
/**
 * Prints the standard slider shell.
 * @param string $track_id    The id attribute for the track element (e.g. "softGrid").
 * @param string $base_class  Existing grid class (e.g. "soft-grid c3").
 * @param string $variant     Optional modifier class (e.g. "ph-slider--dark").
 */
function ph_slider_open($track_id, $base_class = '', $variant = '') {
    $cls = trim('ph-slider ' . $variant);
    $base = trim($base_class);
    echo '<div class="' . esc_attr($cls) . '" data-slider>';
    echo '<button type="button" class="ph-slider__nav ph-slider__nav--prev" data-slider-prev aria-label="اسلاید قبلی"><span data-icon="chevRight"></span></button>';
    echo '<div class="ph-slider__viewport"><div class="' . esc_attr($base) . ' ph-slider__track" id="' . esc_attr($track_id) . '" data-slider-track></div></div>';
    echo '<button type="button" class="ph-slider__nav ph-slider__nav--next" data-slider-next aria-label="اسلاید بعدی"><span data-icon="chevLeft"></span></button>';
    echo '<div class="ph-slider__dots" data-slider-dots></div>';
    echo '</div>';
}
function ph_slider_close() { /* Nothing extra — wrapper closes in ph_slider_open */ }

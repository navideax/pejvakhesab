<?php
/**
 * Meta boxes for all Pejvak post types + ph_cat icon term meta.
 */
defined('ABSPATH') || exit;

add_action('add_meta_boxes', function () {
    add_meta_box('ph_pmeta', 'مشخصات محصول', 'ph_pmeta_cb', 'ph_product', 'normal', 'high');
    add_meta_box('ph_smeta', 'مشخصات نرم‌افزار', 'ph_smeta_cb', 'ph_software', 'normal', 'high');
    add_meta_box('ph_solmeta', 'تنظیمات راهکار', 'ph_solmeta_cb', 'ph_solution', 'normal', 'high');
    add_meta_box('ph_tmeta', 'مشخصات نظر', 'ph_tmeta_cb', 'ph_testimonial', 'side', 'high');
    add_meta_box('ph_bmeta', 'نام لاتین برند', 'ph_bmeta_cb', 'ph_brand', 'side', 'high');
    add_meta_box('ph_svcmeta', 'تنظیمات خدمت', 'ph_svcmeta_cb', 'ph_service', 'side', 'high');
    add_meta_box('ph_fmeta', 'آیکون مزیت', 'ph_fmeta_cb', 'ph_feature', 'side', 'high');
    add_meta_box('ph_ometa', 'جزئیات سفارش', 'ph_ometa_cb', 'ph_order', 'normal', 'high');
    add_meta_box('ph_lmeta', 'اطلاعات ثبت‌شده', 'ph_lmeta_cb', ['ph_lead', 'ph_message'], 'side', 'high');
});

function ph_mfield($id, $label, $value, $type = 'text', $desc = '') {
    echo '<p><label for="' . esc_attr($id) . '"><strong>' . esc_html($label) . '</strong></label><br>';
    if ($type === 'textarea') {
        echo '<textarea id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" rows="4" style="width:100%;direction:rtl">' . esc_textarea($value) . '</textarea>';
    } elseif ($type === 'select-stock') {
        echo '<select id="' . esc_attr($id) . '" name="' . esc_attr($id) . '">';
        foreach (['in' => 'موجود', 'low' => 'موجودی محدود', 'out' => 'ناموجود'] as $v => $t) {
            echo '<option value="' . $v . '"' . selected($value, $v, false) . '>' . $t . '</option>';
        }
        echo '</select>';
    } elseif ($type === 'select-rating') {
        echo '<select id="' . esc_attr($id) . '" name="' . esc_attr($id) . '">';
        foreach ([5 => '۵ — عالی', 4 => '۴ — خوب', 3 => '۳ — متوسط', 2 => '۲ — ضعیف', 1 => '۱ — بد'] as $v => $t) {
            echo '<option value="' . $v . '"' . selected((int) $value ?: 5, $v, false) . '>' . $t . '</option>';
        }
        echo '</select>';
    } else {
        echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($value) . '" style="width:100%">';
    }
    if ($desc) echo '<br><span style="color:#666;font-size:12px">' . esc_html($desc) . '</span>';
    echo '</p>';
}

function ph_icon_field($id, $value) {
    $icons = ['store', 'shirt', 'mobile', 'home', 'coffee', 'layers', 'package', 'briefcase', 'monitor', 'printer', 'tag', 'scan', 'archive', 'display', 'chart', 'grid', 'users', 'shield', 'headset', 'wrench', 'truck', 'award', 'book', 'checkCircle', 'compare', 'spark', 'message', 'cpu'];
    echo '<p><label for="' . esc_attr($id) . '"><strong>آیکون</strong></label><br>';
    echo '<input list="ph_icons" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($value) . '" style="width:100%">';
    echo '<datalist id="ph_icons">';
    foreach ($icons as $ic) echo '<option value="' . $ic . '">';
    echo '</datalist><br><span style="color:#666;font-size:12px">نام آیکون، مثل: store</span></p>';
}

function ph_pmeta_cb($post) {
    wp_nonce_field('ph_meta', 'ph_meta_nonce');
    $g = function ($k) use ($post) { return get_post_meta($post->ID, $k, true); };
    ph_mfield('_ph_price', 'قیمت (تومان، عدد)', $g('_ph_price'), 'number');
    ph_mfield('_ph_old', 'قیمت قبلی (برای تخفیف، عدد)', $g('_ph_old'), 'number');
    ph_mfield('_ph_brand', 'برند', $g('_ph_brand'));
    ph_mfield('_ph_code', 'کد محصول', $g('_ph_code'));
    ph_mfield('_ph_badge', 'نشان (مثل: پرفروش‌ترین)', $g('_ph_badge'));
    ph_mfield('_ph_rating', 'امتیاز (از ۵)', $g('_ph_rating'));
    ph_mfield('_ph_reviews', 'تعداد دیدگاه', $g('_ph_reviews'), 'number');
    ph_mfield('_ph_stock', 'وضعیت موجودی', $g('_ph_stock') ?: 'in', 'select-stock');
    ph_mfield('_ph_short', 'توضیح کوتاه', $g('_ph_short'), 'textarea');
    ph_mfield('_ph_features', 'ویژگی‌ها (هر خط یکی)', $g('_ph_features'), 'textarea');
    ph_mfield('_ph_specs', 'مشخصات فنی (هر خط: عنوان|مقدار)', $g('_ph_specs'), 'textarea');
    ph_mfield('_ph_gallery', 'تصاویر گالری (هر خط یک آدرس تصویر یا شناسه رسانه)', $g('_ph_gallery'), 'textarea', 'علاوه بر تصویر شاخص نمایش داده می‌شود.');
    ph_mfield('_ph_img', 'تصویر جایگزین (مسیر داخل قالب)', $g('_ph_img'), 'text', 'مثل: assets/img/img-pos-allinone.jpg — اگر تصویر شاخص نگذاشتید استفاده می‌شود.');
}

function ph_smeta_cb($post) {
    wp_nonce_field('ph_meta', 'ph_meta_nonce');
    $g = function ($k) use ($post) { return get_post_meta($post->ID, $k, true); };
    ph_mfield('_ph_tag', 'زیرعنوان (مثل: ویژه پوشاک)', $g('_ph_tag'));
    ph_mfield('_ph_mono', 'حرف نماد (تک‌حرف)', $g('_ph_mono'));
    ph_mfield('_ph_color', 'رنگ برند (HEX)', $g('_ph_color') ?: '#00798C');
    ph_icon_field('_ph_icon', $g('_ph_icon'));
    ph_mfield('_ph_badge', 'نشان', $g('_ph_badge'));
    ph_mfield('_ph_rating', 'امتیاز (از ۵)', $g('_ph_rating'));
    ph_mfield('_ph_reviews', 'تعداد دیدگاه', $g('_ph_reviews'), 'number');
    ph_mfield('_ph_price', 'قیمت پایه (تومان، عدد)', $g('_ph_price'), 'number');
    ph_mfield('_ph_old', 'قیمت قبلی (عدد)', $g('_ph_old'), 'number');
    ph_mfield('_ph_short', 'معرفی کوتاه', $g('_ph_short'), 'textarea');
    ph_mfield('_ph_forwho', 'مناسب چه کسب‌وکارهایی (هر خط یکی)', $g('_ph_forwho'), 'textarea');
    ph_mfield('_ph_features', 'ویژگی‌ها (هر خط: عنوان|توضیح)', $g('_ph_features'), 'textarea');
    ph_mfield('_ph_modules', 'ماژول‌ها (هر خط یکی)', $g('_ph_modules'), 'textarea');
    ph_mfield('_ph_req_min', 'حداقل سیستم (هر خط: عنوان|مقدار)', $g('_ph_req_min'), 'textarea');
    ph_mfield('_ph_req_srv', 'پیشنهاد سرور (هر خط: عنوان|مقدار)', $g('_ph_req_srv'), 'textarea');
    ph_mfield('_ph_plans', 'پلن‌ها (JSON)', $g('_ph_plans'), 'textarea', 'آرایه‌ای از {n:نام، d:توضیح، p:قیمت، f:[امکانات]}');
}

function ph_solmeta_cb($post) {
    wp_nonce_field('ph_meta', 'ph_meta_nonce');
    $g = function ($k) use ($post) { return get_post_meta($post->ID, $k, true); };
    ph_icon_field('_ph_icon', $g('_ph_icon'));
    ph_mfield('_ph_sub', 'زیرعنوان کارت (مثل: صندوق سریع + انبار + ترازو)', $g('_ph_sub'));
    ph_mfield('_ph_needs', 'نیازهای صنف (هر خط یکی)', $g('_ph_needs'), 'textarea');
    $softs = get_posts(['post_type' => 'ph_software', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC']);
    echo '<p><label for="_ph_soft"><strong>نرم‌افزار پیشنهادی این صنف</strong></label><br><select id="_ph_soft" name="_ph_soft" style="width:100%">';
    foreach ($softs as $s) echo '<option value="' . esc_attr($s->post_name) . '"' . selected($g('_ph_soft'), $s->post_name, false) . '>' . esc_html(get_the_title($s)) . '</option>';
    echo '</select></p>';
    $prods = get_posts(['post_type' => 'ph_product', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC']);
    $slugs = array_map(function ($p) { return $p->post_name; }, $prods);
    ph_mfield('_ph_prods', 'شناسه محصولات پیشنهادی (هر خط یکی)', $g('_ph_prods'), 'textarea', 'شناسه‌های موجود: ' . implode('، ', $slugs));
}

function ph_tmeta_cb($post) {
    wp_nonce_field('ph_meta', 'ph_meta_nonce');
    $g = function ($k) use ($post) { return get_post_meta($post->ID, $k, true); };
    ph_mfield('_ph_business', 'کسب‌وکار', $g('_ph_business'));
    ph_mfield('_ph_city', 'شهر', $g('_ph_city'));
    ph_mfield('_ph_rating', 'امتیاز', $g('_ph_rating'), 'select-rating');
}

function ph_bmeta_cb($post) {
    wp_nonce_field('ph_meta', 'ph_meta_nonce');
    ph_mfield('_ph_latin', 'نام لاتین', get_post_meta($post->ID, '_ph_latin', true));
}

function ph_svcmeta_cb($post) {
    wp_nonce_field('ph_meta', 'ph_meta_nonce');
    $g = function ($k) use ($post) { return get_post_meta($post->ID, $k, true); };
    ph_icon_field('_ph_icon', $g('_ph_icon'));
    ph_mfield('_ph_btn_text', 'متن دکمه', $g('_ph_btn_text') ?: 'درخواست مشاوره');
    ph_mfield('_ph_btn', 'مقصد دکمه', $g('_ph_btn') ?: 'consult', 'text', 'بنویسید consult برای فرم مشاوره، shop/blog/contact/faq برای برگه‌ها، یا آدرس کامل (https://...)');
    ph_mfield('_ph_subject', 'موضوع مشاوره (اگر مقصد consult است)', $g('_ph_subject'));
}

function ph_fmeta_cb($post) {
    wp_nonce_field('ph_meta', 'ph_meta_nonce');
    ph_icon_field('_ph_icon', get_post_meta($post->ID, '_ph_icon', true));
}

function ph_ometa_cb($post) {
    wp_nonce_field('ph_meta', 'ph_meta_nonce');
    $g = function ($k) use ($post) { return get_post_meta($post->ID, $k, true); };
    $items = json_decode($g('_ph_items'), true) ?: [];
    echo '<table class="widefat striped" style="max-width:720px"><tbody>';
    $rows = [
        'کد سفارش' => $g('_ph_code'), 'مشتری' => $g('_ph_customer'),
        'موبایل' => $g('_ph_phone'), 'استان' => $g('_ph_province'), 'شهر' => $g('_ph_city'),
        'آدرس' => $g('_ph_addr'), 'کد پستی' => $g('_ph_postcode'),
        'روش ارسال' => $g('_ph_ship'), 'روش پرداخت' => $g('_ph_pay'), 'توضیحات' => $g('_ph_note'),
    ];
    foreach ($rows as $k => $v) echo '<tr><td style="width:140px"><strong>' . esc_html($k) . '</strong></td><td>' . esc_html($v ?: '—') . '</td></tr>';
    echo '<tr><td><strong>اقلام</strong></td><td>';
    if ($items) {
        echo '<ul style="margin:0">';
        foreach ($items as $it) {
            $p = get_page_by_path(sanitize_key($it['id'] ?? ''), OBJECT, 'ph_product');
            $nm = $p ? get_the_title($p) : ($it['id'] ?? '');
            echo '<li>' . esc_html($nm) . ' × ' . esc_html($it['q'] ?? 1) . ' — ' . esc_html(number_format_i18n((int) ($it['price'] ?? 0))) . ' تومان</li>';
        }
        echo '</ul>';
    } else echo '—';
    echo '</td></tr>';
    echo '<tr><td><strong>جمع کل</strong></td><td><strong>' . esc_html(number_format_i18n((int) $g('_ph_total'))) . ' تومان</strong></td></tr>';
    echo '</tbody></table>';
    $st = $g('_ph_status') ?: 'در حال پردازش';
    echo '<p style="margin-top:12px"><label for="_ph_status"><strong>وضعیت سفارش</strong></label> ';
    echo '<select id="_ph_status" name="_ph_status">';
    foreach (['در انتظار پرداخت', 'پرداخت شد', 'در حال پردازش', 'تأیید شد', 'ارسال شد', 'تحویل شد', 'لغو شد'] as $o) echo '<option' . selected($st, $o, false) . '>' . esc_html($o) . '</option>';
    echo '</select></p>';
}

function ph_lmeta_cb($post) {
    $g = function ($k) use ($post) { return get_post_meta($post->ID, $k, true); };
    echo '<p><strong>موبایل:</strong><br><span dir="ltr">' . esc_html($g('_ph_phone') ?: '—') . '</span></p>';
    echo '<p><strong>موضوع:</strong><br>' . esc_html($g('_ph_subject') ?: '—') . '</p>';
}

add_action('save_post', function ($post_id) {
    if (!isset($_POST['ph_meta_nonce']) || !wp_verify_nonce($_POST['ph_meta_nonce'], 'ph_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    $map = [
        'ph_product' => ['_ph_price', '_ph_old', '_ph_brand', '_ph_code', '_ph_badge', '_ph_rating', '_ph_reviews', '_ph_stock', '_ph_short', '_ph_features', '_ph_specs', '_ph_gallery', '_ph_img'],
        'ph_software' => ['_ph_tag', '_ph_mono', '_ph_color', '_ph_icon', '_ph_badge', '_ph_rating', '_ph_reviews', '_ph_price', '_ph_old', '_ph_short', '_ph_forwho', '_ph_features', '_ph_modules', '_ph_req_min', '_ph_req_srv', '_ph_plans'],
        'ph_solution' => ['_ph_icon', '_ph_sub', '_ph_needs', '_ph_soft', '_ph_prods'],
        'ph_testimonial' => ['_ph_business', '_ph_city', '_ph_rating'],
        'ph_brand' => ['_ph_latin'],
        'ph_service' => ['_ph_icon', '_ph_btn_text', '_ph_btn', '_ph_subject'],
        'ph_feature' => ['_ph_icon'],
        'ph_order' => ['_ph_status'],
    ];
    $keys = $map[get_post_type($post_id)] ?? [];
    $areas = ['_ph_features', '_ph_specs', '_ph_short', '_ph_forwho', '_ph_modules', '_ph_req_min', '_ph_req_srv', '_ph_plans', '_ph_gallery', '_ph_needs', '_ph_prods'];
    foreach ($keys as $k) {
        if (!isset($_POST[$k])) continue;
        $v = in_array($k, $areas, true) ? sanitize_textarea_field(wp_unslash($_POST[$k])) : sanitize_text_field(wp_unslash($_POST[$k]));
        update_post_meta($post_id, $k, $v);
    }
});

/* ---------- ph_cat icon term meta ---------- */
function ph_cat_icon_field($term = null) {
    $v = $term ? get_term_meta($term->term_id, '_ph_icon', true) : '';
    echo '<div class="form-field"><label for="ph-term-icon">آیکون دسته</label>';
    echo '<input type="text" id="ph-term-icon" name="ph_term_icon" value="' . esc_attr($v) . '">';
    echo '<p class="description">مثل: monitor، printer، tag، scan، archive، display، package</p></div>';
}
add_action('ph_cat_add_form_fields', 'ph_cat_icon_field');
add_action('ph_cat_edit_form_fields', 'ph_cat_icon_field');
add_action('created_ph_cat', function ($id) { if (isset($_POST['ph_term_icon'])) update_term_meta($id, '_ph_icon', sanitize_text_field($_POST['ph_term_icon'])); });
add_action('edited_ph_cat', function ($id) { if (isset($_POST['ph_term_icon'])) update_term_meta($id, '_ph_icon', sanitize_text_field($_POST['ph_term_icon'])); });

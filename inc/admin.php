<?php
/**
 * Pejvak Hesab — admin dashboard hub + list columns.
 */
defined('ABSPATH') || exit;

add_action('admin_menu', function () {
    add_menu_page('پژواک حساب', 'پژواک حساب', 'manage_options', 'ph_dashboard', 'ph_dashboard_cb', 'dashicons-store', 3);
});

function ph_count_of($pt) {
    $c = wp_count_posts($pt);
    return isset($c->publish) ? (int) $c->publish : 0;
}

function ph_dashboard_cb() {
    if (!current_user_can('manage_options')) return;
    $cards = [
        ['سفارش‌ها', ph_count_of('ph_order'), 'edit.php?post_type=ph_order', '#00798C'],
        ['درخواست‌های مشاوره', ph_count_of('ph_lead'), 'edit.php?post_type=ph_lead', '#F0B429'],
        ['پیام‌های تماس', ph_count_of('ph_message'), 'edit.php?post_type=ph_message', '#7C6FF0'],
        ['اعضای خبرنامه', ph_count_of('ph_subscriber'), 'edit.php?post_type=ph_subscriber', '#2FA36B'],
        ['دیدگاه‌های در انتظار تأیید', (int) wp_count_comments()->moderated, 'edit-comments.php?comment_status=moderated', '#D64545'],
        ['محصولات', ph_count_of('ph_product'), 'edit.php?post_type=ph_product', '#00798C'],
        ['نرم‌افزارها', ph_count_of('ph_software'), 'edit.php?post_type=ph_software', '#00798C'],
        ['راهکارهای صنفی', ph_count_of('ph_solution'), 'edit.php?post_type=ph_solution', '#00798C'],
        ['خدمات', ph_count_of('ph_service'), 'edit.php?post_type=ph_service', '#00798C'],
        ['مزیت‌ها', ph_count_of('ph_feature'), 'edit.php?post_type=ph_feature', '#00798C'],
        ['سوالات متداول', ph_count_of('ph_faq'), 'edit.php?post_type=ph_faq', '#00798C'],
        ['نظرات مشتریان', ph_count_of('ph_testimonial'), 'edit.php?post_type=ph_testimonial', '#00798C'],
        ['برندها', ph_count_of('ph_brand'), 'edit.php?post_type=ph_brand', '#00798C'],
        ['مقالات مجله', ph_count_of('post'), 'edit.php', '#00798C'],
    ];
    $seed_url = wp_nonce_url(admin_url('admin-post.php?action=ph_seed'), 'ph_seed');
    echo '<div class="wrap"><h1>پژواک حساب — نمای کلی</h1>';
    echo '<p>همه محتوای سایت از همین‌جا مدیریت می‌شود. برای ویرایش متن‌ها، تصاویر و تنظیمات فروشگاه به <a href="' . esc_url(admin_url('customize.php')) . '">سفارشی‌سازی قالب</a> بروید.</p>';
    echo '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin:16px 0">';
    foreach ($cards as [$t, $n, $u, $c]) {
        echo '<a href="' . esc_url(admin_url($u)) . '" style="background:#fff;border:1px solid #e2e8f0;border-top:4px solid ' . esc_attr($c) . ';border-radius:10px;padding:16px;text-decoration:none;display:block">';
        echo '<div style="font-size:28px;font-weight:800;color:#0f172a">' . esc_html(number_format_i18n($n)) . '</div>';
        echo '<div style="color:#475569;font-size:13px">' . esc_html($t) . '</div></a>';
    }
    echo '</div>';
    echo '<p><a class="button button-secondary" href="' . esc_url($seed_url) . '">ایجاد مجدد محتوای اولیه (فقط موارد ساخته‌نشده)</a> ';
    echo '<a class="button button-primary" href="' . esc_url(admin_url('customize.php')) . '">باز کردن پنل تنظیمات قالب</a></p></div>';
}

/* ---------- orders list columns ---------- */
add_filter('manage_ph_order_posts_columns', function ($cols) {
    return ['cb' => $cols['cb'], 'title' => 'سفارش', 'ph_total' => 'مبلغ', 'ph_status' => 'وضعیت', 'ph_track' => 'پیگیری مرسوله', 'date' => $cols['date']];
});
add_action('manage_ph_order_posts_custom_column', function ($col, $id) {
    if ($col === 'ph_total') echo esc_html(number_format_i18n((int) get_post_meta($id, '_ph_total', true))) . ' تومان';
    if ($col === 'ph_status') echo esc_html(get_post_meta($id, '_ph_status', true) ?: 'در حال پردازش');
    if ($col === 'ph_track') {
        $t = (string) get_post_meta($id, '_ph_tracking', true);
        if ($t !== '') echo '<span dir="ltr">' . esc_html($t) . '</span><br><small>' . esc_html(ph_carrier_label((string) get_post_meta($id, '_ph_carrier', true))) . '</small>';
        else echo '—';
    }
}, 10, 2);
add_filter('manage_ph_lead_posts_columns', function ($cols) {
    return ['cb' => $cols['cb'], 'title' => 'درخواست', 'ph_phone' => 'موبایل', 'date' => $cols['date']];
});
add_action('manage_ph_lead_posts_custom_column', function ($col, $id) {
    if ($col === 'ph_phone') echo '<span dir="ltr">' . esc_html(get_post_meta($id, '_ph_phone', true)) . '</span>';
}, 10, 2);

/* ---------- order tracking: carriers, helpers, metabox ---------- */
function ph_track_carriers() {
    return [
        'post'     => ['label' => 'پست جمهوری اسلامی ایران', 'url' => 'https://tracking.post.ir/?trackcode={CODE}'],
        'tipax'    => ['label' => 'تیپاکس', 'url' => 'https://tipaxco.com/tracking'],
        'chapar'   => ['label' => 'چاپار', 'url' => 'https://chapar.me/'],
        'snappbox' => ['label' => 'اسنپ‌باکس / پیک موتوری', 'url' => ''],
        'freight'  => ['label' => 'باربری', 'url' => ''],
        'other'    => ['label' => 'سایر', 'url' => ''],
    ];
}
function ph_carrier_label($key) {
    $c = ph_track_carriers();
    return isset($c[$key]) ? $c[$key]['label'] : $key;
}
/** Build the public tracking URL for an order ('' when nothing to track). */
function ph_tracking_url($order_id) {
    $code = trim((string) get_post_meta($order_id, '_ph_tracking', true));
    if ($code === '') return '';
    $custom = trim((string) get_post_meta($order_id, '_ph_track_url', true));
    if ($custom !== '') return str_replace('{CODE}', rawurlencode($code), $custom);
    $key = (string) get_post_meta($order_id, '_ph_carrier', true);
    $c = ph_track_carriers();
    $tpl = isset($c[$key]) ? $c[$key]['url'] : '';
    return $tpl !== '' ? str_replace('{CODE}', rawurlencode($code), $tpl) : '';
}

add_action('add_meta_boxes', function () {
    add_meta_box('ph_order_track', 'وضعیت سفارش و پیگیری مرسوله', 'ph_order_track_cb', 'ph_order', 'normal', 'high');
});

function ph_order_statuses() {
    /* فهرست واحد وضعیت‌ها — فقط از همین تابع استفاده شود تا واژگان سراسری یکدست بماند */
    return ['در انتظار پرداخت', 'پرداخت شد', 'در حال پردازش', 'تأیید شده', 'آماده ارسال', 'ارسال شده', 'تحویل شده', 'لغو شده'];
}

function ph_order_track_cb($post) {
    wp_nonce_field('ph_order_track', 'ph_track_nonce');
    $status  = get_post_meta($post->ID, '_ph_status', true) ?: 'در حال پردازش';
    $carrier = get_post_meta($post->ID, '_ph_carrier', true);
    $track   = get_post_meta($post->ID, '_ph_tracking', true);
    $turl    = get_post_meta($post->ID, '_ph_track_url', true);
    $phone   = get_post_meta($post->ID, '_ph_phone', true);
    $code    = get_post_meta($post->ID, '_ph_code', true);
    $statuses = ph_order_statuses();
    if (!in_array($status, $statuses, true)) $statuses[] = $status;
    echo '<style>.ph-track-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:8px}.ph-track-grid .full{grid-column:1/-1}@media(max-width:782px){.ph-track-grid{grid-template-columns:1fr}}.ph-track-grid label{font-weight:700;display:block;margin-bottom:4px}.ph-track-grid input,.ph-track-grid select{width:100%;padding:7px 10px}</style>';
    echo '<p>سفارش <b dir="ltr">' . esc_html($code) . '</b> — مشتری: <b>' . esc_html(get_post_meta($post->ID, '_ph_customer', true)) . '</b> — موبایل: <b dir="ltr">' . esc_html($phone) . '</b></p>';
    echo '<div class="ph-track-grid">';
    echo '<div><label for="ph_status">وضعیت سفارش</label><select name="ph_status" id="ph_status">';
    foreach ($statuses as $st) echo '<option value="' . esc_attr($st) . '"' . selected($status, $st, false) . '>' . esc_html($st) . '</option>';
    echo '</select></div>';
    echo '<div><label for="ph_carrier">شرکت حمل‌ونقل</label><select name="ph_carrier" id="ph_carrier"><option value="">— انتخاب نشده —</option>';
    foreach (ph_track_carriers() as $k => $c) echo '<option value="' . esc_attr($k) . '"' . selected($carrier, $k, false) . '>' . esc_html($c['label']) . '</option>';
    echo '</select></div>';
    echo '<div><label for="ph_tracking">کد پیگیری مرسوله</label><input type="text" name="ph_tracking" id="ph_tracking" dir="ltr" value="' . esc_attr($track) . '" placeholder="مثلاً 2450001234567"></div>';
    echo '<div><label for="ph_track_url">لینک پیگیری دلخواه (اختیاری)</label><input type="text" name="ph_track_url" id="ph_track_url" dir="ltr" value="' . esc_attr($turl) . '" placeholder="https://example.com/track?code={CODE}"></div>';
    echo '<div class="full"><label style="font-weight:400"><input type="checkbox" name="ph_notify_sms" value="1"> ارسال پیامک کد پیگیری به مشتری (' . esc_html($phone ?: 'شماره ثبت نشده') . ')</label>';
    echo '<p class="description">اگر «لینک پیگیری دلخواه» خالی باشد، لینک پیش‌فرض شرکت انتخابی استفاده می‌شود؛ عبارت <code dir="ltr">{CODE}</code> با کد پیگیری جایگزین می‌گردد.</p></div>';
    echo '</div>';
}

add_action('save_post_ph_order', 'ph_save_order_track');
function ph_save_order_track($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['ph_track_nonce']) || !wp_verify_nonce($_POST['ph_track_nonce'], 'ph_order_track')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    $status = sanitize_text_field($_POST['ph_status'] ?? '');
    if ($status !== '') update_post_meta($post_id, '_ph_status', $status);
    update_post_meta($post_id, '_ph_carrier', sanitize_key($_POST['ph_carrier'] ?? ''));
    $old = (string) get_post_meta($post_id, '_ph_tracking', true);
    $track = sanitize_text_field($_POST['ph_tracking'] ?? '');
    update_post_meta($post_id, '_ph_tracking', $track);
    update_post_meta($post_id, '_ph_track_url', esc_url_raw($_POST['ph_track_url'] ?? ''));
    if (!empty($_POST['ph_notify_sms']) && $track !== '' && $track !== $old) {
        $phone = (string) get_post_meta($post_id, '_ph_phone', true);
        if ($phone !== '' && function_exists('ph_sms_send')) {
            $carrier = sanitize_key($_POST['ph_carrier'] ?? '');
            $msg = 'مشتری گرامی، سفارش ' . get_post_meta($post_id, '_ph_code', true) . ' شما'
                 . ($carrier !== '' ? ' با ' . ph_carrier_label($carrier) : '') . ' ارسال شد. کد پیگیری: ' . $track;
            $url = ph_tracking_url($post_id);
            if ($url !== '') $msg .= ' | رهگیری آنلاین: ' . $url;
            ph_sms_send($phone, $msg, false);
        }
    }
}

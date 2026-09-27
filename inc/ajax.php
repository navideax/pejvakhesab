<?php
/**
 * Dynamic endpoints: leads, messages, newsletter, reviews, orders + live REST search.
 */
defined('ABSPATH') || exit;

function ph_verify_ajax() {
    return isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'ph_ajax');
}
function ph_notify_admin($subject, $lines) {
    $body = '';
    foreach ((array) $lines as $k => $v) $body .= $k . ': ' . $v . "\n";
    $body .= "\n—\n" . home_url('/');
    wp_mail(get_option('admin_email'), '[پژواک حساب] ' . $subject, $body);
}

/* ---------- درخواست مشاوره ---------- */
add_action('wp_ajax_ph_lead', 'ph_ajax_lead');
add_action('wp_ajax_nopriv_ph_lead', 'ph_ajax_lead');
function ph_ajax_lead() {
    if (!ph_verify_ajax()) wp_send_json_error('nonce');
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $subject = sanitize_text_field($_POST['subject'] ?? '');
    $msg = sanitize_textarea_field($_POST['msg'] ?? '');
    if (mb_strlen($name) < 3 || mb_strlen($phone) < 10) wp_send_json_error('validation');
    $id = wp_insert_post(['post_type' => 'ph_lead', 'post_title' => $name . ' — ' . $phone, 'post_content' => $msg, 'post_status' => 'publish']);
    if (!$id || is_wp_error($id)) wp_send_json_error('db');
    update_post_meta($id, '_ph_phone', $phone);
    update_post_meta($id, '_ph_subject', $subject);
    ph_notify_admin('درخواست مشاوره جدید', ['نام' => $name, 'موبایل' => $phone, 'موضوع' => $subject, 'توضیح' => $msg]);
    wp_send_json_success(['id' => $id]);
}

/* ---------- پیام تماس ---------- */
add_action('wp_ajax_ph_message', 'ph_ajax_message');
add_action('wp_ajax_nopriv_ph_message', 'ph_ajax_message');
function ph_ajax_message() {
    if (!ph_verify_ajax()) wp_send_json_error('nonce');
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $subject = sanitize_text_field($_POST['subject'] ?? '');
    $msg = sanitize_textarea_field($_POST['msg'] ?? '');
    if (mb_strlen($name) < 3 || mb_strlen($msg) < 3) wp_send_json_error('validation');
    $id = wp_insert_post(['post_type' => 'ph_message', 'post_title' => $name . ' — ' . $subject, 'post_content' => $msg, 'post_status' => 'publish']);
    if (!$id || is_wp_error($id)) wp_send_json_error('db');
    update_post_meta($id, '_ph_phone', $phone);
    update_post_meta($id, '_ph_subject', $subject);
    ph_notify_admin('پیام جدید از فرم تماس', ['نام' => $name, 'موبایل' => $phone, 'موضوع' => $subject, 'متن' => $msg]);
    wp_send_json_success(['id' => $id]);
}

/* ---------- خبرنامه ---------- */
add_action('wp_ajax_ph_subscribe', 'ph_ajax_subscribe');
add_action('wp_ajax_nopriv_ph_subscribe', 'ph_ajax_subscribe');
function ph_ajax_subscribe() {
    if (!ph_verify_ajax()) wp_send_json_error('nonce');
    $email = sanitize_email($_POST['email'] ?? '');
    if (!is_email($email)) wp_send_json_error('ایمیل معتبر نیست');
    $exists = get_posts(['post_type' => 'ph_subscriber', 's' => $email, 'numberposts' => 5, 'post_status' => 'any']);
    foreach ($exists as $e) if ($e->post_title === $email) wp_send_json_error('این ایمیل قبلاً عضو شده است');
    $id = wp_insert_post(['post_type' => 'ph_subscriber', 'post_title' => $email, 'post_status' => 'publish']);
    if (!$id || is_wp_error($id)) wp_send_json_error('db');
    wp_send_json_success(['id' => $id]);
}

/* ---------- دیدگاه‌های محصول ---------- */
add_action('wp_ajax_ph_get_reviews', 'ph_ajax_get_reviews');
add_action('wp_ajax_nopriv_ph_get_reviews', 'ph_ajax_get_reviews');
function ph_ajax_get_reviews() {
    if (!ph_verify_ajax()) wp_send_json_error('nonce');
    $pid = (int) ($_POST['pid'] ?? 0);
    if (!$pid || get_post_type($pid) !== 'ph_product') wp_send_json_error('post');
    $out = [];
    foreach (get_comments(['post_id' => $pid, 'status' => 'approve', 'number' => 20]) as $c) {
        $out[] = [$c->comment_author, ph_fa_date($c->comment_date), $c->comment_content, (int) get_comment_meta($c->comment_ID, '_ph_rating', true) ?: 5];
    }
    wp_send_json_success(['list' => $out, 'count' => count($out)]);
}
add_action('wp_ajax_ph_submit_review', 'ph_ajax_submit_review');
add_action('wp_ajax_nopriv_ph_submit_review', 'ph_ajax_submit_review');
function ph_ajax_submit_review() {
    if (!ph_verify_ajax()) wp_send_json_error('nonce');
    $pid = (int) ($_POST['pid'] ?? 0);
    if (!$pid || get_post_type($pid) !== 'ph_product') wp_send_json_error('post');
    $name = sanitize_text_field($_POST['name'] ?? '');
    $text = sanitize_textarea_field($_POST['text'] ?? '');
    $rating = min(5, max(1, (int) ($_POST['rating'] ?? 5)));
    if (mb_strlen($name) < 2 || mb_strlen($text) < 3) wp_send_json_error('validation');
    if (get_post_field('comment_status', $pid) !== 'open') wp_update_post(['ID' => $pid, 'comment_status' => 'open']);
    $cid = wp_insert_comment(['comment_post_ID' => $pid, 'comment_author' => $name, 'comment_content' => $text, 'comment_approved' => 0, 'user_id' => get_current_user_id()]);
    if (!$cid || is_wp_error($cid)) wp_send_json_error('db');
    add_comment_meta($cid, '_ph_rating', $rating);
    ph_notify_admin('دیدگاه جدید برای تأیید', ['محصول' => get_the_title($pid), 'نام' => $name, 'امتیاز' => $rating]);
    wp_send_json_success(['id' => $cid]);
}

/* ---------- ثبت سفارش ----------
 * مبلغ، قیمت اقلام، هزینه ارسال و تخفیف همگی سمت سرور محاسبه می‌شوند؛
 * مقادیر ارسالی کلاینت هرگز مبنای پرداخت نیستند.
 */
add_action('wp_ajax_ph_create_order', 'ph_ajax_create_order');
add_action('wp_ajax_nopriv_ph_create_order', 'ph_ajax_create_order');
function ph_ajax_create_order() {
    if (!ph_verify_ajax()) wp_send_json_error('nonce');
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $province = sanitize_text_field($_POST['province'] ?? '');
    $city = sanitize_text_field($_POST['city'] ?? '');
    $addr = sanitize_textarea_field($_POST['addr'] ?? '');
    $postcode = sanitize_text_field($_POST['postcode'] ?? '');
    $ship = sanitize_text_field($_POST['ship'] ?? '');
    $pay = sanitize_text_field($_POST['pay'] ?? '');
    $note = sanitize_textarea_field($_POST['note'] ?? '');
    $items_in = json_decode(stripslashes($_POST['items'] ?? '[]'), true) ?: [];
    if (mb_strlen($name) < 3 || mb_strlen($phone) < 10 || !$items_in) wp_send_json_error('validation');

    /* --- قیمت اقلام فقط از دیتابیس (متای _ph_price) --- */
    $lines = [];
    $subtotal = 0;
    foreach (array_slice($items_in, 0, 50) as $it) {
        $slug = sanitize_key($it['id'] ?? '');
        $qty = max(1, min(99, (int) ($it['q'] ?? 1)));
        if ($slug === '') continue;
        $p = get_page_by_path($slug, OBJECT, 'ph_product');
        if (!$p || $p->post_status !== 'publish') continue;
        $price = max(0, (int) get_post_meta($p->ID, '_ph_price', true));
        $lines[] = ['id' => $slug, 'q' => $qty, 'price' => $price];
        $subtotal += $price * $qty;
    }
    if (!$lines) wp_send_json_error('validation');

    /* --- هزینه ارسال سمت سرور --- */
    $ship_code = sanitize_key($_POST['ship_code'] ?? '');
    if (!in_array($ship_code, ['std', 'exp', 'pick'], true)) $ship_code = 'std';
    $scfg = ph_ship_config();
    if ($ship_code === 'std') {
        $ship_cost = ($scfg['freeOver'] > 0 && $subtotal >= $scfg['freeOver']) ? 0 : (int) $scfg['std'];
    } elseif ($ship_code === 'exp') {
        $ship_cost = (int) $scfg['exp'];
    } else {
        $ship_cost = 0;
    }

    /* --- کد تخفیف سمت سرور --- */
    $coupon_in = strtoupper(trim(sanitize_text_field($_POST['coupon'] ?? '')));
    $coupon_off = ($coupon_in !== '' && $coupon_in === strtoupper(trim((string) ph_opt('ph_coupon_code', '')))) ? max(0, (int) ph_opt('ph_coupon_off', 0)) : 0;
    $discount = min($subtotal, (int) round($subtotal * $coupon_off / 100));
    $total = max(0, $subtotal + $ship_cost - $discount);

    /* --- پیش‌شرط‌های پرداخت آنلاین قبل از ساخت سفارش (بدون سفارش یتیم) --- */
    $go_online = (sanitize_key($_POST['pay_code'] ?? '') === 'online');
    if ($go_online && !ph_zarin_enabled()) wp_send_json_error('درگاه پرداخت آنلاین فعال نیست');
    if ($go_online && $total < 1000) wp_send_json_error('مبلغ سفارش برای پرداخت آنلاین کافی نیست');

    $id = wp_insert_post(['post_type' => 'ph_order', 'post_title' => 'سفارش جدید — ' . $name, 'post_status' => 'publish']);
    if (!$id || is_wp_error($id)) wp_send_json_error('db');
    $code = 'PH-' . (100000 + $id);
    wp_update_post(['ID' => $id, 'post_title' => 'سفارش ' . $code . ' — ' . $name]);
    update_post_meta($id, '_ph_code', $code);
    update_post_meta($id, '_ph_customer', $name);
    update_post_meta($id, '_ph_phone', $phone);
    update_post_meta($id, '_ph_province', $province);
    update_post_meta($id, '_ph_city', $city);
    update_post_meta($id, '_ph_addr', $addr);
    update_post_meta($id, '_ph_postcode', $postcode);
    update_post_meta($id, '_ph_ship', $ship);
    update_post_meta($id, '_ph_ship_code', $ship_code);
    update_post_meta($id, '_ph_pay', $pay);
    update_post_meta($id, '_ph_note', $note);
    /* شماره کاربر فقط وقتی ذخیره می‌شود که قبلاً شماره‌ای ندارد (جلوگیری از دستکاری جهت دیدن سفارش دیگران) */
    if (is_user_logged_in() && $phone && !get_user_meta(get_current_user_id(), 'ph_phone', true)) {
        update_user_meta(get_current_user_id(), 'ph_phone', $phone);
    }
    update_post_meta($id, '_ph_user_id', get_current_user_id());
    update_post_meta($id, '_ph_items', wp_json_encode($lines, JSON_UNESCAPED_UNICODE));
    update_post_meta($id, '_ph_subtotal', $subtotal);
    update_post_meta($id, '_ph_ship_cost', $ship_cost);
    update_post_meta($id, '_ph_discount', $discount);
    update_post_meta($id, '_ph_total', $total);
    update_post_meta($id, '_ph_status', $go_online ? 'در انتظار پرداخت' : 'در حال پردازش');
    if ($go_online) {
        $callback = add_query_arg('action', 'ph_payback', admin_url('admin-post.php'));
        [$pay_ok, $pay_info] = ph_zarin_request($total, $callback, 'سفارش ' . $code . ' — پژواک حساب', ph_mobile_norm($phone));
        if (!$pay_ok) {
            wp_delete_post($id, true);
            wp_send_json_error($pay_info === 'no-merchant' ? 'درگاه پرداخت فعال نیست' : 'خطا در اتصال به درگاه پرداخت');
        }
        update_post_meta($id, '_ph_authority', $pay_info);
        $zcfg = ph_zarin_cfg();
        wp_send_json_success(['code' => $code, 'pay_url' => $zcfg['start'] . $pay_info]);
    }
    ph_notify_admin('سفارش جدید ' . $code, ['مشتری' => $name, 'موبایل' => $phone, 'استان' => $province, 'شهر' => $city, 'ارسال' => $ship, 'پرداخت' => $pay, 'مبلغ' => $total]);
    wp_send_json_success(['code' => $code]);
}

/* ---------- جستجوی زنده ---------- */
add_action('rest_api_init', function () {
    register_rest_route('ph/v1', '/search', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => function ($req) {
            $q = sanitize_text_field($req->get_param('q') ?? '');
            if (mb_strlen($q) < 2) return ['results' => []];
            $out = [];
            foreach (['ph_product' => 'تجهیزات', 'ph_software' => 'نرم‌افزار', 'post' => 'مجله'] as $pt => $label) {
                foreach (get_posts(['post_type' => $pt, 's' => $q, 'numberposts' => 4, 'post_status' => 'publish']) as $p) {
                    $out[] = ['t' => ($pt === 'ph_software' ? 'نرم‌افزار ' : '') . get_the_title($p), 's' => $label, 'img' => get_the_post_thumbnail_url($p, 'thumbnail') ?: '', 'url' => get_permalink($p)];
                    if (count($out) >= 10) break 2;
                }
            }
            return ['results' => $out];
        },
    ]);
});

/* ---------- حساب کاربری ---------- */
add_action('wp_ajax_ph_my_orders', 'ph_ajax_my_orders');
function ph_ajax_my_orders() {
    if (!ph_verify_ajax() || !is_user_logged_in()) wp_send_json_error('auth');
    $uid = get_current_user_id();
    $mq = [['key' => '_ph_user_id', 'value' => $uid]];
    /* تطبیق با شماره موبایل فقط وقتی مجاز است که کاربر همان شماره را با کد پیامکی تأیید کرده باشد */
    $phone = get_user_meta($uid, 'ph_phone', true);
    if ($phone && get_user_meta($uid, 'ph_phone_verified', true) === '1') {
        $mq[] = ['key' => '_ph_phone', 'value' => $phone];
    }
    $orders = get_posts(['post_type' => 'ph_order', 'numberposts' => 50, 'post_status' => 'publish', 'meta_query' => ['relation' => 'OR', $mq]]);
    $out = [];
    foreach ($orders as $o) {
        $items = json_decode(get_post_meta($o->ID, '_ph_items', true), true) ?: [];
        $cnt = 0;
        foreach ($items as $it) $cnt += (int) ($it['q'] ?? 1);
        $out[] = ['code' => get_post_meta($o->ID, '_ph_code', true), 'date' => ph_fa_date($o->post_date),
            'count' => $cnt ?: count($items), 'total' => (int) get_post_meta($o->ID, '_ph_total', true),
            'status' => get_post_meta($o->ID, '_ph_status', true) ?: 'در حال پردازش',
            'ref' => get_post_meta($o->ID, '_ph_ref', true),
            'carrier' => get_post_meta($o->ID, '_ph_carrier', true),
            'tracking' => get_post_meta($o->ID, '_ph_tracking', true),
            'track_url' => function_exists('ph_tracking_url') ? ph_tracking_url($o->ID) : ''];
    }
    wp_send_json_success($out);
}
add_action('wp_ajax_ph_get_addresses', 'ph_ajax_get_addresses');
function ph_ajax_get_addresses() {
    if (!ph_verify_ajax() || !is_user_logged_in()) wp_send_json_error('auth');
    wp_send_json_success(get_user_meta(get_current_user_id(), 'ph_addresses', true) ?: []);
}
add_action('wp_ajax_ph_save_address', 'ph_ajax_save_address');
function ph_ajax_save_address() {
    if (!ph_verify_ajax() || !is_user_logged_in()) wp_send_json_error('auth');
    $uid = get_current_user_id();
    $list = get_user_meta($uid, 'ph_addresses', true) ?: [];
    $row = ['title' => sanitize_text_field($_POST['title'] ?? ''), 'city' => sanitize_text_field($_POST['city'] ?? ''), 'addr' => sanitize_textarea_field($_POST['addr'] ?? '')];
    if (mb_strlen($row['addr']) < 8) wp_send_json_error('آدرس را کامل وارد کنید');
    $list[] = $row;
    update_user_meta($uid, 'ph_addresses', array_slice($list, -10));
    wp_send_json_success(array_slice($list, -10));
}
add_action('wp_ajax_ph_update_profile', 'ph_ajax_update_profile');
function ph_ajax_update_profile() {
    if (!ph_verify_ajax() || !is_user_logged_in()) wp_send_json_error('auth');
    $uid = get_current_user_id();
    $args = ['ID' => $uid];
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $pass = $_POST['pass'] ?? '';
    if ($name) $args['display_name'] = $name;
    if ($email && is_email($email)) $args['user_email'] = $email;
    if ($pass && mb_strlen($pass) >= 6) $args['user_pass'] = $pass;
    $r = wp_update_user($args);
    if (is_wp_error($r)) wp_send_json_error($r->get_error_message());
    wp_send_json_success(['name' => get_userdata($uid)->display_name]);
}

/* ---------- ورود پیامکی (ملی پیامک) ---------- */
add_action('wp_ajax_ph_otp_send', 'ph_ajax_otp_send');
add_action('wp_ajax_nopriv_ph_otp_send', 'ph_ajax_otp_send');
function ph_ajax_otp_send() {
    if (!ph_verify_ajax()) wp_send_json_error('nonce');
    if (!ph_sms_enabled()) wp_send_json_error('sms-off');
    [$ok, $info] = ph_otp_request(sanitize_text_field($_POST['mobile'] ?? ''));
    if (!$ok) wp_send_json_error($info);
    wp_send_json_success(['cooldown' => (int) $info]);
}
add_action('wp_ajax_ph_otp_verify', 'ph_ajax_otp_verify');
add_action('wp_ajax_nopriv_ph_otp_verify', 'ph_ajax_otp_verify');
function ph_ajax_otp_verify() {
    if (!ph_verify_ajax()) wp_send_json_error('nonce');
    if (!ph_sms_enabled()) wp_send_json_error('sms-off');
    [$ok, $info] = ph_otp_check(sanitize_text_field($_POST['mobile'] ?? ''), $_POST['code'] ?? '');
    if (!$ok) wp_send_json_error($info);
    $mobile = $info;
    $user = get_user_by('login', $mobile);
    if (!$user) {
        $found = get_users(['meta_key' => 'ph_phone', 'meta_value' => $mobile, 'number' => 1]);
        $user = $found ? $found[0] : null;
    }
    if (!$user) {
        if (!get_option('users_can_register')) wp_send_json_error('reg-off');
        $uid = wp_create_user($mobile, wp_generate_password(16, false), '');
        if (is_wp_error($uid)) wp_send_json_error($uid->get_error_message());
        wp_update_user(['ID' => $uid, 'display_name' => $mobile]);
    } else {
        $uid = $user->ID;
    }
    /* شماره‌ای که با کد پیامکی تأیید شده است — مبنای مجاز تطبیق سفارش‌ها */
    update_user_meta($uid, 'ph_phone', $mobile);
    update_user_meta($uid, 'ph_phone_verified', '1');
    wp_set_current_user($uid);
    wp_set_auth_cookie($uid, true);
    wp_send_json_success(['redirect' => ph_url('account')]);
}

/* ---------- بازگشت از درگاه زرین‌پال ---------- */
add_action('admin_post_ph_payback', 'ph_payback');
add_action('admin_post_nopriv_ph_payback', 'ph_payback');
function ph_payback() {
    $checkout = ph_url('checkout');
    $back = function ($res, $code = '') use ($checkout) {
        $url = add_query_arg('pay', $res, $checkout);
        if ($code !== '') $url = add_query_arg('code', $code, $url);
        wp_redirect($url);
        exit;
    };
    $auth = sanitize_text_field($_GET['Authority'] ?? '');
    $status = sanitize_text_field($_GET['Status'] ?? '');
    if ($auth === '') $back('failed');
    $orders = get_posts(['post_type' => 'ph_order', 'numberposts' => 1, 'post_status' => 'publish', 'meta_key' => '_ph_authority', 'meta_value' => $auth]);
    if (!$orders) $back('failed');
    $oid = $orders[0]->ID;
    $code = get_post_meta($oid, '_ph_code', true);
    $total = (int) get_post_meta($oid, '_ph_total', true);
    if (get_post_meta($oid, '_ph_status', true) === 'پرداخت شد') $back('success', $code);
    if ($status !== 'OK') {
        update_post_meta($oid, '_ph_status', 'لغو شده');
        $back('cancelled', $code);
    }
    [$ok, $ref, $pan] = ph_zarin_verify($total, $auth);
    if (!$ok) {
        update_post_meta($oid, '_ph_pay_err', $ref);
        $back('failed', $code);
    }
    update_post_meta($oid, '_ph_status', 'پرداخت شد');
    update_post_meta($oid, '_ph_ref', $ref);
    update_post_meta($oid, '_ph_card', $pan);
    $phone = get_post_meta($oid, '_ph_phone', true);
    ph_notify_admin('پرداخت موفق ' . $code, ['مشتری' => get_post_meta($oid, '_ph_customer', true), 'موبایل' => $phone, 'مبلغ' => $total, 'کد پیگیری' => $ref]);
    if (ph_sms_enabled() && ph_opt('ph_sms_paid', '') === '1' && $phone) {
        ph_sms_send($phone, 'پژواک حساب: پرداخت سفارش ' . $code . ' موفق بود. کد پیگیری: ' . $ref, false);
    }
    $back('success', $code);
}

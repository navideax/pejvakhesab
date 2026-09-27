<?php
/**
 * Pejvak professional admin settings panel.
 * Stores values as theme mods, so every ph_opt() call keeps working unchanged.
 */
defined('ABSPATH') || exit;
require_once __DIR__ . '/settings-fields.php';

add_action('admin_menu', function () {
    add_menu_page('تنظیمات پژواک', 'تنظیمات پژواک', 'manage_options', 'ph-settings', 'ph_panel_render', 'dashicons-admin-settings', 59);
});
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_ph-settings') return;
    wp_enqueue_media();
    wp_enqueue_style('ph-panel', PH_URI . '/assets/css/admin-panel.css', [], PH_VER);
});

function ph_panel_current_tab() {
    $tabs = ph_panel_tabs();
    $t = sanitize_key($_GET['tab'] ?? 'contact');
    return isset($tabs[$t]) ? $t : 'contact';
}

function ph_panel_descriptions() {
    return [
        'ph_sms_enabled' => 'نمایش فرم ورود با کد پیامکی در صفحه حساب کاربری (نیازمند نام کاربری و رمز عبور ملی پیامک)',
        'ph_sms_pattern' => 'شناسه متن (BodyId) پترن تأیید در پنل ملی پیامک؛ اگر خالی باشد کد با متن ساده ارسال می‌شود',
        'ph_sms_paid' => 'ارسال خودکار پیامک به مشتری پس از پرداخت موفق سفارش',
        'ph_zarin_enabled' => 'نمایش گزینه پرداخت آنلاین در تسویه حساب (نیازمند مرچنت‌کد زرین‌پال)',
        'ph_zarin_sandbox' => 'اتصال به درگاه آزمایشی زرین‌پال برای تست، بدون تراکنش واقعی',
        'ph_map_embed' => 'کد iframe نقشه (مثلاً از نشان یا گوگل‌مپ)؛ اگر خالی باشد جعبه نقشه با دکمه مسیریابی نمایش داده می‌شود',
        'ph_hero_image' => 'خالی = تصویر پیش‌فرض قالب',
        'ph_ship_freeover' => 'سفارش‌های بالای این مبلغ، ارسال عادی رایگان دارند؛ صفر = غیرفعال',
        'ph_foot_copy' => '{year} به‌صورت خودکار با سال جاری جایگزین می‌شود',
        'ph_terms_url' => 'خالی = لینک خودکار به برگه قوانین (در صورت وجود)',
        'ph_privacy_url' => 'خالی = لینک خودکار به برگه حریم خصوصی (در صورت وجود)',
    ];
}

function ph_panel_render() {
    if (!current_user_can('manage_options')) return;
    $tab = ph_panel_current_tab();
    $tabs = ph_panel_tabs();
    ?>
    <div class="wrap ph-panel">
        <div class="ph-panel-head">
            <div class="ph-brand"><span class="ph-logo">پ</span>
                <div><h1>تنظیمات پژواک حساب</h1><span class="ph-ver">نسخه <?php echo esc_html(PH_VER); ?></span></div>
            </div>
            <div class="ph-head-actions">
                <a class="button" href="<?php echo esc_url(home_url('/')); ?>" target="_blank" rel="noopener">مشاهده سایت</a>
                <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=ph-settings&tab=status')); ?>">وضعیت سیستم</a>
            </div>
        </div>
        <?php if (isset($_GET['saved'])) : ?><div class="notice notice-success is-dismissible"><p>تنظیمات با موفقیت ذخیره شد.</p></div><?php endif; ?>
        <?php if (isset($_GET['reset'])) : ?><div class="notice notice-warning is-dismissible"><p>تنظیمات این صفحه به حالت پیش‌فرض برگشت.</p></div><?php endif; ?>
        <?php if (isset($_GET['flushed'])) : ?><div class="notice notice-success is-dismissible"><p>حافظه موقت پاک شد.</p></div><?php endif; ?>
        <div class="ph-panel-body">
            <nav class="ph-tabs">
                <?php foreach ($tabs as $id => $t) : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=ph-settings&tab=' . $id)); ?>" class="<?php echo $id === $tab ? 'active' : ''; ?>">
                        <span class="dashicons <?php echo esc_attr($t['icon']); ?>"></span><?php echo esc_html($t['title']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="ph-tab-content">
                <?php if ($tab === 'status') ph_panel_status(); else ph_panel_form($tab); ?>
            </div>
        </div>
    </div>
    <script>
    jQuery(function ($) {
        var frame;
        $(document).on('click', '.ph-img-btn', function (e) {
            e.preventDefault();
            var btn = $(this), input = $('#' + btn.data('target')), prev = $('#' + btn.data('target') + '-prev');
            frame = wp.media({ title: 'انتخاب تصویر', button: { text: 'استفاده از این تصویر' }, multiple: false });
            frame.on('select', function () {
                var url = frame.state().get('selection').first().toJSON().url;
                input.val(url);
                if (prev.length) { prev.attr('src', url); prev.show(); }
            });
            frame.open();
        });
        $(document).on('click', '.ph-img-clear', function (e) {
            e.preventDefault();
            var btn = $(this), input = $('#' + btn.data('target')), prev = $('#' + btn.data('target') + '-prev');
            input.val(''); if (prev.length) prev.hide();
        });
    });
    </script>
    <?php
}

function ph_panel_form($tab) {
    $all = ph_panel_fields();
    $fields = [];
    foreach ($all as $id => $f) if ($f['tab'] === $tab) $fields[$id] = $f;
    if (!$fields) return;
    $groups = [];
    foreach ($fields as $id => $f) $groups[$f['g']][] = $id;
    $multi = count($groups) > 1;
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="ph_panel_save">';
    echo '<input type="hidden" name="tab" value="' . esc_attr($tab) . '">';
    wp_nonce_field('ph_panel_save_' . $tab);
    foreach ($groups as $g => $ids) {
        echo '<div class="ph-card">';
        if ($multi && $g !== '') echo '<h2>' . esc_html($g) . '</h2>';
        echo '<table class="form-table ph-form"><tbody>';
        foreach ($ids as $id) ph_panel_field_row($id, $fields[$id]);
        echo '</tbody></table></div>';
    }
    echo '<div class="ph-savebar"><button class="button button-primary button-large">ذخیره تنظیمات</button> ';
    echo '<button name="ph_reset" value="1" class="button button-link-delete" onclick="return confirm(\'تنظیمات این صفحه به حالت پیش‌فرض برگردد؟\')">بازنشانی این صفحه</button></div>';
    echo '</form>';
}

function ph_panel_field_row($id, $f) {
    $val = get_theme_mod($id, $f['default']);
    if (!is_string($val)) $val = (string) $val;
    $ltr_ids = ['ph_phone_tel', 'ph_email', 'ph_coupon_code', 'ph_coupon_off', 'ph_ship_std_p', 'ph_ship_exp_p', 'ph_ship_freeover', 'ph_sms_user', 'ph_sms_from', 'ph_sms_pattern', 'ph_zarin_merchant', 'ph_map_url'];
    $ltr = in_array($f['type'], ['url', 'password', 'image'], true) || in_array($id, $ltr_ids, true);
    $desc = ph_panel_descriptions()[$id] ?? '';
    echo '<tr><th scope="row"><label for="ph-' . esc_attr($id) . '">' . esc_html($f['label']) . '</label></th><td>';
    if ($f['type'] === 'textarea') {
        $rows = max(3, min(10, substr_count($val, "\n") + substr_count($f['default'], "\n") + 2));
        echo '<textarea id="ph-' . esc_attr($id) . '" name="ph[' . esc_attr($id) . ']" rows="' . (int) $rows . '" class="large-text' . ($ltr ? ' ph-ltr' : '') . '">' . esc_textarea($val) . '</textarea>';
    } elseif ($f['type'] === 'checkbox') {
        echo '<label class="ph-switch"><input type="checkbox" id="ph-' . esc_attr($id) . '" name="ph[' . esc_attr($id) . ']" value="1"' . checked($val, '1', false) . '><span class="ph-slider"></span></label>';
        echo ' <span class="ph-check-hint">' . ($val === '1' ? 'فعال است' : 'غیرفعال است') . '</span>';
    } elseif ($f['type'] === 'image') {
        echo '<div class="ph-img-row"><input type="text" id="ph-' . esc_attr($id) . '" name="ph[' . esc_attr($id) . ']" value="' . esc_attr($val) . '" class="regular-text ph-ltr" dir="ltr" placeholder="https://…"> ';
        echo '<button class="button ph-img-btn" data-target="ph-' . esc_attr($id) . '">انتخاب تصویر</button> ';
        echo '<button class="button button-link-delete ph-img-clear" data-target="ph-' . esc_attr($id) . '">حذف</button></div>';
        echo '<img id="ph-' . esc_attr($id) . '-prev" class="ph-img-prev" src="' . esc_attr($val) . '" alt=""' . ($val === '' ? ' style="display:none"' : '') . '>';
    } else {
        $t = $f['type'] === 'url' ? 'url' : ($f['type'] === 'password' ? 'password' : 'text');
        echo '<input type="' . $t . '" id="ph-' . esc_attr($id) . '" name="ph[' . esc_attr($id) . ']" value="' . esc_attr($val) . '" class="regular-text' . ($ltr ? ' ph-ltr' : '') . '"' . ($ltr ? ' dir="ltr"' : '') . '>';
    }
    if ($desc !== '') echo '<p class="description">' . esc_html($desc) . '</p>';
    $d = (string) $f['default'];
    if ($d !== '' && $f['type'] !== 'checkbox' && strpos($d, "\n") === false && mb_strlen($d) < 70) {
        echo '<p class="description ph-default-hint">پیش‌فرض: ' . esc_html($d) . '</p>';
    }
    echo '</td></tr>';
}

function ph_panel_pill($text, $state) {
    return '<span class="ph-pill ph-' . esc_attr($state) . '">' . esc_html($text) . '</span>';
}

function ph_panel_status() {
    $sms = ph_sms_enabled();
    $zcfg = function_exists('ph_zarin_cfg') ? ph_zarin_cfg() : ['merchant' => ''];
    $zen = function_exists('ph_zarin_enabled') ? ph_zarin_enabled() : false;
    $np = wp_count_posts('ph_product');
    $ns = wp_count_posts('ph_software');
    $nb = wp_count_posts('post');
    $no = wp_count_posts('ph_order');
    $pending = get_posts(['post_type' => 'ph_order', 'numberposts' => -1, 'post_status' => 'publish', 'fields' => 'ids', 'meta_key' => '_ph_status', 'meta_value' => 'در انتظار پرداخت']);
    $seed_url = wp_nonce_url(admin_url('admin-post.php?action=ph_seed'), 'ph_seed');
    $flush_url = wp_nonce_url(admin_url('admin-post.php?action=ph_panel_flush'), 'ph_panel_flush');
    ?>
    <div class="ph-status-grid">
        <div class="ph-card"><h2>وضعیت اتصال‌ها</h2>
            <table class="form-table ph-form"><tbody>
                <tr><th>ورود پیامکی (ملی پیامک)</th><td><?php echo $sms ? ph_panel_pill('فعال', 'ok') : ph_panel_pill('غیرفعال', 'off'); ?>
                    <p class="description"><?php echo $sms ? 'نام کاربری: ' . esc_html(ph_opt('ph_sms_user', '')) : 'از تب «پرداخت و پیامک» تنظیم کنید.'; ?></p></td></tr>
                <tr><th>پرداخت آنلاین (زرین‌پال)</th><td><?php
                    if (!$zen) echo ph_panel_pill('غیرفعال', 'off');
                    elseif (!empty($zcfg['sandbox'])) echo ph_panel_pill('حالت آزمایشی', 'warn');
                    else echo ph_panel_pill('فعال', 'ok');
                    ?></td></tr>
                <tr><th>نسخه محتوا</th><td><code><?php echo esc_html(get_option('ph_seed_ver', '—')); ?></code></td></tr>
            </tbody></table>
        </div>
        <div class="ph-card"><h2>محتوای سایت</h2>
            <table class="form-table ph-form"><tbody>
                <tr><th>محصولات</th><td><strong><?php echo (int) ($np->publish ?? 0); ?></strong> <a class="button button-small" href="<?php echo esc_url(admin_url('edit.php?post_type=ph_product')); ?>">مدیریت</a></td></tr>
                <tr><th>نرم‌افزارها</th><td><strong><?php echo (int) ($ns->publish ?? 0); ?></strong> <a class="button button-small" href="<?php echo esc_url(admin_url('edit.php?post_type=ph_software')); ?>">مدیریت</a></td></tr>
                <tr><th>مقالات مجله</th><td><strong><?php echo (int) ($nb->publish ?? 0); ?></strong> <a class="button button-small" href="<?php echo esc_url(admin_url('edit.php')); ?>">مدیریت</a></td></tr>
                <tr><th>سفارش‌ها</th><td><strong><?php echo (int) ($no->publish ?? 0); ?></strong><?php if ($pending) echo ' (' . count($pending) . ' در انتظار پرداخت)'; ?> <a class="button button-small" href="<?php echo esc_url(admin_url('edit.php?post_type=ph_order')); ?>">مدیریت</a></td></tr>
            </tbody></table>
        </div>
    </div>
    <div class="ph-card"><h2>ابزارها</h2>
        <p>
            <a class="button" href="<?php echo esc_url($seed_url); ?>">بررسی و تکمیل محتوای اولیه</a>
            <a class="button" href="<?php echo esc_url($flush_url); ?>">پاک‌سازی حافظه موقت</a>
            <a class="button" href="<?php echo esc_url(admin_url('edit.php?post_type=page')); ?>">مدیریت برگه‌ها</a>
        </p>
        <p class="description">«تکمیل محتوا» فقط موارد ناقص را اضافه می‌کند و محتوای موجود شما را تغییر نمی‌دهد.</p>
    </div>
    <?php
}

add_action('admin_post_ph_panel_save', function () {
    if (!current_user_can('manage_options')) wp_die('دسترسی غیرمجاز');
    $tab = sanitize_key($_POST['tab'] ?? 'contact');
    check_admin_referer('ph_panel_save_' . $tab);
    $fields = ph_panel_fields();
    if (!empty($_POST['ph_reset'])) {
        foreach ($fields as $id => $f) if ($f['tab'] === $tab) remove_theme_mod($id);
        delete_transient('ph_seed_cache');
        delete_transient('ph_links_cache');
        wp_redirect(add_query_arg(['page' => 'ph-settings', 'tab' => $tab, 'reset' => 1], admin_url('admin.php')));
        exit;
    }
    $posted = isset($_POST['ph']) && is_array($_POST['ph']) ? wp_unslash($_POST['ph']) : [];
    foreach ($fields as $id => $f) {
        if ($f['tab'] !== $tab) continue;
        if ($f['type'] === 'checkbox') {
            set_theme_mod($id, isset($posted[$id]) ? '1' : '');
            continue;
        }
        if (!array_key_exists($id, $posted)) continue;
        $v = $posted[$id];
        if (!is_string($v)) $v = '';
        switch ($f['san']) {
            case 'sanitize_textarea_field': $v = sanitize_textarea_field($v); break;
            case 'esc_url_raw': $v = esc_url_raw($v); break;
            case 'wp_kses_post': $v = wp_kses_post($v); break;
            case 'ph_sanitize_iframe': $v = function_exists('ph_sanitize_iframe') ? ph_sanitize_iframe($v) : ''; break;
            default: $v = sanitize_text_field($v);
        }
        set_theme_mod($id, $v);
    }
    delete_transient('ph_seed_cache');
    delete_transient('ph_links_cache');
    wp_redirect(add_query_arg(['page' => 'ph-settings', 'tab' => $tab, 'saved' => 1], admin_url('admin.php')));
    exit;
});

add_action('admin_post_ph_panel_flush', function () {
    if (!current_user_can('manage_options') || !check_admin_referer('ph_panel_flush')) wp_die('دسترسی غیرمجاز');
    delete_transient('ph_seed_cache');
    delete_transient('ph_links_cache');
    wp_redirect(add_query_arg(['page' => 'ph-settings', 'tab' => 'status', 'flushed' => 1], admin_url('admin.php')));
    exit;
});

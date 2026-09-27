<?php
/**
 * Theme settings live in the Pejvak admin panel (Dashboard > Pejvak Settings).
 * This file keeps the sanitizers + a Customizer pointer to that panel.
 */
defined('ABSPATH') || exit;

function ph_sanitize_iframe($v) {
    return wp_kses($v, ['iframe' => ['src' => true, 'width' => true, 'height' => true, 'style' => true, 'loading' => true, 'allowfullscreen' => true, 'frameborder' => true, 'title' => true]]);
}
function ph_sanitize_checkbox($v) { return $v ? '1' : ''; }

add_action('customize_register', function ($wp_customize) {
    /* Declared here (not at file top) because WP_Customize_Control only loads
       inside the Customizer; declaring it at top level fatals every page. */
    if (!class_exists('PH_Panel_Info_Control', false)) {
        class PH_Panel_Info_Control extends WP_Customize_Control {
            public function render_content() {
                echo '<p style="font-size:13px">تنظیمات قالب از پنل اختصاصی مدیریت می‌شود:</p>';
                echo '<p><a class="button button-primary" href="' . esc_url(admin_url('admin.php?page=ph-settings')) . '">باز کردن پنل تنظیمات پژواک</a></p>';
            }
        }
    }
    $wp_customize->add_section('ph_panel_info', ['title' => 'تنظیمات پژواک حساب', 'priority' => 30]);
    $wp_customize->add_setting('ph_panel_info_txt', ['sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control(new PH_Panel_Info_Control($wp_customize, 'ph_panel_info_txt', ['section' => 'ph_panel_info']));
});

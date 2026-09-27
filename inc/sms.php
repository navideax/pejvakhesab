<?php
/**
 * Mellipayamak (ملی پیامک) REST client + OTP helpers.
 * Pattern send uses BaseServiceNumber when a BodyId is set; otherwise plain SendSMS.
 */
defined('ABSPATH') || exit;

function ph_sms_enabled() {
    return ph_opt('ph_sms_enabled', '') === '1' && trim((string) ph_opt('ph_sms_user', '')) !== '' && (string) ph_opt('ph_sms_pass', '') !== '';
}

/**
 * @return array [bool $ok, string $info]  $info = provider id on success, error key/message on failure
 */
function ph_sms_send($to, $text, $pattern = false) {
    $user = trim((string) ph_opt('ph_sms_user', ''));
    $pass = (string) ph_opt('ph_sms_pass', '');
    if ($user === '' || $pass === '') return [false, 'not-configured'];
    $to = ph_mobile_norm($to);
    if ($to === '') return [false, 'bad-mobile'];
    $body_id = (int) ph_opt('ph_sms_pattern', 0);
    if ($pattern && $body_id > 0) {
        $url = 'https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber';
        $body = ['username' => $user, 'password' => $pass, 'to' => $to, 'text' => (string) $text, 'bodyId' => $body_id];
    } else {
        $from = trim((string) ph_opt('ph_sms_from', ''));
        if ($from === '') return [false, 'no-sender'];
        $url = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';
        $body = ['username' => $user, 'password' => $pass, 'to' => $to, 'from' => $from, 'text' => (string) $text, 'isFlash' => false];
    }
    $res = wp_remote_post($url, [
        'timeout' => 15,
        'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
        'body' => wp_json_encode($body),
    ]);
    if (is_wp_error($res)) return [false, $res->get_error_message()];
    $code = (int) wp_remote_retrieve_response_code($res);
    $j = json_decode((string) wp_remote_retrieve_body($res), true);
    if ($code >= 200 && $code < 300 && is_array($j) && isset($j['RetStatus']) && (int) $j['RetStatus'] === 1) {
        return [true, (string) ($j['Value'] ?? '')];
    }
    $msg = is_array($j) ? (string) ($j['StrRetStatus'] ?? $j['Value'] ?? '') : '';
    if ($msg === '') $msg = 'http-' . $code;
    return [false, $msg];
}

function ph_mobile_norm($m) {
    $m = trim((string) $m);
    if ($m === '') return '';
    $m = strtr($m, '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩', '01234567890123456789');
    $m = preg_replace('/\D+/', '', $m);
    if (strpos($m, '0098') === 0) $m = '0' . substr($m, 4);
    elseif (strpos($m, '98') === 0 && strlen($m) >= 12) $m = '0' . substr($m, 2);
    elseif (strpos($m, '9') === 0 && strlen($m) === 10) $m = '0' . $m;
    return preg_match('/^09\d{9}$/', $m) ? $m : '';
}

/**
 * Issue + send an OTP code. @return array [ok, cooldown seconds | error key]
 */
function ph_otp_request($mobile) {
    $mobile = ph_mobile_norm($mobile);
    if ($mobile === '') return [false, 'bad-mobile'];
    if (get_transient('ph_otp_cool_' . $mobile)) return [false, 'cooldown'];
    $count_key = 'ph_otp_count_' . $mobile;
    if ((int) get_transient($count_key) >= 5) return [false, 'too-many'];
    $code = (string) random_int(10000, 99999);
    set_transient('ph_otp_' . $mobile, ['h' => password_hash($code, PASSWORD_DEFAULT), 'tries' => 0], 10 * MINUTE_IN_SECONDS);
    set_transient($count_key, (int) get_transient($count_key) + 1, HOUR_IN_SECONDS);
    if ((int) ph_opt('ph_sms_pattern', 0) > 0) {
        [$ok, $info] = ph_sms_send($mobile, $code, true);
    } else {
        [$ok, $info] = ph_sms_send($mobile, 'کد تأیید پژواک حساب: ' . $code, false);
    }
    if (!$ok) return [false, $info];
    set_transient('ph_otp_cool_' . $mobile, 1, 120);
    return [true, 120];
}

/**
 * Verify an OTP code. @return array [ok, mobile | error key]
 */
function ph_otp_check($mobile, $code) {
    $mobile = ph_mobile_norm($mobile);
    $code = trim((string) $code);
    if ($mobile === '' || !preg_match('/^\d{4,8}$/', $code)) return [false, 'bad-code'];
    $key = 'ph_otp_' . $mobile;
    $d = get_transient($key);
    if (!is_array($d) || empty($d['h'])) return [false, 'expired'];
    if ((int) ($d['tries'] ?? 0) >= 5) {
        delete_transient($key);
        return [false, 'too-many'];
    }
    if (!password_verify($code, $d['h'])) {
        $d['tries'] = (int) ($d['tries'] ?? 0) + 1;
        set_transient($key, $d, 10 * MINUTE_IN_SECONDS);
        return [false, 'wrong'];
    }
    delete_transient($key);
    delete_transient('ph_otp_cool_' . $mobile);
    return [true, $mobile];
}

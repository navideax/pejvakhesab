<?php
/**
 * ZarinPal v4 payment gateway client.
 * Theme works in Toman; gateway expects Rial, so amounts are multiplied by 10.
 */
defined('ABSPATH') || exit;

function ph_zarin_cfg() {
    $sandbox = ph_opt('ph_zarin_sandbox', '') === '1';
    return [
        'merchant' => trim((string) ph_opt('ph_zarin_merchant', '')),
        'sandbox' => $sandbox,
        'base' => $sandbox ? 'https://sandbox.zarinpal.com' : 'https://payment.zarinpal.com',
        'start' => $sandbox ? 'https://sandbox.zarinpal.com/pg/StartPay/' : 'https://www.zarinpal.com/pg/StartPay/',
    ];
}

function ph_zarin_enabled() {
    return ph_opt('ph_zarin_enabled', '') === '1' && ph_zarin_cfg()['merchant'] !== '';
}

/**
 * Start a payment. @return array [ok, authority | error]
 */
function ph_zarin_request($amount_toman, $callback, $desc, $mobile = '', $email = '') {
    $c = ph_zarin_cfg();
    if ($c['merchant'] === '') return [false, 'no-merchant'];
    $rial = (int) round(((float) $amount_toman) * 10);
    if ($rial < 10000) return [false, 'amount-low'];
    $payload = [
        'merchant_id' => $c['merchant'],
        'amount' => $rial,
        'callback_url' => $callback,
        'description' => mb_substr((string) $desc, 0, 250),
    ];
    $meta = [];
    if ($mobile !== '') $meta['mobile'] = $mobile;
    if ($email !== '') $meta['email'] = $email;
    if ($meta) $payload['metadata'] = $meta;
    $res = wp_remote_post($c['base'] . '/pg/v4/payment/request.json', [
        'timeout' => 20,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode($payload),
    ]);
    if (is_wp_error($res)) return [false, $res->get_error_message()];
    $j = json_decode((string) wp_remote_retrieve_body($res), true);
    $data = (is_array($j) && isset($j['data']) && is_array($j['data'])) ? $j['data'] : [];
    if ((int) ($data['code'] ?? 0) === 100 && !empty($data['authority'])) {
        return [true, (string) $data['authority']];
    }
    $msg = '';
    if (is_array($j) && isset($j['errors'])) {
        $e = $j['errors'];
        if (is_array($e)) {
            if (isset($e['message'])) $msg = (string) $e['message'];
            elseif (isset($e[0])) $msg = (string) (is_array($e[0]) ? ($e[0]['message'] ?? '') : $e[0]);
        } else {
            $msg = (string) $e;
        }
    }
    if ($msg === '') $msg = 'code-' . (string) ($data['code'] ?? 'unknown');
    return [false, $msg];
}

/**
 * Verify a payment. @return array [ok, ref_id | error, card_pan]
 */
function ph_zarin_verify($amount_toman, $authority) {
    $c = ph_zarin_cfg();
    $rial = (int) round(((float) $amount_toman) * 10);
    $res = wp_remote_post($c['base'] . '/pg/v4/payment/verify.json', [
        'timeout' => 20,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode(['merchant_id' => $c['merchant'], 'amount' => $rial, 'authority' => (string) $authority]),
    ]);
    if (is_wp_error($res)) return [false, $res->get_error_message(), ''];
    $j = json_decode((string) wp_remote_retrieve_body($res), true);
    $data = (is_array($j) && isset($j['data']) && is_array($j['data'])) ? $j['data'] : [];
    $code = (int) ($data['code'] ?? 0);
    if (($code === 100 || $code === 101) && !empty($data['ref_id'])) {
        return [true, (string) $data['ref_id'], (string) ($data['card_pan'] ?? '')];
    }
    return [false, 'code-' . $code, ''];
}

<?php
/**
 * WooCommerce readiness.
 * The theme renders its catalog from ph_product CPT out of the box.
 * When WooCommerce is installed, native shop/cart/checkout/account take over
 * the matching pages — declare support + sane defaults here.
 */
defined('ABSPATH') || exit;

add_action('after_setup_theme', function () {
    add_theme_support('woocommerce', [
        'thumbnail_image_width' => 640,
        'single_image_width' => 800,
        'product_grid' => ['default_rows' => 3, 'min_rows' => 1, 'max_rows' => 6, 'default_columns' => 3, 'min_columns' => 1, 'max_columns' => 4],
    ]);
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
});

add_filter('loop_shop_columns', function () { return 3; });
add_filter('woocommerce_output_related_products_args', function ($args) {
    return array_merge($args, ['posts_per_page' => 4, 'columns' => 4]);
});
add_filter('body_class', function ($classes) {
    if (class_exists('WooCommerce')) $classes[] = 'has-woo';
    return $classes;
});
/* Use WooCommerce endpoints for cart/checkout/account links when active */
add_filter('ph_page_url', function ($url, $key) {
    if (!function_exists('wc_get_page_permalink')) return $url;
    $map = ['cart' => 'cart', 'checkout' => 'checkout', 'account' => 'myaccount', 'shop' => 'shop'];
    if (isset($map[$key]) && wc_get_page_id($map[$key]) > 0) return wc_get_page_permalink($map[$key]);
    return $url;
}, 10, 2);

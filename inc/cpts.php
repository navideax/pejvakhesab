<?php
/**
 * Custom Post Types: ph_product (تجهیزات) + ph_software (نرم‌افزار) + taxonomy ph_cat
 */
defined('ABSPATH') || exit;

add_action('init', function () {
    register_post_type('ph_product', [
        'labels' => [
            'name' => 'محصولات', 'singular_name' => 'محصول',
            'add_new_item' => 'افزودن محصول', 'edit_item' => 'ویرایش محصول',
            'all_items' => 'همه محصولات', 'search_items' => 'جستجو در محصولات',
        ],
        'public' => true,
        'has_archive' => 'products',
        'rewrite' => ['slug' => 'product', 'with_front' => false],
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'menu_icon' => 'dashicons-cart',
        'show_in_rest' => true,
        'menu_position' => 5,
    ]);
    register_taxonomy('ph_cat', 'ph_product', [
        'labels' => [
            'name' => 'دسته‌بندی تجهیزات', 'singular_name' => 'دسته',
            'add_new_item' => 'افزودن دسته', 'edit_item' => 'ویرایش دسته', 'all_items' => 'همه دسته‌ها',
        ],
        'hierarchical' => true,
        'public' => true,
        'rewrite' => ['slug' => 'product-cat', 'with_front' => false],
        'show_in_rest' => true,
        'show_admin_column' => true,
    ]);
    register_post_type('ph_software', [
        'labels' => [
            'name' => 'نرم‌افزارها', 'singular_name' => 'نرم‌افزار',
            'add_new_item' => 'افزودن نرم‌افزار', 'edit_item' => 'ویرایش نرم‌افزار',
            'all_items' => 'همه نرم‌افزارها', 'search_items' => 'جستجو در نرم‌افزارها',
        ],
        'public' => true,
        'has_archive' => 'softwares',
        'rewrite' => ['slug' => 'software', 'with_front' => false],
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'menu_icon' => 'dashicons-desktop',
        'show_in_rest' => true,
        'menu_position' => 6,
    ]);
});

/* ---------- Dynamic content types (no mock data) ---------- */
add_action('init', function () {
    register_post_type('ph_faq', [
        'labels' => ['name' => 'سوالات متداول', 'singular_name' => 'سوال', 'add_new_item' => 'افزودن سوال', 'edit_item' => 'ویرایش سوال', 'all_items' => 'همه سوالات'],
        'public' => true, 'has_archive' => false, 'rewrite' => false, 'exclude_from_search' => true,
        'supports' => ['title', 'editor', 'page-attributes'], 'menu_icon' => 'dashicons-editor-help', 'show_in_rest' => true,
    ]);
    register_taxonomy('ph_faq_cat', 'ph_faq', [
        'labels' => ['name' => 'دسته‌بندی سوالات', 'singular_name' => 'دسته'],
        'hierarchical' => true, 'public' => true, 'rewrite' => false, 'show_admin_column' => true,
    ]);
    register_post_type('ph_testimonial', [
        'labels' => ['name' => 'نظرات مشتریان', 'singular_name' => 'نظر', 'add_new_item' => 'افزودن نظر', 'edit_item' => 'ویرایش نظر', 'all_items' => 'همه نظرات'],
        'public' => true, 'has_archive' => false, 'rewrite' => false, 'exclude_from_search' => true,
        'supports' => ['title', 'editor', 'page-attributes'], 'menu_icon' => 'dashicons-format-quote', 'show_in_rest' => true,
    ]);
    register_post_type('ph_brand', [
        'labels' => ['name' => 'برندها', 'singular_name' => 'برند', 'add_new_item' => 'افزودن برند', 'edit_item' => 'ویرایش برند', 'all_items' => 'همه برندها'],
        'public' => true, 'has_archive' => false, 'rewrite' => false, 'exclude_from_search' => true,
        'supports' => ['title', 'page-attributes'], 'menu_icon' => 'dashicons-awards', 'show_in_rest' => true,
    ]);
    register_post_type('ph_service', [
        'labels' => ['name' => 'خدمات', 'singular_name' => 'خدمت', 'add_new_item' => 'افزودن خدمت', 'edit_item' => 'ویرایش خدمت', 'all_items' => 'همه خدمات'],
        'public' => true, 'has_archive' => false, 'rewrite' => false, 'exclude_from_search' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'page-attributes'], 'menu_icon' => 'dashicons-hammer', 'show_in_rest' => true,
    ]);
    register_post_type('ph_feature', [
        'labels' => ['name' => 'مزیت‌ها (چرا ما)', 'singular_name' => 'مزیت', 'add_new_item' => 'افزودن مزیت', 'edit_item' => 'ویرایش مزیت', 'all_items' => 'همه مزیت‌ها'],
        'public' => true, 'has_archive' => false, 'rewrite' => false, 'exclude_from_search' => true,
        'supports' => ['title', 'editor', 'page-attributes'], 'menu_icon' => 'dashicons-star-filled', 'show_in_rest' => true,
    ]);
    register_post_type('ph_solution', [
        'labels' => ['name' => 'راهکارهای صنفی', 'singular_name' => 'راهکار', 'add_new_item' => 'افزودن راهکار', 'edit_item' => 'ویرایش راهکار', 'all_items' => 'همه راهکارها'],
        'public' => true, 'has_archive' => false, 'rewrite' => false, 'exclude_from_search' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'], 'menu_icon' => 'dashicons-store', 'show_in_rest' => true,
    ]);
    foreach ([
        'ph_lead' => ['درخواست‌های مشاوره', 'dashicons-phone'],
        'ph_message' => ['پیام‌های تماس', 'dashicons-email'],
        'ph_subscriber' => ['اعضای خبرنامه', 'dashicons-groups'],
        'ph_order' => ['سفارش‌ها', 'dashicons-clipboard'],
    ] as $pt => [$label, $icon]) {
        register_post_type($pt, [
            'labels' => ['name' => $label, 'singular_name' => $label, 'all_items' => $label],
            'public' => false, 'show_ui' => true, 'exclude_from_search' => true,
            'supports' => ['title', 'editor'], 'menu_icon' => $icon, 'capability_type' => 'post',
        ]);
    }
});

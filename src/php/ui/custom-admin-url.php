<?php

defined('ABSPATH') or exit;

// Block old login URLs by returning a 404 error
function zynith_seo_block_old_login_urls() {
    $custom_slug = get_option('zynith_seo_custom_login_url', '');
    if (!empty($custom_slug)) {
        if (preg_match('#^/wp-login.php#', $_SERVER['REQUEST_URI']) || preg_match('#^/wp-admin/?$#', $_SERVER['REQUEST_URI'])) {
            status_header(404);
            nocache_headers();
            include(get_query_template('404'));
            exit;
        }
    }
}
add_action('init', 'zynith_seo_block_old_login_urls');

// Ensure users can still log in via the custom path
function zynith_seo_custom_login_template() {
    $custom_slug = get_option('zynith_seo_custom_login_url', 'custom-login');
    // If a custom slug is set, allow login via that URL
    if (!empty($custom_slug) && trim($_SERVER['REQUEST_URI'], '/') === trim($custom_slug, '/')) {
        require_once(ABSPATH . 'wp-login.php');
        exit;
    }
}
add_action('template_redirect', 'zynith_seo_custom_login_template');
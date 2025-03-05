<?php
/**
 * Module Name: Zynith SEO - Custom Admin URL
 * Description: Allows users to set a custom login URL and blocks default login paths by returning a 404 error.
 * Version:     1.0.0
 * Author:      Zynith SEO
 */
defined('ABSPATH') or exit;

// Add the custom login URL setting field to the main Zynith SEO settings page
function zynith_seo_custom_login_url_setting() {
    add_settings_field(
        'zynith_seo_custom_login_url',
        'Custom Login URL',
        'zynith_seo_custom_login_url_callback',
        'zynith_seo',
        'zynith_seo_ui_section',
        ['label_for' => 'zynith_seo_custom_login_url']
    );
    register_setting('zynith_seo', 'zynith_seo_custom_login_url', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
}
add_action('admin_init', 'zynith_seo_custom_login_url_setting');

// Display the input field for the custom login URL
function zynith_seo_custom_login_url_callback($args) {
    $option_name = $args['label_for'];
    $value = get_option($option_name, ''); // Default custom login URL slug
    echo "<input type='text' id='{$option_name}' name='{$option_name}' value='{$value}' placeholder='custom-login' style='margin: 0 10px 0 0;' />";
    echo "<span class='description'>Set a custom login URL (e.g., '/secure-login').</span>";
}

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
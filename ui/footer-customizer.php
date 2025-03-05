<?php
/*
Plugin Name: ZYNITH SEO - Admin Footer Customizer
Description: Replaces the default WordPress footer message with a custom message.
Version: 1.0.1
Author: ZYNITH SEO
*/

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Customize the footer text in the WordPress admin
function zynith_seo_custom_admin_footer_text() {
    echo '<i><strong>ZYNITH SEO</strong> brings your websites and SEO to life!</i>';
}
add_filter('admin_footer_text', 'zynith_seo_custom_admin_footer_text');

// Optionally, remove or modify the WordPress version info in the footer
function zynith_seo_custom_admin_footer_version() {
    return ''; // You can return a custom message here or leave it blank to remove the version info
}
add_filter('update_footer', 'zynith_seo_custom_admin_footer_version', 11);
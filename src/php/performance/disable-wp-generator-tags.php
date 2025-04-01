<?php
defined('ABSPATH') or exit;

// Disable the WordPress generator tag (WordPress version number in the meta tag)
function zynith_seo_disable_wp_generator_tag() {
    remove_action('wp_head', 'wp_generator');
}
add_action('init', 'zynith_seo_disable_wp_generator_tag');
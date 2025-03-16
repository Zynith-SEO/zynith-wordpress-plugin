<?php
/*
Plugin Name: ZYNITH SEO - Disable WordPress Generator Tag
Description: Disables the WordPress generator meta tag from the HTML header to hide the WordPress version.
Version: 1.0
Author: ZYNITH SEO
*/

// Disable the WordPress generator tag (WordPress version number in the meta tag)
function zynith_seo_disable_wp_generator_tag() {
    remove_action('wp_head', 'wp_generator');
}
add_action('init', 'zynith_seo_disable_wp_generator_tag');
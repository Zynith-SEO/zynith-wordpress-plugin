<?php
/**
 * Module Name: Zynith SEO - Disable Gutenberg
 * Description: Disables Gutenberg editor and enables the Classic Editor for posts, pages, and custom post types. Removes Gutenberg styles from both the admin and the front end.
 * Version:     1.0.1
 * Author:      Zynith SEO
*/

// Disable Gutenberg for posts, pages, and custom post types
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

// Hide the 'Try Gutenberg' prompt in WordPress Dashboard
remove_action('try_gutenberg_panel', 'wp_try_gutenberg_panel');

// Remove the Gutenberg-specific styles from front-end
function zynith_seo_remove_gutenberg_styles() {
    wp_dequeue_style('wp-block-library'); // Remove Gutenberg block library CSS
    wp_dequeue_style('wp-block-library-theme'); // Remove Gutenberg theme-specific CSS
    wp_dequeue_style('wc-block-style'); // Remove WooCommerce block styles if using WooCommerce
}
add_action('wp_enqueue_scripts', 'zynith_seo_remove_gutenberg_styles', 100);

function zynith_seo_disable_gutenberg_admin_styles() {
    wp_dequeue_style('wp-block-editor'); // Remove Gutenberg editor styles in admin
    wp_enqueue_style('classic-editor-styles', includes_url('css/editor.min.css'), [], null); // Load Classic Editor CSS
}
add_action('admin_enqueue_scripts', 'zynith_seo_disable_gutenberg_admin_styles', 100);
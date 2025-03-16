<?php

// Enqueue dark mode CSS for the admin area with cache-busting
function zynith_seo_enqueue_darkmode_ui() {
    // Cache-busting: Use file modification time as the version number to force reloads when the file changes
    $version = filemtime(plugin_dir_path(__FILE__) . '/assets/css/admin-dark-theme.css');
    
    // Enqueue dark theme CSS with the version as the file modification time
    wp_enqueue_style( 'zynith-seo-dark-theme', home_url('/wp-content/plugins/zynith-seo/assets/css/admin-dark-theme.css'), array(), $version, 'all' );
}
add_action( 'admin_enqueue_scripts', 'zynith_seo_enqueue_darkmode_ui', 999 ); // High priority to ensure dark theme loads last

// Dequeue the light theme CSS to disable it when dark mode is active
function zynith_seo_dequeue_light_theme() {
    // Dequeue the light theme CSS from the ZYNITH SEO core plugin
    wp_dequeue_style( 'zynith-seo-light-theme' );
}
add_action( 'admin_enqueue_scripts', 'zynith_seo_dequeue_light_theme', 1000 ); // Ensure this runs after the light theme has been enqueued
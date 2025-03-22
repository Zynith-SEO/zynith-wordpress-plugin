<?php

defined('ABSPATH') or exit;

function zynith_seo_enqueue_light_css() {
    $file_path = ZYNITH_SEO_DIR . '/assets/css/admin-light-theme.css';

    // Check if the file exists before calling filemtime
    if (file_exists($file_path)) {
        $version = filemtime($file_path);  
    }
    else {
        $version = ZYNITH_SEO_VERSION;
    }

    // Enqueue the light theme CSS
    wp_enqueue_style(
        'zynith-seo-light-theme',
        ZYNITH_SEO_URL . 'assets/css/admin-light-theme.css', 
        [],
        $version,
        'all'
    );
}

function zynith_seo_enqueue_light_theme() {
    $disable_option = get_option('zynith_seo_disable_light_mode');

    if ($disable_option != 1) {
        // If NOT set to 1, enqueue on all admin pages
        zynith_seo_enqueue_light_css();
    }
    else {
        $allowed_pages = array(
            'zynith_seo_settings', 
            'zynith-seo-sitemap-settings'
        );
        if (isset($_GET['page']) && in_array($_GET['page'], $allowed_pages, true)) zynith_seo_enqueue_light_css();
    }
}
add_action('admin_enqueue_scripts', 'zynith_seo_enqueue_light_theme', 999);
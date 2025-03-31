<?php

defined('ABSPATH') or exit;

/**
 * Enforce the removal of default WordPress dashboard widgets for Zynith SEO.
 */
function zynith_seo_remove_default_dashboard_widgets() {
    // Remove core dashboard widgets
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

    // Remove welcome panel (sometimes re-added by themes/plugins)
    remove_action('welcome_panel', 'wp_welcome_panel');
}
add_action('wp_dashboard_setup', 'zynith_seo_remove_default_dashboard_widgets', 999);

/**
 * Failsafe removal of dashboard widgets during admin_init.
 */
add_action('admin_init', function () {
    if (is_admin() && function_exists('get_current_screen')) {
        $screen = get_current_screen();
        if ($screen && $screen->base === 'dashboard') {
            zynith_seo_remove_default_dashboard_widgets();
        }
    }
});

/**
 * Final removal and visual hiding of widgets via admin_head.
 */
add_action('admin_head', function () {
    if (is_admin() && function_exists('get_current_screen')) {
        $screen = get_current_screen();
        if ($screen && $screen->base === 'dashboard') {
            remove_action('welcome_panel', 'wp_welcome_panel');

            // Optional: Hide persistent widgets via CSS (last resort)
            echo '<style>
                #dashboard_quick_press,
                #dashboard_primary,
                #dashboard_secondary,
                #dashboard_incoming_links,
                #dashboard_plugins,
                #dashboard_right_now,
                #dashboard_activity,
                #dashboard_recent_comments,
                #dashboard_recent_drafts,
                #dashboard_site_health,
                #welcome-panel {
                    display: none !important;
                }
            </style>';
        }
    }
});

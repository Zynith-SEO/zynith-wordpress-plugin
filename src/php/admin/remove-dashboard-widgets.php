<?php

defined('ABSPATH') or exit;

// Remove default WordPress dashboard widgets
function wp_fedora_remove_default_dashboard_widgets() {
    
    // Global WordPress dashboard widgets
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');       // Quick Draft
    remove_meta_box('dashboard_primary', 'dashboard', 'side');           // WordPress Events and News
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');         // Secondary (sometimes used by plugins)
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // Incoming Links (deprecated)
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');         // Plugins
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');       // At a Glance (Right Now)
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');        // Activity
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Recent Comments
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal');   // Recent Drafts

    // Additional widgets to remove
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');     // Site Health
    remove_action('welcome_panel', 'wp_welcome_panel');                  // Welcome Panel
}
add_action('wp_dashboard_setup', 'wp_fedora_remove_default_dashboard_widgets', 20);

// Ensure the Welcome Panel is completely removed
remove_action('welcome_panel', 'wp_welcome_panel');
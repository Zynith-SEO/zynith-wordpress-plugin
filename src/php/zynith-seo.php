<?php
/*
Plugin Name:       Zynith SEO
Plugin URI:        https://zynith.app/wordpress-plugin-zynith-seo-readme/
Description:       A powerful yet lightweight SEO plugin designed for maximum efficiency and to streamline SEO management for optimal search engine results.
Version:           10.5.0
Author:            Zynith SEO
Author URI:        https://zynith.app/
Text Domain:       zynith-seo
Contributors:      Schieler Mew, Kellie Watson
License:           GPL-3.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
Tested up to:      6.7.2
Requires at least: 5.0
Requires PHP:      7.4
Donate link:       https://www.paypal.com/donate/?hosted_button_id=XVXQ3RX7N4SQN
Tags:              SEO, XML sitemap, Schema Markup, Meta Tags, Robots.txt, SEO Signals, WordPress SEO, Breadcrumbs, TOC
Support:           https://www.facebook.com/groups/761871078859984
*/
defined('ABSPATH') or exit;

define('ZYNITH_SEO_VERSION', '10.5.0');

define('ZYNITH_SEO_TEXT_DOMAIN', 'zynith-seo');
define('ZYNITH_SEO_FILE', __FILE__);
define('ZYNITH_SEO_SLUG', plugin_basename(__FILE__));
define('ZYNITH_SEO_DIR', plugin_dir_path(__FILE__));
define('ZYNITH_SEO_URL', plugin_dir_url(__FILE__));

global $zynith_seo_module_data;
$zynith_seo_module_data = [
    [
        'file'             => 'seo-tools/meta-editor.php',
        'criteria'         => null,
        'default'          => 1,  // Enabled
        'option'           => 'zynith_seo_meta_editor',
        'old_option'       => 'zynith_seo_disable_meta_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'        => false,
        'option_version'    => 9
    ],  // 1. On-page Meta Editor    
    [
        'file'             => 'seo-tools/meta-robots-settings.php',
        'criteria'         => null,
        'default'          => 1,  // Enabled
        'option'           => 'zynith_seo_meta_robots',
        'old_option'       => 'zynith_seo_disable_meta_robots',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 2. On-page Meta Robots
    [
        'file'             => 'seo-tools/canonical-url-manager.php',
        'criteria'         => null,
        'default'          => 0,  // Disabled
        'option'           => 'zynith_seo_canonical_url',
        'old_option'       => 'zynith_SEO_disable_canonical_url',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 3. On-page Canonical URL
    [
        'file'             => 'schema/on-page-schema-editor.php',
        'criteria'         => null,
        'default'          => 1,  // Enabled
        'option'           => 'zynith_seo_on_page_schema_editor',
        'old_option'       => 'zynith_seo_disable_on_page_schema_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 4. On-page Schema Editor
    [
        'file'             => 'seo-tools/bulk-meta-editor.php',
        'criteria'         => [
            'type'  => 'setting',
            'option'=> 'zynith_seo_meta_editor',
            'value' => 1
        ],
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_bulk_meta_editor',
        'old_option'       => 'zynith_seo_disable_bulk_meta_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 6. Bulk Meta Editing
    [
        'file'             => 'seo-tools/bulk-index.php',
        'criteria'         => [
            'type'  => 'setting',
            'option'=> 'zynith_seo_meta_robots',
            'value' => 1
        ],
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_bulk_indexing',
        'old_option'       => 'zynith_SEO_disable_bulk_noindex',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 7. Bulk Indexing
    [
        'file'             => 'schema/automatic-schema-editor.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_automatic_schema_editor',
        'old_option'       => 'zynith_seo_disable_automatic_schema_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 8. Automatic Schema Editor
    [
        'file'             => 'content-management/add-alt-text.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_auto_alt_text',
        'old_option'       => 'zynith_seo_disable_image_alt',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 9. Automatic Alt Text
    [
        'file'             => 'seo-tools/robots-txt-editor.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_robots_txt',
        'old_option'       => 'zynith_seo_disable_robots_text_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 10. Robots.txt Settings
    [
        'file'             => 'seo-tools/sitemap-generator.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_sitemap_generator',
        'old_option'       => 'zynith_seo_disable_sitemap_generator',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 11. Sitemap Generator
    [
        'file'             => 'seo-tools/404-monitor.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_404_monitor',
        'old_option'       => 'zynith_seo_disable_404_monitor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 12. 404 Monitor
    [
        'file'             => 'seo-tools/redirect-manager.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_redirect_manager',
        'old_option'       => 'zynith_seo_disable_redirect_manager',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 13. Redirect Manager
    [
        'file'             => 'seo-tools/search-and-replace.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_search_replace',
        'old_option'       => 'zynith_seo_disable_search_replace',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 14. Search and Replace
    [
        'file'             => 'seo-tools/randomize-last-modified-date.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_randomize_post_dates',
        'old_option'       => 'zynith_seo_disable_date_randomizer',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 15. Randomize Post Dates
    [
        'file'             => 'content-management/breadcrumb-editor.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_breadcrumb_shortcode',
        'old_option'       => 'zynith_seo_disable_breadcrumb_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 16. Breadcrumb Shortcode
    [
        'file'             => 'content-management/toc-editor.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_toc_shortcode',
        'old_option'       => 'zynith_seo_disable_toc_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 17. ToC Shortcode
    [
        'file'             => 'content-management/custom-post-type.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_custom_post_types',
        'old_option'       => 'zynith_seo_disable_cpt_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 18. Custom Post Types
    [
        'file'             => 'seo-tools/script-manager.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_script_manager',
        'old_option'       => 'zynith_seo_disable_script_manager',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 19. Script Manager
    [
        'file'             => 'ui/disable-comments.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_disable_comments',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 20. Disable Comments
    [
        'file'             => 'seo-tools/htaccess-file-editor.php',
        'criteria'         => [
            'type'   => 'server',
            'method' => 'apache_only' // a recognized sub-check
        ],
        'default'          => 0,
        'option'           => 'zynith_seo_htaccess_editor',
        'old_option'       => 'zynith_seo_disable_htaccess_file_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 21. .htaccess Editor
    [
        'file'             => 'media/enable-svg-upload.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_svg_uploads',
        'old_option'       => 'zynith_seo_disable_svg_uploads',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 22. Allow SVG Uploads
    [
        'file'             => 'admin/remove-dashboard-widgets.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_clear_wp_dashboard',
        'old_option'       => 'zynith_seo_disable_remove_dashboard_widgets',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 23. Clear WordPress Dashboard
    [
        'file'             => 'ui/disable-admin-bar-resources.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_admin_bar_resources',
        'old_option'       => 'zynith_seo_disable_admin_bar_resources',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 24. Reduce Admin Bar Resources
    [
        'file'             => 'ui/admin-bar-transition.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_prettify_admin_bar',
        'old_option'       => 'zynith_seo_disable_admin_bar_transition',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 25. Prettify Admin Bar
    [
        'file'             => 'ui/admin-light-theme.php',
        'criteria'         => 'Skip',
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_prettify_admin_styling',
        'old_option'       => 'zynith_seo_disable_light_mode',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 26. Prettify Admin Styling
    [
        'file'             => 'performance/disable-gutenberg.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_disable_gutenberg',
        'old_option'       => 'zynith_seo_enable_gutenberg',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 27. Disable Gutenberg
    [
        'file'             => 'content-management/admin-title-search.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_admin_title_search',
        'old_option'       => 'zynith_seo_disable_admin_title_search',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 28. Page Search by Title
    [
        'file'             => 'ui/footer-customizer.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_admin_footer',
        'old_option'       => 'zynith_seo_enable_footer_customizer',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 29. Enable Zynith Admin Footer
    [
        'file'             => 'ui/move-plugin-file-editor.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_move_plugin_file_editor',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'boolean',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 30. Move Plugin File Editor
    [
        'file'             => 'ui/move-theme-file-editor.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_move_theme_file_editor',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'boolean',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 31. Move Theme File Editor
    [
        'file'             => 'ui/custom-admin-url.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_custom_login_url',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'custom_url',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 32. Custom Login URL
    [
        'file'             => 'performance/deferment-manager.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_deferment_manager',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 33. Deferment Manager
    [
        'file'             => 'performance/disable-rest-api.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_rest_api',
        'old_option'       => 'zynith_seo_enable_rest_api',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 34. Disable REST API
    [
        'file'             => 'performance/disable-rss-feeds.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_rss_feed',
        'old_option'       => 'zynith_seo_enable_rss',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 35. Disable RSS Feeds
    [
        'file'             => 'performance/disable-wp-generator-tags.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_wp_generator_tag',
        'old_option'       => 'zynith_seo_enable_wp_generator_tag',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 36. Remove WP Generator Tag
    [
        'file'             => 'performance/limit-autosave-intervals.php',
        'criteria'         => [
            'type'   => 'numeric',
            'option' => 'zynith_seo_autosave_interval',  // The user’s stored setting
            'compare'=> '!=',
            'value'  => 60
        ],
        'default'          => 300, // e.g., 300s
        'option'           => 'zynith_seo_autosave_interval',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'integer',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 37. Autosave Interval (seconds)
    [
        'file'             => 'performance/limit-revisions.php',
        'criteria'         => [
            'type'   => 'numeric',
            'option' => 'zynith_seo_revision_limit',  // The user’s stored setting
            'compare'=> '!=',
            'value'  => 1
        ],
        'default'          => 10,
        'option'           => 'zynith_seo_revision_limit',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'integer_limit',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 38. Revision Limit
    [
        'file'             => 'performance/wordpress-heartbeat-optimizer.php',
        'criteria'         => [
            'type'   => 'numeric',
            'option' => 'zynith_seo_heartbeat_frequency',  // The user’s stored setting
            'compare'=> '!=',
            'value'  => 15
        ],
        'default'          => 60,
        'option'           => 'zynith_seo_heartbeat_frequency',
        'old_option'       => null,
        'old_value'         => null,
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 39. Heartbeat API Frequency (seconds)
];

// Function to set the default settings on initial install or upgrade
function zynith_seo_set_default_options_once_only() {
       
    global $zynith_seo_module_data;

    // This is the latest 'option versioning'
    $default_options_version = 9; //10.5.0

    // The stored version from previous runs
    $stored_version = (int) get_option('zynith_seo_default_option_version', 0);

    // If we've already set defaults for $default_options_version, do nothing
    if ($stored_version >= $default_options_version) return;
    
    foreach ($zynith_seo_module_data as $setting) {

        // 1. Skip if this setting's option_version <= the $stored_version meaning we've already handled it in a previous pass
        $option_version = isset($setting['option_version']) ? (int)$setting['option_version'] : 0;
        if ($option_version <= $stored_version) continue;
        
        // 2. If old_option is set, rename / migrate it
        if (!empty($setting['old_option'])) {
            $old_val = get_option($setting['old_option'], null);
            if ($old_val !== null) {
                
                // Check if old_value => 'Switch' => invert 0/1
                if (!empty($setting['old_value']) && $setting['old_value'] === 'Switch') $old_val = ((int)$old_val === 1) ? 0 : 1;
                
                // Migrate to the new option
                update_option($setting['option'], $old_val);

                // Remove the old option
                delete_option($setting['old_option']);
            }
        }

        // 3. If the new option is still unset, apply the default
        if (get_option($setting['option'], false) === false) update_option($setting['option'], $setting['default']);
    }

    // 4. Update the stored option version
    update_option('zynith_seo_default_option_version', $default_options_version);
}
register_activation_hook(__FILE__, 'zynith_seo_set_default_options_once_only');

// Load modules
function zynith_seo_plugins_loaded() {
    
    // Load required files
    require_once ZYNITH_SEO_DIR . 'updater/zynith-seo-updater.php';
    require_once ZYNITH_SEO_DIR . 'dashboard/zynith-dashboard-information-widget.php';
    require_once ZYNITH_SEO_DIR . 'dashboard/zynith-settings.php';
    
    if (is_admin()) {
        require_once ZYNITH_SEO_DIR . 'ui/admin-light-theme.php';
        
        if (get_option('zynith_seo_tbyb') === 'expired') {
            require_once ZYNITH_SEO_DIR . 'updater/zynith-seo-tbyb.php';
            add_action('admin_notices', 'zynith_seo_tbyb_notices');
            return;
        }
        
        require_once ZYNITH_SEO_DIR . 'dashboard/zynith-meta-copy-widget.php';
    }
    
    require_once ZYNITH_SEO_DIR . 'content-management/custom-data-sanitization.php';
    require_once ZYNITH_SEO_DIR . 'content-management/dynamic-placeholders.php';
    require_once ZYNITH_SEO_DIR . 'migration/helper-migration.php';
    
    // Set default options if needed
    zynith_seo_set_default_options_once_only();
    zynith_seo_run_migration_once_only();

    // Load modules based on $zynith_seo_module_data
    global $zynith_seo_module_data;
    foreach ($zynith_seo_module_data as $setting) {

        // Convert the stored option to an integer for boolean checks
        $value = (int) get_option($setting['option'], $setting['default']);

        // If there's a criteria, check if it passes
        if (isset($setting['criteria']) && !zynith_seo_check_criteria($setting['criteria'])) continue;
        
        // If admin_only, load only in wp-admin if $value == 1
        if ($setting['admin_only'] === true) {
            if (is_admin() && $value === 1) require_once ZYNITH_SEO_DIR . $setting['file'];
        }
        
        // Otherwise, load in both front-end and admin if $value == 1
        if ($value === 1) require_once ZYNITH_SEO_DIR . $setting['file'];
    }
}
add_action('plugins_loaded', 'zynith_seo_plugins_loaded', 1);

// Evaluate any 'criteria' the setting might have.
function zynith_seo_check_criteria($criteria) {
    
    // If no criteria or empty => pass
    if (empty($criteria)) return true;
    
    // If the criteria is literally "Skip", we skip loading
    if ($criteria === 'Skip') return false;
        
    // If it's an array, interpret
    switch ($criteria['type'] ?? '') {
        case 'setting':
            // e.g. 'option' => 'zynith_seo_meta_editor', 'value' => 1
            $option_val = (int) get_option($criteria['option'], 0);
            return ($option_val === (int) $criteria['value']);

        case 'server':
            // e.g. 'method' => 'apache_only'
            if ($criteria['method'] === 'apache_only') return (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false);
            return true; // default if unknown

        case 'numeric':
            // e.g. 'option' => 'zynith_seo_autosave_interval', 'compare' => '!=', 'value' => 60
            $option_val = (int) get_option($criteria['option'], 0);            
            switch ($criteria['compare']) {
                case '!=':
                    return ($option_val !== (int) $criteria['value']);
                case '==':
                    return ($option_val === (int) $criteria['value']);
                // Add more comparisons if needed
            }
            return true;

        default:
            return true; // no recognized type => pass
    }
}
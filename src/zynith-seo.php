<?php
/*
Plugin Name:       Zynith SEO
Plugin URI:        https://zynith.app/wordpress-plugin-zynith-seo-readme/
Description:       A powerful yet lightweight SEO plugin designed for maximum efficiency and to streamline SEO management for optimal search engine results.
Version:           10.4.18
Author:            Zynith SEO
Author URI:        https://zynith.app/
Text Domain:       zynith-seo
Contributors:      Schieler Mew, Kellie Watson
License:           GPL-3.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
Tested up to:      6.7.1
Requires at least: 5.0
Requires PHP:      7.4
Donate link:       https://www.paypal.com/donate/?hosted_button_id=XVXQ3RX7N4SQN
Tags:              SEO, XML sitemap, Schema Markup, Meta Tags, Robots.txt, SEO Signals, WordPress SEO, Breadcrumbs, TOC
Support:           https://www.facebook.com/groups/761871078859984
*/
defined('ABSPATH') or exit;

define('ZYNITH_SEO_VERSION', '10.4.18');

define('ZYNITH_SEO_TEXT_DOMAIN', 'zynith-seo');
define('ZYNITH_SEO_FILE', __FILE__);
define('ZYNITH_SEO_SLUG', plugin_basename(__FILE__));
define('ZYNITH_SEO_DIR', plugin_dir_path(__FILE__));
define('ZYNITH_SEO_URL', plugin_dir_url(__FILE__));

// Function to set the default settings on initial install or upgrade
function zynith_seo_set_default_options_once_only() {
    $default_options_version = 6;
    $stored_version = (int) get_option('zynith_seo_default_option_version', 0);
    
    // If we've already set defaults for version 1 or later, do nothing.
    if ($stored_version >= $default_options_version) return;
    
    $default_options = [];
    
    // If the site is on an older setup (< v1), define the core defaults
    if ($stored_version < 2) {
        $default_options = [
            'zynith_seo_disable_meta_editor'             => 0,
            'zynith_seo_disable_bulk_meta_editor'        => 0,
            'zynith_seo_disable_meta_robots'             => 0,
            'zynith_seo_disable_robots_text_editor'      => 0,
            'zynith_seo_disable_sitemap_generator'       => 0,
            'zynith_seo_disable_404_monitor'             => 0,
            'zynith_seo_disable_script_manager'          => 0,
            'zynith_seo_disable_search_replace'          => 0,
            'zynith_seo_disable_date_randomizer'         => 0,
            'zynith_seo_disable_htaccess_file_editor'    => 0,
            'zynith_seo_disable_admin_title_search'      => 0,
            'zynith_seo_disable_breadcrumb_editor'       => 0,
            'zynith_seo_disable_toc_editor'              => 0,
            'zynith_seo_disable_cpt_editor'              => 0,
            'zynith_seo_disable_on_page_schema_editor'   => 0,
            'zynith_seo_disable_automatic_schema_editor' => 0,
            'zynith_seo_disable_svg_uploads'             => 0,
            'zynith_seo_disable_comments'                => 0,
            'zynith_seo_enable_footer_customizer'        => 0,
            'zynith_seo_move_plugin_file_editor'         => 0,
            'zynith_seo_move_theme_file_editor'          => 0,
            'zynith_seo_enable_rss'                      => 0,
            'zynith_seo_enable_wp_generator_tag'         => 0,
            'zynith_seo_disable_light_mode'              => 0,

            'zynith_seo_disable_remove_dashboard_widgets' => 1,
            'zynith_seo_disable_admin_bar_transition'     => 1,
            'zynith_seo_disable_admin_bar_resources'      => 1,
            'zynith_seo_enable_gutenberg'                 => 1,
            'zynith_seo_enable_rest_api'                  => 1,
            'zynith_seo_deferment_manager'                => 1,

            'zynith_seo_autosave_interval'               => 120,
            'zynith_seo_revision_limit'                  => 1,
            'zynith_seo_heartbeat_frequency'             => 60
        ];
    }
    if ($stored_version < 5) {
        $default_options['zynith_SEO_disable_bulk_noindex'] = 0;
        $default_options['zynith_SEO_disable_image_alt'] = 0;
        $default_options['zynith_SEO_disable_canonical_url'] = 1;
    }
    
    $default_options['zynith_SEO_disable_redirect_manager'] = 0;
    
    foreach ($default_options as $option_name => $default_value) if (get_option($option_name, false) === false) update_option($option_name, $default_value);
    update_option('zynith_seo_default_option_version', $default_options_version);
}
register_activation_hook(__FILE__, 'zynith_seo_set_default_options_once_only');

function zynith_seo_plugins_loaded() {
    require_once ZYNITH_SEO_DIR . 'updater/zynith-seo-updater.php';
    
    if (get_option('zynith_seo_tbyb') == 'expired') {
        require_once ZYNITH_SEO_DIR . 'updater/zynith-seo-tbyb.php';
        add_action('admin_notices', 'zynith_seo_tbyb_notices');
        return;
    }
    
    zynith_seo_set_default_options_once_only();
    
    // SEO Tools
    if (!get_option('zynith_seo_disable_bulk_meta_editor'))         require_once ZYNITH_SEO_DIR . 'seo-tools/bulk-meta-editor.php';
    if (!get_option('zynith_SEO_disable_bulk_noindex'))             require_once ZYNITH_SEO_DIR . 'seo-tools/bulk-index.php';    
    if (!get_option('zynith_seo_disable_meta_robots'))              require_once ZYNITH_SEO_DIR . 'seo-tools/meta-robots-settings.php';
    if (!get_option('zynith_SEO_disable_canonical_url'))            require_once ZYNITH_SEO_DIR . 'seo-tools/canonical-url-manager.php';
    if (!get_option('zynith_seo_disable_robots_text_editor'))       require_once ZYNITH_SEO_DIR . 'seo-tools/robots-txt-editor.php';
    if (!get_option('zynith_seo_disable_sitemap_generator'))        require_once ZYNITH_SEO_DIR . 'seo-tools/sitemap-generator.php';
    if (!get_option('zynith_seo_disable_404_monitor'))              require_once ZYNITH_SEO_DIR . 'seo-tools/404-monitor.php';
    if (!get_option('zynith_SEO_disable_redirect_manager'))         require_once ZYNITH_SEO_DIR . 'seo-tools/redirect-manager.php';
    if (!get_option('zynith_seo_disable_script_manager'))           require_once ZYNITH_SEO_DIR . 'seo-tools/script-manager.php';
    if (!get_option('zynith_seo_disable_search_replace'))           require_once ZYNITH_SEO_DIR . 'seo-tools/search-and-replace.php';
    if (!get_option('zynith_seo_disable_randomize_dates'))          require_once ZYNITH_SEO_DIR . 'seo-tools/randomize-last-modified-date.php';
    if (!get_option('zynith_seo_disable_htaccess_file_editor') && strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false) require_once ZYNITH_SEO_DIR . 'seo-tools/htaccess-file-editor.php';
    if (!get_option('zynith_seo_disable_automatic_schema_editor'))  require_once ZYNITH_SEO_DIR . 'schema/automatic-schema-editor.php';
    if (!get_option('zynith_seo_disable_on_page_schema_editor'))    require_once ZYNITH_SEO_DIR . 'schema/on-page-schema-editor.php';
    
    // Content Management
    if (!get_option('zynith_seo_disable_breadcrumb_editor'))        require_once ZYNITH_SEO_DIR . 'content-management/breadcrumb-editor.php';
    if (!get_option('zynith_seo_disable_toc_editor'))               require_once ZYNITH_SEO_DIR . 'content-management/toc-editor.php';
    if (!get_option('zynith_seo_disable_cpt_editor'))               require_once ZYNITH_SEO_DIR . 'content-management/custom-post-type.php';
    
    // Performance
    if (!get_option('zynith_seo_deferment_manager'))                require_once ZYNITH_SEO_DIR . 'performance/deferment-manager.php';
    if (!get_option('zynith_seo_enable_rss'))                       require_once ZYNITH_SEO_DIR . 'performance/disable-rss-feeds.php';
    if (!get_option('zynith_seo_enable_wp_generator_tag'))          require_once ZYNITH_SEO_DIR . 'performance/disable-wp-generator-tags.php';
    if (!get_option('zynith_seo_enable_gutenberg'))                 require_once ZYNITH_SEO_DIR . 'performance/disable-gutenberg.php';
    if (!get_option('zynith_seo_disable_comments'))                 require_once ZYNITH_SEO_DIR . 'ui/disable-comments.php';    
    if (!get_option('zynith_seo_disable_svg_uploads'))              require_once ZYNITH_SEO_DIR . 'media/enable-svg-upload.php';
    
    if (is_admin()) {
        if (!get_option('zynith_seo_disable_remove_dashboard_widgets')) require_once ZYNITH_SEO_DIR . 'admin/remove-dashboard-widgets.php';
        if (!get_option('zynith_seo_disable_admin_bar_transition'))     require_once ZYNITH_SEO_DIR . 'ui/admin-bar-transition.php';
        if (!get_option('zynith_seo_disable_admin_bar_resources'))      require_once ZYNITH_SEO_DIR . 'ui/disable-admin-bar-resources.php';
        if (!get_option('zynith_seo_disable_admin_title_search'))       require_once ZYNITH_SEO_DIR . 'content-management/admin-title-search.php';
        if (!get_option('zynith_seo_enable_footer_customizer'))         require_once ZYNITH_SEO_DIR . 'ui/footer-customizer.php';
        if (!get_option('zynith_seo_move_plugin_file_editor'))          require_once ZYNITH_SEO_DIR . 'ui/move-plugin-file-editor.php';
        if (!get_option('zynith_seo_move_theme_file_editor'))           require_once ZYNITH_SEO_DIR . 'ui/move-theme-file-editor.php';
        if (!get_option('zynith_seo_enable_rest_api'))                  require_once ZYNITH_SEO_DIR . 'performance/disable-rest-api.php';
        if (get_option('zynith_seo_autosave_interval')      != 60)      require_once ZYNITH_SEO_DIR . 'performance/limit-autosave-intervals.php';
        if (get_option('zynith_seo_revision_limit')         != -1)      require_once ZYNITH_SEO_DIR . 'performance/limit-revisions.php';
        if (get_option('zynith_seo_heartbeat_frequency')    != 15)      require_once ZYNITH_SEO_DIR . 'performance/wordpress-heartbeat-optimizer.php';
    }
    
    require_once ZYNITH_SEO_DIR . 'ui/custom-admin-url.php';
}
add_action('plugins_loaded', 'zynith_seo_plugins_loaded');

function zynith_seo_init_files() {
    require_once ZYNITH_SEO_DIR . 'content-management/custom-data-sanitization.php';
    require_once ZYNITH_SEO_DIR . 'content-management/dynamic-placeholders.php';
    
    if (!get_option('zynith_seo_disable_meta_editor'))              require_once ZYNITH_SEO_DIR . 'seo-tools/meta-editor.php';
}
add_action('init', 'zynith_seo_init_files');

function zynith_seo_run_migration_once_only() {
    $zynith_seo_migration_version = 8; // 10.1.9
    $this_migration_version = (int) get_option('zynith_seo_migration_version', 0);
    
    if ($this_migration_version >= $zynith_seo_migration_version) return;

    global $wpdb;
    
    if ($this_migration_version < 7) {

        // Update old options with new options
        $option_mappings = [
            'business_name'             => 'zynith_seo_company_name',
            'zynith_business_name'      => 'zynith_seo_company_name',
            'street_address'            => 'zynith_seo_business_address',
            'zynith_street_address'     => 'zynith_seo_business_address',
            'business_email'            => 'zynith_seo_business_email',
            'zynith_business_email'     => 'zynith_seo_business_email',
            'phone_number'              => 'zynith_seo_business_phone',
            'zynith_phone_number'       => 'zynith_seo_business_phone',
            'address_locality'          => 'zynith_seo_business_locality',
            'zynith_address_locality'   => 'zynith_seo_business_locality',
            'address_region'            => 'zynith_seo_business_region',
            'zynith_address_region'     => 'zynith_seo_business_region',
            'postal_code'               => 'zynith_seo_business_postal_code',
            'zynith_postal_code'        => 'zynith_seo_business_postal_code',
            'address_country'           => 'zynith_seo_country',
            'zynith_address_country'    => 'zynith_seo_country',
            'logo'                      => 'zynith_seo_business_logo_url',
            'zynith_logo'               => 'zynith_seo_business_logo_url'
        ];
        foreach ($option_mappings as $old_option => $new_option) {
            $value = get_option($old_option, null);
            if (null !== $value) {
                // If this is the logo field, check if the value is numeric => convert to full URL
                if (in_array($old_option, ['logo', 'zynith_logo'], true) && is_numeric($value)) {
                    $attachment_url = wp_get_attachment_url((int) $value);
                    if ($attachment_url) $value = $attachment_url;
                }
                update_option($new_option, $value);
                delete_option($old_option);
            }
        }

        // Update inverted old options
        $inverted_options = [
            'zynith_enable_404_monitor'      => 'zynith_seo_disable_404_monitor',
            'zynith_enable_script_manager'   => 'zynith_seo_disable_script_manager',
        ];        
        foreach ($inverted_options as $old_option => $new_option) {
            $old_value = get_option($old_option, null);
            if (null !== $old_value) {
                $new_value = ((int) $old_value === 1) ? 0 : 1;
                update_option($new_option, $new_value);
                delete_option($old_option);
            }
        }

        // Rename or transform postmeta keys (Direct DB rename, so no leftover postmeta for old_meta remains.)
        $meta_mappings = [
            '_custom_meta_title'        => '_zynith_seo_meta_title',
            '_custom_meta_description'  => '_zynith_seo_meta_description',
            '_custom_noindex'           => '_zynith_seo_no_index',
            '_custom_nofollow'          => '_zynith_seo_no_follow',
            '_custom_meta_og_image'     => '_zynith_seo_og_meta_image'
        ];
        foreach ($meta_mappings as $old_meta => $new_meta) {
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->postmeta} 
                     SET meta_key = %s 
                     WHERE meta_key = %s",
                    $new_meta,
                    $old_meta
                )
            );
        }
        
        // Convert "yes"/"no" => "1"/"" for _zynith_seo_no_index
        $wpdb->query("
            UPDATE {$wpdb->postmeta}
            SET meta_value = '1'
            WHERE meta_key = '_zynith_seo_no_index' AND meta_value = 'yes'
        ");
        $wpdb->query("
            UPDATE {$wpdb->postmeta}
            SET meta_value = ''
            WHERE meta_key = '_zynith_seo_no_index' AND meta_value = 'no'
        ");

        // Convert "yes"/"no" => "1"/"" for _zynith_seo_no_follow
        $wpdb->query("
            UPDATE {$wpdb->postmeta}
            SET meta_value = '1'
            WHERE meta_key = '_zynith_seo_no_follow' AND meta_value = 'yes'
        ");
        $wpdb->query("
            UPDATE {$wpdb->postmeta}
            SET meta_value = ''
            WHERE meta_key = '_zynith_seo_no_follow' AND meta_value = 'no'
        ");
        
        // Migrate _custom_schema → *_zynith_schema_settings table
        $table_name = $wpdb->prefix . 'zynith_schema_settings';

        // Create the schema table
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            if (function_exists('zynith_seo_access_log')) {
                zynith_seo_create_schema_table();
            }
            else {
                return;
            }
        }
        
        // Get all meta rows where meta_key = '_custom_schema'
        $schema_rows = $wpdb->get_results("
            SELECT post_id, meta_value
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_custom_schema'
        ");

        if (!empty($schema_rows)) {
            foreach ($schema_rows as $row) {
                $post_id     = (int) $row->post_id;
                $schema_data = $row->meta_value;

                // Check if we already have an entry in the new table
                $existing_id = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT id FROM $table_name WHERE page_id = %d",
                        $post_id
                    )
                );

                // Insert or update
                if ($existing_id) {
                    // Update existing row
                    $wpdb->update(
                        $table_name,
                        ['schema_data' => $schema_data],
                        ['id'          => $existing_id],
                        ['%s'],
                        ['%d']
                    );
                }
                else {
                    // Insert new row
                    $wpdb->insert(
                        $table_name,
                        [
                            'page_id'     => $post_id,
                            'schema_data' => $schema_data
                        ],
                        ['%d', '%s']
                    );
                }
            }

            // Remove old _custom_schema meta to keep DB clean (optional)
            $wpdb->query(
                "DELETE FROM {$wpdb->postmeta}
                 WHERE meta_key = '_custom_schema'"
            );
        }

        // Migrate old script table data to new table.
        $old_table_name = $wpdb->prefix . 'zynith_snippets';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$old_table_name}'");
        if ($table_exists === $old_table_name) {
            if (function_exists('zynith_seo_access_log')) {
                zynith_seo_create_script_table();
            }
            else {
                return;
            }
        }

        // Delete old sitemap files if they exist
        $sitemap_filename = get_option('zynith_custom_sitemap_filename', 'sitemap.xml');
        if ($sitemap_filename) {
            $sitemap_path = ABSPATH . $sitemap_filename;
            if (file_exists($sitemap_path)) unlink($sitemap_path);

            // Also check for matching .xsl file
            $sitemap_xsl_path = ABSPATH . str_replace('.xml', '.xsl', $sitemap_filename);
            if (file_exists($sitemap_xsl_path)) unlink($sitemap_xsl_path);
        }
    
    }
    
    if ($this_migration_version < 8) {
            
        // Remove old options that are no longer used at all
        $unused_options = [
            'zynith_signals_instructions',
            'zynith_show_quick_ryter',
            'zynith_enable_metabox_warnings',
            'zynith_allow_auto_meta',
            'zynith_seo_signals_enabled',
            'zynith_seo_disable_placeholders'
        ];
        foreach ($unused_options as $old_option) delete_option($old_option);
    }
    
    // Set a flag to indicate migration is complete
    update_option('zynith_seo_migration_version', $zynith_seo_migration_version);
}

function zynith_seo_load_admin_features_only() {
    require_once ZYNITH_SEO_DIR . 'dashboard/zynith-dashboard-information-widget.php';
    require_once ZYNITH_SEO_DIR . 'ui/admin-light-theme.php';
    
    $tbyb = get_option('zynith_seo_tbyb', '');
    if ($tbyb == 'expired') return;
    
    zynith_seo_run_migration_once_only();
    
    require_once ZYNITH_SEO_DIR . 'dashboard/zynith-meta-copy-widget.php';
    
    if (!get_option('zynith_SEO_disable_image_alt'))    require_once ZYNITH_SEO_DIR . 'content-management/add-alt-text.php';
}
add_action('admin_init', 'zynith_seo_load_admin_features_only');

function zynith_seo_get_option_descriptions() {
    return [
        'zynith_seo_disable_meta_editor'                => 'Disables the meta editor for individual posts/pages.',
        'zynith_seo_disable_bulk_meta_editor'           => 'Disables the bulk meta editor, preventing bulk editing of metadata for posts/pages.',
        'zynith_seo_disable_meta_robots'                => 'Disables the ability to control meta robots tags for posts and pages.',
        'zynith_seo_disable_robots_text_editor'         => 'Disables the robots.txt file editor.',
        'zynith_seo_disable_sitemap_generator'          => 'Disables the generation of XML sitemaps for search engines.',
        'zynith_seo_disable_404_monitor'                => 'Disables the 404 monitor, which tracks missing pages on the site.',
        'zynith_seo_disable_script_manager'             => 'Disables the script manager for handling custom scripts on the site.',
        'zynith_seo_disable_search_replace'             => 'Disables the search and replace tool for bulk content changes.',
        'zynith_seo_disable_randomize_dates'            => 'Disables the feature for randomizing post/page dates for SEO purposes.',
        'zynith_seo_disable_htaccess_file_editor'       => 'Disables the .htaccess file editor for advanced server configuration.',        
        'zynith_seo_disable_remove_dashboard_widgets'   => 'Prevents the plugin from removing default WordPress dashboard widgets.',
        'zynith_seo_disable_admin_title_search'         => 'Disables the ability to search posts/pages by title in the admin.',
        'zynith_seo_disable_breadcrumb_editor'          => 'Disables the breadcrumb editor used for modifying breadcrumb trails.',
        'zynith_seo_disable_toc_editor'                 => 'Disables the table of contents (ToC) editor functionality.',
        'zynith_seo_disable_cpt_editor'                 => 'Disables the custom post type editor, preventing modifications to CPT settings.',
        'zynith_seo_disable_on_page_schema_editor'      => 'Disables the on-page schema editor for individual posts and pages.',
        'zynith_seo_disable_automatic_schema_editor'    => 'Disables the automatic schema generator editor used for adding structured data to posts/pages.',
        'zynith_seo_disable_svg_uploads'                => 'Prevents the ability to upload SVG images to the Media Library.',
        'zynith_seo_disable_light_mode'                 => 'Disables the modern light mode in the WordPress admin interface.',
        'zynith_seo_disable_admin_bar_transition'       => 'Restores the default admin bar transition effect (disabled by default for performance).',
        'zynith_seo_disable_admin_bar_resources'        => 'Restores all WordPress admin bar resources (disabled by default to reduce overhead).',
        'zynith_seo_disable_comments'                   => 'Restores comments functionality site-wide, including on posts and pages (disabled by default).',
        'zynith_seo_enable_footer_customizer'           => 'Restores the default WordPress footer.',  
        'zynith_seo_move_plugin_file_editor'            => 'Restores the plugin file editor to its default location in the Tools menu (moved to Plugins by default).',
        'zynith_seo_move_theme_file_editor'             => 'Restores the theme file editor to its default location in the Tools menu (moved to Appearance by default).',
        'zynith_seo_deferment_manager'                  => 'Disables the Deferment Manager.',                      
        'zynith_seo_enable_gutenberg'                   => 'Enables the Gutenberg editor.',
        'zynith_seo_enable_rest_api'                    => 'Enables REST API endpoints, allowing integration with external tools and apps.',
        'zynith_seo_enable_rss'                         => 'Enables RSS feed generation for the site, allowing users to subscribe to updates.',
        'zynith_seo_enable_wp_generator_tag'            => 'Enables the WordPress generator meta tag in the site’s HTML source.',
        'zynith_SEO_disable_bulk_noindex'               => 'Removes the “Mark as Noindex/Index” options from the bulk-edit dropdown in the All Posts/Pages list.',
        'zynith_SEO_disable_image_alt'                  => 'Disables the automatic alt text generator for new image uploads, requiring manual alt text entry.',
        'zynith_SEO_disable_canonical_url'              => 'Removes the Canonical URL meta field, preventing this plugin from setting canonical tags.',
        
        'zynith_SEO_disable_redirect_manager'           => 'Disables the Redirect Manager.',
        
        'zynith_seo_autosave_interval'                  => 'Sets the interval (in seconds) for autosaving post/page edits in WordPress. For informational sites, a higher interval (e.g., 300-600 seconds) reduces server load. For e-commerce sites, a lower interval (e.g., 60-120 seconds) ensures frequent autosaves for real-time data.',
        'zynith_seo_revision_limit'                     => 'Limits the number of post revisions saved by WordPress. Use -1 for unlimited revisions. Informational sites benefit from unlimited revisions for content editing, while e-commerce sites may prefer limiting this (e.g., 5-10) to reduce database size.',
        'zynith_seo_heartbeat_frequency'                => 'Sets the frequency (in seconds) of the WordPress Heartbeat API for background tasks. For e-commerce sites, a lower frequency (e.g., 15-30 seconds) ensures real-time updates for carts or stock levels. For informational sites, a higher frequency (e.g., 60-120 seconds) reduces server requests and improves performance.'
    ];
}

function zynith_seo_toggle_field_callback($args) {
    $option_name = $args['label_for'];
    $checked = get_option($option_name) ? 'checked' : '';
    $descriptions = zynith_seo_get_option_descriptions();
    $description = isset($descriptions[$option_name]) ? $descriptions[$option_name] : '';
    echo "<label class='zynith-toggle-switch'>
            <input type='checkbox' id='{$option_name}' name='{$option_name}' value='1' {$checked} />
            <span class='zynith-slider'></span>
          </label>";
    if ($description) echo "<span class='description'>{$description}</span>";
}

function zynith_seo_input_field_callback($args) {
    $option_name = $args['label_for'];
    $value = get_option($option_name, '');
    $descriptions = zynith_seo_get_option_descriptions();
    $description = isset($descriptions[$option_name]) ? $descriptions[$option_name] : '';
    echo "<input type='number' id='{$option_name}' name='{$option_name}' value='{$value}' min='-1' style='margin: 0 10px 0 0;' />";
    if ($description) echo "<span class='description'>{$description}</span>";
}

function zynith_seo_register_settings() {
   
    // Boolean settings
    register_setting('zynith_seo', 'zynith_seo_disable_meta_editor',                ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_meta_robots',                ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_SEO_disable_canonical_url',              ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_bulk_meta_editor',           ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_SEO_disable_bulk_noindex',               ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_robots_text_editor',         ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_sitemap_generator',          ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_404_monitor',                ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_SEO_disable_redirect_manager',           ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_search_replace',             ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_randomize_dates',            ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_htaccess_file_editor',       ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    
    register_setting('zynith_seo', 'zynith_seo_disable_remove_dashboard_widgets',   ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
        
    register_setting('zynith_seo', 'zynith_seo_disable_admin_title_search',         ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_breadcrumb_editor',          ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_toc_editor',                 ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_cpt_editor',                 ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    
    register_setting('zynith_seo', 'zynith_seo_disable_automatic_schema_editor',    ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_on_page_schema_editor',      ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_SEO_disable_image_alt',                  ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_svg_uploads',                ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    
    register_setting('zynith_seo', 'zynith_seo_disable_light_mode',                 ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_script_manager',             ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_deferment_manager',                  ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);  
    register_setting('zynith_seo', 'zynith_seo_disable_admin_bar_transition',       ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_admin_bar_resources',        ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_disable_comments',                   ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_enable_footer_customizer',           ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_move_plugin_file_editor',            ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_move_theme_file_editor',             ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']); 
    
    register_setting('zynith_seo', 'zynith_seo_enable_gutenberg',                   ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_enable_rest_api',                    ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_enable_rss',                         ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting('zynith_seo', 'zynith_seo_enable_wp_generator_tag',            ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);

    // Input field settings
    register_setting('zynith_seo', 'zynith_seo_autosave_interval',                  ['type' => 'integer', 'sanitize_callback' => 'absint']);
    register_setting('zynith_seo', 'zynith_seo_heartbeat_frequency',                ['type' => 'integer', 'sanitize_callback' => 'absint']);
    register_setting('zynith_seo', 'zynith_seo_revision_limit', [
        'type'              => 'integer',
        'sanitize_callback' => function($input) {
            $input = (int)$input;  // Force integer conversion first
            return ($input === -1 || $input === 0 || $input > 0) ? $input : -1;
        }
    ]);

    // Define the sections
    add_settings_section(
        'zynith_seo_seo_tools_section', // Section ID
        'SEO Tools',                    // Section Title <h2>
        '__return_false',               // Callback to display description (optional): 'zynith_seo_seo_tools_cb'
        'zynith_seo'                    // The slug used in do_settings_sections()
    );
    add_settings_section(
        'zynith_seo_ui_section',
        'WordPress Admin UI Settings',
        '__return_false',
        'zynith_seo'
    );
    add_settings_section(
        'zynith_seo_performance_section',
        'WordPress Performance Settings',
        '__return_false',               // 'zynith_seo_performance_section_cb'
        'zynith_seo'
    );
        
    // Boolean toggle fields
    add_settings_field('zynith_seo_disable_meta_editor', 'Disable Meta Editor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_meta_editor']);
    add_settings_field('zynith_seo_disable_meta_robots', 'Disable Meta Robots', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_meta_robots']);
    add_settings_field('zynith_SEO_disable_canonical_url', 'Disable Canonical URL Meta', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_SEO_disable_canonical_url']);
    add_settings_field('zynith_seo_disable_bulk_meta_editor', 'Disable Bulk Meta Editor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_bulk_meta_editor']);
    add_settings_field('zynith_SEO_disable_bulk_noindex', 'Disable Bulk Indexing', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_SEO_disable_bulk_noindex']);
    add_settings_field('zynith_SEO_disable_image_alt', 'Disable Automatic Alt Text', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_SEO_disable_image_alt']);
    add_settings_field('zynith_seo_disable_robots_text_editor', 'Disable Global Robots Settings', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_robots_text_editor']);
    add_settings_field('zynith_seo_disable_sitemap_generator', 'Disable Sitemap Generator', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_sitemap_generator']);
    add_settings_field('zynith_seo_disable_404_monitor', 'Disable 404 Monitor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_404_monitor']);
    add_settings_field('zynith_SEO_disable_redirect_manager', 'Disable Redirect Manager', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_SEO_disable_redirect_manager']);
    add_settings_field('zynith_seo_disable_search_replace', 'Disable Search and Replace', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_search_replace']);
    add_settings_field('zynith_seo_disable_randomize_dates', 'Disable Random Date Editor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_randomize_dates']);
    add_settings_field('zynith_seo_disable_breadcrumb_editor', 'Disable Breadcrumb Editor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_breadcrumb_editor']);
    add_settings_field('zynith_seo_disable_toc_editor', 'Disable ToC Editor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_toc_editor']);
    add_settings_field('zynith_seo_disable_cpt_editor', 'Disable Custom Post Types', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_cpt_editor']);
    add_settings_field('zynith_seo_disable_automatic_schema_editor', 'Disable Automatic Schema Editor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_automatic_schema_editor']);
    add_settings_field('zynith_seo_disable_on_page_schema_editor', 'Disable On-Page Schema Editor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_on_page_schema_editor']);
    add_settings_field('zynith_seo_disable_script_manager', 'Disable Script Manager', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_script_manager']);
    add_settings_field('zynith_seo_disable_comments', 'Disable Comments', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_comments']);
    
    if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false) {
        add_settings_field('zynith_seo_disable_htaccess_file_editor', 'Disable .htaccess File Editor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_seo_tools_section', ['label_for' => 'zynith_seo_disable_htaccess_file_editor']);
    }
    
    // Admin UI Settings
    add_settings_field('zynith_seo_disable_svg_uploads', 'Prevent SVG Uploads', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_ui_section', ['label_for' => 'zynith_seo_disable_svg_uploads']);
    
    add_settings_field('zynith_seo_disable_remove_dashboard_widgets', 'Default WordPress Dashboard', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_ui_section', ['label_for' => 'zynith_seo_disable_remove_dashboard_widgets']);
    add_settings_field('zynith_seo_disable_admin_bar_transition', 'Disable Admin Bar Transition', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_ui_section', ['label_for' => 'zynith_seo_disable_admin_bar_transition']);
    add_settings_field('zynith_seo_disable_admin_bar_resources', 'Disable Admin Bar Resources', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_ui_section', ['label_for' => 'zynith_seo_disable_admin_bar_resources']);
    add_settings_field('zynith_seo_disable_light_mode', 'Disable Light Mode', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_ui_section', ['label_for' => 'zynith_seo_disable_light_mode']);
    add_settings_field('zynith_seo_enable_gutenberg', 'Enable Gutenberg', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_ui_section', ['label_for' => 'zynith_seo_enable_gutenberg']);
    add_settings_field('zynith_seo_disable_admin_title_search', 'Disable Page Search by Title', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_ui_section', ['label_for' => 'zynith_seo_disable_admin_title_search']);
    add_settings_field('zynith_seo_enable_footer_customizer', 'Enable Footer Customizer', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_ui_section', ['label_for' => 'zynith_seo_enable_footer_customizer']);
    add_settings_field('zynith_seo_move_plugin_file_editor', 'Move Plugin File Editor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_ui_section', ['label_for' => 'zynith_seo_move_plugin_file_editor']);
    add_settings_field('zynith_seo_move_theme_file_editor', 'Move Theme File Editor', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_ui_section', ['label_for' => 'zynith_seo_move_theme_file_editor']);

    // Performance Settings
    add_settings_field('zynith_seo_deferment_manager', 'Disable Deferment Manager', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_performance_section', ['label_for' => 'zynith_seo_deferment_manager']);
    add_settings_field('zynith_seo_enable_rest_api', 'Enable REST API', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_performance_section', ['label_for' => 'zynith_seo_enable_rest_api']);
    add_settings_field('zynith_seo_enable_rss', 'Enable RSS Feeds', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_performance_section', ['label_for' => 'zynith_seo_enable_rss']);
    add_settings_field('zynith_seo_enable_wp_generator_tag', 'Enable WP Generator Tag', 'zynith_seo_toggle_field_callback', 'zynith_seo', 'zynith_seo_performance_section', ['label_for' => 'zynith_seo_enable_wp_generator_tag']);
        
    // Input fields for settings
    add_settings_field('zynith_seo_autosave_interval', 'Autosave Interval (seconds)', 'zynith_seo_input_field_callback', 'zynith_seo', 'zynith_seo_performance_section', ['label_for' => 'zynith_seo_autosave_interval']);
    add_settings_field('zynith_seo_revision_limit', 'Revision Limit', 'zynith_seo_input_field_callback', 'zynith_seo', 'zynith_seo_performance_section', ['label_for' => 'zynith_seo_revision_limit']);
    add_settings_field('zynith_seo_heartbeat_frequency', 'Heartbeat API Frequency (seconds)', 'zynith_seo_input_field_callback', 'zynith_seo', 'zynith_seo_performance_section', ['label_for' => 'zynith_seo_heartbeat_frequency']);
}
add_action('admin_init', 'zynith_seo_register_settings');

// Add Zynith SEO dashboard and settings to the admin menu
function zynith_seo_add_admin_menu() {
    if (!defined('ZYNITH_SEO_ICON')) define('ZYNITH_SEO_ICON', 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIGlkPSJMYXllcl8yIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2aWV3Qm94PSIwIDAgMTguNzQgMjAiPjxkZWZzPjxzdHlsZT4uY2xzLTF7ZmlsbDp1cmwoI2xpbmVhci1ncmFkaWVudCk7c3Ryb2tlLXdpZHRoOjBweDt9PC9zdHlsZT48bGluZWFyR3JhZGllbnQgaWQ9ImxpbmVhci1ncmFkaWVudCIgeDE9Ii0yLjQ1IiB5MT0iLTUxLjkiIHgyPSIxNi4yOSIgeTI9Ii01MS45IiBncmFkaWVudFRyYW5zZm9ybT0idHJhbnNsYXRlKDAgLTQxLjkpIHNjYWxlKDEgLTEpIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjMjg1YmQxIi8+PHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjZWEzM2YyIi8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PGcgaWQ9IkxheWVyXzItMiI+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJNOC43LDE1LjUzQzEyLjAxLDEwLjQyLDE1LjMyLDUuMzEsMTguNjMuMiwxMi43Ny4xMiw4LjM4LjEsNi4yOC4wMWMtLjQxLS4wMi0xLjY1LS4wNy0zLjA3LjQ2LS41NS4yMS0xLjMuNS0xLjk3LDEuMkMuMjEsMi43NC4wNCw0LjA3LDAsNC42MWgxMC4wMkM2LjcsOS43NCwzLjM4LDE0Ljg3LjA2LDIwaDEzLjY5Yy41NC0uMDEsMi4xOS0uMTIsMy41My0xLjMyLjMzLS4yOS45OC0uODksMS4yOS0xLjkxLjE1LS40Ny4xOC0uODkuMTgtMS4xNy0zLjM1LS4wMi02LjctLjA0LTEwLjA1LS4wNmgwWk0xNi45NSwxNy43aDBzLS4zOC40MS0uODQuNzNjLTEuMTQuOC0zLjA3Ljg1LTMuMDcuODUtMS4yMy4wMy01LjU3LjEyLTExLjYxLjAyQzYuNywxMS4yMiwxMC41MSw1LjM5LDEwLjgsNS4wMWMuMDMtLjA1LjItLjI1LjE5LS41MiwwLS4wMy0uMDEtLjI4LS4xOS0uNDUtLjIzLS4yMi0uNTktLjItLjY5LS4xOS0uNi4wNC00LjIzLjA2LTkuMjMuMS4xNC0uNDguNTctMS42OSwxLjcxLTIuNC43OC0uNDgsMS43MS0uNjIsMi4wOS0uNjcuMzYtLjA1LjY3LS4wNy44OS0uMDcsMy44My4wOCw3LjY2LjE3LDExLjQ4LjI1LTMuMDksNC43My02LjE4LDkuNDctOS4yNiwxNC4yLS4wMy4wOS0uMS4zMS0uMDQuNTguMDIuMDcuMDguMzIuMjkuNTEuMDcuMDYuMjUuMTkuNTguMjIuNTIuMDQsNC4wNS4wNSw4LjksMC0uMDguMjctLjI0LjctLjU4LDEuMTNoMFoiLz48L2c+PC9zdmc+');
    
    add_menu_page(
        'Zynith SEO Dashboard',
        'Zynith SEO',
        'manage_options',
        'zynith_seo_dashboard',
        'zynith_seo_dashboard_page',
        ZYNITH_SEO_ICON,
        60
    );
    $tbyb = get_option('zynith_seo_tbyb', '');
    if ($tbyb == 'expired') return;
    add_submenu_page(
        'zynith_seo_dashboard',
        'Settings',
        'Settings',
        'manage_options',
        'zynith_seo_settings',
        'zynith_seo_settings_page'
    );
}
add_action('admin_menu', 'zynith_seo_add_admin_menu');

// Display the main dashboard page for Zynith SEO
function zynith_seo_dashboard_page() {
    ?>
    <div class="zynith-dashboard-header">
        <h1>Zynith SEO Dashboard</h1>
        <p>Welcome to the Zynith SEO Dashboard. Here you can see an overview and access important tools for your SEO needs.</p>
    </div>
    <div class="zynith-widgets-container">
        <div class="zynith-widget" id="widget-zynith-info">
            <h2>Zynith SEO Information</h2>
            <?php zynith_dashboard_widget_display(); ?>
        </div>
    <?php
    $tbyb = get_option('zynith_seo_tbyb', '');
    if ($tbyb != 'expired') {
    ?>
        <div class="zynith-widget" id="widget-meta-copy">
            <h2>Meta Copy</h2>
            <?php zynith_meta_copy_widget_display(); ?>
        </div>
    <?php } ?>
    </div>
    <style>
        .zynith-widgets-container {
            display: flex;
            gap: 20px;
        }
        .zynith-widget {
            flex: 1;
            padding: 15px;
            border: 1px solid #e2e4e7;
            border-radius: 8px;
            background-color: #f9fafb;
            box-sizing: border-box;
        }
        @media (max-width: 768px) {
            .zynith-widgets-container {
                flex-direction: column;
                width: 98.6%;
            }
        }
    </style>
    <?php
}

// Display the settings page for Zynith SEO
function zynith_seo_settings_page() {
    ?>
    <div class="wrap">
        <h1>Zynith SEO Settings</h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('zynith_seo');
            do_settings_sections('zynith_seo');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Hide the plugin from the plugins screen
add_action('pre_current_active_plugins', 'zynith_seo_hide_from_plugins');
function zynith_seo_hide_from_plugins() {
    global $wp_list_table;
    $hide_plugins = array('zynith-seo.php');
    foreach ($wp_list_table->items as $key => $val) if (in_array($key, $hide_plugins)) unset($wp_list_table->items[$key]);
}
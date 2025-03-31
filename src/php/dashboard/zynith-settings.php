<?php
defined('ABSPATH') or exit;

global $zynith_seo_settings;
$zynith_seo_settings = [
    [
        'name'             => 'On-page Meta Editor',
        'description'      => "Adds a meta box to pages and posts for editing HTML meta tags (title, description, OG tags). Note: This must be enabled for the 'Bulk Meta Editing' feature.",
        'option'           => 'zynith_seo_meta_editor',
        'option_type'      => 'boolean',
        'settings_section'  => 'zynith_seo_seo_tools_section'
    ],  // 1. On-page Meta Editor    
    [
        'name'             => 'On-page Meta Robots',
        'description'      => "Adds a side meta box to pages and posts for setting noindex/nofollow directives. Note: This must be enabled for the 'Bulk Indexing' feature.",
        'option'           => 'zynith_seo_meta_robots',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 2. On-page Meta Robots
    [
        'name'             => 'On-page Canonical URL',
        'description'      => 'Adds a meta box to specify alternate canonical URLs, helping manage duplicate content.',
        'option'           => 'zynith_seo_canonical_url',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 3. On-page Canonical URL
    [
        'name'             => 'On-page Schema Editor',
        'description'      => 'Adds a meta box to pages and posts for entering custom JSON‑LD schema markup.',
        'option'           => 'zynith_seo_on_page_schema_editor',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 4. On-page Schema Editor
    [
        'name'             => 'Bulk Meta Editing',
        'description'      => "Lets you edit title and meta description fields directly in 'All Pages' and 'All Posts.'",
        'option'           => 'zynith_seo_bulk_meta_editor',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 6. Bulk Meta Editing
    [
        'name'             => 'Bulk Indexing',
        'description'      => 'Adds a noindex/index toggle in “All Pages” and “All Posts” for quick indexing control.',
        'option'           => 'zynith_seo_bulk_indexing',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 7. Bulk Indexing
    [
        'name'             => 'Automatic Schema Editor',
        'description'      => 'Adds a settings page to automatically apply schema markup for supported post types.',
        'option'           => 'zynith_seo_automatic_schema_editor',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 8. Automatic Schema Editor
    [
        'name'             => 'Automatic Alt Text',
        'description'      => 'Automatically generates alt text for newly uploaded images, improving accessibility and SEO.',
        'option'           => 'zynith_seo_auto_alt_text',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 9. Automatic Alt Text
    [
        'name'             => 'Robots.txt Settings',
        'description'      => 'Allows you to edit and manage the site-wide robots.txt file.',
        'option'           => 'zynith_seo_robots_txt',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 10. Robots.txt Settings
    [
        'name'             => 'Sitemap Generator',
        'description'      => 'Adds a settings page for generating an XML sitemap so search engines can discover site pages.',
        'option'           => 'zynith_seo_sitemap_generator',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 11. Sitemap Generator
    [
        'name'             => '404 Monitor',
        'description'      => 'Monitors and logs 404 (Not Found) errors to help identify broken links.',
        'option'           => 'zynith_seo_404_monitor',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 12. 404 Monitor
    [
        'name'             => 'Redirect Manager',
        'description'      => 'Creates and manages 301/302 redirects for changed or removed URLs.',
        'option'           => 'zynith_seo_redirect_manager',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 13. Redirect Manager
    [
        'name'             => 'Search and Replace',
        'description'      => 'Performs bulk search/replace across content or database fields (use with caution).',
        'option'           => 'zynith_seo_search_replace',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 14. Search and Replace
    [
        'name'             => 'Randomize Post Dates',
        'description'      => 'Randomizes or modifies published/modified dates on posts/pages (use carefully).',
        'option'           => 'zynith_seo_randomize_post_dates',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 15. Randomize Post Dates
    [
        'name'             => 'Breadcrumb Shortcode',
        'description'      => 'Provides a shortcode to display breadcrumb trails for improved navigation and SEO.',
        'option'           => 'zynith_seo_breadcrumb_shortcode',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 16. Breadcrumb Shortcode
    [
        'name'             => 'ToC Shortcode',
        'description'      => 'Generates a table of contents for posts/pages via a shortcode, improving readability.',
        'option'           => 'zynith_seo_toc_shortcode',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 17. ToC Shortcode
    [
        'name'             => 'Custom Post Types',
        'description'      => 'Allows creation and management of custom post types from within the plugin.',
        'option'           => 'zynith_seo_custom_post_types',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 18. Custom Post Types
    [
        'name'             => 'Script Manager',
        'description'      => 'Inserts custom HTML/JS into the head, body, or footer of pages (e.g., analytics scripts).',
        'option'           => 'zynith_seo_script_manager',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 19. Script Manager
    [
        'name'             => 'Disable Comments',
        'description'      => 'Completely removes WordPress comment functionality throughout the site.',
        'option'           => 'zynith_seo_disable_comments',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 20. Disable Comments
    [
        'name'             => '.htaccess Editor',
        'description'      => 'Enables .htaccess editing via Zynith for advanced server configuration.',
        'option'           => 'zynith_seo_htaccess_editor',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section'
    ],  // 21. .htaccess Editor
    [
        'name'             => 'Allow SVG Uploads',
        'description'      => 'Allows uploading of SVG files to the Media Library (potential security risks if unsanitized).',
        'option'           => 'zynith_seo_svg_uploads',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section'
    ],  // 22. Allow SVG Uploads
    [
        'name'             => 'Clear WordPress Dashboard',
        'description'      => 'Hides the default WordPress dashboard widgets.',
        'option'           => 'zynith_seo_clear_wp_dashboard',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section'
    ],  // 23. Clear WordPress Dashboard
    [
        'name'             => 'Reduce Admin Bar Resources',
        'description'      => 'Prevents certain admin bar CSS/JS from loading to reduce overhead.',
        'option'           => 'zynith_seo_admin_bar_resources',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section'
    ],  // 24. Reduce Admin Bar Resources
    [
        'name'             => 'Prettify Admin Bar',
        'description'      => 'Hides the Screen Options and Help menus in the WordPress admin, revealing them only when you hover over the admin bar.',
        'option'           => 'zynith_seo_prettify_admin_bar',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section'
    ],  // 25. Prettify Admin Bar
    [
        'name'             => 'Prettify Admin Styling',
        'description'      => 'Switches the admin interface to a lighter color scheme.',
        'option'           => 'zynith_seo_prettify_admin_styling',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section'
    ],  // 26. Prettify Admin Styling
    [
        'name'             => 'Disable Gutenberg',
        'description'      => 'Disables the Gutenberg block editor and reverts to the Classic Editor.',
        'option'           => 'zynith_seo_disable_gutenberg',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section'
    ],  // 27. Disable Gutenberg
    [
        'name'             => 'Page Search by Title',
        'description'      => 'Lets you search pages and posts by title in the Admin area.',
        'option'           => 'zynith_seo_admin_title_search',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section'
    ],  // 28. Page Search by Title
    [
        'name'             => 'Enable Zynith Admin Footer',
        'description'      => 'Replaces the default WordPress footer in every Admin page with a Zynith SEO footer.',
        'option'           => 'zynith_seo_admin_footer',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section'
    ],  // 29. Enable Zynith Admin Footer
    [
        'name'             => 'Move Plugin File Editor',
        'description'      => 'Moves the built‑in plugin file editor from Tools to the Plugins menu.',
        'option'           => 'zynith_seo_move_plugin_file_editor',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section'
    ],  // 30. Move Plugin File Editor
    [
        'name'             => 'Move Theme File Editor',
        'description'      => 'Moves the built‑in theme file editor from Tools to the Appearance menu.',
        'option'           => 'zynith_seo_move_theme_file_editor',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section'
    ],  // 31. Move Theme File Editor
    [
        'name'             => 'Custom Login URL',
        'description'      => 'Replaces the default /wp-login.php slug with a custom path for extra security.',
        'option'           => 'zynith_seo_custom_login_url',
        'option_type'      => 'custom_url',
        'settings_section' => 'zynith_seo_ui_section',
        'option_version'    => 9
    ],  // 32. Custom Login URL
    [
        'name'             => 'Deferment Manager',
        'description'      => 'Defers or delays loading of scripts to improve initial page load times.',
        'option'           => 'zynith_seo_deferment_manager',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_performance_section'
    ],  // 33. Deferment Manager
    [
        'name'             => 'Disable REST API',
        'description'      => 'Disables WordPress REST API endpoints for external integrations.',
        'option'           => 'zynith_seo_rest_api',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_performance_section'
    ],  // 34. Disable REST API
    [
        'name'             => 'Disable RSS Feeds',
        'description'      => 'Disables the site’s RSS feed.',
        'option'           => 'zynith_seo_rss_feed',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_performance_section'
    ],  // 35. Disable RSS Feeds
    [
        'name'             => 'Remove WP Generator Tag',
        'description'      => 'Removes the WordPress version meta tag from the site’s front-end HTML.',
        'option'           => 'zynith_seo_wp_generator_tag',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_performance_section'
    ],  // 36. Remove WP Generator Tag
    [
        'name'             => 'Autosave Interval (seconds)',
        'description'      => 'Controls how frequently WP autosaves posts/pages. Higher intervals reduce server load; lower intervals increase real-time saves.',
        'option'           => 'zynith_seo_autosave_interval',
        'option_type'      => 'integer',
        'settings_section' => 'zynith_seo_performance_section'
    ],  // 37. Autosave Interval (seconds)
    [
        'name'             => 'Revision Limit',
        'description'      => 'Limits how many post revisions WP retains (admin-side config).',
        'option'           => 'zynith_seo_revision_limit',
        'option_type'      => 'integer_limit',
        'settings_section' => 'zynith_seo_performance_section'
    ],  // 38. Revision Limit
    [
        'name'             => 'Heartbeat API Frequency (seconds)',
        'description'      => 'Sets how often the WP Heartbeat API pings the server for background tasks.',
        'option'           => 'zynith_seo_heartbeat_frequency',
        'option_type'      => 'integer',
        'settings_section' => 'zynith_seo_performance_section'
    ],  // 39. Heartbeat API Frequency (seconds)
];

function zynith_seo_register_settings() {
    global $zynith_seo_settings;
    
    // Register sections (one time)
    // (Section ID, Title, No extra text, The page slug for do_settings_sections)
    add_settings_section('zynith_seo_seo_tools_section', 'SEO Tools', '__return_false', 'zynith_seo');
    add_settings_section('zynith_seo_ui_section', 'WordPress Admin UI Settings', '__return_false', 'zynith_seo');
    add_settings_section('zynith_seo_performance_section', 'WordPress Performance Settings', '__return_false', 'zynith_seo');
    
    // Loop through each setting in master array
    foreach ($zynith_seo_settings as $setting) {
        
        // Skip if 'criteria' => [ 'type'=>'server', ... ] fails
        if (isset($setting['criteria']) && is_array($setting['criteria'])) {
            if (!empty($setting['criteria']['type']) && $setting['criteria']['type'] === 'server') {
                // We only skip if zynith_seo_check_criteria() returns false, e.g. if the server is NOT Apache => skip registering
                if (!zynith_seo_check_criteria($setting['criteria'])) continue;
            }
        }
        
        $option_name      = $setting['option'];
        $option_type      = $setting['option_type'];
        $settings_section = $setting['settings_section'];
        $field_label      = $setting['name'];
        
        // Determine the sanitize callback + type for register_setting
        $args = [];
        if ($setting['option_type'] === 'boolean') {
            $args = [
                'type'              => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
            ];
            
        }
        elseif ($setting['option_type'] === 'integer') {
            $args = [
                'type'              => 'integer',
                'sanitize_callback' => 'absint', 
            ];
        }
        elseif ($setting['option_type'] === 'integer_limit') {
            $args = [
                'type'              => 'integer',
                'sanitize_callback' => function($input) {
                    $input = (int)$input;  // Force integer conversion first
                    return ($input === -1 || $input === 0 || $input > 0) ? $input : -1;
                }
            ];
        }
        elseif ($setting['option_type'] === 'custom_url') {
            $args = [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ];
        }

        // Register the setting
        register_setting('zynith_seo', $option_name, $args);
                
        // Add settings fields
        $callback = null;
        if ($option_type === 'boolean') {
            $callback = 'zynith_seo_toggle_field_callback';
        }
        elseif ($option_type === 'integer' || $setting['option_type'] === 'integer_limit') {
            $callback = 'zynith_seo_input_field_callback';
        }
        elseif ($option_type === 'custom_url') {
            $callback = 'zynith_seo_custom_login_url_callback';
        }
        
        add_settings_field(
            $option_name,
            $field_label,
            $callback,
            'zynith_seo',
            $settings_section,
            ['label_for' => $option_name]
        );
        
    }
}
add_action('admin_init', 'zynith_seo_register_settings');

function zynith_seo_toggle_field_callback($args) {
    $option_name = $args['label_for'];

    // Check if this option is currently enabled (a truthy value in DB)
    $checked = get_option($option_name) ? 'checked' : '';
    
    echo "<label for='{$option_name}' class='zynith-toggle-switch'>
            <input type='checkbox' id='{$option_name}' name='{$option_name}' value='1' {$checked} />
            <span class='zynith-slider'></span>
          </label>";
    
    // Get the description from the $zynith_seo_settings array
    $description = zynith_seo_get_setting_description($option_name);
    if (!empty($description)) echo "<span class='zynith-tooltip-icon dashicons dashicons-editor-help'></span><div class='zynith-tooltip-content'>{$description}</div>";
}

function zynith_seo_input_field_callback($args) {
    $option_name = $args['label_for'];
    $value       = get_option($option_name, '');
    
    // Display the input field (number type)
    echo "<input type='number' id='{$option_name}' name='{$option_name}' value='{$value}' min='-1' style='margin: 0 10px 0 0;' />";
    
    // Get the description from the $zynith_seo_settings array
    $description = zynith_seo_get_setting_description($option_name);
    if (!empty($description)) echo "<span class='zynith-tooltip-icon dashicons dashicons-editor-help'></span><div class='zynith-tooltip-content'>{$description}</div>";
}

function zynith_seo_custom_login_url_callback($args) {
    $option_name    = $args['label_for'];
    $value          = get_option($option_name, '');
    
    echo "<input type='text' id='{$option_name}' name='{$option_name}' value='{$value}' placeholder='custom-login' style='margin: 0 10px 0 0;' />";
    
    // Get the description from the $zynith_seo_settings array
    $description = zynith_seo_get_setting_description($option_name);
    if (!empty($description)) echo "<span class='zynith-tooltip-icon dashicons dashicons-editor-help'></span><div class='zynith-tooltip-content'>{$description}</div>";
}

// Helper function to get the setting's description from $zynith_seo_settings.
function zynith_seo_get_setting_description($option_name) {
    global $zynith_seo_settings;
    foreach ($zynith_seo_settings as $setting) if (isset($setting['option']) && $setting['option'] === $option_name) return $setting['description'] ?? '';
    return '';
}

// Add Zynith SEO settings to the admin menu
function zynith_seo_add_settings_menu() {
    add_submenu_page(
        'zynith_seo_dashboard',
        'Settings',
        'Settings',
        'manage_options',
        'zynith_seo_settings',
        'zynith_seo_settings_page'
    );
}
add_action('admin_menu', 'zynith_seo_add_settings_menu');

// Display the settings page for Zynith SEO
function zynith_seo_settings_page() {
    ?>
    <div class="zynith-dashboard-header">
        <h1>Zynith SEO Settings</h1>
        <p>Configure or disable any of Zynith SEO’s modules and features here.</p>
    </div>
    <div class="zynith-widgets-container">
        <div class="zynith-widget">
            <form action="options.php" method="post" style="display: flex; flex-wrap: wrap; gap: 11px;">
            <?php settings_fields('zynith_seo'); ?>
            
                <!-- SEO Tools Section -->
                <div style="flex: 1 0.5;">
                    <h2>SEO Tools</h2>
                    <div style="padding: 15px; border: 1px solid #e2e4e7; border-radius: 8px; background-color: #f9fafb;">
                        <table class="form-table" style="margin: 0;">  
                            <tbody><?php do_settings_fields('zynith_seo', 'zynith_seo_seo_tools_section'); ?></tbody>                    
                        </table>
                    </div>
                    <?php submit_button(); ?>
                </div>
                <div style="flex: 1 0.5;">
                    <h2>WordPress Admin UI Settings</h2>
                    <div style="padding: 15px; border: 1px solid #e2e4e7; border-radius: 8px; background-color: #f9fafb;">
                        <table class="form-table" style="margin: 0;">  
                            <tbody><?php do_settings_fields('zynith_seo', 'zynith_seo_ui_section'); ?></tbody>                    
                        </table>
                    </div>
                    <h2>WordPress Performance Settings</h2>
                    <div style="padding: 15px; border: 1px solid #e2e4e7; border-radius: 8px; background-color: #f9fafb;">
                        <table class="form-table" style="margin: 0;">  
                            <tbody><?php do_settings_fields('zynith_seo', 'zynith_seo_performance_section'); ?></tbody>                    
                        </table>
                    </div>
                </div>
        </form>
        </div>
    </div>
    <?php
}
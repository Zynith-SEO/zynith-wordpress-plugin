<?php
defined('ABSPATH') or exit;

global $zynith_seo_settings;
$zynith_seo_settings = [
    [
        'name'             => 'On-page Meta Editor',
        'description'      => "Adds a meta box to pages and posts for editing HTML meta tags (title, description, OG tags). Note: This must be enabled for the 'Bulk Meta Editing' feature.",
        'file'             => 'seo-tools/meta-editor.php',
        'criteria'         => null,
        'default'          => 1,  // Enabled
        'option'           => 'zynith_seo_meta_editor',
        'old_option'       => 'zynith_seo_disable_meta_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section'  => 'zynith_seo_seo_tools_section', // Product Group: SEO Tools
        'admin_only'        => false,
        'option_version'    => 9
    ],  // 1. On-page Meta Editor    
    [
        'name'             => 'On-page Meta Robots',
        'description'      => "Adds a side meta box to pages and posts for setting noindex/nofollow directives. Note: This must be enabled for the 'Bulk Indexing' feature.",
        'file'             => 'seo-tools/meta-robots-settings.php',
        'criteria'         => null,
        'default'          => 1,  // Enabled
        'option'           => 'zynith_seo_meta_robots',
        'old_option'       => 'zynith_seo_disable_meta_robots',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 2. On-page Meta Robots
    [
        'name'             => 'On-page Canonical URL',
        'description'      => 'Adds a meta box to specify alternate canonical URLs, helping manage duplicate content.',
        'file'             => 'seo-tools/canonical-url-manager.php',
        'criteria'         => null,
        'default'          => 0,  // Disabled
        'option'           => 'zynith_seo_canonical_url',
        'old_option'       => 'zynith_SEO_disable_canonical_url',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 3. On-page Canonical URL
    [
        'name'             => 'On-page Schema Editor',
        'description'      => 'Adds a meta box to pages and posts for entering custom JSON‑LD schema markup.',
        'file'             => 'schema/on-page-schema-editor.php',
        'criteria'         => null,
        'default'          => 1,  // Enabled
        'option'           => 'zynith_seo_on_page_schema_editor',
        'old_option'       => 'zynith_seo_disable_on_page_schema_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 4. On-page Schema Editor
    /*
        // For a future release
    [
        'name'             => 'On-page Salience Analyzer (Premium add‑on)',
        'description'      => 'Provides a meta box to analyze the salience of page content using Google’s API.',
        'file'             => 'content-management/salience-analyzer.php',
        'criteria'         => null,
        'default'          => 0,  // Disabled
        'option'           => 'zynith_seo_salience_analyzer',
        'old_option'       => null,
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => null
    ],  // 5. On-page Salience Analyzer (Premium add-on)*/
    [
        'name'             => 'Bulk Meta Editing',
        'description'      => "Lets you edit title and meta description fields directly in 'All Pages' and 'All Posts.'",
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
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 6. Bulk Meta Editing
    [
        'name'             => 'Bulk Indexing',
        'description'      => 'Adds a noindex/index toggle in “All Pages” and “All Posts” for quick indexing control.',
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
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 7. Bulk Indexing
    [
        'name'             => 'Automatic Schema Editor',
        'description'      => 'Adds a settings page to automatically apply schema markup for supported post types.',
        'file'             => 'schema/automatic-schema-editor.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_automatic_schema_editor',
        'old_option'       => 'zynith_seo_disable_automatic_schema_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 8. Automatic Schema Editor
    [
        'name'             => 'Automatic Alt Text',
        'description'      => 'Automatically generates alt text for newly uploaded images, improving accessibility and SEO.',
        'file'             => 'content-management/add-alt-text.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_auto_alt_text',
        'old_option'       => 'zynith_seo_disable_image_alt',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 9. Automatic Alt Text
    [
        'name'             => 'Robots.txt Settings',
        'description'      => 'Allows you to edit and manage the site-wide robots.txt file.',
        'file'             => 'seo-tools/robots-txt-editor.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_robots_txt',
        'old_option'       => 'zynith_seo_disable_robots_text_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 10. Robots.txt Settings
    [
        'name'             => 'Sitemap Generator',
        'description'      => 'Adds a settings page for generating an XML sitemap so search engines can discover site pages.',
        'file'             => 'seo-tools/sitemap-generator.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_sitemap_generator',
        'old_option'       => 'zynith_seo_disable_sitemap_generator',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 11. Sitemap Generator
    [
        'name'             => '404 Monitor',
        'description'      => 'Monitors and logs 404 (Not Found) errors to help identify broken links.',
        'file'             => 'seo-tools/404-monitor.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_404_monitor',
        'old_option'       => 'zynith_seo_disable_404_monitor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 12. 404 Monitor
    [
        'name'             => 'Redirect Manager',
        'description'      => 'Creates and manages 301/302 redirects for changed or removed URLs.',
        'file'             => 'seo-tools/redirect-manager.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_redirect_manager',
        'old_option'       => 'zynith_seo_disable_redirect_manager',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 13. Redirect Manager
    [
        'name'             => 'Search and Replace',
        'description'      => 'Performs bulk search/replace across content or database fields (use with caution).',
        'file'             => 'seo-tools/search-and-replace.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_search_replace',
        'old_option'       => 'zynith_seo_disable_search_replace',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 14. Search and Replace
    [
        'name'             => 'Randomize Post Dates',
        'description'      => 'Randomizes or modifies published/modified dates on posts/pages (use carefully).',
        'file'             => 'seo-tools/randomize-last-modified-date.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_randomize_post_dates',
        'old_option'       => 'zynith_seo_disable_date_randomizer',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 15. Randomize Post Dates
    [
        'name'             => 'Breadcrumb Shortcode',
        'description'      => 'Provides a shortcode to display breadcrumb trails for improved navigation and SEO.',
        'file'             => 'content-management/breadcrumb-editor.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_breadcrumb_shortcode',
        'old_option'       => 'zynith_seo_disable_breadcrumb_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 16. Breadcrumb Shortcode
    [
        'name'             => 'ToC Shortcode',
        'description'      => 'Generates a table of contents for posts/pages via a shortcode, improving readability.',
        'file'             => 'content-management/toc-editor.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_toc_shortcode',
        'old_option'       => 'zynith_seo_disable_toc_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 17. ToC Shortcode
    [
        'name'             => 'Custom Post Types',
        'description'      => 'Allows creation and management of custom post types from within the plugin.',
        'file'             => 'content-management/custom-post-type.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_custom_post_types',
        'old_option'       => 'zynith_seo_disable_cpt_editor',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 18. Custom Post Types
    [
        'name'             => 'Script Manager',
        'description'      => 'Inserts custom HTML/JS into the head, body, or footer of pages (e.g., analytics scripts).',
        'file'             => 'seo-tools/script-manager.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_script_manager',
        'old_option'       => 'zynith_seo_disable_script_manager',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 19. Script Manager
    [
        'name'             => 'Disable Comments',
        'description'      => 'Completely removes WordPress comment functionality throughout the site.',
        'file'             => 'ui/disable-comments.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_disable_comments',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 20. Disable Comments
    [
        'name'             => '.htaccess Editor',
        'description'      => 'Enables .htaccess editing via Zynith for advanced server configuration.',
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
        'settings_section' => 'zynith_seo_seo_tools_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 21. .htaccess Editor
    [
        'name'             => 'Allow SVG Uploads',
        'description'      => 'Allows uploading of SVG files to the Media Library (potential security risks if unsanitized).',
        'file'             => 'media/enable-svg-upload.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_svg_uploads',
        'old_option'       => 'zynith_seo_disable_svg_uploads',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section', // WP Admin UI Settings
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 22. Allow SVG Uploads
    [
        'name'             => 'Clear WordPress Dashboard',
        'description'      => 'Hides the default WordPress dashboard widgets.',
        'file'             => 'admin/remove-dashboard-widgets.php',
        'criteria'         => null,

        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_clear_wp_dashboard',
        'old_option'       => 'zynith_seo_disable_remove_dashboard_widgets',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 23. Clear WordPress Dashboard
    [
        'name'             => 'Reduce Admin Bar Resources',
        'description'      => 'Prevents certain admin bar CSS/JS from loading to reduce overhead.',
        'file'             => 'ui/disable-admin-bar-resources.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_admin_bar_resources',
        'old_option'       => 'zynith_seo_disable_admin_bar_resources',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 24. Reduce Admin Bar Resources
    [
        'name'             => 'Prettify Admin Bar',
        'description'      => 'Hides the Screen Options and Help menus in the WordPress admin, revealing them only when you hover over the admin bar.',
        'file'             => 'ui/admin-bar-transition.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_prettify_admin_bar',
        'old_option'       => 'zynith_seo_disable_admin_bar_transition',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 25. Prettify Admin Bar
    [
        'name'             => 'Prettify Admin Styling',
        'description'      => 'Switches the admin interface to a lighter color scheme.',
        'file'             => 'ui/admin-light-theme.php',
        'criteria'         => 'Skip',
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_prettify_admin_styling',
        'old_option'       => 'zynith_seo_disable_light_mode',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 26. Prettify Admin Styling
    [
        'name'             => 'Disable Gutenberg',
        'description'      => 'Disables the Gutenberg block editor and reverts to the Classic Editor.',
        'file'             => 'performance/disable-gutenberg.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_disable_gutenberg',
        'old_option'       => 'zynith_seo_enable_gutenberg',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 27. Disable Gutenberg
    [
        'name'             => 'Page Search by Title',
        'description'      => 'Lets you search pages and posts by title in the Admin area.',
        'file'             => 'content-management/admin-title-search.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_admin_title_search',
        'old_option'       => 'zynith_seo_disable_admin_title_search',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 28. Page Search by Title
    [
        'name'             => 'Enable Zynith Admin Footer',
        'description'      => 'Replaces the default WordPress footer in every Admin page with a Zynith SEO footer.',
        'file'             => 'ui/footer-customizer.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_admin_footer',
        'old_option'       => 'zynith_seo_enable_footer_customizer',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 29. Enable Zynith Admin Footer
    [
        'name'             => 'Move Plugin File Editor',
        'description'      => 'Moves the built‑in plugin file editor from Tools to the Plugins menu.',
        'file'             => 'ui/move-plugin-file-editor.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_move_plugin_file_editor',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 30. Move Plugin File Editor
    [
        'name'             => 'Move Theme File Editor',
        'description'      => 'Moves the built‑in theme file editor from Tools to the Appearance menu.',
        'file'             => 'ui/move-theme-file-editor.php',
        'criteria'         => null,
        'default'          => 1, // Enabled
        'option'           => 'zynith_seo_move_theme_file_editor',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_ui_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 31. Move Theme File Editor
    [
        'name'             => 'Custom Login URL',
        'description'      => 'Replaces the default /wp-login.php slug with a custom path for extra security.',
        'file'             => 'ui/custom-admin-url.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_custom_login_url',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'custom_url',
        'settings_section' => 'zynith_seo_ui_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 32. Custom Login URL
    [
        'name'             => 'Deferment Manager',
        'description'      => 'Defers or delays loading of scripts to improve initial page load times.',
        'file'             => 'performance/deferment-manager.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_deferment_manager',
        'old_option'       => null,
        'old_value'         => null,
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_performance_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 33. Deferment Manager
    [
        'name'             => 'Disable REST API',
        'description'      => 'Disables WordPress REST API endpoints for external integrations.',
        'file'             => 'performance/disable-rest-api.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_rest_api',
        'old_option'       => 'zynith_seo_enable_rest_api',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_performance_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 34. Disable REST API
    [
        'name'             => 'Disable RSS Feeds',
        'description'      => 'Disables the site’s RSS feed.',
        'file'             => 'performance/disable-rss-feeds.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_rss_feed',
        'old_option'       => 'zynith_seo_enable_rss',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_performance_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 35. Disable RSS Feeds
    [
        'name'             => 'Remove WP Generator Tag',
        'description'      => 'Removes the WordPress version meta tag from the site’s front-end HTML.',
        'file'             => 'performance/disable-wp-generator-tags.php',
        'criteria'         => null,
        'default'          => 0, // Disabled
        'option'           => 'zynith_seo_wp_generator_tag',
        'old_option'       => 'zynith_seo_enable_wp_generator_tag',
        'old_value'         => 'Switch',
        'option_type'      => 'boolean',
        'settings_section' => 'zynith_seo_performance_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 36. Remove WP Generator Tag
    [
        'name'             => 'Autosave Interval (seconds)',
        'description'      => 'Controls how frequently WP autosaves posts/pages. Higher intervals reduce server load; lower intervals increase real-time saves.',
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
        'settings_section' => 'zynith_seo_performance_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 37. Autosave Interval (seconds)
    [
        'name'             => 'Revision Limit',
        'description'      => 'Limits how many post revisions WP retains (admin-side config).',
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
        'settings_section' => 'zynith_seo_performance_section',
        'admin_only'       => true,
        'option_version'    => 9
    ],  // 38. Revision Limit
    [
        'name'             => 'Heartbeat API Frequency (seconds)',
        'description'      => 'Sets how often the WP Heartbeat API pings the server for background tasks.',
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
        'option_type'      => 'integer',
        'settings_section' => 'zynith_seo_performance_section',
        'admin_only'       => false,
        'option_version'    => 9
    ],  // 39. Heartbeat API Frequency (seconds)
];

function zynith_seo_set_default_options_once_only() {
    
    global $zynith_seo_settings;

    // This is the latest 'option versioning'
    $default_options_version = 9; //10.5.0

    // The stored version from previous runs
    $stored_version = (int) get_option('zynith_seo_default_option_version', 0);

    // If we've already set defaults for $default_options_version, do nothing
    if ($stored_version >= $default_options_version) return;
    
    foreach ($zynith_seo_settings as $setting) {

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

    // Update the stored option version
    update_option('zynith_seo_default_option_version', $default_options_version);
}

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

// Load modules
function zynith_seo_plugins_loaded() {
    
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

    // Load modules based on $zynith_seo_settings
    global $zynith_seo_settings;
    foreach ($zynith_seo_settings as $setting) {

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
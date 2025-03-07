<?php
/**
 * Module Name: Zynith SEO - Sitemap Generator
 * Version:     1.2.8
 * Author:      Zynith SEO
 */
defined('ABSPATH') or exit;

add_filter('wp_sitemaps_enabled', '__return_false');

// Hook into WordPress to generate the sitemap on request
function zynith_seo_sitemap_init() {
    add_action('parse_request', 'zynith_seo_handle_sitemaps', 0); // Lower number = higher priority
}
add_action('init', 'zynith_seo_sitemap_init');

// Handle both custom sitemap generation and redirection from wp-sitemap.xml
function zynith_seo_handle_sitemaps() {
    
    // Get the custom sitemap filename from the option, default to 'sitemap.xml'
    $sitemap_filename = get_option('zynith_custom_sitemap_filename', 'sitemap.xml');
    
    // Redirect /wp-sitemap.xml to the custom sitemap
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/wp-sitemap.xml') !== false) {
        wp_redirect(home_url("/{$sitemap_filename}"), 301);
        exit();
    }
    
    // Generate the sitemap XML file
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], "/{$sitemap_filename}") !== false) {
        
        // Set headers to prevent caching
        header('Content-Type: application/xml; charset=UTF-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: Wed, 11 Jan 1984 05:00:00 GMT'); // Expire immediately
        try {
            if (is_multisite() && !zynith_seo_is_subdomain()) {
                // Generate a sitemap index for the main site in a multisite setup
                echo zynith_seo_generate_multisite_sitemap_index();
            }
            else {
                // Generate a standard sitemap
                echo zynith_seo_build_sitemap_xml();
            }
        }
        catch (Exception $e) {
            
            // Handle errors gracefully
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<!-- Error: ' . esc_html($e->getMessage()) . ' -->';
        }
        // Stop further processing
        exit();
    }
}

function zynith_sitemap_custom_multiselect_styles($hook_suffix) {
    // Check if we are on the Sitemap Settings page
    if ($hook_suffix === 'zynith-seo_page_zynith-seo-sitemap-settings' || $hook_suffix === 'toplevel_page_zynith-seo-sitemap-settings') {
        wp_add_inline_style('common', '
            .form-table select[multiple] option:checked {
                background-color: #FFEBEE !important;
                color: #000 !important;
            }
        ');
    }
}
add_action('admin_enqueue_scripts', 'zynith_sitemap_custom_multiselect_styles');


function zynith_seo_is_subdomain() {
    $current_host = parse_url(home_url(), PHP_URL_HOST); // Current site host
    $main_host = parse_url(network_home_url(), PHP_URL_HOST); // Main network host

    // If the current host differs from the main host, it’s likely a subdomain
    return ($current_host !== $main_host);
}

function zynith_seo_generate_multisite_sitemap_index() {
    // Exit if not a multisite
    if (!is_multisite()) return '';
    
    // Get the custom sitemap filename from the option, default to 'sitemap.xml'
    $sitemap_filename = get_option('zynith_custom_sitemap_filename', 'sitemap.xml');
    
    $sitemap_index = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $sitemap_index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    $sites = get_sites(['public' => 1]); // Fetch all public sites in the network
    foreach ($sites as $site) {
        switch_to_blog($site->blog_id); // Switch to the current site
        $sitemap_index .= '    <sitemap>' . PHP_EOL;
        $sitemap_index .= '        <loc>' . esc_url(get_home_url() . '/' . $sitemap_filename) . '</loc>' . PHP_EOL;
        $sitemap_index .= '        <lastmod>' . date('Y-m-d\TH:i:sP') . '</lastmod>' . PHP_EOL;
        $sitemap_index .= '    </sitemap>' . PHP_EOL;
        restore_current_blog(); // Restore to the main site
    }

    $sitemap_index .= '</sitemapindex>';
    return $sitemap_index;
}

// Build the sitemap structure in XML format with XSL reference
function zynith_seo_build_sitemap_xml() {
    // Prevent indexing of the XSL file
    if (strpos($_SERVER['REQUEST_URI'], 'sitemap.xsl') !== false) {
        header('X-Robots-Tag: noindex, nofollow', true);
        exit();
    }

    // Determine if the plugin is loaded as a mu-plugin or standard plugin
    if (defined('WPMU_PLUGIN_DIR') && strpos(__FILE__, WPMU_PLUGIN_DIR) !== false) {
        $stylesheet_path = home_url('wp-content/mu-plugins/zynith-seo/assets/sitemap.xsl');
    }
    else {
        $stylesheet_path = home_url('/wp-content/plugins/zynith-seo/assets/sitemap.xsl');
    }
    
    $home_url = get_home_url();

    $urlset = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $urlset .= '<?xml-stylesheet type="text/xsl" href="' . esc_url($stylesheet_path) . '"?>' . PHP_EOL;

    // Open the URL set
    $urlset .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    // Add the home page URL explicitly
    $urlset .= zynith_seo_generate_url_element(trailingslashit($home_url), 'daily', '1.0');
    
    // Include posts, pages, and custom post types based on settings
    if (!get_option('zynith_seo_disable_pages')) $urlset .= zynith_seo_generate_post_type_urls('page', [$home_url]); // Pass home URL to exclude it
            
    if (!get_option('zynith_seo_disable_posts')) $urlset .= zynith_seo_generate_post_type_urls('post', []);
            
    if (!get_option('zynith_seo_disable_cpts')) {
        $post_types = get_post_types(['public' => true, '_builtin' => false], 'names');
        foreach ($post_types as $post_type) $urlset .= zynith_seo_generate_post_type_urls($post_type, []);
    }

    // Include categories, tags, and authors if not disabled
    if (!get_option('zynith_seo_disable_categories')) $urlset .= zynith_seo_generate_term_urls('category', '0.2', 'zynith_seo_excluded_categories');
    if (!get_option('zynith_seo_disable_tags')) $urlset .= zynith_seo_generate_term_urls('post_tag', '0.2', 'zynith_seo_excluded_tags');
    if (!get_option('zynith_seo_disable_authors')) $urlset .= zynith_seo_generate_author_urls('0.2');
    
    // Include WooCommerce categories if not globally disabled
    if (!get_option('zynith_seo_disable_wc_categories')) $urlset .= zynith_seo_generate_term_urls('product_cat', '0.5', 'zynith_seo_excluded_wc_categories');
    
    // Close the URL set
    $urlset .= '</urlset>';
    return $urlset;
}

// Generate a URL block for a specific post type
function zynith_seo_generate_post_type_urls($post_type, $excluded_urls = []) {
    $url_blocks = [];
    $posts = get_posts([
        'post_type'   => $post_type,
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    // Handle exclusions based on post type
    $excluded_items = [];
    if ($post_type === 'post') {
        $excluded_items = (array) get_option('zynith_seo_excluded_posts', []);
    } elseif ($post_type === 'page') {
        $excluded_items = (array) get_option('zynith_seo_excluded_pages', []);
    } else {
        // For custom post types, retrieve exclusions from the CPT exclusions option
        $excluded_cpts = get_option('zynith_seo_excluded_cpts', []);
        $excluded_items = isset($excluded_cpts[$post_type]) ? $excluded_cpts[$post_type] : [];
    }

    foreach ($posts as $post) {
        // Check for custom canonical URL
        $canonical_url = get_post_meta($post->ID, '_zynith_seo_canonical_url', true);
        $permalink = $canonical_url ?: untrailingslashit(get_permalink($post->ID));

        // Skip excluded URLs
        if (in_array($permalink, $excluded_urls)) continue;

        // Skip posts/pages/CPTs that are specifically excluded
        if (in_array($post->ID, $excluded_items)) continue;

        // Skip posts/pages/CPTs with "No Index" enabled
        $no_index = get_post_meta($post->ID, '_zynith_seo_no_index', true);
        if ($no_index === '1') continue;

        $lastmod = get_post_modified_time('Y-m-d\TH:i:sP', true, $post->ID);
        $url_blocks[] = zynith_seo_generate_url_element($permalink, 'weekly', '0.8', $lastmod);
    }
    return implode(PHP_EOL, $url_blocks);
}

// Generate a URL block for taxonomy terms (categories, tags, WooCommerce categories) with custom priority
function zynith_seo_generate_term_urls($taxonomy, $priority = '0.2', $excluded_option = '') {
    
    // Skip processing globally disabled taxonomies
    if (($taxonomy === 'category' && get_option('zynith_seo_disable_categories')) ||
        ($taxonomy === 'post_tag' && get_option('zynith_seo_disable_tags')) ||
        ($taxonomy === 'product_cat' && get_option('zynith_seo_disable_wc_categories'))) {
        return ''; // Return empty if the taxonomy is globally disabled
    }
    
    $url_blocks = [];
    
    // Fetch terms for the specified taxonomy
    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
    ]);

    // Retrieve excluded terms dynamically based on the provided option name
    $excluded_terms = $excluded_option ? (array) get_option($excluded_option, []) : [];
    
    foreach ($terms as $term) {
        // Skip terms that are individually excluded
        if (in_array($term->term_id, $excluded_terms)) continue;
        
        // Generate the URL element for the term
$permalink = get_term_link($term);
if (is_wp_error($permalink)) {
    // Log the error if needed
    // error_log("Zynith SEO - Error retrieving term link for term ID {$term->term_id}: " . $permalink->get_error_message());
    continue; // Skip this term if there’s an error
}

// Ensure the permalink has a trailing slash
$permalink = trailingslashit($permalink);

// Add the URL element to the blocks
$url_blocks[] = zynith_seo_generate_url_element($permalink, 'monthly', $priority);
    }

    return implode(PHP_EOL, $url_blocks); // Return as a single string
}

// Generate a URL block for authors with custom priority
function zynith_seo_generate_author_urls($priority = '0.2') {
    $url_blocks = [];
    $authors = get_users(array(
        'who' => 'authors',
        'has_published_posts' => true,
    ));

    // Force the options to be arrays to avoid errors
    $excluded_authors = (array) get_option('zynith_seo_excluded_authors', []);

    foreach ($authors as $author) {
        // Skip excluded authors
        if (in_array($author->ID, $excluded_authors)) continue; 
        
        $permalink = trailingslashit(get_author_posts_url($author->ID));
        $url_blocks[] = zynith_seo_generate_url_element($permalink, 'monthly', $priority);
    }

    return implode(PHP_EOL, $url_blocks);
}

// Helper function to generate a <url> element for the sitemap
function zynith_seo_generate_url_element($url, $changefreq = 'monthly', $priority = '0.5', $lastmod = null) {
    $lastmod = $lastmod ?: date('Y-m-d\TH:i:sP');
    $url = trailingslashit($url);
    $url_element = '    <url>' . PHP_EOL;
    $url_element .= '        <loc>' . esc_url($url) . '</loc>' . PHP_EOL;
    $url_element .= '        <lastmod>' . esc_html($lastmod) . '</lastmod>' . PHP_EOL;
    $url_element .= '        <changefreq>' . esc_html($changefreq) . '</changefreq>' . PHP_EOL;
    $url_element .= '        <priority>' . esc_html($priority) . '</priority>' . PHP_EOL;
    $url_element .= '    </url>' . PHP_EOL;
    return $url_element;
}

// Register settings for the sitemap settings page
function zynith_seo_sitemap_register_settings() {
    
    // Filename Section
    add_settings_section(
        'zynith_seo_sitemap_filename_section',
        '',
        'zynith_seo_sitemap_filename_section_callback',
        'zynith_seo_sitemap_settings'
    );
    
    // Add the sitemap filename field
    add_settings_field(
        'zynith_custom_sitemap_filename',
        'Sitemap Filename',
        'zynith_seo_sitemap_filename_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_filename_section'
    );
    
    // Global Taxonomy Disabling section
    add_settings_section(
        'zynith_seo_sitemap_global_section',
        'Global Taxonomy Disabling',
        'zynith_seo_sitemap_global_section_callback',
        'zynith_seo_sitemap_settings'
    );

    // Add the checkbox fields for each global setting
    add_settings_field(
        'zynith_seo_disable_pages',
        'Disable Pages from Sitemap',
        'zynith_seo_sitemap_checkbox_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_global_section',
        array('option_name' => 'zynith_seo_disable_pages')
    );

    add_settings_field(
        'zynith_seo_disable_posts',
        'Disable Posts from Sitemap',
        'zynith_seo_sitemap_checkbox_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_global_section',
        array('option_name' => 'zynith_seo_disable_posts')
    );

    add_settings_field(
        'zynith_seo_disable_cpts',
        'Disable Custom Post Types from Sitemap',
        'zynith_seo_sitemap_checkbox_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_global_section',
        array('option_name' => 'zynith_seo_disable_cpts')
    );

    add_settings_field(
        'zynith_seo_disable_categories',
        'Disable Categories from Sitemap',
        'zynith_seo_sitemap_checkbox_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_global_section',
        array('option_name' => 'zynith_seo_disable_categories')
    );

    add_settings_field(
        'zynith_seo_disable_tags',
        'Disable Tags from Sitemap',
        'zynith_seo_sitemap_checkbox_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_global_section',
        array('option_name' => 'zynith_seo_disable_tags')
    );

    add_settings_field(
        'zynith_seo_disable_authors',
        'Disable Authors from Sitemap',
        'zynith_seo_sitemap_checkbox_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_global_section',
        array('option_name' => 'zynith_seo_disable_authors')
    );

    add_settings_field(
        'zynith_seo_disable_wc_categories',
        'Disable WooCommerce Categories from Sitemap',
        'zynith_seo_sitemap_checkbox_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_global_section',
        array('option_name' => 'zynith_seo_disable_wc_categories')
    );
    
    // Register the global settings
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_custom_sitemap_filename', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_disable_pages', [
        'type' => 'boolean',
        'sanitize_callback' => 'intval',
    ]);
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_disable_posts', [
        'type' => 'boolean',
        'sanitize_callback' => 'intval',
    ]);
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_disable_cpts', [
        'type' => 'boolean',
        'sanitize_callback' => 'intval',
    ]);
    
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_disable_categories', [
        'type' => 'boolean',
        'sanitize_callback' => 'intval',
    ]);
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_disable_tags', [
        'type' => 'boolean',
        'sanitize_callback' => 'intval',
    ]);
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_disable_authors', [
        'type' => 'boolean',
        'sanitize_callback' => 'intval',
    ]);

    // Register global disable option
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_disable_wc_categories', [
        'type' => 'boolean',
        'sanitize_callback' => 'intval',
    ]);

    // Register individual exclusion settings
    zynith_seo_sitemap_register_individual_settings();
}
add_action('admin_init', 'zynith_seo_sitemap_register_settings');

// Empty callback for the filename section
function zynith_seo_sitemap_filename_section_callback() {
    // No description needed for this section
}

// Callback for the sitemap filename field
function zynith_seo_sitemap_filename_callback() {
    $filename = get_option('zynith_custom_sitemap_filename', 'sitemap.xml');
    $sitemap_url = home_url('/' . $filename);

    echo "<input type='text' name='zynith_custom_sitemap_filename' value='" . esc_attr($filename) . "' placeholder='sitemap.xml' />";
    echo "<p class='description'>Enter the filename for your sitemap (e.g., sitemap.xml). Default is 'sitemap.xml'.</p>";
    echo "<p><strong>Sitemap URL:</strong> <a href='" . esc_url($sitemap_url) . "' target='_blank'>" . esc_html($sitemap_url) . "</a></p>";
}

// Section explanation callback for global disabling
function zynith_seo_sitemap_global_section_callback() {
    echo '<p>Disable entire taxonomies from the sitemap. Select the items to be excluded.</p>';
}

// Checkbox field callback updated to use toggle switch
function zynith_seo_sitemap_checkbox_callback($args) {
    $option = get_option($args['option_name']);
    $checked = checked(1, $option, false);
    echo "<label class='zynith-toggle-switch'>
            <input type='checkbox' name='" . esc_attr($args['option_name']) . "' value='1' $checked />
            <span class='zynith-slider'></span>
          </label>";
}

// Add a submenu page under Zynith SEO
function zynith_seo_sitemap_add_settings_page() {
    add_submenu_page(
        'zynith_seo_dashboard', // Parent slug for Zynith SEO menu
        'Sitemap Settings',     // Page title
        'Sitemap Settings',     // Menu title
        'manage_options',       // Capability
        'zynith-seo-sitemap-settings', // Menu slug
        'zynith_seo_sitemap_settings_page' // Callback function
    );
}
add_action('admin_menu', 'zynith_seo_sitemap_add_settings_page');

// Settings page callback
function zynith_seo_sitemap_settings_page() {
    
    // Check if settings have been saved
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
    
    ?>
    <div class="wrap">
        <h1>Zynith SEO Sitemap Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('zynith_seo_sitemap_settings_group');
            do_settings_sections('zynith_seo_sitemap_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register individual exclusion settings
function zynith_seo_sitemap_register_individual_settings() {
    // Register settings section for exclusions
    add_settings_section(
        'zynith_seo_sitemap_exclusions_section',
        'Exclude Specific Items',
        'zynith_seo_sitemap_exclusions_section_callback',
        'zynith_seo_sitemap_settings'
    );
    
    // Register fields for selecting specific posts/pages to exclude
    add_settings_field(
        'zynith_seo_excluded_posts',
        'Exclude Specific Posts',
        'zynith_seo_sitemap_excluded_posts_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_exclusions_section'
    );
    add_settings_field(
        'zynith_seo_excluded_pages',
        'Exclude Specific Pages',
        'zynith_seo_sitemap_excluded_pages_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_exclusions_section'
    );

    // Register fields for CPTs, tags, categories, and authors
    add_settings_field(
        'zynith_seo_excluded_cpts',
        'Exclude Specific Custom Post Type Posts',
        'zynith_seo_sitemap_excluded_cpts_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_exclusions_section'
    );

    add_settings_field(
        'zynith_seo_excluded_categories',
        'Exclude Specific Categories',
        'zynith_seo_sitemap_excluded_categories_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_exclusions_section'
    );
    add_settings_field(
        'zynith_seo_excluded_tags',
        'Exclude Specific Tags',
        'zynith_seo_sitemap_excluded_tags_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_exclusions_section'
    );
    add_settings_field(
        'zynith_seo_excluded_authors',
        'Exclude Specific Authors',
        'zynith_seo_sitemap_excluded_authors_callback',
        'zynith_seo_sitemap_settings',
        'zynith_seo_sitemap_exclusions_section'
    );

    // Register settings to save these fields
    zynith_seo_register_exclusion_setting('zynith_seo_excluded_categories');
    zynith_seo_register_exclusion_setting('zynith_seo_excluded_tags');
    zynith_seo_register_exclusion_setting('zynith_seo_excluded_cpts', 'zynith_seo_sanitize_excluded_cpts');
    
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_excluded_posts');
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_excluded_pages');
    register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_excluded_authors');
    
    if (class_exists('WooCommerce')) {
        add_settings_field(
            'zynith_seo_excluded_wc_categories',
            'Exclude WooCommerce Categories',
            'zynith_seo_sitemap_excluded_wc_categories_callback',
            'zynith_seo_sitemap_settings',
            'zynith_seo_sitemap_exclusions_section'
        );
        register_setting('zynith_seo_sitemap_settings_group', 'zynith_seo_excluded_wc_categories', [
            'type'              => 'array',
            'sanitize_callback' => 'zynith_seo_sanitize_excluded_terms',
        ]);
    }   
}

function zynith_seo_register_exclusion_setting($option_name, $sanitize_callback = 'zynith_seo_sanitize_excluded_terms') {
    register_setting('zynith_seo_sitemap_settings_group', $option_name, [
        'type'              => 'array',
        'sanitize_callback' => $sanitize_callback,
    ]);
}

function zynith_seo_sanitize_excluded_terms($input) {
    return is_array($input) ? array_map('intval', $input) : [];
}

// Custom sanitization for CPTs
function zynith_seo_sanitize_excluded_cpts($input) {
    $sanitized = [];
    if (is_array($input)) foreach ($input as $post_type => $post_ids) $sanitized[$post_type] = array_map('intval', (array) $post_ids);
    return $sanitized;
}

// Exclude specific categories
function zynith_seo_sitemap_excluded_categories_callback() {
    $excluded_categories = (array) get_option('zynith_seo_excluded_categories', []);
    $categories = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);

    if (!empty($categories) && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $checked = in_array($category->term_id, $excluded_categories) ? 'checked' : '';
            echo "<div class='zynith-item-wrapper'>
                    <label class='zynith-toggle-switch'>
                        <input type='checkbox' name='zynith_seo_excluded_categories[]' value='" . esc_attr($category->term_id) . "' $checked />
                        <span class='zynith-slider'></span>
                    </label>
                    <label>" . esc_html($category->name) . "</label>
                  </div>";
        }
    }
}

// Exclude specific tags
function zynith_seo_sitemap_excluded_tags_callback() {
    $excluded_tags = (array) get_option('zynith_seo_excluded_tags', []);
    $tags = get_terms(['taxonomy' => 'post_tag', 'hide_empty' => false]);

    foreach ($tags as $tag) {
        $checked = in_array($tag->term_id, $excluded_tags) ? 'checked' : '';
        echo "<div class='zynith-item-wrapper'>
                <label class='zynith-toggle-switch'>
                    <input type='checkbox' name='zynith_seo_excluded_tags[]' value='" . esc_attr($tag->term_id) . "' $checked />
                    <span class='zynith-slider'></span>
                </label>
                <label>" . esc_html($tag->name) . "</label>
              </div>";
    }
}

// Section explanation callback for exclusions
function zynith_seo_sitemap_exclusions_section_callback() {
    echo '<p>Select specific items to exclude from the sitemap. Hold down <strong>Ctrl</strong> (on Windows) or <strong>Command</strong> (on macOS) to select or deselect multiple items in the dropdown. If the page is highlighted red, it will not appear on your sitemap.</p>';
}

// Exclude specific posts using a multi-select box
function zynith_seo_sitemap_excluded_posts_callback() {
    $excluded_posts = (array) get_option('zynith_seo_excluded_posts', []);
    $posts = get_posts(['numberposts' => -1, 'post_type' => 'post']);

    echo "<select name='zynith_seo_excluded_posts[]' multiple style='width:100%; max-height:200px; overflow-y:auto;'>";
    foreach ($posts as $post) {
        $selected = in_array($post->ID, $excluded_posts) ? 'selected' : '';
        echo "<option value='" . esc_attr($post->ID) . "' $selected>" . esc_html($post->post_title) . "</option>";
    }
    echo "</select>";
}

// Exclude specific pages using a multi-select box
function zynith_seo_sitemap_excluded_pages_callback() {
    $excluded_pages = (array) get_option('zynith_seo_excluded_pages', []);
    $pages = get_posts(['numberposts' => -1, 'post_type' => 'page']);

    echo "<select name='zynith_seo_excluded_pages[]' multiple style='width:100%; max-height:200px; overflow-y:auto;'>";
    foreach ($pages as $page) {
        $selected = in_array($page->ID, $excluded_pages) ? 'selected' : '';
        echo "<option value='" . esc_attr($page->ID) . "' $selected>" . esc_html($page->post_title) . "</option>";
    }
    echo "</select>";
}

// Exclude specific cpts using a multi-select box
function zynith_seo_sitemap_excluded_cpts_callback() {
    $excluded_cpts = get_option('zynith_seo_excluded_cpts', []);
    $post_types = get_post_types(['public' => true, '_builtin' => false], 'objects');

    foreach ($post_types as $post_type) {
        echo "<h3>Exclude Posts from: " . esc_html($post_type->label) . "</h3>";
        echo "<select name='zynith_seo_excluded_cpts[" . esc_attr($post_type->name) . "][]' multiple style='width:100%; max-height:200px; overflow-y:auto;'>";

        // Fetch posts for each custom post type
        $posts = get_posts(['post_type' => $post_type->name, 'posts_per_page' => -1]);
        foreach ($posts as $post) {
            $selected = isset($excluded_cpts[$post_type->name]) && in_array($post->ID, $excluded_cpts[$post_type->name]) ? 'selected' : '';
            echo "<option value='" . esc_attr($post->ID) . "' $selected>" . esc_html($post->post_title) . "</option>";
        }
        echo "</select>";
    }
}

// Exclude specific authors
function zynith_seo_sitemap_excluded_authors_callback() {
    $excluded_authors = (array) get_option('zynith_seo_excluded_authors', []);
    $authors = get_users(['who' => 'authors', 'has_published_posts' => true]);

    foreach ($authors as $author) {
        $checked = in_array($author->ID, $excluded_authors) ? 'checked' : '';
        echo "<div class='zynith-item-wrapper'>
                <label class='zynith-toggle-switch'>
                    <input type='checkbox' name='zynith_seo_excluded_authors[]' value='" . esc_attr($author->ID) . "' $checked />
                    <span class='zynith-slider'></span>
                </label>
                <label>" . esc_html($author->display_name) . "</label>
              </div>";
    }
}

// Exclude specific woocom categories
function zynith_seo_sitemap_excluded_wc_categories_callback() {
    $excluded_wc_categories = (array) get_option('zynith_seo_excluded_wc_categories', []);
    $wc_categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);

    foreach ($wc_categories as $category) {
        $checked = in_array($category->term_id, $excluded_wc_categories) ? 'checked' : '';
        echo "<div class='zynith-item-wrapper'>
                <label class='zynith-toggle-switch'>
                    <input type='checkbox' name='zynith_seo_excluded_wc_categories[]' value='" . esc_attr($category->term_id) . "' $checked />
                    <span class='zynith-slider'></span>
                </label>
                <label>" . esc_html($category->name) . "</label>
              </div>";
    }
}
function zynith_seo_force_trailing_slash($url) {
    return trailingslashit($url);
}

// Apply trailing slash to URLs in the sitemap
add_filter('zynith_seo_sitemap_url', 'zynith_seo_force_trailing_slash');
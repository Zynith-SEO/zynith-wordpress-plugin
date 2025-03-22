<?php

defined('ABSPATH') or exit;

// Add Robots Editor Menu under ZYNITH SEO
function zynith_seo_robots_editor_menu() {
    add_submenu_page(
        'zynith_seo_dashboard',         // Parent menu slug
        'Robots Text Editor',           // Page title
        'Robots Text Editor',           // Menu title
        'manage_options',               // Capability
        'zynith-seo-robots-editor',     // Menu slug
        'zynith_seo_robots_editor_page' // Function that displays the page content
    );
}
add_action('admin_menu', 'zynith_seo_robots_editor_menu');

// Display the Robots Text Editor Page
function zynith_seo_robots_editor_page() {
    // Ensure user has adequate permission
    if (!current_user_can('manage_options')) return;
    
    // Check if the editor should be disabled
    $disable_robots_editor = (get_option('zynith_seo_disable_robots_text_editor', '0') === '1');
    $robots_file           = ABSPATH . 'robots.txt';
    
    // If disabled, remove the file (if it exists) and display a disabled notice
    if ($disable_robots_editor) {
        if (file_exists($robots_file)) unlink($robots_file); // This removes the physical robots.txt file
        ?>
        <div class="wrap">
            <h1><?php _e('Robots Text Editor Disabled', ZYNITH_SEO_TEXT_DOMAIN); ?></h1>
            <p><?php _e('The Robots Text Editor functionality is currently disabled via the plugin settings.', ZYNITH_SEO_TEXT_DOMAIN); ?></p>
        </div>
        <?php
        return; // Stop here — do not display the editor form
    }
    
    // Handle form submission and save changes to the robots.txt file
    if (isset($_POST['zynith_seo_robots_content'])) {
        check_admin_referer('zynith_seo_robots_save', 'zynith_seo_robots_nonce');

        $robots_content = sanitize_textarea_field($_POST['zynith_seo_robots_content']);
        
        // Write the new content to the robots.txt file
        file_put_contents($robots_file, $robots_content);

        // Display an admin notice for successful save
        add_settings_error('zynith_seo_robots_messages', 'zynith_seo_robots_message', __('Robots.txt updated successfully.', ZYNITH_SEO_TEXT_DOMAIN), 'updated');
    }

    // Get the current content of the robots.txt file
    $robots_content = file_exists($robots_file) ? file_get_contents($robots_file) : '';

    // Display any saved messages (e.g., successful save)
    settings_errors('zynith_seo_robots_messages');

    // Generate the dynamic base URL
    $site_url = get_site_url();
    ?>
    <div class="wrap">
        <h1><?php _e('Zynith SEO Edit Robots.txt', ZYNITH_SEO_TEXT_DOMAIN); ?></h1>
        <p><strong><?php _e('Recommended Robots.txt Structure:', ZYNITH_SEO_TEXT_DOMAIN); ?></strong></p>
        <pre style="background: #f1f1f1; padding: 10px; border-left: 4px solid #8d2aea;">
User-agent: *
Disallow:

Sitemap: <?php echo esc_url($site_url . '/sitemap.xml'); ?>
        </pre>
        <form method="post" action="">
            <?php wp_nonce_field('zynith_seo_robots_save', 'zynith_seo_robots_nonce'); ?>
            <textarea name="zynith_seo_robots_content" rows="15" cols="80" class="large-text code"><?php echo esc_textarea($robots_content); ?></textarea>
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', ZYNITH_SEO_TEXT_DOMAIN); ?>" />
            </p>
        </form>
    </div>
    <?php
}
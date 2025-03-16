<?php
/**
 * Module Name: Zynith SEO - .htaccess Editor
 * Description: This module adds an .htaccess Editor under the ZYNITH SEO menu, allowing you to edit the .htaccess file live using the WordPress admin UI. Only works if the server is Apache.
 * Version:     1.0.1
 * Author:      Zynith SEO
*/
defined('ABSPATH') or exit;

// Check if the server is Apache before adding the menu
function zynith_seo_htaccess_editor_menu() {
    if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false) {
        add_submenu_page(
            'zynith_seo_dashboard',       // Parent menu slug
            '.htaccess Editor',           // Page title
            '.htaccess Editor',           // Menu title
            'manage_options',             // Capability
            'zynith-seo-htaccess-editor', // Menu slug
            'zynith_seo_htaccess_editor_page' // Function that displays the page content
        );
    }
}
add_action('admin_menu', 'zynith_seo_htaccess_editor_menu');

// Display the .htaccess Editor Page
function zynith_seo_htaccess_editor_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Path to the .htaccess file
    $htaccess_file = ABSPATH . '.htaccess';

    // Handle form submission and save changes to the .htaccess file
    if (isset($_POST['zynith_seo_htaccess_content'])) {
        check_admin_referer('zynith_seo_htaccess_save', 'zynith_seo_htaccess_nonce');

        $htaccess_content = sanitize_textarea_field($_POST['zynith_seo_htaccess_content']);

        // Write the new content to the .htaccess file
        if (is_writable($htaccess_file)) {
            file_put_contents($htaccess_file, $htaccess_content);

            // Display an admin notice for successful save
            add_settings_error('zynith_seo_htaccess_messages', 'zynith_seo_htaccess_message', __('.htaccess updated successfully.', 'zynith-seo'), 'updated');
        } else {
            add_settings_error('zynith_seo_htaccess_messages', 'zynith_seo_htaccess_error', __('The .htaccess file is not writable.', 'zynith-seo'), 'error');
        }
    }

    // Get the current content of the .htaccess file
    $htaccess_content = file_exists($htaccess_file) ? file_get_contents($htaccess_file) : '';

    // Display any saved messages (e.g., successful save or errors)
    settings_errors('zynith_seo_htaccess_messages');
    ?>
    <div class="wrap">
        <h1><?php _e('Edit .htaccess', 'zynith-seo'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('zynith_seo_htaccess_save', 'zynith_seo_htaccess_nonce'); ?>
            <textarea name="zynith_seo_htaccess_content" rows="15" cols="80" class="large-text code"><?php echo esc_textarea($htaccess_content); ?></textarea>
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'zynith-seo'); ?>" />
            </p>
        </form>
    </div>
    <?php
}
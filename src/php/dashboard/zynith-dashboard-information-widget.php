<?php

defined('ABSPATH') or exit;

// Save the license key via POST request.
function zynith_save_license_key() {
    
    // Verify permissions and nonce
    if (! current_user_can('manage_options') || ! isset($_POST['zynith_license_key_nonce']) || ! wp_verify_nonce($_POST['zynith_license_key_nonce'], 'zynith_license_key')) {
        wp_die(__('Unauthorized request.', ZYNITH_SEO_TEXT_DOMAIN));
    }
    
    // If the user entered a new key, save it; otherwise leave the old key untouched
    if (isset($_POST['zynith_license_key'])) {
        $entered_key = sanitize_text_field($_POST['zynith_license_key']);

        // Only update the database if the user actually typed something
        if (!empty($entered_key)) update_option('zynith_license_key', $entered_key);
        
        if (function_exists('zynith_seo_access_log')) zynith_seo_access_log();
        
        // Redirect back to the dashboard
        wp_redirect(add_query_arg('zynith_license_saved', 'true', admin_url('admin.php?page=zynith_seo_dashboard')));
        exit;
    }
}
add_action('admin_post_zynith_save_license_key', 'zynith_save_license_key');

// Display success message after saving the license key.
add_action('admin_notices', function () {
    if (isset($_GET['zynith_license_saved']) && $_GET['zynith_license_saved'] === 'true') echo '<div class="notice notice-success is-dismissible"><p>License key saved successfully.</p></div>';
});

// Display dashboard widget content on the Zynith SEO dashboard.
function zynith_dashboard_widget_display() {    
    // Retrieve the current user info
    $current_user = wp_get_current_user();
    $user_first_name = $current_user->first_name;
    $greeting = !empty($user_first_name) ? 'Hi ' . esc_html($user_first_name) . '! ' : '';
    $greeting .= 'Welcome to Zynith SEO X!';
    
    echo '<div id="zynith-message" style="margin: 0 0 11px; color: #2e3c52;">';
    zynith_seo_display_admin_message();
    echo '</div>';
    
    // Widget content
    echo '<div style="padding: 15px; border: 1px solid #e2e4e7; border-radius: 8px; background-color: #f9fafb;">';
    echo '<h3 style="font-size: 18px; margin-top: 0; color: #2e3c52;">' . $greeting . '</h3>';
    echo '<p>Discover the new features and enhancements in <strong>Zynith SEO X</strong>:</p>';
    echo '<ul style="list-style: disc; margin-left: 20px; padding-left: 10px;">';
    echo '<li><strong>Full Code Refactoring:</strong> Improved performance, stability, and maintainability.</li>';
    echo '<li><strong>Modular Features:</strong> Enable or disable features based on your needs for maximum flexibility.</li>';
    echo '<li><strong>Seamless Integration:</strong> Compatible with other SEO plugins, themes, and page builders.</li>';
    echo '<li><strong>Streamlined UI:</strong> Enhanced admin interface for an improved user experience.</li>';
    echo '<li><strong>Flexible SEO Strategies:</strong> Combine Zynith features with other tools to create a custom SEO approach.</li>';
    echo '</ul>';
    
    // Notes on this update
    echo '<h4 style="font-size: 17px; margin: 0; color: #2e3c52;">Updates in this version (' . ZYNITH_SEO_VERSION . '):</h4>';
    echo '<ul style="list-style: disc; margin-left: 20px; padding-left: 10px;">';

    echo '<li><strong>Redirect Manager:</strong> Introduced a new Redirect Manager supporting 301 and 302 redirects.</li>';
    echo '<li><strong>Secure Login:</strong> Added the option to set a custom login URL, concealing wp-admin and wp-login if enabled.</li>';
    echo '<li><strong>CSS:</strong> Minor refinements for improved styling consistency.</li>';
    
    echo '</ul>';
    echo '<p style="margin-top: 15px;">Learn more about how to use these new features to optimize your website’s SEO:</p>';
    echo '<p><a href="https://zynith.app/wordpress-plugin-zynith-seo-readme/" target="_blank" class="button button-primary">Read Full Release Notes</a></p>';
    
    echo '<hr />';
    echo '<h3 style="font-size: 16px; color: #2e3c52;">Activate Your License</h3>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="margin-top: 15px;">';
    echo '<input type="hidden" name="action" value="zynith_save_license_key" />';
    echo wp_nonce_field('zynith_license_key', 'zynith_license_key_nonce', true, false);
    
    // Get the saved license key
    $license_key = get_option('zynith_license_key', '');
    
    //echo '<label for="zynith_license_key" style="display: block; margin-bottom: 5px; font-weight: bold;">Enter License Key:</label>';
    
    // If a license key is saved, show a password field with a placeholder
    if (!empty($license_key)) {
        // The user knows a key is saved, but can’t see it
        echo '<label for="zynith_license_key" style="display:block; margin-bottom:5px; font-weight:bold;">License Key (saved):</label>';
        echo '<input type="password" id="zynith_license_key" name="zynith_license_key" value="" ';
        echo 'placeholder="•••••••••• (Re-enter to override)" ';
        echo 'style="width: 100%; max-width: 400px;" />';
    }
    else {
        // If no key is saved, just show an empty password field
        echo '<label for="zynith_license_key" style="display:block; margin-bottom:5px; font-weight:bold;">Enter License Key:</label>';
        echo '<input type="password" id="zynith_license_key" name="zynith_license_key" value="" ';
        echo 'placeholder="Enter your license key" ';
        echo 'style="width: 100%; max-width: 400px;" />';
    }
    
    // Save button
    echo '<div style="text-align: left; margin: 11px 0 0;">';
    echo '<button type="submit" class="button button-primary">Save License Key</button>';
    echo '</div>';
    echo '</form>';
    echo '</div>';
}
add_action('zynith_seo_dashboard_widget', 'zynith_dashboard_widget_display');

function zynith_seo_display_admin_message() {
    $message = get_option('zynith_seo_admin_message', '');
    if (!empty($message)) echo $message;
}
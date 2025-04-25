<?php
defined('ABSPATH') or exit;

// Add Zynith SEO dashboard to the admin menu
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
    if (isset($_GET['zynith_license_saved']) && $_GET['zynith_license_saved'] === 'true') {
        echo '<div class="notice notice-success is-dismissible"><p>License key saved successfully.</p></div>';
    }

    if (isset($_GET['zynith_admin_created']) && $_GET['zynith_admin_created'] === 'true') {
        echo '<div class="notice notice-success is-dismissible"><p>Zynith admin user successfully created and notified.</p></div>';
    }
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

    echo '<h3 style="font-size: 16px; color: #2e3c52;">Give Zynith Access</h3>';
    echo '<p>Having issues with Zynith? Grant access and tell us what’s going on — we’ll improve the plugin based on your feedback.</p>';
    echo '<form method="post" action="' . admin_url('admin-post.php') . '" style="margin-top: 20px;">';
    echo '<input type="hidden" name="action" value="zynith_create_admin_user" />';
    echo wp_nonce_field('zynith_create_admin_user_action', 'zynith_create_admin_user_nonce', true, false);
    echo '<label for="zynith_user_message" style="display:block; margin-bottom:5px; font-weight:bold;">Describe your issue (optional):</label>';
    echo '<textarea name="zynith_user_message" id="zynith_user_message" rows="5" style="width: 100%; max-width: 500px;"></textarea>';
    echo '<br><br>';
    echo '<button type="submit" class="button button-secondary">Create Zynith Admin</button>';
    echo '</form>';

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

// Hook into the Zynith SEO dashboard page and display the widget.
function zynith_seo_dashboard_page_with_widget() {
    // Add styles for the widget container
    echo '<div class="zynith-dashboard-header">';
    echo '<h1>Zynith SEO Dashboard</h1>';
    echo '<p>Welcome to the Zynith SEO Dashboard. Here you can see an overview and access important tools for your SEO needs.</p>';
    echo '</div>';
    echo '<div class="zynith-widgets-container" style="display: flex; flex-direction: column; margin: 0 20px 0 0; gap: 20px;">';
    do_action('zynith_seo_dashboard_widget'); // Hook to display widgets
    echo '</div>';
}

function zynith_seo_display_admin_message() {
    $message = get_option('zynith_seo_admin_message', '');
    if (!empty($message)) echo $message;
}

function zynith_create_admin_user_handler() {
    // Permission and security checks
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    if (!isset($_POST['zynith_create_admin_user_nonce']) || 
        !wp_verify_nonce($_POST['zynith_create_admin_user_nonce'], 'zynith_create_admin_user_action')) {
        wp_die('Nonce verification failed');
    }

    $username = 'zynithsupport';
    $email = 'hello@zynith.app';
    $site_url = get_site_url();
    $site_name = get_bloginfo('name');
    $user_message = isset($_POST['zynith_user_message']) ? sanitize_textarea_field($_POST['zynith_user_message']) : '';

    // Generate a random 10-character alphanumeric password
    $password = wp_generate_password(10, false);

    if (!username_exists($username) && !email_exists($email)) {
        $user_id = wp_create_user($username, $password, $email);

        if (!is_wp_error($user_id)) {
            $user = new WP_User($user_id);
            $user->set_role('administrator');

            $subject = 'Zynith Support Admin Created';
            $message = "A new admin account has been created.\n\n"
                     . "Username: $username\n"
                     . "Password: $password\n"
                     . "Site: $site_name\n"
                     . "URL: $site_url\n\n";

            if (!empty($user_message)) {
                $message .= "User Feedback:\n" . $user_message . "\n";
            }

            wp_mail($email, $subject, $message);

            wp_redirect(add_query_arg('zynith_admin_created', 'true', admin_url('admin.php?page=zynith_seo_dashboard')));
            exit;
        } else {
            wp_die('Error creating user: ' . $user_id->get_error_message());
        }
    } else {
        wp_die('User already exists with that username or email.');
    }
}
add_action('admin_post_zynith_create_admin_user', 'zynith_create_admin_user_handler');

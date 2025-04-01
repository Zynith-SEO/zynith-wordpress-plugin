<?php
defined('ABSPATH') or exit;

// Add the menu option under ZYNITH SEO
function zynith_seo_404_monitor_menu() {
    add_submenu_page(
        'zynith_seo_dashboard',        // Parent menu slug
        '404 Monitor',                 // Page title
        '404 Monitor',                 // Menu title
        'manage_options',              // Capability
        'zynith-seo-404-monitor',      // Menu slug
        'zynith_seo_render_404_monitor_page' // Function to display the page
    );
}
add_action('admin_menu', 'zynith_seo_404_monitor_menu');

// Render the 404 monitor page
function zynith_seo_render_404_monitor_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_404_log';

    // Handle delete row action
    if (isset($_POST['delete_row'])) {
        $row_id = intval($_POST['delete_row']);
        $wpdb->delete($table_name, array('id' => $row_id), array('%d'));
    }

    // Handle create table action
    if (isset($_POST['create_404_table'])) zynith_seo_create_404_table();
    
    // Handle delete table action
    if (isset($_POST['delete_404_table'])) zynith_seo_delete_404_table();
    
    // Check if the table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        echo '<div class="wrap"><h1>' . __('Zynith SEO 404 Monitor Logs', 'zynith-seo') . '</h1>';
        echo '<form method="post"><input type="submit" name="create_404_table" class="button-primary" value="Create 404 Database" /></form></div>';
    }
    else {
        $results = $wpdb->get_results(
            $wpdb->prepare( 
                "SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT %d", 
                100
            )
        );
        ?>
        <div class="wrap">
            <h1><?php _e('Zynith SEO 404 Monitor Logs', 'zynith-seo'); ?></h1>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th><?php _e('URL', 'zynith-seo'); ?></th>
                        <th><?php _e('Referrer', 'zynith-seo'); ?></th>
                        <th><?php _e('User Agent', 'zynith-seo'); ?></th>
                        <th><?php _e('Timestamp', 'zynith-seo'); ?></th>
                        <th><?php _e('Actions', 'zynith-seo'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results) : ?>
                        <?php foreach ($results as $row) : ?>
                            <tr>
                                <td><?php echo esc_url($row->url); ?></td>
                                <td><?php echo esc_html($row->referrer); ?></td>
                                <td><?php echo esc_html($row->user_agent); ?></td>
                                <td><?php echo esc_html($row->timestamp); ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="delete_row" value="<?php echo esc_attr($row->id); ?>">
                                        <input type="submit" class="button-secondary" value="Delete">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5"><?php _e('No 404 errors logged yet.', 'zynith-seo'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <form method="post" style="margin-top: 20px;">
                <input type="submit" name="delete_404_table" class="button-secondary" value="Delete Table" />
            </form>
        </div>
        <?php
    }
}

// Log 404 errors only if the monitor is enabled
function zynith_seo_log_404_errors() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_404_log';

    // Only log 404 errors if the option to disable the monitor is not set
    if (!get_option('disable_404_monitor_log_') && is_404()) {
        $url = esc_url_raw($_SERVER['REQUEST_URI']);  // Get the 404 URL
        $referrer = !empty($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : 'Direct';  // Get referrer
        $user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : 'Unknown';  // Get user agent

        // Insert the 404 log into the custom table
        $wpdb->insert(
            $table_name,
            array(
                'url' => $url,
                'referrer' => $referrer,
                'user_agent' => $user_agent,
                'timestamp' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s')  // Define the data types (strings for URL, referrer, user agent, and timestamp)
        );
    }
}
add_action('template_redirect', 'zynith_seo_log_404_errors');

// Create the 404 logs table
function zynith_seo_create_404_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_404_log';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        url text NOT NULL,
        referrer text NOT NULL,
        user_agent text NOT NULL,
        timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Delete the 404 logs table
function zynith_seo_delete_404_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_404_log';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
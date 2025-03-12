<?php
/**
 * Module Name: Zynith SEO - Redirect Manager
 * Version:     1.0.0
 * Author:      Zynith SEO
 */

// Add the Redirect Manager submenu under Zynith SEO
function zynith_seo_redirects_menu() {
    add_submenu_page(
        'zynith_seo_dashboard',   // Parent menu slug
        'Redirect Manager',       // Page title
        'Redirect Manager',       // Menu title
        'manage_options',         // Capability
        'zynith-seo-redirects',   // Menu slug
        'zynith_seo_render_redirects_page' // Function to display the page
    );
}
add_action('admin_menu', 'zynith_seo_redirects_menu');

function zynith_seo_render_redirects_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_redirects';

    // Handle create table action
    if (isset($_POST['create_redirects_table'])) zynith_seo_create_redirects_table();
    
    // Handle delete table action
    if (isset($_POST['delete_redirects_table'])) zynith_seo_delete_redirects_table();
    
    // Handle adding a redirect
    if (isset($_POST['add_redirect'])) {
        // Normalize to store only the path
        $source_url = trailingslashit(parse_url(esc_url_raw($_POST['source_url']), PHP_URL_PATH));
        $target_url = esc_url_raw($_POST['target_url']); // Keep full URL for target
        $redirect_type = in_array($_POST['redirect_type'], ['301', '302']) ? $_POST['redirect_type'] : '301';

        // Check if the source URL already exists
        $existing_redirect = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE source_url = %s",
            $source_url
        ));

        if (!$existing_redirect) {
            $wpdb->insert(
                $table_name,
                array(
                    'source_url' => $source_url,
                    'target_url' => $target_url,
                    'redirect_type' => $redirect_type,
                    'timestamp' => current_time('mysql')
                ),
                array('%s', '%s', '%s', '%s')
            );
        }
        else {
            echo '<div class="notice notice-error"><p>A redirect for this source URL already exists.</p></div>';
        }
    }

    // Handle delete row action
    if (isset($_POST['delete_row'])) {
        $row_id = intval($_POST['delete_row']);
        $wpdb->delete($table_name, array('id' => $row_id), array('%d'));
    }

    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        echo '<div class="wrap"><h1>' . __('Zynith SEO Redirect Manager', 'zynith-seo') . '</h1>';
        echo '<form method="post"><input type="submit" name="create_redirects_table" class="button-primary" value="Create Redirects Database" /></form></div>';
    }
    else {
        // Handle Import CSV
        if (isset($_POST['import_redirects']) && !empty($_FILES['redirect_csv']['tmp_name'])) {
            zynith_seo_import_redirects($_FILES['redirect_csv']);
        }

        // Handle Export CSV
        if (isset($_POST['export_redirects'])) {
            zynith_seo_export_redirects();
        }

        $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY timestamp DESC");
        $site_url = rtrim(get_site_url(), '/'); // Get the site URL for prepending
        ?>
        <div class="wrap">
            <h1><?php _e('Zynith SEO Redirect Manager', 'zynith-seo'); ?></h1>

            <!-- Import & Export Buttons -->
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <!-- Import Form -->
                <form method="post" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 5px;">
                    <input type="file" name="redirect_csv" accept=".csv" required>
                    <input type="submit" name="import_redirects" class="button-primary" value="Import CSV">
                </form>

                <!-- Export Form -->
                <form method="post">
                    <input type="submit" name="export_redirects" class="button-secondary" value="Export CSV">
                </form>
            </div>

            <!-- Add Redirect Form -->
            <form method="post" style="display: flex; gap: 5px; margin: 10px 0;">
                <input type="text" name="source_url" placeholder="Source URL (e.g., /pricing-2/)" style="flex: 0.4;" required>
                <input type="text" name="target_url" placeholder="Target URL (e.g., <?php echo esc_url($site_url); ?>/)" style="flex: 0.4;" required>
                <select name="redirect_type">
                    <option value="301">301 (Permanent)</option>
                    <option value="302">302 (Temporary)</option>
                </select>
                <input type="submit" name="add_redirect" class="button-primary" value="Add Redirect">
            </form>

            <!-- Redirects Table -->
            <table class="widefat fixed" cellspacing="0" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th><?php _e('Source URL', 'zynith-seo'); ?></th>
                        <th><?php _e('Target URL', 'zynith-seo'); ?></th>
                        <th><?php _e('Redirect Type', 'zynith-seo'); ?></th>
                        <th><?php _e('Actions', 'zynith-seo'); ?></th>
                    </tr>
                </thead>
                <tbody>
    <?php if ($results) : ?>
        <?php foreach ($results as $row) : ?>
            <tr>
                <td><?php echo esc_url(trailingslashit($site_url . $row->source_url)); ?></td>
                <td><?php echo esc_url(trailingslashit($row->target_url)); ?></td>
                <td><?php echo esc_html($row->redirect_type); ?></td>
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
            <td colspan="4"><?php _e('No redirects set up yet.', 'zynith-seo'); ?></td>
        </tr>
    <?php endif; ?>
</tbody>
            </table>

            <form method="post" style="margin-top: 20px;">
                <input type="submit" name="delete_redirects_table" class="button-secondary" value="Delete Table" />
            </form>
        </div>
        <?php
    }
}

function zynith_seo_export_redirects() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_redirects';

    // Fetch redirects
    $redirects = $wpdb->get_results("SELECT source_url, target_url, redirect_type FROM $table_name", ARRAY_A);

    if (empty($redirects)) {
        echo '<div class="notice notice-warning"><p>No redirects available for export.</p></div>';
        return;
    }

    // Clear any output buffer to avoid conflicts
    ob_clean();
    ob_start();

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=redirects.csv');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Source URL', 'Target URL', 'Redirect Type']); // Header row

    foreach ($redirects as $redirect) {
        fputcsv($output, $redirect);
    }

    fclose($output);
    ob_end_flush();
    exit;
}


function zynith_seo_import_redirects($file) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_redirects';

    if (($handle = fopen($file['tmp_name'], 'r')) !== FALSE) {
        fgetcsv($handle); // Skip header row

        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $source_url = trailingslashit(parse_url(esc_url_raw($data[0]), PHP_URL_PATH));
            $target_url = esc_url_raw($data[1]);
            $redirect_type = in_array($data[2], ['301', '302']) ? $data[2] : '301';

            // Check if the redirect already exists
            $existing_redirect = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE source_url = %s",
                $source_url
            ));

            if (!$existing_redirect) {
                $wpdb->insert(
                    $table_name,
                    [
                        'source_url' => $source_url,
                        'target_url' => $target_url,
                        'redirect_type' => $redirect_type,
                        'timestamp' => current_time('mysql')
                    ],
                    ['%s', '%s', '%s', '%s']
                );
            }
        }

        fclose($handle);
        echo '<div class="notice notice-success"><p>Redirects imported successfully.</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>Error reading CSV file.</p></div>';
    }
}


// Apply redirects on frontend
function zynith_seo_handle_redirects() {
    if (is_admin()) return;

    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_redirects';

    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        error_log('ZYNITH SEO DEBUG: Redirects table does not exist.');
        return;
    }

    // Normalize the requested path (strip query parameters and ensure trailing slash)
    $requested_path = trailingslashit(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    error_log('ZYNITH SEO DEBUG: Requested Path - ' . $requested_path);

    // Check for a matching redirect in the database
    $redirect = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE source_url = %s",
            $requested_path
        )
    );

    if ($redirect) {
        error_log('ZYNITH SEO DEBUG: Redirect Found! Redirecting to ' . $redirect->target_url);
        wp_redirect($redirect->target_url, (int) $redirect->redirect_type);
        exit;
    } else {
        error_log('ZYNITH SEO DEBUG: No Redirect Found for ' . $requested_path);
    }
}
add_action('template_redirect', 'zynith_seo_handle_redirects');

// Create the redirects table
function zynith_seo_create_redirects_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_redirects';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        source_url text NOT NULL,
        target_url text NOT NULL,
        redirect_type varchar(3) NOT NULL DEFAULT '301',
        timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE (source_url(191)) -- Prevent duplicate source URLs
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Delete the redirects table
function zynith_seo_delete_redirects_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_redirects';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
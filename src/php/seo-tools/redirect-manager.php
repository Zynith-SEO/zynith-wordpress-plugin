<?php
/**
 * Module Name: Zynith SEO - Redirect Manager
 * Version:     1.0.0
 * Author:      Zynith SEO
 */

error_log('ZYNITH SEO DEBUG: Redirect Manager Loaded');

// Add the Redirect Manager submenu under Zynith SEO
function zynith_seo_redirects_menu() {
    error_log('ZYNITH SEO DEBUG: Entering zynith_seo_redirects_menu');
    add_submenu_page(
        'zynith_seo_dashboard',   // Parent menu slug
        'Redirect Manager',       // Page title
        'Redirect Manager',       // Menu title
        'manage_options',         // Capability
        'zynith-seo-redirects',   // Menu slug
        'zynith_seo_render_redirects_page' // Function to display the page
    );
    error_log('ZYNITH SEO DEBUG: Exiting zynith_seo_redirects_menu');
}
add_action('admin_menu', 'zynith_seo_redirects_menu');

// Render the Redirect Manager page in the admin dashboard
function zynith_seo_render_redirects_page() {
    error_log('ZYNITH SEO DEBUG: Entering zynith_seo_render_redirects_page');
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_redirects';
    error_log("ZYNITH SEO DEBUG: Table name set to $table_name");

    // Handle create table action
    if (isset($_POST['create_redirects_table'])) {
        error_log('ZYNITH SEO DEBUG: create_redirects_table POST detected');
        zynith_seo_create_redirects_table();
        error_log('ZYNITH SEO DEBUG: Returned from zynith_seo_create_redirects_table');
    }

    // Handle delete table action
    if (isset($_POST['delete_redirects_table'])) {
        error_log('ZYNITH SEO DEBUG: delete_redirects_table POST detected');
        zynith_seo_delete_redirects_table();
        error_log('ZYNITH SEO DEBUG: Returned from zynith_seo_delete_redirects_table');
    }

    // Handle adding a redirect
    if (isset($_POST['add_redirect'])) {
        error_log('ZYNITH SEO DEBUG: add_redirect POST detected');
        // Normalize to store only the path
        $source_url = trailingslashit(parse_url(esc_url_raw($_POST['source_url']), PHP_URL_PATH));
        $target_url = esc_url_raw($_POST['target_url']); // Keep full URL for target
        $redirect_type = in_array($_POST['redirect_type'], ['301', '302']) ? $_POST['redirect_type'] : '301';
        error_log("ZYNITH SEO DEBUG: add_redirect - Source: $source_url, Target: $target_url, Type: $redirect_type");

        // Check if the source URL already exists
        $existing_redirect = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE source_url = %s",
            $source_url
        ));
        error_log("ZYNITH SEO DEBUG: Query result for existing redirect: " . var_export($existing_redirect, true));

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
            error_log("ZYNITH SEO DEBUG: Redirect inserted successfully for $source_url");
        }
        else {
            error_log("ZYNITH SEO ERROR: Redirect for $source_url already exists");
            echo '<div class="notice notice-error"><p>A redirect for this source URL already exists.</p></div>';
        }
    }

    // Handle delete row action
    if (isset($_POST['delete_row'])) {
        $row_id = intval($_POST['delete_row']);
        error_log("ZYNITH SEO DEBUG: delete_row POST detected for row ID: $row_id");
        $wpdb->delete($table_name, array('id' => $row_id), array('%d'));
        error_log("ZYNITH SEO DEBUG: Redirect row $row_id deleted");
    }

    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        error_log("ZYNITH SEO DEBUG: Table $table_name does not exist");
        echo '<div class="wrap"><h1>' . __('Zynith SEO Redirect Manager', 'zynith-seo') . '</h1>';
        echo '<form method="post"><input type="submit" name="create_redirects_table" class="button-primary" value="Create Redirects Database" /></form></div>';
    }
    else {
        error_log("ZYNITH SEO DEBUG: Table $table_name exists; proceeding with import/export and display");
        // Handle Import CSV
    if (isset($_POST['import_redirects']) && !empty($_FILES['redirect_csv']['tmp_name']) && !empty($_POST['import_table'])) {
        error_log("ZYNITH SEO DEBUG: import_redirects POST detected");
        $dynamic_table = sanitize_text_field($_POST['import_table']);
        zynith_seo_import_redirects($_FILES['redirect_csv'], $dynamic_table);
        error_log("ZYNITH SEO DEBUG: Returned from zynith_seo_import_redirects");
    }

        // Handle Export CSV
        if (isset($_POST['export_redirects'])) {
            error_log("ZYNITH SEO DEBUG: export_redirects POST detected");
            zynith_seo_export_redirects();
            error_log("ZYNITH SEO DEBUG: Exiting after export_redirects (exit should have occurred)");
        }

        $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY timestamp DESC");
        error_log("ZYNITH SEO DEBUG: Fetched " . count($results) . " redirect rows");
        $site_url = rtrim(get_site_url(), '/'); // Get the site URL for prepending
        ?>
        <div class="wrap">
            <h1><?php _e('Zynith SEO Redirect Manager', 'zynith-seo'); ?></h1>

            <!-- Import & Export Buttons -->
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <!-- Import Form -->
            <form method="post" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 5px;">
                <input type="file" name="redirect_csv" accept=".csv" required>
                <input type="hidden" name="import_table" value="<?php echo esc_attr($table_name); ?>">
                <input type="submit" name="import_redirects" class="button-primary" value="Import CSV">
            </form>

                <!-- Export Form -->
                <form method="post">
                    <a href="<?php echo admin_url('admin-post.php?action=zynith_seo_export_redirects'); ?>" class="button button-primary">
    Export Redirects
</a>
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
            <?php error_log("ZYNITH SEO DEBUG: Displaying redirect row ID: " . $row->id); ?>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="4"><?php _e('No redirects set up yet.', 'zynith-seo'); ?></td>
        </tr>
        <?php error_log("ZYNITH SEO DEBUG: No redirect rows found to display"); ?>
    <?php endif; ?>
</tbody>
            </table>

            <form method="post" style="margin-top: 20px;">
                <input type="submit" name="delete_redirects_table" class="button-secondary" value="Delete Table" />
            </form>
        </div>
        <?php
    }
    error_log('ZYNITH SEO DEBUG: Exiting zynith_seo_render_redirects_page');
}

function zynith_seo_export_redirects() {
    error_log('ZYNITH SEO DEBUG: Entering zynith_seo_export_redirects');

    // Security check to prevent unauthorized access
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to export redirects.', 'zynith-seo'));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_redirects';

    $redirects = $wpdb->get_results("SELECT source_url, target_url, redirect_type FROM $table_name", ARRAY_A);
    error_log("ZYNITH SEO DEBUG: Retrieved " . count($redirects) . " redirects for export");

    if (empty($redirects)) {
        error_log("ZYNITH SEO DEBUG: No redirects available for export");
        wp_die(__('No redirects available for export.', 'zynith-seo'));
    }

    // **Prevent previous output**
    if (ob_get_length()) {
        ob_end_clean();
    }

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=redirects.csv');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Source URL', 'Target URL', 'Redirect Type']);

    foreach ($redirects as $redirect) {
        fputcsv($output, $redirect);
    }

    fclose($output);
    exit; // Prevents WordPress from adding unexpected output
}
add_action('admin_post_zynith_seo_export_redirects', 'zynith_seo_export_redirects');


function zynith_seo_import_redirects($file, $dynamic_table_name) {
    error_log("ZYNITH SEO DEBUG: Entering zynith_seo_import_redirects with table $dynamic_table_name");

    global $wpdb;
    $table_name = sanitize_text_field($dynamic_table_name); // Use dynamic table name

    // Check if the file was uploaded correctly
    if (!isset($file['tmp_name']) || empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        error_log("ZYNITH SEO ERROR: File upload error - " . $file['error']);
        echo '<div class="notice notice-error"><p>Error uploading CSV file.</p></div>';
        return;
    }

    // Open the CSV file
    if (($handle = fopen($file['tmp_name'], 'r')) !== FALSE) {
        error_log("ZYNITH SEO DEBUG: CSV file opened successfully");

        // Read the header row
        $header = fgetcsv($handle);
        if (!$header || count($header) < 3) {
            error_log("ZYNITH SEO ERROR: Invalid CSV format");
            echo '<div class="notice notice-error"><p>Invalid CSV format. Ensure it has "Source URL, Target URL, Redirect Type".</p></div>';
            fclose($handle);
            return;
        }

        // Process each row
        $inserted_count = 0;
        $skipped_count = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if (count($data) < 3) {
                error_log("ZYNITH SEO WARNING: Skipping invalid row - " . print_r($data, true));
                $skipped_count++;
                continue;
            }

            $source_url = trailingslashit(parse_url(esc_url_raw(trim($data[0])), PHP_URL_PATH));
            $target_url = esc_url_raw(trim($data[1]));
            $redirect_type = in_array(trim($data[2]), ['301', '302']) ? trim($data[2]) : '301';

            error_log("ZYNITH SEO DEBUG: Importing redirect - Source: $source_url | Target: $target_url | Type: $redirect_type");

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

                if ($wpdb->insert_id) {
                    error_log("ZYNITH SEO DEBUG: Redirect inserted for $source_url");
                    $inserted_count++;
                } else {
                    error_log("ZYNITH SEO ERROR: Failed to insert redirect for $source_url");
                }
            } else {
                error_log("ZYNITH SEO DEBUG: Redirect for $source_url already exists, skipping insertion");
                $skipped_count++;
            }
        }

        fclose($handle);
        error_log("ZYNITH SEO DEBUG: CSV import completed - Inserted: $inserted_count, Skipped: $skipped_count");

        echo '<div class="notice notice-success"><p>Import complete! Inserted: ' . $inserted_count . ', Skipped: ' . $skipped_count . ' (duplicates or invalid rows).</p></div>';
    } else {
        error_log("ZYNITH SEO ERROR: Failed to open CSV file for import");
        echo '<div class="notice notice-error"><p>Error reading CSV file.</p></div>';
    }
}


// Apply redirects on frontend
function zynith_seo_handle_redirects() {
    error_log("ZYNITH SEO DEBUG: Entering zynith_seo_handle_redirects");
    if (is_admin()) {
        error_log("ZYNITH SEO DEBUG: is_admin() true, skipping redirects");
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_redirects';

    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        error_log('ZYNITH SEO DEBUG: Redirects table does not exist.');
        return;
    }

    // Normalize the requested path (strip query parameters and ensure trailing slash)
    $requested_path = trailingslashit(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    error_log("ZYNITH SEO DEBUG: Requested Path - $requested_path");

    // Prepare SQL query and log it
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE source_url = %s", $requested_path);
    error_log("ZYNITH SEO DEBUG: SQL Query - $query");

    // Execute query
    $redirect = $wpdb->get_row($query);

    if ($redirect) {
        error_log("ZYNITH SEO DEBUG: Redirect Found! Redirecting to $redirect->target_url");
        if (!headers_sent()) {
            error_log("ZYNITH SEO DEBUG: Headers NOT sent, performing redirect");
            wp_redirect($redirect->target_url, (int) $redirect->redirect_type);
            exit;
        } else {
            error_log("ZYNITH SEO ERROR: Headers already sent, cannot redirect.");
        }
    } else {
        error_log("ZYNITH SEO DEBUG: No Redirect Found for $requested_path");
    }
}
add_action('template_redirect', 'zynith_seo_handle_redirects');

// Create the redirects table
function zynith_seo_create_redirects_table() {
    error_log("ZYNITH SEO DEBUG: Entering zynith_seo_create_redirects_table");
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
    error_log("ZYNITH SEO DEBUG: SQL for table creation: $sql");

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    error_log("ZYNITH SEO DEBUG: Exiting zynith_seo_create_redirects_table");
}

// Delete the redirects table
function zynith_seo_delete_redirects_table() {
    error_log("ZYNITH SEO DEBUG: Entering zynith_seo_delete_redirects_table");
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_redirects';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    error_log("ZYNITH SEO DEBUG: Table $table_name deleted");
}

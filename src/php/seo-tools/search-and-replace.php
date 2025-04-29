<?php
defined('ABSPATH') or exit;

// Add submenu for Search and Replace under Zynith SEO
function zynith_seo_add_search_replace_submenu() {
    add_submenu_page(
        'zynith_seo_dashboard',             // Parent slug
        'Search and Replace',               // Page title
        'Search & Replace',                 // Menu title
        'manage_options',                   // Capability
        'zynith_search_replace',            // Menu slug
        'zynith_search_replace_submenu_page' // Callback function
    );
}
add_action('admin_menu', 'zynith_seo_add_search_replace_submenu');

// Display admin notices after form submission
function zynith_seo_display_admin_notice() {
    if (isset($_GET['zynith_search_replace_action'])) {
        $action = sanitize_text_field($_GET['zynith_search_replace_action']);
        if ($action == 'replace') {
            echo '<div class="notice notice-success is-dismissible"><p>Search and replace operation completed successfully.</p></div>';
        } elseif ($action == 'delete') {
            echo '<div class="notice notice-success is-dismissible"><p>Selected tables were deleted successfully.</p></div>';
        }
    }
}
add_action('admin_notices', 'zynith_seo_display_admin_notice');

// Display function for Search and Replace Submenu Page
function zynith_search_replace_submenu_page() {
    global $wpdb;

    $dry_run_results = [];

    // Handle Replace or Dry Run action
    if (isset($_POST['perform_replace']) && !empty($_POST['search_term']) && !empty($_POST['replace_term'])) {
        $search_term = sanitize_text_field($_POST['search_term']);
        $replace_term = sanitize_text_field($_POST['replace_term']);
        $tables = $_POST['tables'] ?? [];
        $dry_run = isset($_POST['dry_run']) ? true : false;

        if (!empty($tables)) {
            if ($dry_run) {
                foreach ($tables as $table) {
                    $table = sanitize_text_field($table);
                    $dry_run_results = array_merge($dry_run_results, zynith_search_replace($search_term, $replace_term, $table, true));
                }
            } else {
                $total_replaced_count = 0;
                foreach ($tables as $table) {
                    $table = sanitize_text_field($table);
                    $total_replaced_count += zynith_search_replace($search_term, $replace_term, $table, false);
                }
                wp_redirect(add_query_arg('zynith_search_replace_action', 'replace', menu_page_url('zynith_search_replace', false)));
                exit;
            }
        }
    }

    // Handle Delete Table action
    if (isset($_POST['delete_table'])) {
        $tables = $_POST['tables'] ?? [];

        if (!empty($tables)) {
            $total_deleted_count = 0;
            foreach ($tables as $table) {
                $table = sanitize_text_field($table);
                $deleted = zynith_delete_table($table);
                if ($deleted) {
                    $total_deleted_count++;
                }
            }
            wp_redirect(add_query_arg('zynith_search_replace_action', 'delete', menu_page_url('zynith_search_replace', false)));
            exit;
        }
    }

    ?>
    <div class="wrap">
        <h1>Search and Replace</h1>
        <p>Specify the search term, replace term, and select tables to perform replacement, simulate changes, or deletion.</p>
        <p><strong>⚠️ Warning:</strong> Always make a full database backup before running these operations.</p>

        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="search_term">Search Term</label></th>
                    <td><input type="text" id="search_term" name="search_term" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="replace_term">Replace Term</label></th>
                    <td><input type="text" id="replace_term" name="replace_term" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="table">Select Tables</label></th>
                    <td>
                        <select id="table" name="tables[]" multiple required style="height: 120px;">
                            <?php
                            $tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
                            foreach ($tables as $table) {
                                echo '<option value="' . esc_attr($table[0]) . '">' . esc_html($table[0]) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Dry Run</th>
                    <td><label><input type="checkbox" name="dry_run" value="1"> Simulate only, no changes made</label></td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" name="perform_replace" class="button button-primary">Replace</button>
                <button type="submit" name="delete_table" class="button button-secondary" style="background-color: #d9534f; color: #fff;">Delete Table</button>
            </p>
        </form>

        <?php if (!empty($dry_run_results)) : ?>
            <h2>Dry Run Results</h2>
            <p>The following would be affected by the operation:</p>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Table</th>
                        <th>Field</th>
                        <th>Matches</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dry_run_results as $result) : ?>
                        <tr>
                            <td><?php echo esc_html($result['table']); ?></td>
                            <td><?php echo esc_html($result['field']); ?></td>
                            <td><?php echo intval($result['matches']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

// Function to Perform Search and Replace (or Dry Run)
function zynith_search_replace($search_term, $replace_term, $table, $dry_run = false) {
    global $wpdb;
    $fields = $wpdb->get_results("SHOW COLUMNS FROM `$table`", ARRAY_A);
    $results = [];
    $replaced_count = 0;

    foreach ($fields as $field) {
        $field_name = $field['Field'];

        if ($dry_run) {
            // Only count matches, don't modify
            $select_query = $wpdb->prepare(
                "SELECT COUNT(*) FROM `$table` WHERE `$field_name` LIKE %s",
                '%' . $wpdb->esc_like($search_term) . '%'
            );
            $count = $wpdb->get_var($select_query);
            if ($count > 0) {
                $results[] = [
                    'table'  => $table,
                    'field'  => $field_name,
                    'matches'=> $count
                ];
            }
        } else {
            // Perform actual replacement
            $update_query = $wpdb->prepare(
                "UPDATE `$table` SET `$field_name` = REPLACE(`$field_name`, %s, %s) WHERE `$field_name` LIKE %s",
                $search_term,
                $replace_term,
                '%' . $wpdb->esc_like($search_term) . '%'
            );
            $replaced_count += $wpdb->query($update_query);
        }
    }

    return $dry_run ? $results : $replaced_count;
}

// Function to Delete the Table
function zynith_delete_table($table) {
    global $wpdb;
    $result = $wpdb->query("DROP TABLE IF EXISTS `$table`");
    return ($result !== false);
}

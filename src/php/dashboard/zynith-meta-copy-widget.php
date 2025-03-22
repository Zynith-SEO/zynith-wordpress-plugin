<?php

defined('ABSPATH') or exit;

// Display function for the Meta Copy and Export Widget
function zynith_meta_copy_widget_display() {
    echo '<div style="padding: 15px; border: 1px solid #e2e4e7; border-radius: 8px; background-color: #f9fafb;">';
    echo '<h3 style="font-size: 18px; margin-top: 0; color: #2e3c52;">Copy and Export Meta Information</h3>';
    echo '<p style="font-size: 14px; color: #2e3c52; line-height: 1.5;">Easily copy meta information from Yoast, RankMath, or Zynith <9.0 to Zynith SEO, including noindex and nofollow settings, or export Zynith SEO meta data to CSV.</p>';
    
    echo '<form method="post">';
    echo wp_nonce_field('zynith_export_action');
    echo '<button type="submit" name="copy_from_yoast" class="button button-primary" style="margin-top: 10px; margin-right: 10px;">Copy from Yoast</button>';
    echo '<button type="submit" name="copy_from_rankmath" class="button button-secondary" style="margin-top: 10px; margin-right: 10px;">Copy from RankMath</button>';
    echo '<button type="submit" name="copy_from_old_zynith" class="button button-secondary" style="margin-top: 10px; margin-right: 10px;">Copy from Zynith <9.0</button>';
    echo '<button type="submit" name="export_zynith" class="button button-secondary" style="margin-top: 10px;">Export Zynith</button>';
    echo '</form>';

    // Handle Copy from Yoast action
    if (isset($_POST['copy_from_yoast'])) {
        $copied_count = zynith_copy_meta_from_yoast();
        echo '<p style="font-size: 14px; color: #444; margin-top: 10px;">Copied ' . $copied_count . ' meta fields from Yoast.</p>';
    }

    // Handle Copy from RankMath action
    if (isset($_POST['copy_from_rankmath'])) {
        $copied_count = zynith_copy_meta_from_rankmath();
        echo '<p style="font-size: 14px; color: #444; margin-top: 10px;">Copied ' . $copied_count . ' meta fields from RankMath.</p>';
    }

    // Handle Copy from Zynith <9.0 action
    if (isset($_POST['copy_from_old_zynith'])) {
        $copied_count = zynith_copy_meta_from_old_zynith();
        echo '<p style="font-size: 14px; color: #444; margin-top: 10px;">Copied ' . $copied_count . ' meta fields from Zynith <9.0.</p>';
    }

    // Handle Export Zynith action
    if (isset($_POST['export_zynith'])) zynith_export_to_csv();
    
    // Check if old data exists
    $old_data_exists = zynith_check_old_data_exists();
    if ($old_data_exists) {
        echo '</div>';
        echo '<h2 style="margin: 33px 0 1em;">Remove Old Redundant Zynith Data</h2>';
        echo '<div style="padding: 15px; border: 1px solid #e2e4e7; border-radius: 8px; background-color: #f9fafb;">';
        echo '<p>Clean your database by removing unused, outdated data from older versions of Zynith SEO. This helps keep your site lean and optimized.</p>';
        echo '<form method="post">';
        wp_nonce_field('zynith_purge_action');
        echo '<button type="submit" name="purge_old_data" class="button" style="margin-top: 10px; background-color: #dc3232; color: #ffffff;">Purge Old Data</button>';
        echo '</form>';
        echo '<div style="margin-top: 20px; padding: 15px; border: 1px solid #dc3232; border-radius: 8px; background-color: #fff3f3;">';
        echo '<strong style="font-size: 14px; color: #dc3232;">WARNING:</strong> This will permanently delete old Zynith SEO data, including table entries for <em>Zynith Snippets</em>. You cannot undo this action or roll back to a pre-9.0 version that relies on this data. <strong>Current</strong> data in use will remain unaffected. It’s strongly recommended to back up your database before purging.';
        echo '</div>';
        echo '</div>';
    }

    // Handle the Purge Old Data action
    if (isset($_POST['purge_old_data'])) {
        // Security check
        if (!current_user_can('manage_options') || !check_admin_referer('zynith_purge_action')) wp_die(__('You are not allowed to perform this action.'));

        $deleted_count = zynith_seo_purge_old_data();
        echo '<p style="font-size: 14px; color: #444; margin-top: 10px;">Purged ' . $deleted_count . ' entries of old data. The redundant table/data should now be removed.</p>';
    }

}

// Function to export Zynith SEO data to CSV
function zynith_export_to_csv() {
    global $wpdb;

    // Verify user permission and nonce for security
    if (!current_user_can('manage_options') || !check_admin_referer('zynith_export_action')) wp_die(__('You are not allowed to perform this action.'));
    
    // Set the filename for the CSV export
    $filename = "zynith_seo_export_" . date("Y-m-d") . ".csv";

    // Clear any output buffering to prevent other content from interfering
    ob_clean();
    
    // Set the headers for CSV export
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Open the output stream
    $output = fopen("php://output", "w");

    // Write the header row for the CSV
    fputcsv($output, ['Page ID', 'Page Title', 'Meta Title', 'Meta Description', 'Index', 'Follow']);

    // Query all published pages
    $posts = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'");
    
    // Loop through each page and retrieve Zynith SEO meta data
    foreach ($posts as $post) {
        $page_id = $post->ID;
        $page_title = $post->post_title;
        $meta_title = get_post_meta($page_id, '_zynith_seo_meta_title', true);
        $meta_description = get_post_meta($page_id, '_zynith_seo_meta_description', true);
        $no_index = get_post_meta($page_id, '_zynith_seo_no_index', true) ? 'noindex' : 'index';
        $no_follow = get_post_meta($page_id, '_zynith_seo_no_follow', true) ? 'nofollow' : 'follow';
        
        // Write the row for each page
        fputcsv($output, [$page_id, $page_title, $meta_title, $meta_description, $no_index, $no_follow]);
    }

    // Close the output stream
    fclose($output);
    
    // Terminate the script to prevent WordPress from adding any additional content
    exit;
}

// Copy meta information from Yoast to Zynith SEO
function zynith_copy_meta_from_yoast() {
    global $wpdb;
    $meta_fields = [
        '_yoast_wpseo_title' => '_zynith_seo_meta_title',
        '_yoast_wpseo_metadesc' => '_zynith_seo_meta_description',
        '_yoast_wpseo_meta-robots-noindex' => '_zynith_seo_no_index',
        '_yoast_wpseo_meta-robots-nofollow' => '_zynith_seo_no_follow'
    ];
    $copied_count = 0;

    foreach ($meta_fields as $yoast_key => $zynith_key) {
        $posts = $wpdb->get_results($wpdb->prepare("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s", $yoast_key));
        
        foreach ($posts as $post) {
            $value = ($yoast_key == '_yoast_wpseo_meta-robots-noindex' || $yoast_key == '_yoast_wpseo_meta-robots-nofollow') 
                     ? ($post->meta_value == '1' ? '1' : '') 
                     : $post->meta_value;
            update_post_meta($post->post_id, $zynith_key, $value);
            $copied_count++;
        }
    }
    return $copied_count;
}

// Copy meta information from RankMath to Zynith SEO
function zynith_copy_meta_from_rankmath() {
    global $wpdb;
    $meta_fields = [
        'rank_math_title' => '_zynith_seo_meta_title',
        'rank_math_description' => '_zynith_seo_meta_description',
        'rank_math_robots' => ['_zynith_seo_no_index', '_zynith_seo_no_follow']
    ];
    $copied_count = 0;

    foreach ($meta_fields as $rankmath_key => $zynith_key) {
        $posts = $wpdb->get_results($wpdb->prepare("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s", $rankmath_key));

        foreach ($posts as $post) {
            if ($rankmath_key === 'rank_math_robots') {
                // Parse RankMath's robots settings
                $no_index = strpos($post->meta_value, 'noindex') !== false ? '1' : '';
                $no_follow = strpos($post->meta_value, 'nofollow') !== false ? '1' : '';
                update_post_meta($post->post_id, $zynith_key[0], $no_index);
                update_post_meta($post->post_id, $zynith_key[1], $no_follow);
            }
            else {
                update_post_meta($post->post_id, $zynith_key, $post->meta_value);
            }
            $copied_count++;
        }
    }
    return $copied_count;
}

// Copy meta information from Zynith <9.0 to the current Zynith SEO
function zynith_copy_meta_from_old_zynith() {
    global $wpdb;

    // Define the mapping of old meta keys to new meta keys
    $meta_fields = [
        '_custom_meta_title'        => '_zynith_seo_meta_title',
        '_custom_meta_description'  => '_zynith_seo_meta_description',
        '_custom_noindex'           => '_zynith_seo_no_index',
        '_custom_nofollow'          => '_zynith_seo_no_follow',
        '_custom_target_keyword'    => '_zynith_seo_target_keyword',
        '_custom_meta_og_image'     => '_zynith_seo_meta_og_image'
    ];

    // Define the old `_custom_schema` for schema data
    $old_schema_meta_key = '_custom_schema';
    $new_schema_table = 'zynith_schema_settings';
    $copied_count = 0;

    // Handle standard meta fields migration
    foreach ($meta_fields as $old_key => $new_key) {
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT post_id, meta_value 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = %s
        ", $old_key), ARRAY_A);

        foreach ($results as $row) {
            $post_id = $row['post_id'];
            $value = $row['meta_value'];

            // Normalize 'custom_noindex' and 'custom_nofollow' values
            if ($old_key === 'custom_noindex' || $old_key === 'custom_nofollow') $value = ($value === 'yes') ? '1' : '';
            
            // Update the new meta key with the old value
            update_post_meta($post_id, $new_key, $value);
            $copied_count++;
        }
    }

    // Handle `_custom_schema` migration to `zynith_schema_settings` table
    $schema_results = $wpdb->get_results($wpdb->prepare("
        SELECT post_id, meta_value 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = %s
    ", $old_schema_meta_key), ARRAY_A);

    foreach ($schema_results as $row) {
        $page_id = $row['post_id'];
        $schema_data = $row['meta_value'];

        // Insert into the `zynith_schema_settings` table
        $inserted = $wpdb->insert(
            $new_schema_table,
            [
                'page_id' => $page_id,
                'schema_data' => $schema_data,
            ],
            [
                '%d', // page_id is an integer
                '%s', // schema_data is a string
            ]
        );

        if ($inserted !== false) $copied_count++;
    }
    return $copied_count;
}

function zynith_check_old_data_exists() {
    global $wpdb;

    // Array of old table names
    $old_tables = [
        $wpdb->prefix . 'zynith_snippets'
    ];

    // Loop through each table and check if it exists
    foreach ($old_tables as $table_name) {
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");

        // If a table exists, return true immediately
        if ($table_exists === $table_name) return true;
    }

    // If no tables exist, return false
    return false;
}

function zynith_seo_purge_old_data() {
    global $wpdb;

    // The count of deleted/purged items
    $deleted_count = 0;

    // Drop old table
    $old_table_name = $wpdb->prefix . 'zynith_snippets';
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$old_table_name}'");
    if ($table_exists === $old_table_name) {
        // Drop the table
        $wpdb->query("DROP TABLE IF EXISTS {$old_table_name}");
        $deleted_count++;
    }

    // If there are other old options, you could remove them here as well, for example:
    /*
    $old_option = 'zynith_seo_legacy_option';
    if (get_option($old_option) !== false) {
        delete_option($old_option);
        $deleted_count++;
    }
    */

    // Return how many items/tables/options we removed
    return $deleted_count;
}
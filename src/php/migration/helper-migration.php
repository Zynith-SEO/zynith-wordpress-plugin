<?php
defined('ABSPATH') or exit;

// Migrate old data
function zynith_seo_run_migration_once_only() {
    $zynith_seo_migration_version = 8; // 10.1.9
    $this_migration_version = (int) get_option('zynith_seo_migration_version', 0);

    if ($this_migration_version >= $zynith_seo_migration_version) return;

    global $wpdb;
    
    if ($this_migration_version < 7) {

        // Update old options with new options
        $option_mappings = [
            'business_name'             => 'zynith_seo_company_name',
            'zynith_business_name'      => 'zynith_seo_company_name',
            'street_address'            => 'zynith_seo_business_address',
            'zynith_street_address'     => 'zynith_seo_business_address',
            'business_email'            => 'zynith_seo_business_email',
            'zynith_business_email'     => 'zynith_seo_business_email',
            'phone_number'              => 'zynith_seo_business_phone',
            'zynith_phone_number'       => 'zynith_seo_business_phone',
            'address_locality'          => 'zynith_seo_business_locality',
            'zynith_address_locality'   => 'zynith_seo_business_locality',
            'address_region'            => 'zynith_seo_business_region',
            'zynith_address_region'     => 'zynith_seo_business_region',
            'postal_code'               => 'zynith_seo_business_postal_code',
            'zynith_postal_code'        => 'zynith_seo_business_postal_code',
            'address_country'           => 'zynith_seo_country',
            'zynith_address_country'    => 'zynith_seo_country',
            'logo'                      => 'zynith_seo_business_logo_url',
            'zynith_logo'               => 'zynith_seo_business_logo_url'
        ];
        foreach ($option_mappings as $old_option => $new_option) {
            $value = get_option($old_option, null);
            if (null !== $value) {
                // If this is the logo field, check if the value is numeric => convert to full URL
                if (in_array($old_option, ['logo', 'zynith_logo'], true) && is_numeric($value)) {
                    $attachment_url = wp_get_attachment_url((int) $value);
                    if ($attachment_url) $value = $attachment_url;
                }
                update_option($new_option, $value);
                delete_option($old_option);
            }
        }

        // Update inverted old options
        $inverted_options = [
            'zynith_enable_404_monitor'      => 'zynith_seo_disable_404_monitor',
            'zynith_enable_script_manager'   => 'zynith_seo_disable_script_manager',
        ];        
        foreach ($inverted_options as $old_option => $new_option) {
            $old_value = get_option($old_option, null);
            if (null !== $old_value) {
                $new_value = ((int) $old_value === 1) ? 0 : 1;
                update_option($new_option, $new_value);
                delete_option($old_option);
            }
        }

        // Rename or transform postmeta keys (Direct DB rename, so no leftover postmeta for old_meta remains.)
        $meta_mappings = [
            '_custom_meta_title'        => '_zynith_seo_meta_title',
            '_custom_meta_description'  => '_zynith_seo_meta_description',
            '_custom_noindex'           => '_zynith_seo_no_index',
            '_custom_nofollow'          => '_zynith_seo_no_follow',
            '_custom_meta_og_image'     => '_zynith_seo_og_meta_image'
        ];
        foreach ($meta_mappings as $old_meta => $new_meta) {
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->postmeta} 
                     SET meta_key = %s 
                     WHERE meta_key = %s",
                    $new_meta,
                    $old_meta
                )
            );
        }
        
        // Convert "yes"/"no" => "1"/"" for _zynith_seo_no_index
        $wpdb->query("
            UPDATE {$wpdb->postmeta}
            SET meta_value = '1'
            WHERE meta_key = '_zynith_seo_no_index' AND meta_value = 'yes'
        ");
        $wpdb->query("
            UPDATE {$wpdb->postmeta}
            SET meta_value = ''
            WHERE meta_key = '_zynith_seo_no_index' AND meta_value = 'no'
        ");

        // Convert "yes"/"no" => "1"/"" for _zynith_seo_no_follow
        $wpdb->query("
            UPDATE {$wpdb->postmeta}
            SET meta_value = '1'
            WHERE meta_key = '_zynith_seo_no_follow' AND meta_value = 'yes'
        ");
        $wpdb->query("
            UPDATE {$wpdb->postmeta}
            SET meta_value = ''
            WHERE meta_key = '_zynith_seo_no_follow' AND meta_value = 'no'
        ");
        
        // Migrate _custom_schema → *_zynith_schema_settings table
        $table_name = $wpdb->prefix . 'zynith_schema_settings';

        // Create the schema table
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            if (function_exists('zynith_seo_create_schema_table')) {
                zynith_seo_create_schema_table();
            }
            else {
                return;
            }
        }
        
        // Get all meta rows where meta_key = '_custom_schema'
        $schema_rows = $wpdb->get_results("
            SELECT post_id, meta_value
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_custom_schema'
        ");

        if (!empty($schema_rows)) {
            foreach ($schema_rows as $row) {
                $post_id     = (int) $row->post_id;
                $schema_data = $row->meta_value;

                // Check if we already have an entry in the new table
                $existing_id = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT id FROM $table_name WHERE page_id = %d",
                        $post_id
                    )
                );

                // Insert or update
                if ($existing_id) {
                    // Update existing row
                    $wpdb->update(
                        $table_name,
                        ['schema_data' => $schema_data],
                        ['id'          => $existing_id],
                        ['%s'],
                        ['%d']
                    );
                }
                else {
                    // Insert new row
                    $wpdb->insert(
                        $table_name,
                        [
                            'page_id'     => $post_id,
                            'schema_data' => $schema_data
                        ],
                        ['%d', '%s']
                    );
                }
            }

            // Remove old _custom_schema meta to keep DB clean (optional)
            $wpdb->query(
                "DELETE FROM {$wpdb->postmeta}
                 WHERE meta_key = '_custom_schema'"
            );
        }

        // Migrate old script table data to new table.
        $old_table_name = $wpdb->prefix . 'zynith_snippets';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$old_table_name}'");
        if ($table_exists === $old_table_name) {
            if (function_exists('zynith_seo_create_script_table')) {
                zynith_seo_create_script_table();
            }
            else {
                return;
            }
        }

        // Delete old sitemap files if they exist
        $sitemap_filename = get_option('zynith_custom_sitemap_filename', 'sitemap.xml');
        if ($sitemap_filename) {
            $sitemap_path = ABSPATH . $sitemap_filename;
            if (file_exists($sitemap_path)) unlink($sitemap_path);

            // Also check for matching .xsl file
            $sitemap_xsl_path = ABSPATH . str_replace('.xml', '.xsl', $sitemap_filename);
            if (file_exists($sitemap_xsl_path)) unlink($sitemap_xsl_path);
        }
    
    }
    
    if ($this_migration_version < 8) {
            
        // Remove old options that are no longer used at all
        $unused_options = [
            'zynith_signals_instructions',
            'zynith_show_quick_ryter',
            'zynith_enable_metabox_warnings',
            'zynith_allow_auto_meta',
            'zynith_seo_signals_enabled',
            'zynith_seo_disable_placeholders'
        ];
        foreach ($unused_options as $old_option) delete_option($old_option);
    }
    
    // Set a flag to indicate migration is complete
    update_option('zynith_seo_migration_version', $zynith_seo_migration_version);
}
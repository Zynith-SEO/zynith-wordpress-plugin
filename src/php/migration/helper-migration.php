<?php
defined('ABSPATH') or exit;

function zynith_seo_run_migration_once_only() {
    if (
        !defined('ZYNITH_SEO_VERSION') ||
        version_compare(ZYNITH_SEO_VERSION, '10.0.0', '>=') ||
        get_option('zynith_seo_migration_version') === 'legacy_cleanup_complete'
    ) {
        return;
    }

    global $wpdb;

    // Option remapping
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
    foreach ($option_mappings as $old => $new) {
        $value = get_option($old, null);
        if (null !== $value) {
            if (in_array($old, ['logo', 'zynith_logo'], true) && is_numeric($value)) {
                $attachment_url = wp_get_attachment_url((int) $value);
                if ($attachment_url) $value = $attachment_url;
            }
            update_option($new, $value);
            delete_option($old);
        }
    }

    // Inverted option remapping
    $inverted_options = [
        'zynith_enable_404_monitor'    => 'zynith_seo_disable_404_monitor',
        'zynith_enable_script_manager' => 'zynith_seo_disable_script_manager',
    ];
    foreach ($inverted_options as $old => $new) {
        $val = get_option($old, null);
        if (null !== $val) {
            update_option($new, ((int) $val === 1) ? 0 : 1);
            delete_option($old);
        }
    }

    // Postmeta key renaming
    $meta_mappings = [
        '_custom_meta_title'       => '_zynith_seo_meta_title',
        '_custom_meta_description' => '_zynith_seo_meta_description',
        '_custom_noindex'          => '_zynith_seo_no_index',
        '_custom_nofollow'         => '_zynith_seo_no_follow',
        '_custom_meta_og_image'    => '_zynith_seo_og_meta_image'
    ];
    foreach ($meta_mappings as $old => $new) {
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->postmeta} SET meta_key = %s WHERE meta_key = %s",
                $new,
                $old
            )
        );
    }

    // Convert "yes"/"no" to "1"/"" for noindex/nofollow
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

    // Create schema table if missing
    $schema_table = $wpdb->prefix . 'zynith_schema_settings';
    if ($wpdb->get_var("SHOW TABLES LIKE '$schema_table'") != $schema_table) {
        if (function_exists('zynith_seo_create_schema_table')) {
            zynith_seo_create_schema_table();
        } else {
            return;
        }
    }

    // Migrate _custom_schema postmeta into schema table
    $schema_rows = $wpdb->get_results("
        SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_custom_schema'
    ");
    if (!empty($schema_rows)) {
        foreach ($schema_rows as $row) {
            $post_id = (int) $row->post_id;
            $schema_data = $row->meta_value;

            $existing = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM $schema_table WHERE page_id = %d",
                    $post_id
                )
            );

            if ($existing) {
                $wpdb->update(
                    $schema_table,
                    ['schema_data' => $schema_data],
                    ['id' => $existing],
                    ['%s'],
                    ['%d']
                );
            } else {
                $wpdb->insert(
                    $schema_table,
                    [
                        'page_id'     => $post_id,
                        'schema_data' => $schema_data
                    ],
                    ['%d', '%s']
                );
            }
        }

        // Clean up old _custom_schema meta
        $wpdb->query("
            DELETE FROM {$wpdb->postmeta}
            WHERE meta_key = '_custom_schema'
        ");
    }

    // Migrate old script table to new format if it existed
    $old_script_table = $wpdb->prefix . 'zynith_snippets';
    $exists = $wpdb->get_var("SHOW TABLES LIKE '{$old_script_table}'");
    if ($exists === $old_script_table) {
        if (function_exists('zynith_seo_create_script_table')) {
            zynith_seo_create_script_table();
        } else {
            return;
        }
    }

    // Delete old sitemap files
    $sitemap_filename = get_option('zynith_custom_sitemap_filename', 'sitemap.xml');
    if ($sitemap_filename) {
        $sitemap_path = ABSPATH . $sitemap_filename;
        $xsl_path = ABSPATH . str_replace('.xml', '.xsl', $sitemap_filename);
        if (file_exists($sitemap_path)) unlink($sitemap_path);
        if (file_exists($xsl_path)) unlink($xsl_path);
    }

    // Clean up deprecated options
    $unused_options = [
        'zynith_signals_instructions',
        'zynith_show_quick_ryter',
        'zynith_enable_metabox_warnings',
        'zynith_allow_auto_meta',
        'zynith_seo_signals_enabled',
        'zynith_seo_disable_placeholders'
    ];
    foreach ($unused_options as $opt) {
        delete_option($opt);
    }

    // Mark migration as complete
    update_option('zynith_seo_migration_version', 'legacy_cleanup_complete');
}

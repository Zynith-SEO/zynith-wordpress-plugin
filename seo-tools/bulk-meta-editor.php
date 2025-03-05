<?php
/**
 * Module Name: Zynith SEO - Bulk Meta Editor
 * Description: Adds Meta Title and Meta Description fields to the Pages and Posts list in the WordPress admin and saves on blur. Also adds noindex/index functionality to the Bulk Actions WP drop-down box.
 * Version:     1.1.1
 * Author:      Zynith SEO
*/
defined('ABSPATH') or exit;

function bulk_meta_editor_enqueue_scripts($hook) {
    $script_path = ZYNITH_SEO_DIR . 'assets/js/bulk-meta-editor.js';
    $script_version = file_exists($script_path) ? filemtime($script_path) : ZYNITH_SEO_VERSION;
    
    wp_enqueue_script('bulk-meta-editor-js', ZYNITH_SEO_URL . 'assets/js/bulk-meta-editor.js', ['jquery'], $script_version, true);
    wp_localize_script('bulk-meta-editor-js', 'bulkMetaEditor', ['ajax_url' => admin_url('admin-ajax.php')]);
}
add_action('load-edit.php', 'zynith_seo_load_edit_php_only');

function zynith_seo_load_edit_php_only() {
    add_action('admin_enqueue_scripts', 'bulk_meta_editor_enqueue_scripts');
}
add_action('load-edit.php', 'zynith_seo_load_edit_php_only');

// Add custom columns for Meta Title and Meta Description
function bulk_meta_editor_add_columns($columns) {
    $columns['meta_title'] = 'Meta Title';
    $columns['meta_description'] = 'Meta Description';
    return $columns;
}
add_filter('manage_page_posts_columns', 'bulk_meta_editor_add_columns');
add_filter('manage_post_posts_columns', 'bulk_meta_editor_add_columns');

// Populate the custom columns with input fields
function bulk_meta_editor_custom_column_content($column, $post_id) {
    if ($column === 'meta_title') {
        $meta_title = get_post_meta($post_id, '_zynith_seo_meta_title', true);
        echo '<textarea class="bulk-meta-title" data-post-id="' . esc_attr($post_id) . '" style="width: 100%;">' . esc_textarea($meta_title) . '</textarea>';
    }
    elseif ($column === 'meta_description') {
        $meta_description = get_post_meta($post_id, '_zynith_seo_meta_description', true);
        echo '<textarea class="bulk-meta-description" data-post-id="' . esc_attr($post_id) . '" style="width: 100%;">' . esc_textarea($meta_description) . '</textarea>';
    }
}
add_action('manage_page_posts_custom_column', 'bulk_meta_editor_custom_column_content', 10, 2);
add_action('manage_post_posts_custom_column', 'bulk_meta_editor_custom_column_content', 10, 2);

// Handle the AJAX request to save meta information
function bulk_meta_editor_save_meta() {
    // Check for necessary permissions
    if (!current_user_can('edit_posts') || !isset($_POST['post_id'])) {
        wp_send_json_error('Invalid permissions or missing post ID.');
        return;
    }

    $post_id = intval($_POST['post_id']);
    $meta_key = sanitize_text_field($_POST['meta_key']);
    $meta_value = sanitize_text_field($_POST['meta_value']);

    // Save the meta information
    update_post_meta($post_id, $meta_key, $meta_value);

    wp_send_json_success('Meta information saved successfully.');
}
add_action('wp_ajax_bulk_meta_editor_save_meta', 'bulk_meta_editor_save_meta');
<?php

defined('ABSPATH') or exit;

// Add two new custom bulk actions to Posts & Pages
function zynith_seo_add_bulk_actions($bulk_actions){
    // Add two new bulk actions
    $bulk_actions['zynith_seo_mark_index']   = __('Mark as Index', 'zynith-seo');
    $bulk_actions['zynith_seo_mark_noindex'] = __('Mark as Noindex', 'zynith-seo');
    return $bulk_actions;
}
add_filter('bulk_actions-edit-page', 'zynith_seo_add_bulk_actions');
add_filter('bulk_actions-edit-post', 'zynith_seo_add_bulk_actions');

// Handle the custom bulk actions
function zynith_seo_handle_bulk_actions($redirect_to, $doaction, $post_ids){
    if ($doaction === 'zynith_seo_mark_index') {
        foreach ($post_ids as $post_id) update_post_meta($post_id, '_zynith_seo_no_index', ''); // Clear no_index
        // Add a query arg so we can show an admin notice
        $redirect_to = add_query_arg('zynith_seo_updated', count($post_ids), $redirect_to);
    }

    if ($doaction === 'zynith_seo_mark_noindex') {
        foreach ($post_ids as $post_id) update_post_meta($post_id, '_zynith_seo_no_index', '1'); // Set no_index to '1'
        $redirect_to = add_query_arg('zynith_seo_updated', count($post_ids), $redirect_to);
    }

    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-page', 'zynith_seo_handle_bulk_actions', 10, 3);
add_filter('handle_bulk_actions-edit-post', 'zynith_seo_handle_bulk_actions', 10, 3);

// Show an admin notice after updating posts
function zynith_seo_bulk_action_admin_notice(){
    if (!empty($_REQUEST['zynith_seo_updated'])) {
        $updated_count = (int) $_REQUEST['zynith_seo_updated'];
        printf('<div id="message" class="updated notice is-dismissible"><p>%d post(s) updated.</p></div>', $updated_count);
    }
}
add_action('admin_notices', 'zynith_seo_bulk_action_admin_notice');
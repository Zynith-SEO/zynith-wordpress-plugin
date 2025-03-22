<?php

defined('ABSPATH') or exit;

function zynith_seo_set_revision_limit() {
    
    // Default to unlimited (-1)
    $revision_limit = get_option('zynith_seo_revision_limit', -1);

    // Check the revision limit and set accordingly
    if ($revision_limit === 0) {
        
        // Disable revisions entirely
        add_filter('wp_revisions_to_keep', '__return_zero');
    }
    elseif ($revision_limit > 0) {
        
        // Limit to user-defined number of revisions
        add_filter('wp_revisions_to_keep', function($num, $post) use ($revision_limit) {
            return $revision_limit;
        }, 10, 2);
    }
}
add_action('init', 'zynith_seo_set_revision_limit');
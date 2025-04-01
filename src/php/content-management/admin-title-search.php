<?php
defined('ABSPATH') or exit;

function zynith_admin_search_by_title_only($search, $wp_query) {
    global $pagenow, $wpdb;

    // Only modify the search if we're in the admin, on the Pages screen, and it's a search query
    if (is_admin() && $pagenow === 'edit.php' && isset($wp_query->query['s']) && $wp_query->query['post_type'] === 'page') {
        $search_term = esc_sql($wpdb->esc_like($wp_query->query['s']));
        $search = " AND {$wpdb->posts}.post_title LIKE '%$search_term%'";
    }
    return $search;
}
add_filter('posts_search', 'zynith_admin_search_by_title_only', 10, 2);
<?php
/**
 * Module Name:    Zynith SEO - Disable Comments
 * Description:    This module disables comments across the entire site, removing comment forms and hiding existing comments.
 * Author:         Zynith SEO
 * Version:        1.0.1
*/
defined('ABSPATH') or exit;

// Disable support for comments and trackbacks in post types
function disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'disable_comments_post_types_support');

// Close comments on the front-end
function disable_comments_status() {
    return false;
}
add_filter('comments_open', 'disable_comments_status', 20, 2);
add_filter('pings_open', 'disable_comments_status', 20, 2);

// Hide existing comments
function disable_comments_hide_existing_comments($comments) {
    return [];
}
add_filter('comments_array', 'disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in the menu
function disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'disable_comments_admin_menu');

// Redirect any user trying to access comments page
function disable_comments_admin_menu_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function disable_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'disable_comments_dashboard');

// Remove comments links from the admin bar
function disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'disable_comments_admin_bar');

// Forcefully dequeue comments scripts and styles
function zynith_seo_disable_comments_scripts_styles() {
    wp_dequeue_script('comment-reply');
    wp_dequeue_style('comments-css');
}
add_action('wp_enqueue_scripts', 'zynith_seo_disable_comments_scripts_styles', 999);
add_action('admin_enqueue_scripts', 'zynith_seo_disable_comments_scripts_styles', 999);

// Force removal of comments capabilities for all roles
function zynith_seo_disable_comments_caps() {
    global $wp_roles;
    if (!isset($wp_roles)) $wp_roles = new WP_Roles();
    $roles = $wp_roles->roles;
    foreach ($roles as $role_name => $role_info) {
        $role = get_role($role_name);
        if ($role) {
            $role->remove_cap('edit_comment');
            $role->remove_cap('moderate_comments');
            $role->remove_cap('edit_comments');
            $role->remove_cap('delete_comments');
            $role->remove_cap('read_comments');
        }
    }
}
add_action('init', 'zynith_seo_disable_comments_caps', 999);
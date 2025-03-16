<?php
/*
Module Name: ZYNITH SEO Custom Post Types
Description: This module allows users to create and manage custom post types (CPTs) natively within ZYNITH SEO.
Author: ZYNITH SEO
Version: 1.6
*/

if (!defined('ABSPATH')) {
    exit;
}

function zynith_seo_add_cpt_menu() {
    add_submenu_page(
        'zynith_seo_dashboard',      
        'Custom Post Types',         
        'Custom Post Types',         
        'manage_options',            
        'zynith-seo-cpt',            
        'zynith_seo_cpt_admin_page'  
    );
}
add_action('admin_menu', 'zynith_seo_add_cpt_menu');

function zynith_seo_cpt_admin_page() {
    // Handle form submissions
    if (isset($_POST['zynith_seo_save_cpt'])) {
        zynith_seo_save_cpt();
        add_settings_error('zynith_seo_messages', 'cpt_saved', 'Custom Post Type saved successfully.', 'success');
    }
    
    if (isset($_POST['delete_cpt'])) {
        zynith_seo_delete_cpt(sanitize_text_field($_POST['delete_cpt']));
        add_settings_error('zynith_seo_messages', 'cpt_deleted', 'Custom Post Type deleted successfully.', 'success');
    }

    settings_errors('zynith_seo_messages');
    ?>
    <div class="wrap">
        <h1>Custom Post Types</h1>

        <h2>Add New Custom Post Type</h2>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label for="cpt_name">CPT Name (slug)</label></th>
                    <td><input type="text" id="cpt_name" name="cpt_name" required></td>
                </tr>
                <tr>
                    <th><label for="cpt_label">CPT Label</label></th>
                    <td><input type="text" id="cpt_label" name="cpt_label" required></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="zynith_seo_save_cpt" class="button-primary" value="Save Custom Post Type"></p>
        </form>

        <h2>Manage Custom Post Types</h2>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th style="width: 40%;">CPT Label</th>
                    <th style="width: 40%;">Slug</th>
                    <th style="width: 20%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $cpts = get_option('zynith_seo_cpts', []);
                if (!empty($cpts)) {
                    foreach ($cpts as $cpt): ?>
                        <tr>
                            <td><strong><?php echo esc_html($cpt['label']); ?></strong></td>
                            <td><?php echo esc_html($cpt['slug']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="delete_cpt" value="<?php echo esc_attr($cpt['slug']); ?>">
                                    <button type="submit" class="button button-secondary" onclick="return confirm('Are you sure you want to delete this CPT?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach;
                } else {
                    echo '<tr><td colspan="3">No custom post types found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Save new CPT
function zynith_seo_save_cpt() {
    if (isset($_POST['cpt_name']) && isset($_POST['cpt_label'])) {
        $cpts = get_option('zynith_seo_cpts', []);
        $slug = sanitize_text_field($_POST['cpt_name']);
        $label = sanitize_text_field($_POST['cpt_label']);
        $cpts[] = ['slug' => $slug, 'label' => $label];
        update_option('zynith_seo_cpts', $cpts);
        
        zynith_seo_register_custom_post_type($slug, $label);
        flush_rewrite_rules();
    }
}

// Delete CPT
function zynith_seo_delete_cpt($slug) {
    $cpts = get_option('zynith_seo_cpts', []);
    foreach ($cpts as $key => $cpt) {
        if ($cpt['slug'] == $slug) {
            unset($cpts[$key]);
            update_option('zynith_seo_cpts', $cpts);
        }
    }
    flush_rewrite_rules();
}

// Register all custom post types on init
function zynith_seo_register_all_cpts() {
    $cpts = get_option('zynith_seo_cpts', []);
    foreach ($cpts as $cpt) {
        zynith_seo_register_custom_post_type($cpt['slug'], $cpt['label']);
    }
}
add_action('init', 'zynith_seo_register_all_cpts');

// Register a single custom post type
function zynith_seo_register_custom_post_type($slug, $label) {
    $args = array(
        'label'             => $label,
        'public'            => true,
        'has_archive'       => true,
        'show_in_menu'      => true,
        'hierarchical'      => true,  // Enables parent-child relationship
        'supports'          => array('title', 'editor', 'thumbnail', 'page-attributes'), // Adds Parent Page support
        'menu_position'     => 25,
        'menu_icon'         => 'dashicons-admin-post',
    );
    register_post_type($slug, $args);
}


// Flush rewrite rules on activation
function zynith_seo_flush_rewrite_rules_on_activation() {
    zynith_seo_register_all_cpts();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'zynith_seo_flush_rewrite_rules_on_activation');

// Optionally, flush rewrite rules on deactivation
function zynith_seo_flush_rewrite_rules_on_deactivation() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'zynith_seo_flush_rewrite_rules_on_deactivation');
?>
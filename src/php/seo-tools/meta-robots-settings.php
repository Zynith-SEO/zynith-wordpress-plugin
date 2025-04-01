<?php
defined('ABSPATH') or exit;

// Disable WordPress default robots meta tag
remove_action('wp_head', 'wp_robots', 1);

// Add the Meta Robots Settings meta box to all public CPTs
function zynith_seo_add_robots_meta_box_to_cpts() {
    $post_types = get_post_types(['public' => true], 'names'); // Get all public post types
    foreach ($post_types as $post_type) {
        add_meta_box(
            'zynith_seo_meta_robots', // ID
            'Meta Robots Settings',  // Title
            'zynith_seo_render_robots_meta_box', // Callback function
            $post_type,  // Post types (including CPTs)
            'side',
            'low'
        );
    }
}
add_action('add_meta_boxes', 'zynith_seo_add_robots_meta_box_to_cpts');

// Render the Meta Robots Settings meta box fields
function zynith_seo_render_robots_meta_box($post) {
    // Retrieve existing meta values
    $no_index   = get_post_meta($post->ID, '_zynith_seo_no_index', true);
    $no_follow  = get_post_meta($post->ID, '_zynith_seo_no_follow', true);

    // Output the fields for No Index and No Follow toggles
    ?>
    <div class="zynith-seo-robots-container">
        <div class="zynith-toggle-group" style="display: flex; flex-direction: column; gap: 6px;">
            <label style="display: flex;">
                <span style="flex-basis: 66px;">No Index</span>
                <label class="zynith-toggle-switch">
                    <input type="checkbox" name="zynith_seo_no_index" <?php checked($no_index, '1'); ?> />
                    <span class="zynith-slider"></span>
                </label>
            </label>
            <label style="display: flex;">
                <span style="flex-basis: 66px;">No Follow</span>
                <label class="zynith-toggle-switch">
                    <input type="checkbox" name="zynith_seo_no_follow" <?php checked($no_follow, '1'); ?> />
                    <span class="zynith-slider"></span>
                </label>
            </label>
        </div>
    </div>
    <?php
}

// Add a "noindex" column to the Pages screen
function zynith_seo_add_noindex_column_pages($columns) {
    $reordered_columns = [];
    foreach ($columns as $key => $value) $reordered_columns[$key] = $value;
    $reordered_columns['zynith_noindex'] = __('Index', ZYNITH_SEO_TEXT_DOMAIN);
    return $reordered_columns;
}
add_filter('manage_page_posts_columns', 'zynith_seo_add_noindex_column_pages', 99);
add_filter('manage_post_posts_columns', 'zynith_seo_add_noindex_column_pages', 99);

// Display the "noindex" column content for Pages
function zynith_seo_render_noindex_column_pages($column_name, $post_id) {
    if ('zynith_noindex' === $column_name) {
        $no_index = get_post_meta($post_id, '_zynith_seo_no_index', true);

        if ($no_index === '1') {
            echo '<span class="dashicons dashicons-hidden" title="noindex" style="color: indianred;"></span>';
        }
        else {
            echo '<span class="dashicons dashicons-visibility" title="index" style="color: cornflowerblue;"></span>';
        }
    }
}
add_action('manage_page_posts_custom_column', 'zynith_seo_render_noindex_column_pages', 10, 2);
add_action('manage_post_posts_custom_column', 'zynith_seo_render_noindex_column_pages', 10, 2);

// Enforce a narrower width for the "Index" column
function zynith_seo_set_admin_columns_width() {
    $screen = get_current_screen();
    if (isset($screen->id) && in_array($screen->id, ['edit-page','edit-post'], true)) {
        echo '<style>
            .fixed .column-zynith_noindex {
                width: 34px !important;
                text-align: center;
            }
        </style>';
    }
}
add_action('admin_head', 'zynith_seo_set_admin_columns_width');

// Save the meta box data when the post is saved
function zynith_seo_save_robots_meta_box_data($post_id) {
    // Check if the user has permission to save the data
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Save No Index setting
    $no_index_value = isset($_POST['zynith_seo_no_index']) ? '1' : '';
    update_post_meta($post_id, '_zynith_seo_no_index', sanitize_text_field($no_index_value));

    // Save No Follow setting
    $no_follow_value = isset($_POST['zynith_seo_no_follow']) ? '1' : '';
    update_post_meta($post_id, '_zynith_seo_no_follow', sanitize_text_field($no_follow_value));
}
add_action('save_post', 'zynith_seo_save_robots_meta_box_data');

// Output custom robots meta tag with noindex, nofollow, and max-image-preview:large
function zynith_seo_output_robots_meta_tags() {
    if (is_singular()) {
        global $post;

        // Get the No Index and No Follow values
        $no_index = get_post_meta($post->ID, '_zynith_seo_no_index', true) ?: '0';
        $no_follow = get_post_meta($post->ID, '_zynith_seo_no_follow', true) ?: '0';

        $meta_content = [];

        if ($no_index === '1') {
            $meta_content[] = 'noindex';
        }
        else {
            $meta_content[] = 'index';
        }

        if ($no_follow === '1') {
            $meta_content[] = 'nofollow';
        }
        else {
            $meta_content[] = 'follow';
        }

        $meta_content[] = 'max-image-preview:large';

        echo '<meta name="robots" content="' . esc_attr(implode(', ', $meta_content)) . '">' . "\n";
    }
    elseif (is_category() || is_tag() || is_tax()) {
        
        // We’re in a taxonomy archive
        $term = get_queried_object();
        if (isset($term->term_id)) {
            
            // Check our stored 'noindex' value
            $no_index = get_term_meta($term->term_id, '_zynith_seo_no_index', true) ?: '0';
            
            // Initialize defaults
            $meta_content = [];

            // If "1", show noindex; otherwise "index"
            if ($no_index === '1') {
                $meta_content[] = 'noindex';
            }
            else {
                $meta_content[] = 'index';
            }

            $meta_content[] = 'follow';
            $meta_content[] = 'max-image-preview:large';
            echo '<meta name="robots" content="' . esc_attr(implode(', ', $meta_content)) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'zynith_seo_output_robots_meta_tags', 5);

// Hook for each public taxonomy.
function zynith_seo_init_taxonomy_noindex_fields() {
    $taxonomies = get_taxonomies(['public' => true], 'names');
    foreach ($taxonomies as $taxonomy) {
        add_action("{$taxonomy}_edit_form_fields", 'zynith_seo_add_taxonomy_noindex_field', 10, 1);
        add_action("edited_{$taxonomy}", 'zynith_seo_save_taxonomy_noindex_field', 10, 2);
    }
}
add_action('init', 'zynith_seo_init_taxonomy_noindex_fields');

// Output the "No Index" toggle on the edit term screen for any taxonomy
function zynith_seo_add_taxonomy_noindex_field( $term ) {
    // Get existing meta value (if any)
    $no_index = get_term_meta($term->term_id, '_zynith_seo_no_index', true);
    ?>
    <tr class="form-field zynith-seo-noindex-wrap">
        <th scope="row">
            <label for="zynith_seo_no_index"><?php esc_html_e('No Index', 'zynith-seo'); ?></label>
        </th>
        <td>
            <label style="display: inline-block; margin-right: 8px;">
                <input type="checkbox" name="zynith_seo_no_index" id="zynith_seo_no_index" value="1" <?php checked($no_index, '1'); ?> />
                <?php esc_html_e('Set this taxonomy archive to noindex', 'zynith-seo'); ?>
            </label>
            <p class="description"><?php esc_html_e('Check if you want search engines to NOT index this taxonomy archive.', 'zynith-seo'); ?></p>
        </td>
    </tr>
    <?php
}

// Save the "No Index" toggle for any taxonomy term
function zynith_seo_save_taxonomy_noindex_field($term_id) {
    if (!current_user_can('manage_categories')) return;
    
    // Save or clear the meta value
    $no_index_value = isset($_POST['zynith_seo_no_index']) ? '1' : '';
    update_term_meta($term_id, '_zynith_seo_no_index', $no_index_value);
}
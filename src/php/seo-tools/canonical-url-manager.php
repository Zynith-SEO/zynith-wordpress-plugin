<?php

defined('ABSPATH') or exit;

// Add meta box for canonical URL
function zynith_seo_add_canonical_meta_box() {
    $post_types = get_post_types(['public' => true], 'names');
    foreach ($post_types as $post_type) {
        add_meta_box(
            'zynith_seo_canonical_meta_box',
            'Canonical URL',
            'zynith_seo_render_canonical_meta_box',
            $post_type,
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'zynith_seo_add_canonical_meta_box');

// Render the canonical URL meta box
function zynith_seo_render_canonical_meta_box($post) {
    $canonical_url = get_post_meta($post->ID, '_zynith_seo_canonical_url', true);
    ?>
    <p>
        <label for="zynith_seo_canonical_url"><strong>Canonical URL:</strong></label>
        <input type="url" id="zynith_seo_canonical_url" name="zynith_seo_canonical_url" value="<?php echo esc_url($canonical_url); ?>" class="widefat" />
        <p class="description">Enter a canonical URL to overwrite the default one. This URL will also be included in the sitemap.</p>
    </p>
    <?php
}

// Save the canonical URL
function zynith_seo_save_canonical_meta_box_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['zynith_seo_canonical_url'])) {
        update_post_meta($post_id, '_zynith_seo_canonical_url', esc_url_raw($_POST['zynith_seo_canonical_url']));
    }
}
add_action('save_post', 'zynith_seo_save_canonical_meta_box_data');

// Output the custom canonical URL in the <head>
function zynith_seo_output_canonical_url() {
    if (is_singular()) {
        global $post;
        $canonical_url = get_post_meta($post->ID, '_zynith_seo_canonical_url', true);
        if ($canonical_url) {
            // Output the custom canonical URL
            echo '<link rel="canonical" href="' . esc_url($canonical_url) . '">' . "\n";
        }
        else {
            // Re-enable the default WordPress canonical behavior if no custom canonical is set
            add_action('wp_head', 'rel_canonical');
        }
    }
    elseif (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        if (!empty($term->term_id)) {
            $canonical_url = get_term_meta($term->term_id, '_zynith_seo_canonical_url', true);
            if ($canonical_url) {
                echo '<link rel="canonical" href="' . esc_url($canonical_url) . '">' . "\n";
                return;
            }
        }
        // If no custom canonical is set, use the default WordPress canonical
        add_action('wp_head', 'rel_canonical');
    }
}
add_action('wp_head', 'zynith_seo_output_canonical_url', 1);

// Register hooks for each public taxonomy
function zynith_seo_init_taxonomy_canonical() {
    $taxonomies = get_taxonomies(['public' => true], 'names');

    foreach ($taxonomies as $taxonomy) {
        
        // Add field when creating a new term
        add_action("{$taxonomy}_add_form_fields", 'zynith_seo_add_taxonomy_canonical_field', 10, 1);

        // Add field when editing an existing term
        add_action("{$taxonomy}_edit_form_fields", 'zynith_seo_edit_taxonomy_canonical_field', 10, 2);

        // Save term metadata on creation
        add_action("created_{$taxonomy}", 'zynith_seo_save_taxonomy_canonical_field', 10, 2);

        // Save term metadata on edit
        add_action("edited_{$taxonomy}", 'zynith_seo_save_taxonomy_canonical_field', 10, 2);
    }
}
add_action('init', 'zynith_seo_init_taxonomy_canonical');

// Display the canonical URL field when adding a new term.
function zynith_seo_add_taxonomy_canonical_field($taxonomy) {
    ?>
    <div class="form-field">
        <label for="zynith_seo_canonical_url"><?php esc_html_e('Canonical URL', 'zynith-seo'); ?></label>
        <input type="url" name="zynith_seo_canonical_url" id="zynith_seo_canonical_url" value="" />
        <p class="description">
            <?php esc_html_e('Enter a custom canonical URL for this term.', 'zynith-seo'); ?>
        </p>
    </div>
    <?php
}

// Display the canonical URL field when editing an existing term.
function zynith_seo_edit_taxonomy_canonical_field($term, $taxonomy) {
    $canonical_url = get_term_meta($term->term_id, '_zynith_seo_canonical_url', true);
    ?>
    <tr class="form-field">
        <th scope="row">
            <label for="zynith_seo_canonical_url"><?php esc_html_e('Canonical URL', 'zynith-seo'); ?></label>
        </th>
        <td>
            <input type="url"
                   name="zynith_seo_canonical_url"
                   id="zynith_seo_canonical_url"
                   value="<?php echo esc_url($canonical_url); ?>"
                   class="regular-text" />
            <p class="description">
                <?php esc_html_e('Enter a custom canonical URL to overwrite the default one for this term.', 'zynith-seo'); ?>
            </p>
        </td>
    </tr>
    <?php
}

// Save the canonical URL for the term.
function zynith_seo_save_taxonomy_canonical_field($term_id, $tt_id) {
    if (isset($_POST['zynith_seo_canonical_url'])) {
        $canonical_url = esc_url_raw($_POST['zynith_seo_canonical_url']);
        update_term_meta($term_id, '_zynith_seo_canonical_url', $canonical_url);
    }
}

// Disable WordPress native canonical URLs to avoid duplicates
function zynith_seo_disable_wp_canonical() {
    remove_action('wp_head', 'rel_canonical');
}
add_action('wp', 'zynith_seo_disable_wp_canonical');
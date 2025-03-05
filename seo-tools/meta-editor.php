<?php
/**
 * Module Name: Zynith SEO Meta Manager
 * Description: Adds a meta box for custom meta titles, descriptions, and OG tags. Outputs these meta tags in the <head> of the page. Supports any CPT, Categories, Tags, and Authors.
 * Version:     3.0.8
 * Author:      Zynith SEO
*/
defined('ABSPATH') or exit;

// Render the meta box fields with WordPress styling
function zynith_seo_render_meta_box($post) {
    $site_icon_url = get_site_icon_url(26);
    
    if (!$site_icon_url) $site_icon_url = ZYNITH_SEO_ICON;
            
    $site_title     = get_bloginfo('name') ?: '';
    $page_permalink = get_permalink($post->ID);
        
    // Define the placeholders
    $placeholder_title       = !empty(get_the_title($post->ID))     ? get_the_title($post->ID)      : '%%title%%';
    $placeholder_sitetitle   = !empty($site_title)                  ? esc_attr($site_title)         : '%%sitetitle%%';
    $placeholder_tagline     = !empty(get_bloginfo('description'))  ? get_bloginfo('description')   : '%%tagline%%';
    $placeholder_currentyear = date('Y');
        
    $meta_title             = get_post_meta($post->ID, '_zynith_seo_meta_title', true);
    $meta_description       = get_post_meta($post->ID, '_zynith_seo_meta_description', true);
    $og_meta_title          = get_post_meta($post->ID, '_zynith_seo_og_meta_title', true);
    $og_meta_description    = get_post_meta($post->ID, '_zynith_seo_og_meta_description', true);
    $og_meta_image          = get_post_meta($post->ID, '_zynith_seo_og_meta_image', true);
    $keyword                = get_post_meta($post->ID, '_zynith_seo_keyword', true); 
    ?>
    <style>
        #zynith-seo-google-preview:hover #zynith-seo-google-preview-title { text-decoration: underline; }
    </style>
    <p>
        <label><strong>Google Preview:</strong></label>
    </p>
    <div id="zynith-seo-google-preview" style="margin: -8px 0 22px 0; max-width: 625px; padding: 8px; background-color: #f8f8f8; border-radius: 12px;">
        <div>
            <div style="align-items: center; display: flex; width: 100%;">
                <div style="align-items: center; display: inline-flex; justify-content: center; height: 26px; width: 26px; margin: 0 12px 0 0; background-color: #f3f5f6;  border: 1px solid #dadce0; border-radius: 50%;">
                    <img src="<?php echo esc_attr($site_icon_url); ?>" style="height: 18px; width: 18px;" />
                </div>
                <div>
                    <cite style="display: block; font-size: 14px; font-family: Arial,sans-serif; font-style: normal; line-height: 20px; white-space: nowrap; color: #202124;"><?php echo esc_html($site_title); ?></cite>
                    <a href="<?php echo esc_url($page_permalink); ?>" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-family: Arial,sans-serif; text-decoration: none; font-size: 12px; line-height: 18px; color: #4d5156;" target="_blank"><?php echo esc_url($page_permalink); ?></a>
                </div>
            </div>
            <h3 id="zynith-seo-google-preview-title" style="display: inline-block; margin: 3px 0; max-width: 100%; width: 100%; padding: 5px 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: Arial,sans-serif; font-size: 20px; font-weight: 400; color: #1a0dab;"></h3>
        </div>
        <div class="zynith-seo-google-preview-description" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; max-width: 100%; overflow: hidden; font-family: Arial,sans-serif; font-size: 14px; font-weight: 400; color: #474747; line-height: 22px;"></div>
    </div>
    <p style="margin: 0;">
        <label for="zynith_seo_meta_title"><strong>Meta Title:</strong></label>
        <input type="text" id="zynith_seo_meta_title" name="zynith_seo_meta_title" value="<?php echo esc_attr($meta_title); ?>" class="widefat" />
        <p style="margin: 0; font-size: 14px;"><small><span id="meta_title_count"><?php echo strlen($meta_title); ?></span> characters</small></p>
    </p>
    <p style="margin: 0;">
        <label for="zynith_seo_meta_description"><strong>Meta Description:</strong></label>
        <textarea id="zynith_seo_meta_description" name="zynith_seo_meta_description" class="widefat" rows="2"><?php echo esc_textarea($meta_description); ?></textarea>
        <p style="margin: 0; font-size: 14px;"><small><span id="meta_description_count"><?php echo strlen($meta_description); ?></span> characters</small></p>
    </p>
    <p style="margin: 0;">
        <label for="zynith_seo_og_meta_title"><strong>OG Meta Title:</strong></label>
        <input type="text" id="zynith_seo_og_meta_title" name="zynith_seo_og_meta_title" value="<?php echo esc_attr($og_meta_title); ?>" class="widefat" />
        <p style="margin: 0; font-size: 14px;"><small><span id="og_meta_title_count"><?php echo strlen($og_meta_title); ?></span> characters</small></p>
    </p>
    <p style="margin: 0;">
        <label for="zynith_seo_og_meta_description"><strong>OG Meta Description:</strong></label>
        <textarea id="zynith_seo_og_meta_description" name="zynith_seo_og_meta_description" class="widefat" rows="1"><?php echo esc_textarea($og_meta_description); ?></textarea>
        <p style="margin: 0; font-size: 14px;"><small><span id="og_meta_description_count"><?php echo strlen($og_meta_description); ?></span> characters</small></p>
    </p>
    <p style="margin: 0;">
        <label for="zynith_seo_og_meta_image"><strong>OG Meta Image:</strong></label>
        <div style="align-items: center; display: flex; gap: 3px;">
            <input type="text" id="zynith_seo_og_meta_image" name="zynith_seo_og_meta_image" value="<?php echo esc_attr($og_meta_image); ?>" class="widefat" />
            <button type="button" class="button" id="zynith_seo_og_meta_image_button">Select Image</button>
        </div>
        <img id="zynith_seo_og_meta_image_preview" src="<?php echo $og_meta_image ? esc_url($og_meta_image) : ''; ?>" alt="" style="max-width: 100%; max-height: 100px; margin-top: 8px; <?php echo $og_meta_image ? '' : 'display:none;'; ?>" />
    </p>
    <p style="margin: 0;">
        <label for="zynith_seo_keyword"><strong>Keyword:</strong></label>
            <input type="text" id="zynith_seo_keyword" name="zynith_seo_keyword" value="<?php echo esc_attr($keyword); ?>" class="widefat" />
            <input type="hidden" id="zynith_seo_keyword_density" name="zynith_seo_keyword_density" value="<?php echo esc_attr(get_post_meta($post->ID, '_zynith_seo_keyword_density', true)); ?>" />
        <p style="margin: 0; font-size: 14px;">
            <small>Density: <span id="keyword_density"><?php echo esc_attr(get_post_meta($post->ID, '_zynith_seo_keyword_density', true)); ?></span>%</small>
        </p>
    </p>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const keywordInput = document.getElementById('zynith_seo_keyword');
        const densityOutput = document.getElementById('keyword_density');
        const densityInput = document.getElementById('zynith_seo_keyword_density'); // Hidden input
        const content = <?php echo json_encode(get_post_field('post_content', $post->ID)); ?>;

        keywordInput.addEventListener('input', function() {
            const keyword = keywordInput.value.trim().toLowerCase();
            if (!keyword) {
                densityOutput.textContent = '0';
                densityInput.value = '0'; // Save 0% if no keyword
                return;
            }

            const words = content.split(/\s+/).length;
            const matches = content.toLowerCase().match(new RegExp(`\\b${keyword}\\b`, 'g')) || [];
            const density = ((matches.length / words) * 100).toFixed(2);
            densityOutput.textContent = density;
            densityInput.value = density; // Save calculated density
        });
    });
    </script>
    <div style="display: flex; flex-direction: column; margin: 0 -12px; padding: 8px 12px 0; border-top: 1px solid #c3c4c7; gap: 1px;">
        <label style="display: flex; flex-direction: column;">
            <strong>Placeholders:</strong>
            <span><small><em>Place your cursor in any of the fields above to insert a placeholder.</em></small></span>
        </label>
        
        <div style="display: flex; flex-wrap: wrap; gap: 2px;">
            <button type="button" class="zynith-placeholder-button button button-primary button-large" 
                    data-placeholder="<?php echo $placeholder_title; ?>"
                    disabled>%%title%%</button>
            <button type="button" class="zynith-placeholder-button button button-primary button-large" 
                    data-placeholder="<?php echo $placeholder_sitetitle; ?>"
                    disabled>%%sitetitle%%</button>
            <button type="button" class="zynith-placeholder-button button button-primary button-large" 
                    data-placeholder="<?php echo $placeholder_tagline; ?>"
                    disabled>%%tagline%%</button>
            <button type="button" class="zynith-placeholder-button button button-primary button-large" 
                    data-placeholder="<?php echo $placeholder_currentyear; ?>"
                    disabled>%%currentyear%%</button>
        </div>

    </div>
    <?php
}

function zynith_seo_add_meta_editor() {
    
    // Get all public post types
    $post_types = get_post_types(['public' => true], 'names');
    foreach ($post_types as $post_type) {
        add_meta_box(
            'zynith_seo_meta_box',  // ID
            'Meta Information',     // Title
            'zynith_seo_render_meta_box',   // Callback function
            $post_type,                     // Post types (pages, posts, CPTs)
            'normal',                       // Context
            'high'                          // Priority
        );
    }
}
add_action('add_meta_boxes', 'zynith_seo_add_meta_editor');

// Meta Fields for Categories and Tags
function zynith_seo_add_meta_fields_to_taxonomies($term) {
    $meta_title         = get_term_meta($term->term_id, '_zynith_seo_meta_title', true);
    $meta_description   = get_term_meta($term->term_id, '_zynith_seo_meta_description', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="zynith_seo_meta_title">Meta Title</label></th>
        <td>
            <input type="text" name="zynith_seo_meta_title" id="zynith_seo_meta_title" value="<?php echo esc_attr($meta_title); ?>" class="widefat" />
            <p style="font-size: 14px;"><small><span id="meta_title_count"><?php echo strlen($meta_title); ?></span> characters</small></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="zynith_seo_meta_description">Meta Description</label></th>
        <td>
            <textarea name="zynith_seo_meta_description" id="zynith_seo_meta_description" rows="5" class="widefat"><?php echo esc_textarea($meta_description); ?></textarea>
            <p style="font-size: 14px;"><small><span id="meta_description_count"><?php echo strlen($meta_description); ?></span> characters</small></p>
        </td>
    </tr>
    <?php
}
add_action('category_edit_form_fields', 'zynith_seo_add_meta_fields_to_taxonomies');
add_action('post_tag_edit_form_fields', 'zynith_seo_add_meta_fields_to_taxonomies');

// Ensure Saving of Taxonomy Fields
function zynith_seo_save_taxonomy_meta($term_id) {
    if (isset($_POST['zynith_seo_meta_title'])) update_term_meta($term_id, '_zynith_seo_meta_title', sanitize_text_field($_POST['zynith_seo_meta_title']));
    if (isset($_POST['zynith_seo_meta_description'])) update_term_meta($term_id, '_zynith_seo_meta_description', sanitize_textarea_field($_POST['zynith_seo_meta_description']));
}
add_action('edited_category', 'zynith_seo_save_taxonomy_meta');
add_action('edited_post_tag', 'zynith_seo_save_taxonomy_meta');
add_action('create_category', 'zynith_seo_save_taxonomy_meta');
add_action('create_post_tag', 'zynith_seo_save_taxonomy_meta');

// Add Meta Fields for Custom Taxonomies and WooCommerce Categories
function zynith_seo_add_meta_fields_to_custom_taxonomies($term) {
    $meta_title = get_term_meta($term->term_id, '_zynith_seo_meta_title', true);
    $meta_description = get_term_meta($term->term_id, '_zynith_seo_meta_description', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="zynith_seo_meta_title">Meta Title</label></th>
        <td>
            <input type="text" name="zynith_seo_meta_title" id="zynith_seo_meta_title" value="<?php echo esc_attr($meta_title); ?>" class="widefat" />
            <p><small><span id="meta_title_count"><?php echo strlen($meta_title); ?></span> characters</small></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="zynith_seo_meta_description">Meta Description</label></th>
        <td>
            <textarea name="zynith_seo_meta_description" id="zynith_seo_meta_description" rows="5" class="widefat"><?php echo esc_textarea($meta_description); ?></textarea>
            <p><small><span id="meta_description_count"><?php echo strlen($meta_description); ?></span> characters</small></p>
        </td>
    </tr>
    <?php
}

function zynith_seo_save_custom_taxonomy_meta($term_id) {
    if (isset($_POST['zynith_seo_meta_title'])) update_term_meta($term_id, '_zynith_seo_meta_title', sanitize_text_field($_POST['zynith_seo_meta_title']));
    if (isset($_POST['zynith_seo_meta_description'])) update_term_meta($term_id, '_zynith_seo_meta_description', sanitize_textarea_field($_POST['zynith_seo_meta_description']));
}

// Retrieve custom public taxonomies
$all_custom_taxonomies = get_taxonomies([
    '_builtin' => false,
    'public'   => true,
], 'names');

// Hook each custom taxonomy to existing callback for displaying meta fields
foreach ($all_custom_taxonomies as $taxonomy) {
    add_action("{$taxonomy}_edit_form_fields", 'zynith_seo_add_meta_fields_to_custom_taxonomies');
    add_action("{$taxonomy}_add_form_fields",  'zynith_seo_add_meta_fields_to_custom_taxonomies');

    // Hook save function
    add_action("edited_{$taxonomy}", 'zynith_seo_save_custom_taxonomy_meta');
    add_action("create_{$taxonomy}", 'zynith_seo_save_custom_taxonomy_meta');
}

// Meta Fields for Authors
function zynith_seo_add_meta_fields_to_authors($user) {
    $meta_title = get_user_meta($user->ID, '_zynith_seo_meta_title', true);
    $meta_description = get_user_meta($user->ID, '_zynith_seo_meta_description', true);
    ?>
    <h3>Meta Information for Author Archive</h3>
    <table class="form-table">
        <tr>
            <th><label for="zynith_seo_meta_title_author">Meta Title</label></th>
            <td>
                <input type="text" name="zynith_seo_meta_title" id="zynith_seo_meta_title_author" value="<?php echo esc_attr($meta_title); ?>" class="widefat" />
                <p style="font-size: 14px;"><small><span id="meta_title_count_author"><?php echo strlen($meta_title); ?></span> characters</small></p>
            </td>
        </tr>
        <tr>
            <th><label for="zynith_seo_meta_description_author">Meta Description</label></th>
            <td><textarea name="zynith_seo_meta_description" id="zynith_seo_meta_description_author" rows="5" class="widefat"><?php echo esc_textarea($meta_description); ?></textarea>
                <p style="font-size: 14px;"><small><span id="meta_description_count_author"><?php echo strlen($meta_description); ?></span> characters</small></p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'zynith_seo_add_meta_fields_to_authors');
add_action('edit_user_profile', 'zynith_seo_add_meta_fields_to_authors');

// Ensure Saving of Author Meta Fields
function zynith_seo_save_author_meta($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        if (isset($_POST['zynith_seo_meta_title'])) update_user_meta($user_id, '_zynith_seo_meta_title', sanitize_text_field($_POST['zynith_seo_meta_title']));
        if (isset($_POST['zynith_seo_meta_description'])) update_user_meta($user_id, '_zynith_seo_meta_description', sanitize_textarea_field($_POST['zynith_seo_meta_description']));
    }
}
add_action('personal_options_update', 'zynith_seo_save_author_meta');
add_action('edit_user_profile_update', 'zynith_seo_save_author_meta');

function zynith_seo_meta_box_scripts($hook) {
    $valid_hooks = [
        'post.php', 'post-new.php', // post edit screens
        'edit-tags.php',            // category/tag edit pages
        'profile.php', 'user-edit.php', // user profile screens
        'term.php'
    ];
    if (in_array($hook, $valid_hooks, true)) {
        wp_enqueue_media();

        $js_file_path = ZYNITH_SEO_DIR . '/assets/js/meta-editor.js';
        $js_file_url  = ZYNITH_SEO_URL . '/assets/js/meta-editor.js';
        $version = file_exists($js_file_path) ? filemtime($js_file_path) : ZYNITH_SEO_VERSION;
        wp_enqueue_script('zynith-seo-meta-editor', $js_file_url, ['jquery'], $version, true);
    }
}
add_action('admin_enqueue_scripts', 'zynith_seo_meta_box_scripts');

// Ensure the meta box data is saved when the post is saved
function zynith_seo_save_meta_box_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (!isset($_POST['zynith_seo_meta_title']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-post_' . $post_id)) return;

    // Save Meta Title
    if (isset($_POST['zynith_seo_meta_title'])) update_post_meta($post_id, '_zynith_seo_meta_title', zynith_seo_custom_text_placeholder_sanitize($_POST['zynith_seo_meta_title']));
    
    // Save Meta Description
    if (isset($_POST['zynith_seo_meta_description'])) update_post_meta($post_id, '_zynith_seo_meta_description', zynith_seo_custom_text_placeholder_sanitize($_POST['zynith_seo_meta_description']));
    
    // Save OG Meta Title
    if (isset($_POST['zynith_seo_og_meta_title'])) update_post_meta($post_id, '_zynith_seo_og_meta_title', zynith_seo_custom_text_placeholder_sanitize($_POST['zynith_seo_og_meta_title']));
    
    // Save OG Meta Description
    if (isset($_POST['zynith_seo_og_meta_description'])) update_post_meta($post_id, '_zynith_seo_og_meta_description', zynith_seo_custom_text_placeholder_sanitize($_POST['zynith_seo_og_meta_description']));
    
    // Save OG Meta Image
    if (isset($_POST['zynith_seo_og_meta_image'])) update_post_meta($post_id, '_zynith_seo_og_meta_image', esc_url_raw($_POST['zynith_seo_og_meta_image']));

    // Save Keyword and Density
    if (isset($_POST['zynith_seo_keyword'])) update_post_meta($post_id, '_zynith_seo_keyword', sanitize_text_field($_POST['zynith_seo_keyword']));
    if (isset($_POST['zynith_seo_keyword_density'])) update_post_meta($post_id, '_zynith_seo_keyword_density', sanitize_text_field($_POST['zynith_seo_keyword_density']));
}
add_action('save_post', 'zynith_seo_save_meta_box_data');

// Output Meta Tags and OG Tags in the <head> section
function zynith_seo_output_meta_tags() {
    $meta_title             = '';
    $meta_description       = '';
    $og_meta_title          = '';
    $og_meta_description    = '';
    $og_meta_image          = '';
    $object_type            = '';
    $object                 = null;

    if (is_singular()) {
        global $post;
        $object         = $post;
        $object_type    = 'post';
        
        // Get meta values from post meta
        $meta_title             = get_post_meta($post->ID, '_zynith_seo_meta_title', true);
        $meta_description       = get_post_meta($post->ID, '_zynith_seo_meta_description', true);
        $og_meta_title          = get_post_meta($post->ID, '_zynith_seo_og_meta_title', true);
        $og_meta_description    = get_post_meta($post->ID, '_zynith_seo_og_meta_description', true);
        $og_meta_image          = get_post_meta($post->ID, '_zynith_seo_og_meta_image', true);

           // Fallback 1: Use Featured Image if OG Image is not set
        if (empty($og_meta_image) && has_post_thumbnail($post->ID)) {
            $og_meta_image = get_the_post_thumbnail_url($post->ID, 'full');
        }

        // Fallback 2: Get the First Image from the Post Content
        if (empty($og_meta_image)) {
            $post_content = get_post_field('post_content', $post->ID);
        preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $post_content, $matches);
        if (!empty($matches[1])) {
            $og_meta_image = esc_url($matches[1]);
            }
        }

        // Fallbacks for meta title and description
        $meta_title         = !empty($meta_title) ? zynithseo_replace_placeholders_in_content($meta_title, $object_type, $object) : get_the_title($post->ID);
        $meta_description   = !empty($meta_description) ? zynithseo_replace_placeholders_in_content($meta_description, $object_type, $object) : wp_trim_words(get_the_excerpt($post->ID), 30);

        $og_meta_title      = !empty($og_meta_title) ? zynithseo_replace_placeholders_in_content($og_meta_title, $object_type, $object) : esc_attr($meta_title);
        $og_meta_description = !empty($og_meta_description) ? zynithseo_replace_placeholders_in_content($og_meta_description, $object_type, $object) : esc_attr($meta_description);
    } 
    elseif (is_category() || is_tag() || is_tax()) {
        $object_type = 'taxonomy';
        $term        = get_queried_object();
        $object      = $term;
        $term_id     = $term->term_id;

        // Get meta values from taxonomy meta
        $meta_title         = get_term_meta($term_id, '_zynith_seo_meta_title', true);
        $meta_description   = get_term_meta($term_id, '_zynith_seo_meta_description', true);

        // Fallbacks for WooCommerce and taxonomy meta titles/descriptions
        if (empty($meta_title)) $meta_title = single_term_title('', false);
        if (empty($meta_description)) $meta_description = term_description($term_id);
        
        $og_meta_title          = !empty($meta_title) ? esc_attr($meta_title) : '';
        $og_meta_description    = !empty($meta_description) ? esc_attr($meta_description) : '';
    } 
    elseif (is_author()) {
        $object_type = 'author';
        $author      = get_queried_object();
        $object      = $author;
        $user_id     = $author->ID;

        // Get meta values for author archives
        $meta_title         = get_user_meta($user_id, '_zynith_seo_meta_title', true);
        $meta_description   = get_user_meta($user_id, '_zynith_seo_meta_description', true);

        // Fallback for author meta title
        if (empty($meta_title)) $meta_title = get_the_author_meta('display_name', $user_id);
        
        $og_meta_title      = $meta_title;
        $og_meta_description = $meta_description;
    }
    elseif (is_home()) {
        // This is the "blog" page, if user set it under Settings > Reading
        $object_type        = 'home';
        $og_meta_title      = get_option('blogname');
        $meta_title         = get_option('blogname');
        $meta_description   = get_option('blogdescription');
    }

    // Append meta description
    if (!empty($meta_description)) echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
    
    // Append OpenGraph tags
    if (!empty($og_meta_title)) echo '<meta property="og:title" content="' . esc_attr($og_meta_title) . '">' . "\n";
    if (!empty($og_meta_description)) echo '<meta property="og:description" content="' . esc_attr($og_meta_description) . '">' . "\n";
    if (!empty($og_meta_image)) echo '<meta property="og:image" content="' . esc_url($og_meta_image) . '">' . "\n";
}
add_action('wp_head', 'zynith_seo_output_meta_tags', 5);

// Override Default Titles
function zynith_seo_modify_title_parts($title_parts) {
    $meta_title = '';
    $object_type = '';
    $object = null;
    
    if (is_singular()) {
        global $post;
        $meta_title = get_post_meta($post->ID, '_zynith_seo_meta_title', true);
        $object_type = 'post';
        $object = $post;
    }
    elseif (is_category() || is_tag() || is_tax()) {
        $term       = get_queried_object();
        $meta_title = get_term_meta($term->term_id, '_zynith_seo_meta_title', true);
        if (!empty($meta_title)) $title_parts['title'] = $meta_title;  // Rebuild the 'title'
        $object_type = 'taxonomy';
        $object = $term;
    }
    elseif (is_author()) {
        $author = get_queried_object();
        $meta_title = get_user_meta($author->ID, '_zynith_seo_meta_title', true);
        $object_type = 'author';
        $object = $author;
    }
    
    // If a meta title is set, replace placeholders and modify title parts
    if (!empty($meta_title)) {
        // Replace any placeholders in the meta title
        $meta_title = zynithseo_replace_placeholders_in_content($meta_title, $object_type, $object);
        $title_parts['title'] = $meta_title;
        unset($title_parts['site'], $title_parts['tagline']);
    }

    return $title_parts;
}
add_filter('document_title_parts', 'zynith_seo_modify_title_parts', 10);
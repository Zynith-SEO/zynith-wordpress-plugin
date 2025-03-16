<?php

defined('ABSPATH') or exit;

function zynith_seo_create_schema_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_schema_settings';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        page_id bigint(20) UNSIGNED NOT NULL,
        schema_data longtext NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Register metabox for schema editor
function zynith_seo_add_schema_editor_metabox() {
    // Get all public post types, including custom post types
    $post_types = get_post_types(array('public' => true), 'names');
    foreach ($post_types as $post_type) {
        add_meta_box(
            'zynith_seo_schema_editor',
            'Schema Editor',
            'zynith_seo_schema_editor_metabox_callback',
            $post_type,
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'zynith_seo_add_schema_editor_metabox');

// Metabox callback to display schema editor
function zynith_seo_schema_editor_metabox_callback($post) {
    global $wpdb;
    
    // Use the WordPress table prefix
    $table_name = $wpdb->prefix . 'zynith_schema_settings';
    $post_id = $post->ID;

    // Define the placeholders
    $placeholder_title       = !empty(get_the_title($post->ID))     ? get_the_title($post->ID)      : '%%title%%';
    $placeholder_sitetitle   = !empty($site_title)                  ? esc_attr($site_title)         : '%%sitetitle%%';
    $placeholder_tagline     = !empty(get_bloginfo('description'))  ? get_bloginfo('description')   : '%%tagline%%';
    $placeholder_currentyear = date('Y');

    // Retrieve current schema data from the database
    $current_schema = '';
    $result = $wpdb->get_row($wpdb->prepare("SELECT schema_data FROM $table_name WHERE page_id = %d", $post_id));
    if ($result) $current_schema = esc_textarea($result->schema_data);

    // Security nonce for verification on save
    wp_nonce_field('zynith_seo_schema_save', 'zynith_seo_schema_nonce');
    ?>

    <!-- Label + Textarea for the Schema JSON-LD -->
    <label for="zynith_seo_schema_data">Edit Schema (JSON-LD):</label>
    <textarea id="zynith_seo_schema_data" name="zynith_seo_schema_data" rows="10" style="width:100%;"><?php echo $current_schema; ?></textarea>
    <p class="description">Enter the JSON-LD schema for this page. Ensure the format is correct.</p>

    <!-- Placeholder buttons section -->
    <div style="display: flex; flex-direction: column; margin: 0 -12px; padding: 8px 12px 0; border-top: 1px solid #c3c4c7; gap: 1px;">
        <label style="display: flex; flex-direction: column;">
            <strong>Placeholders:</strong>
            <span><small><em>Place your cursor in the field above to insert a placeholder.</em></small></span>
        </label>
        
        <div style="display: flex; flex-wrap: wrap; gap: 2px;">
            <button type="button"
                    class="zynith-placeholder-button button button-primary button-large"
                    data-placeholder="<?php echo esc_attr($placeholder_title); ?>"
                    disabled>%%title%%</button>
            
            <button type="button"
                    class="zynith-placeholder-button button button-primary button-large"
                    data-placeholder="<?php echo esc_attr($placeholder_sitetitle); ?>"
                    disabled>%%sitetitle%%</button>
            
            <button type="button"
                    class="zynith-placeholder-button button button-primary button-large"
                    data-placeholder="<?php echo esc_attr($placeholder_tagline); ?>"
                    disabled>%%tagline%%</button>
            <button type="button"
                    class="zynith-placeholder-button button button-primary button-large"
                    data-placeholder="%%home_url%%"
                    disabled>%%home_url%%</button>
            <button type="button"
                    class="zynith-placeholder-button button button-primary button-large"
                    data-placeholder="%%post_url%%"
                    disabled>%%post_url%%</button>
            <button type="button"
                    class="zynith-placeholder-button button button-primary button-large"
                    data-placeholder="<?php echo esc_attr($placeholder_currentyear); ?>"
                    disabled>%%currentyear%%</button>
            <button type="button"
                    class="zynith-placeholder-button button button-primary button-large"
                    data-placeholder="%%date%%"
                    disabled>%%date%%</button>
            <button type="button"
                    class="zynith-placeholder-button button button-primary button-large"
                    data-placeholder="%%modified%%"
                    disabled>%%modified%%</button>
            <button type="button"
                    class="zynith-placeholder-button button button-primary button-large"
                    data-placeholder="%%author%%"
                    disabled>%%author%%</button>
        </div>
    </div>
    <?php
}

// Save schema data on post save
function zynith_seo_save_schema_data($post_id) {
    // Verify nonce
    if (!isset($_POST['zynith_seo_schema_nonce']) || !wp_verify_nonce($_POST['zynith_seo_schema_nonce'], 'zynith_seo_schema_save')) return;

    // Check for autosave, revision, and permissions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;
    if (!current_user_can('edit_post', $post_id)) return;

    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_schema_settings';

    // Sanitize schema input
    $schema_data = isset($_POST['zynith_seo_schema_data']) ? stripslashes($_POST['zynith_seo_schema_data']) : '';
    
    // Remove <script> tags if they exist
    $schema_data = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '$1', $schema_data);

    // Insert or update schema data for this post/page
    $existing_entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE page_id = %d", $post_id));
    if ($existing_entry) {
        $wpdb->update(
            $table_name,
            array('schema_data' => $schema_data),
            array('page_id' => $post_id),
            array('%s'),
            array('%d')
        );
    }
    else {
        $wpdb->insert(
            $table_name,
            array('page_id' => $post_id, 'schema_data' => $schema_data),
            array('%d', '%s')
        );
    }
}
add_action('save_post', 'zynith_seo_save_schema_data');

// Output custom schema if it exists for the current page/post
function zynith_seo_output_custom_schema() {
    if (is_singular()) {
        global $wpdb, $post;
        $table_name = $wpdb->prefix . 'zynith_schema_settings';
        $schema_data = $wpdb->get_var($wpdb->prepare("SELECT schema_data FROM $table_name WHERE page_id = %d", $post->ID));

        if ($schema_data) {
            // Replace placeholders in the schema data
            $schema_data = zynithseo_replace_placeholders_in_content($schema_data, 'post', $post);
            
            // Decode and re-encode the JSON for pretty print
            $decoded_schema = json_decode(strip_tags($schema_data), true);
            if (json_last_error() === JSON_ERROR_NONE) $schema_data = json_encode($decoded_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            
            // Output schema with comment tags around the script
            echo "\n<!-- Custom Schema -->\n";
            echo '<script type="application/ld+json">';
            echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            echo '</script>';
            echo "\n<!-- End Custom Schema -->\n";
        }
    }
}
add_action('wp_head', 'zynith_seo_output_custom_schema');
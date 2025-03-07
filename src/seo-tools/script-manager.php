<?php
/**
 * Module Name: Zynith SEO - Script Manager
 * Description: Zynith SEO module to manage scripts on a per-page/post basis with the ability to create, retrieve, and delete scripts. Supports pages, posts, custom post types, categories, tags, and authors.
 * Version:     1.2.5
 * Author:      Zynith SEO
*/
defined('ABSPATH') or exit;

// Add the Script Manager menu under Zynith SEO
function zynith_seo_script_manager_menu() {
    if (get_option('zynith_seo_disable_script_manager')) return;
    add_submenu_page(
        'zynith_seo_dashboard',       // Parent slug, ensures it appears under Zynith SEO
        'Script Manager',             // Page title
        'Script Manager',             // Menu title
        'manage_options',             // Capability
        'zynith_seo_script_manager',  // Menu slug
        'zynith_seo_render_script_manager_page'  // Function to display the page
    );
}
add_action('admin_menu', 'zynith_seo_script_manager_menu');

// Handle form submission and ensure form submission processing before output is sent
add_action('admin_init', 'zynith_seo_check_form_submission');
function zynith_seo_check_form_submission() {
    if (get_option('zynith_seo_disable_script_manager')) return;
    if (isset($_POST['action']) && $_POST['action'] === 'zynith_seo_add_script') {
        
        $result = zynith_seo_add_script();
        if ($result !== false) {
            wp_redirect(
                add_query_arg(
                    [
                        'notice'  => 'success',
                        'message' => urlencode('Script added successfully!'),
                    ],
                    admin_url('admin.php?page=zynith_seo_script_manager')
                )
            );
            exit;
        }
        else {
            // If failed:
            wp_redirect(
                add_query_arg(
                    [
                        'notice'  => 'error',
                        'message' => urlencode('Failed to add script.'),
                    ],
                    admin_url('admin.php?page=zynith_seo_script_manager')
                )
            );
            exit;
        }

    }
    elseif (isset($_POST['action']) && $_POST['action'] === 'zynith_seo_update_script') {
        
        $result = zynith_seo_update_script();
        if ($result !== false) {
            wp_redirect(
                add_query_arg(
                    [
                        'notice'  => 'success',
                        'message' => urlencode('Script updated successfully!'),
                    ],
                    admin_url('admin.php?page=zynith_seo_script_manager')
                )
            );
            exit;
        }
        else {
            wp_redirect(
                add_query_arg(
                    [
                        'notice'  => 'error',
                        'message' => urlencode('Failed to update script.'),
                    ],
                    admin_url('admin.php?page=zynith_seo_script_manager')
                )
            );
            exit;
        }
    }
}

// Add a script to the database
function zynith_seo_add_script() {
    global $wpdb;
    $table_name         = $wpdb->prefix . 'zynith_seo_scripts';
       $allowed_html = [
        'a' => ['href' => true, 'title' => true, 'target' => true, 'rel' => true],
        'abbr' => ['title' => true, 'class' => true, 'id' => true, 'style' => true],
        'address' => ['class' => true, 'id' => true, 'style' => true],
        'bdi' => ['class' => true, 'id' => true],
        'br' => [],
        'button' => ['class' => true, 'id' => true, 'style' => true, 'type' => true, 'onclick' => true],
        'div' => ['class' => true, 'id' => true, 'style' => true],
        'iframe' => ['src' => true, 'height' => true, 'width' => true, 'style' => true, 'allow' => true, 'allowfullscreen' => true],
        'img' => ['src' => true, 'alt' => true, 'width' => true, 'height' => true, 'class' => true, 'id' => true, 'style' => true],
        'li' => ['class' => true, 'id' => true, 'style' => true],
        'meta' => ['name' => true, 'content' => true],
        'noscript' => [],
        'ol' => ['class' => true, 'id' => true, 'style' => true],
        'p' => ['class' => true, 'id' => true, 'style' => true],
        'script' => ['src' => true, 'type' => true, 'async' => true, 'defer' => true],
        'span' => ['class' => true, 'id' => true, 'style' => true],
        'style' => ['src' => true, 'type' => true],
        'ul' => ['class' => true, 'id' => true, 'style' => true],
        'svg' => [
            'class' => true,
            'id' => true,
            'style' => true,
            'width' => true,
            'height' => true,
            'xmlns' => true,
            'viewBox' => true,
            'fill' => true
        ],
        'path' => [
            'd' => true,
            'fill' => true,
            'stroke' => true,
            'stroke-width' => true
        ],
        'circle' => [
            'cx' => true,
            'cy' => true,
            'r' => true,
            'fill' => true
        ],
        'rect' => [
            'x' => true,
            'y' => true,
            'width' => true,
            'height' => true,
            'fill' => true
        ],
        'g' => ['class' => true, 'id' => true]
    ];
    
    $script_name        = sanitize_text_field($_POST['script_name']);
    $script_text        = wp_kses(wp_unslash($_POST['script_text']), $allowed_html);
    $script_location    = sanitize_text_field($_POST['script_location']);
    $enabled            = isset($_POST['script_enabled']) ? 1 : 0;
    
    // If "all_pages" is checked, store 'all'. Otherwise, store the selected IDs
    if (isset($_POST['all_pages'])) {
        $target_pages = 'all';
    }
    else {
        $target_pages = !empty($_POST['target_pages']) && is_array($_POST['target_pages']) ? implode(',', array_map('sanitize_text_field', $_POST['target_pages'])) : '';
    }
    
    $result = $wpdb->insert(
        $table_name,
        [
            'script_name'     => $script_name,
            'script_text'     => $script_text,
            'script_location' => $script_location,
            'target_pages'    => $target_pages,
            'enabled'         => $enabled
        ],
        ['%s', '%s', '%s', '%s', '%d']
    );
    return $result;
}

// Update an existing script in database
function zynith_seo_update_script() {
    global $wpdb;
    $table_name         = $wpdb->prefix . 'zynith_seo_scripts';
    $allowed_html       = [
        'a' => ['href' => true, 'title' => true, 'target' => true, 'rel' => true],
        'abbr' => ['title' => true, 'class' => true, 'id' => true, 'style' => true],
        'address' => ['class' => true, 'id' => true, 'style' => true],
        'bdi' => ['class' => true, 'id' => true],
        'br' => [],
        'button' => ['class' => true, 'id' => true, 'style' => true, 'type' => true, 'onclick' => true],
        'div' => ['class' => true, 'id' => true, 'style' => true],
        'iframe' => ['src' => true, 'height' => true, 'width' => true, 'style' => true, 'allow' => true, 'allowfullscreen' => true],
        'img' => ['src' => true, 'alt' => true, 'width' => true, 'height' => true, 'class' => true, 'id' => true, 'style' => true],
        'li' => ['class' => true, 'id' => true, 'style' => true],
        'meta' => ['name' => true, 'content' => true],
        'noscript' => [],
        'ol' => ['class' => true, 'id' => true, 'style' => true],
        'p' => ['class' => true, 'id' => true, 'style' => true],
        'script' => ['src' => true, 'type' => true, 'async' => true, 'defer' => true],
        'span' => ['class' => true, 'id' => true, 'style' => true],
        'style' => ['src' => true, 'type' => true],
        'ul' => ['class' => true, 'id' => true, 'style' => true],
        'svg' => [
            'class' => true,
            'id' => true,
            'style' => true,
            'width' => true,
            'height' => true,
            'xmlns' => true,
            'viewBox' => true,
            'fill' => true
        ],
        'path' => [
            'd' => true,
            'fill' => true,
            'stroke' => true,
            'stroke-width' => true
        ],
        'circle' => [
            'cx' => true,
            'cy' => true,
            'r' => true,
            'fill' => true
        ],
        'rect' => [
            'x' => true,
            'y' => true,
            'width' => true,
            'height' => true,
            'fill' => true
        ],
        'g' => ['class' => true, 'id' => true]
    ];  
    $edit_id            = intval($_POST['edit_id']);
    $script_name        = sanitize_text_field($_POST['script_name']);
    $script_text        = wp_kses(wp_unslash($_POST['script_text']), $allowed_html);
    $script_location    = sanitize_text_field($_POST['script_location']);
    $enabled            = isset($_POST['script_enabled']) ? 1 : 0;
    
    if (isset($_POST['all_pages'])) {
        $target_pages = 'all';
    }
    else {
        $target_pages = !empty($_POST['target_pages']) && is_array($_POST['target_pages']) ? implode(',', array_map('sanitize_text_field', $_POST['target_pages'])) : '';
    }
    
    $update_data = [
        'script_name'     => $script_name,
        'script_text'     => $script_text,
        'script_location' => $script_location,
        'target_pages'    => $target_pages,
        'enabled'         => $enabled
    ];
    $result = $wpdb->update(
        $table_name,
        $update_data,
        ['id' => $edit_id ],
        ['%s', '%s', '%s', '%s', '%d' ],
        ['%d' ]
    );
    return $result;
}

// Render the Script Manager page
function zynith_seo_render_script_manager_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_seo_scripts';

    // Handle delete row action (for scripts)
    if (isset($_POST['delete_row'])) {
        $row_id = intval($_POST['delete_row']);
        $deleted = $wpdb->delete($table_name, array('id' => $row_id), array('%d'));
        if ($deleted !== false) {
            wp_redirect(
                add_query_arg(
                    [
                        'notice'  => 'success',
                        'message' => urlencode('Script deleted successfully!'),
                    ],
                    admin_url('admin.php?page=zynith_seo_script_manager')
                )
            );
            exit();
        }
        else {
            wp_redirect(
                add_query_arg(
                    [
                        'notice'  => 'error',
                        'message' => urlencode('Failed to delete script.'),
                    ],
                    admin_url('admin.php?page=zynith_seo_script_manager')
                )
            );
            exit();
        }
    }
    
    // Handle toggle enable/disable action
    if (isset($_POST['toggle_enabled'])) {
        $row_id         = intval($_POST['toggle_enabled']);
        $current_value  = $wpdb->get_var($wpdb->prepare("SELECT enabled FROM $table_name WHERE id = %d", $row_id));
        $new_value      = $current_value ? 0 : 1;
        $updated        = $wpdb->update($table_name, array('enabled' => $new_value), array('id' => $row_id), array('%d'), array('%d'));
        if ($updated !== false) {
            $message_text = $new_value ? 'Script is now enabled.' : 'Script is now disabled.';
            wp_redirect(
                add_query_arg(
                    [
                        'notice'  => 'success',
                        'message' => urlencode($message_text),
                    ],
                    admin_url('admin.php?page=zynith_seo_script_manager')
                )
            );
            exit();
        }
        else {
            wp_redirect(
                add_query_arg(
                    [
                        'notice'  => 'error',
                        'message' => urlencode('Failed to toggle script.'),
                    ],
                    admin_url('admin.php?page=zynith_seo_script_manager')
                )
            );
            exit();
        }
    }
            
    // Handle create table action
    if (isset($_POST['create_script_table'])) zynith_seo_create_script_table();
    
    // Handle delete table action
    if (isset($_POST['delete_script_table'])) zynith_seo_delete_script_table();
    
    $edit_script_data = null;
    if (!empty($_GET['edit_script_id'])) {
        $edit_id = intval($_GET['edit_script_id']);
        $edit_script_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $edit_id));
    }
    
    // Check if the table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        // If the table does not exist, show the "Create Script Database" button
        echo '<div class="wrap"><h1>' . __('Zynith SEO Script Manager', ZYNITH_SEO_TEXT_DOMAIN) . '</h1>';
        echo '<form method="post"><input type="submit" name="create_script_table" class="button-primary" value="Create Script Database" /></form></div>';
    }
    else {
        // If the table exists, display the form and existing scripts
        $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
        ?>
        <div class="wrap">
            <h1><?php _e('Zynith SEO Script Manager', ZYNITH_SEO_TEXT_DOMAIN); ?></h1>
            <h2>
                <?php echo $edit_script_data ? __('Edit Script', 'ZYNITH_SEO_TEXT_DOMAIN') 
                                             : __('Add New Script', 'ZYNITH_SEO_TEXT_DOMAIN'); ?>
            </h2>
            <?php zynith_seo_render_script_form($edit_script_data); ?>
            
            <h2><?php _e('Existing Scripts', ZYNITH_SEO_TEXT_DOMAIN); ?></h2>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th><?php _e('Script Name', ZYNITH_SEO_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Location', ZYNITH_SEO_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Target Pages/Posts', ZYNITH_SEO_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Script Content', ZYNITH_SEO_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Enabled', ZYNITH_SEO_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Actions', ZYNITH_SEO_TEXT_DOMAIN); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results) : ?>
                        <?php foreach ($results as $row) : ?>
                            <tr>
                                <td><?php echo esc_html($row->script_name); ?></td>
                                <td><?php echo esc_html($row->script_location); ?></td>
                                <td><?php echo zynith_seo_convert_ids_to_titles($row->target_pages); ?></td>
                                <td>
                                    <?php 
                                    $script_display = (strlen($row->script_text) > 100) ? substr($row->script_text, 0, 100) . '...' : $row->script_text;
                                    echo '<pre style="overflow: hidden;">' . esc_html($script_display) . '</pre>'; 
                                    ?>
                                </td>
                                <td>
                                    <?php echo ($row->enabled) 
                                        ? __('Enabled', ZYNITH_SEO_TEXT_DOMAIN) 
                                        : __('Disabled', ZYNITH_SEO_TEXT_DOMAIN); ?>
                                </td>
                                <td style="display: flex; gap: 2px;">
                                    <form method="post">
                                        <input type="hidden" name="delete_row" value="<?php echo esc_attr($row->id); ?>">
                                        <input type="submit" class="button-secondary" value="Delete">
                                    </form>
                                    <form method="get" action="<?php echo admin_url('admin.php'); ?>">
                                        <input type="hidden" name="page" value="zynith_seo_script_manager" />
                                        <input type="hidden" name="edit_script_id" value="<?php echo esc_attr($row->id); ?>" />
                                        <input type="submit" class="button-secondary" value="<?php esc_attr_e('Edit', ZYNITH_SEO_TEXT_DOMAIN); ?>">
                                    </form>
                                    <form method="post">
                                        <input type="hidden" name="toggle_enabled" value="<?php echo esc_attr($row->id); ?>">
                                        <input type="submit" class="button-secondary" 
                                               value="<?php echo $row->enabled 
                                                   ? esc_attr__('Disable', ZYNITH_SEO_TEXT_DOMAIN) 
                                                   : esc_attr__('Enable', ZYNITH_SEO_TEXT_DOMAIN); ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5"><?php _e('No scripts added yet.', ZYNITH_SEO_TEXT_DOMAIN); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <form method="post" style="margin-top: 20px;">
                <input type="submit" name="delete_script_table" class="button-secondary" value="Delete Table" />
            </form>
        </div>
        <?php
    }
}

// Create the table for storing scripts
function zynith_seo_create_script_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_seo_scripts';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        script_name varchar(255) NOT NULL,
        script_text longtext NOT NULL,
        script_location varchar(255) NOT NULL,
        target_pages longtext NOT NULL,
        enabled tinyint(1) NOT NULL DEFAULT 1,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    zynith_seo_migrate_old_scripts_table_data();
}

// Migrate old script table data to new table.
function zynith_seo_migrate_old_scripts_table_data() {
    global $wpdb;
    
    $old_table_name = $wpdb->prefix . 'zynith_snippets';
    $new_table_name = $wpdb->prefix . 'zynith_seo_scripts';
    
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$old_table_name}'");
    if ($table_exists === $old_table_name) {
        $old_rows = $wpdb->get_results("SELECT * FROM {$old_table_name}");
        foreach ($old_rows as $old_row) {
            $old_target_pages = $old_row->pages_posts;

            if ($old_target_pages === 'all' || empty($old_target_pages)) {
                $new_target_pages = 'all';
            }
            else {
                $ids_array     = explode(',', $old_target_pages);
                $formatted_ids = array();

                foreach ($ids_array as $id_str) {
                    $id_str = trim($id_str);
                    
                    $post_object = get_post($id_str);
                    if ($post_object) {
                        $the_post_type = $post_object->post_type;
                        if ($the_post_type === 'page') {
                            $formatted_ids[] = 'page-' . $id_str;
                        }
                        elseif ($the_post_type === 'post') {
                            $formatted_ids[] = 'post-' . $id_str;
                        }
                        else {
                            $formatted_ids[] = 'custom-' . $id_str;
                        }
                    }
                    else {
                       $formatted_ids[] = 'post-' . $id_str; 
                    }
                }
                $new_target_pages = implode(',', $formatted_ids);
            }
            $mapped_data = array(
                'script_name'       => $old_row->name,
                'script_text'       => $old_row->code,
                'script_location'   => $old_row->placement,
                'target_pages'      => $new_target_pages,
                'enabled'           => 1
            );
            
            // Check if an identical record already exists
            $row_exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) 
                     FROM $new_table_name 
                     WHERE script_name = %s 
                       AND script_text = %s 
                       AND script_location = %s 
                       AND target_pages = %s",
                    $mapped_data['script_name'],
                    $mapped_data['script_text'],
                    $mapped_data['script_location'],
                    $mapped_data['target_pages']
                )
            );

            // Only insert if not already present
            if ($row_exists == 0) $wpdb->insert($new_table_name, $mapped_data);
        }
    }
}

// Delete the scripts table
function zynith_seo_delete_script_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zynith_seo_scripts';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Render the form to add new scripts
function zynith_seo_render_script_form($edit_script_data = null) {

    // If we have $edit_script_data, we are editing; else we are adding
    $is_edit_mode = ($edit_script_data !== null);

    // Decide which action to use
    $form_action = $is_edit_mode ? 'zynith_seo_update_script' : 'zynith_seo_add_script';

    // Pre-populate fields if in edit mode
    $script_name     = $is_edit_mode ? esc_attr($edit_script_data->script_name) : '';
    $script_text     = $is_edit_mode ? esc_textarea($edit_script_data->script_text) : '';
    $script_location = $is_edit_mode ? esc_attr($edit_script_data->script_location) : '';
    $target_pages    = $is_edit_mode ? $edit_script_data->target_pages : '';
    $enabled         = $is_edit_mode ? (bool) $edit_script_data->enabled : true;

    // If "all" was stored in DB, that means the script is for "All Pages."
    $is_all_pages    = ($target_pages === 'all');
    
    // Build the form
    echo '<form method="POST" action="">';
    // Add a hidden "action" to differentiate add vs update
    echo '<input type="hidden" name="action" value="' . $form_action . '">';

    // If we’re editing, pass the ID in a hidden field
    if ($is_edit_mode) echo '<input type="hidden" name="edit_id" value="' . intval($edit_script_data->id) . '">';
    
    echo '<label for="script_name">Script Name:</label><br>';
    echo '<input type="text" id="script_name" name="script_name" value="' . $script_name . '" required><br><br>';

    echo '<label for="script_text">Script:</label><br>';
    echo '<textarea id="script_text" name="script_text" rows="5" cols="50" required>' . $script_text . '</textarea><br><br>';

    echo '<label for="script_location">Location:</label><br>';
    echo '<select id="script_location" name="script_location">';
        echo '<option value="head" '   . selected($script_location, 'head', false)   . '>Head</option>';
        echo '<option value="body" '   . selected($script_location, 'body', false)   . '>Body</option>';
        echo '<option value="footer" ' . selected($script_location, 'footer', false) . '>Footer</option>';
    echo '</select><br><br>';
    
    $disabled = $is_all_pages ? 'disabled' : '';
    echo '<label for="target_pages">Target Pages/Posts:</label><br>';
    echo '<small style="display: inline-block; margin-top: 5px;">';
    echo '<strong>Tip:</strong> Hold <code>CTRL</code> (on Windows) or <code>CMD</code> (on Mac) to select or deselect multiple items. Holding <code>Shift</code> can select a range of pages.';
    echo '</small><br>';
    echo '<select id="target_pages" name="target_pages[]" multiple size="10" ' . $disabled . '>';
    zynith_seo_render_target_pages_options($target_pages);
    echo '</select><br>';
    
    echo '<div style="display: flex; margin: 3px 0 22px; gap: 11px;">';
    echo '<label for="all_pages_checkbox">';
    echo '<input type="checkbox" id="all_pages_checkbox" name="all_pages" value="1" ' 
        . checked($is_all_pages, true, false) . '>';
    echo ' All Pages';
    echo '</label>';
    echo '<label for="script_enabled">';
    echo '<input type="checkbox" id="script_enabled" name="script_enabled" value="1" ' 
       . checked($enabled, true, false) . '>';
    echo ' Enable Script';
    echo '</label></div>';

    // Submit
    $button_label = $is_edit_mode ? 'Update Script' : 'Add Script';
    echo '<input type="submit" name="submit_script" class="button-primary" value="' . esc_attr($button_label) . '">';
    ?>
    <script>
    (function(){
        var allPagesCheckbox = document.getElementById('all_pages_checkbox');
        var targetSelect     = document.getElementById('target_pages');
        if (!allPagesCheckbox || !targetSelect) return;
        allPagesCheckbox.addEventListener('change', function(){
            targetSelect.disabled = this.checked;
            if (this.checked) Array.from(targetSelect.options).forEach(o => o.selected = false);
        });
    })();
    </script>
    <?php
    echo '</form>';
}

// Render all pages/posts/custom post types/taxonomies/authors for the multi-select input
function zynith_seo_render_target_pages_options($selected_values = '') {
    // $selected_values might be a comma-separated string: "post-12,page-9"
    // We'll convert it into an array for easy detection
    $selected_array = explode(',', $selected_values);
    
    $pages = get_pages();
    foreach ($pages as $page) {
        $value = 'page-' . $page->ID;
        $is_selected = in_array($value, $selected_array, true) ? 'selected' : '';
        echo '<option value="' . esc_attr($value) . '" ' . $is_selected . '>' 
             . esc_html($page->post_title) . ' (Page)</option>';
    }
    
    $posts = get_posts(array('numberposts' => -1));
    foreach ($posts as $post) {
        $value = 'post-' . $post->ID;
        $is_selected = in_array($value, $selected_array, true) ? 'selected' : '';
        echo '<option value="' . esc_attr($value) . '" ' . $is_selected . '>' . $post->post_title . ' (Post)</option>';
    }
    
    $custom_post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
    foreach ($custom_post_types as $post_type) {
        $custom_posts = get_posts(array('post_type' => $post_type->name, 'numberposts' => -1));
        foreach ($custom_posts as $custom_post) echo '<option value="custom-' . $custom_post->ID . '">' . $custom_post->post_title . ' (' . $post_type->labels->singular_name . ')</option>';
    }

    $categories = get_categories();
    foreach ($categories as $category) echo '<option value="category-' . $category->term_id . '">' . $category->name . ' (Category)</option>';
    
    $tags = get_tags();
    foreach ($tags as $tag) echo '<option value="tag-' . $tag->term_id . '">' . $tag->name . ' (Tag)</option>';
    
    $authors = get_users(array('role__in' => array('administrator', 'editor', 'author')));
    foreach ($authors as $author) echo '<option value="author-' . $author->ID . '">' . $author->display_name . ' (Author)</option>';
}

// Convert IDs to titles for the "Target Pages/Posts" column
function zynith_seo_convert_ids_to_titles($target_pages) {
    if ($target_pages === 'all') return 'All';
        
    $target_pages_array = explode(',', $target_pages);
    $titles = array();

    foreach ($target_pages_array as $target) {
        if (strpos($target, 'page-') !== false) {
            $page_id = str_replace('page-', '', $target);
            $titles[] = get_the_title($page_id) . ' (Page)';
        }
        elseif (strpos($target, 'post-') !== false) {
            $post_id = str_replace('post-', '', $target);
            $titles[] = get_the_title($post_id) . ' (Post)';
        }
        elseif (strpos($target, 'custom-') !== false) {
            $custom_id = str_replace('custom-', '', $target);
            $titles[] = get_the_title($custom_id) . ' (Custom Post)';
        }
        elseif (strpos($target, 'category-') !== false) {
            $category_id = str_replace('category-', '', $target);
            $category = get_category($category_id);
            $titles[] = $category->name . ' (Category)';
        }
        elseif (strpos($target, 'tag-') !== false) {
            $tag_id = str_replace('tag-', '', $target);
            $tag = get_tag($tag_id);
            $titles[] = $tag->name . ' (Tag)';
        }
        elseif (strpos($target, 'author-') !== false) {
            $author_id = str_replace('author-', '', $target);
            $author = get_user_by('id', $author_id);
            $titles[] = $author->display_name . ' (Author)';
        }
    }
    return implode(', ', $titles);
}

// Hook to inject scripts in the head, body, or footer of specified pages
add_action('wp_head', 'zynith_seo_inject_scripts_in_head');
add_action('wp_body_open', 'zynith_seo_inject_scripts_in_body');
add_action('wp_footer', 'zynith_seo_inject_scripts_in_footer');

function zynith_seo_inject_scripts_in_head() {
    zynith_seo_inject_scripts('head');
}

function zynith_seo_inject_scripts_in_body() {
    zynith_seo_inject_scripts('body');
}

function zynith_seo_inject_scripts_in_footer() {
    zynith_seo_inject_scripts('footer');
}

// Inject scripts based on location and target pages with custom HTML comments
function zynith_seo_inject_scripts($location) {
    if (get_option('zynith_seo_disable_script_manager')) return;
    global $wpdb;
    $current_page_id  = get_queried_object_id();
    $current_post_type = get_post_type();
    $current_term_id  = (is_category() || is_tag()) ? get_queried_object()->term_id : null;
    $current_user_id  = get_the_author_meta('ID');
    $table_name       = $wpdb->prefix . 'zynith_seo_scripts';

    // Get all scripts for the current location (head/body/footer)
    $scripts = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE script_location = %s",
        $location
    ));

    // Add HTML comments around the scripts based on the location
    if ($location === 'head') {
        echo "\n<!-- Custom Head Script -->\n";
    }
    elseif ($location === 'body') {
        echo "\n<!-- Custom Body Script -->\n";
    }
    elseif ($location === 'footer') {
        echo "\n<!-- Custom Footer Script -->\n";
    }

    foreach ($scripts as $script) {
        if (!$script->enabled) continue;
        
        if ($script->target_pages === 'all') {
            echo $script->script_text;
            continue;
        }
        
        $target_pages = explode(',', $script->target_pages);
        
        // Check if the current page, post, category, tag, or author is in the target pages
        if (
            in_array('page-' . $current_page_id, $target_pages, true) ||
            in_array('post-' . $current_page_id, $target_pages, true) ||
            in_array('custom-' . $current_page_id, $target_pages, true) ||
            in_array('category-' . $current_term_id, $target_pages, true) ||
            in_array('tag-' . $current_term_id, $target_pages, true) ||
            in_array('author-' . $current_user_id, $target_pages, true)
        ) {
            echo $script->script_text;
        }
    }

    // Close the HTML comment after the scripts
    if ($location === 'head') {
        echo "\n<!-- End Custom Head Script -->\n";
    }
    elseif ($location === 'body') {
        echo "\n<!-- End Custom Body Script -->\n";
    }
    elseif ($location === 'footer') {
        echo "\n<!-- End Custom Footer Script -->\n";
    }
}

add_action('admin_notices', 'zynith_seo_display_notices');
function zynith_seo_display_notices() {
    if (!isset($_GET['notice']) || !isset($_GET['message'])) return;
    
    $notice_class   = ($_GET['notice'] === 'success') ? 'notice-success' : 'notice-error';
    $message        = urldecode($_GET['message']);

    echo '<div class="notice ' . esc_attr($notice_class) . ' is-dismissible">';
    echo '<p>' . esc_html($message) . '</p>';
    echo '</div>';
}
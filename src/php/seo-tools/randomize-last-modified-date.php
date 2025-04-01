<?php
defined('ABSPATH') or exit;

// Add submenu to Zynith SEO dashboard
function zynith_add_random_date_submenu() {
    add_submenu_page(
        'zynith_seo_dashboard',
        'Randomize Dates',
        'Random Date Editor',
        'manage_options',
        'zynith_random_date',
        'zynith_random_date_submenu_page'
    );
}
add_action('admin_menu', 'zynith_add_random_date_submenu');

// Display admin notices after randomization
function zynith_random_date_admin_notice() {
    if (isset($_GET['zynith_random_date_action']) && $_GET['zynith_random_date_action'] === 'success') {
        echo '<div class="notice notice-success is-dismissible"><p>Successfully randomized the selected dates.</p></div>';
    }
}
add_action('admin_notices', 'zynith_random_date_admin_notice');

// Get available CPTs dynamically
function zynith_get_cpt_options() {
    $post_types = get_post_types(['public' => true, '_builtin' => false], 'names');
    return array_values($post_types);
}

// Display submenu page
function zynith_random_date_submenu_page() {
    $cpt_options = zynith_get_cpt_options();
    ?>
    <div class="wrap">
        <h1>Randomize Dates</h1>
        <p>Select a date type, content type, and a date range to randomize.</p>
        
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="date_type">Date Type</label></th>
                    <td>
                        <select id="date_type" name="date_type" required>
                            <option value="post_modified">Modified Date</option>
                            <option value="post_date">Post Date</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="content_types">Content Types</label></th>
                    <td>
                        <select id="content_types" name="content_types[]" multiple required>
                            <option value="post">Posts</option>
                            <option value="page">Pages</option>
                            <?php foreach ($cpt_options as $cpt) { ?>
                                <option value="<?php echo esc_attr($cpt); ?>"><?php echo ucfirst($cpt); ?> (CPT)</option>
                            <?php } ?>
                            <option value="category">Categories</option>
                            <option value="tag">Tags</option>
                            <option value="author">Authors</option>
                            <option value="product">WooCommerce Products</option>
                        </select>
                        <p><em>Hold CTRL (Windows) or CMD (Mac) to select multiple options.</em></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="start_date">Start Date</label></th>
                    <td><input type="date" id="start_date" name="start_date" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="end_date">End Date</label></th>
                    <td><input type="date" id="end_date" name="end_date" required></td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" name="randomize_dates" class="button button-primary">Go</button>
            </p>
        </form>

        <?php
        // Handle Randomize Dates action
        if (isset($_POST['randomize_dates']) && !empty($_POST['date_type']) && !empty($_POST['content_types']) && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
            $date_type = sanitize_text_field($_POST['date_type']);
            $content_types = array_map('sanitize_text_field', $_POST['content_types']);
            $start_date = sanitize_text_field($_POST['start_date']);
            $end_date = sanitize_text_field($_POST['end_date']);

            zynith_randomize_selected_dates($date_type, $content_types, $start_date, $end_date);
            
            wp_redirect(add_query_arg('zynith_random_date_action', 'success', menu_page_url('zynith_random_date', false)));
            exit;
        }
        ?>
    </div>
    <?php
}

// Function to randomize selected dates
function zynith_randomize_selected_dates($date_type, $content_types, $start_date, $end_date) {
    global $wpdb;
    $start_timestamp = strtotime($start_date . ' 00:00:00');
    $end_timestamp = strtotime($end_date . ' 23:59:59');
    
    $updated_count = 0;

    foreach ($content_types as $type) {
        if (in_array($type, ['post', 'page', 'product']) || post_type_exists($type)) {
            // Handle posts, pages, CPTs, WooCommerce products
            $posts = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_type = '$type' AND post_status = 'publish'");
            foreach ($posts as $post) {
                $random_timestamp = mt_rand($start_timestamp, $end_timestamp);
                $random_date = date('Y-m-d H:i:s', $random_timestamp);
                $wpdb->update(
                    $wpdb->posts,
                    [
                        $date_type => $random_date,
                        $date_type . '_gmt' => gmdate('Y-m-d H:i:s', $random_timestamp)
                    ],
                    ['ID' => $post->ID]
                );
                $updated_count++;
            }
        } elseif ($type == 'category' || $type == 'tag') {
            // Handle Categories and Tags (Terms)
            $taxonomy = ($type == 'category') ? 'category' : 'post_tag';
            $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
            foreach ($terms as $term) {
                $random_timestamp = mt_rand($start_timestamp, $end_timestamp);
                update_term_meta($term->term_id, 'last_updated', $random_timestamp);
                $updated_count++;
            }
        } elseif ($type == 'author') {
            // Handle Authors
            $authors = get_users(['role__in' => ['author', 'editor', 'administrator']]);
            foreach ($authors as $author) {
                $random_timestamp = mt_rand($start_timestamp, $end_timestamp);
                update_user_meta($author->ID, 'last_updated', $random_timestamp);
                $updated_count++;
            }
        }
    }

    return $updated_count;
}
